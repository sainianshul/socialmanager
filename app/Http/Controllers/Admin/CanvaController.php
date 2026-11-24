<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CanvaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CanvaController extends Controller
{
    protected CanvaService $canvaService;

    public function __construct(CanvaService $canvaService)
    {
        $this->canvaService = $canvaService;
    }

    /**
     * Start Canva OAuth Authorization.
     */
    public function auth(Request $request): RedirectResponse
    {
        if (!$this->canvaService->isConfigured()) {
            return redirect()->route('admin.posts.create')
                ->with('error', 'Canva integration is not configured. Please check your credentials in .env.');
        }

        $authData = $this->canvaService->getAuthorizationUrl();

        session([
            'canva_oauth_verifier' => $authData['code_verifier'],
            'canva_oauth_state'    => $authData['state'],
        ]);

        return redirect()->away($authData['url']);
    }

    /**
     * Canva OAuth Callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('admin.posts.create')
                ->with('error', 'Canva connection was cancelled or rejected: ' . $request->input('error_description', $request->input('error')));
        }

        $code = $request->input('code');
        $state = $request->input('state');
        $savedState = session('canva_oauth_state');
        $verifier = session('canva_oauth_verifier');

        if (!$code || !$state || $state !== $savedState || !$verifier) {
            return redirect()->route('admin.posts.create')
                ->with('error', 'Invalid Canva authentication state. Please try connecting again.');
        }

        try {
            $tokens = $this->canvaService->exchangeCodeForToken($code, $verifier);

            $user = auth()->user();
            $userId = $user ? $user->id : 'guest';
            $ttl = $tokens['expires_in'] ?? 14400;
            $expiresAt = now()->addSeconds($ttl);

            if ($user) {
                $user->update([
                    'canva_access_token'     => $tokens['access_token'],
                    'canva_refresh_token'    => $tokens['refresh_token'] ?? $user->canva_refresh_token,
                    'canva_token_expires_at' => $expiresAt,
                ]);
            }

            session([
                'canva_access_token'  => $tokens['access_token'],
                'canva_refresh_token' => $tokens['refresh_token'] ?? null,
                'canva_connected'     => true,
            ]);

            Cache::put('canva_token_' . $userId, $tokens['access_token'], $expiresAt);
            if (!empty($tokens['refresh_token'])) {
                Cache::put('canva_refresh_token_' . $userId, $tokens['refresh_token'], now()->addDays(30));
            }

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_CANVA,
                "Connected Canva account via OAuth",
                \App\Models\SystemActivityLog::STATUS_SUCCESS,
                null,
                ['expires_in' => $tokens['expires_in'] ?? null],
                null,
                null,
                $user ? $user->id : null
            );

            return redirect()->route('admin.posts.create')
                ->with('success', 'Canva successfully connected! You can now create designs and auto-import them into your posts.');
        } catch (\Throwable $e) {
            Log::error('Canva OAuth Callback Exception: ' . $e->getMessage());

            return redirect()->route('admin.posts.create')
                ->with('error', 'Failed to complete Canva authentication: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a valid Canva access token, automatically refreshing using refresh_token if needed.
     */
    protected function getValidAccessToken($userOrId = null, bool $forceRefresh = false): ?string
    {
        $user = $userOrId instanceof \App\Models\User 
            ? $userOrId 
            : (auth()->user() ?? (is_numeric($userOrId) ? \App\Models\User::find($userOrId) : null));
        $userId = $user ? $user->id : (is_scalar($userOrId) ? $userOrId : 'guest');

        // 1. Check if unexpired in-database or cache token is available
        if (!$forceRefresh) {
            if ($user && !empty($user->canva_access_token)) {
                // If token expires in more than 2 minutes, return it directly
                if (!$user->canva_token_expires_at || $user->canva_token_expires_at->gt(now()->addMinutes(2))) {
                    return $user->canva_access_token;
                }
            }

            $token = session('canva_access_token') ?? Cache::get('canva_token_' . $userId);
            if (!empty($token)) {
                return $token;
            }
        }

        // 2. Token needs refresh — retrieve refresh token from DB or Cache/Session
        $refreshToken = null;
        if ($user && !empty($user->canva_refresh_token)) {
            $refreshToken = $user->canva_refresh_token;
        }
        if (empty($refreshToken)) {
            $refreshToken = session('canva_refresh_token') ?? Cache::get('canva_refresh_token_' . $userId);
        }

        if (empty($refreshToken)) {
            return null;
        }

        // 3. Perform atomic token refresh with Canva API
        try {
            $tokens = $this->canvaService->refreshToken($refreshToken);
            $newToken = $tokens['access_token'] ?? null;
            if ($newToken) {
                $ttl = $tokens['expires_in'] ?? 14400;
                $newRefresh = $tokens['refresh_token'] ?? $refreshToken;
                $expiresAt = now()->addSeconds($ttl);

                if ($user) {
                    $user->update([
                        'canva_access_token'     => $newToken,
                        'canva_refresh_token'    => $newRefresh,
                        'canva_token_expires_at' => $expiresAt,
                    ]);
                }

                session([
                    'canva_access_token'  => $newToken,
                    'canva_refresh_token' => $newRefresh,
                    'canva_connected'     => true,
                ]);

                Cache::put('canva_token_' . $userId, $newToken, $expiresAt);
                Cache::put('canva_refresh_token_' . $userId, $newRefresh, now()->addDays(30));

                Log::info("Canva token refreshed successfully for User #{$userId}");

                return $newToken;
            }
        } catch (\Throwable $e) {
            Log::warning("Canva auto token refresh failed for User #{$userId}: " . $e->getMessage());
            $this->clearTokens($user, $userId);
        }

        return null;
    }

    /**
     * Clear cached, session and database Canva tokens when invalidated.
     */
    protected function clearTokens($user = null, $userId = null): void
    {
        $user = $user instanceof \App\Models\User ? $user : auth()->user();
        $userId = $userId ?? ($user ? $user->id : 'guest');

        if ($user) {
            $user->update([
                'canva_access_token'     => null,
                'canva_refresh_token'    => null,
                'canva_token_expires_at' => null,
            ]);
        }

        session()->forget(['canva_access_token', 'canva_refresh_token', 'canva_connected']);
        Cache::forget('canva_token_' . $userId);
        Cache::forget('canva_refresh_token_' . $userId);
    }

    /**
     * Get Canva connection status & available templates.
     */
    public function status(Request $request): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';
        $token = $this->getValidAccessToken($userId);

        return response()->json([
            'is_configured' => $this->canvaService->isConfigured(),
            'is_connected'  => !empty($token),
            'auth_url'      => route('admin.canva.auth'),
            'templates'     => $this->canvaService->getQuickTemplates(),
        ]);
    }

    /**
     * Create a new design in Canva.
     */
    public function createDesign(Request $request): JsonResponse
    {
        $request->validate([
            'title'  => 'nullable|string|max:100',
            'width'  => 'nullable|integer|min:40|max:8000',
            'height' => 'nullable|integer|min:40|max:8000',
            'preset' => 'nullable|string|max:50',
        ]);

        $userId = auth()->id() ?? 'guest';
        $token = $this->getValidAccessToken($userId);

        if (empty($token)) {
            return response()->json([
                'success'      => false,
                'is_connected' => false,
                'message'      => 'Please connect your Canva account first or re-authorize.',
                'auth_url'     => route('admin.canva.auth'),
            ], 401);
        }

        try {
            $title = $request->input('title', 'Social Post - ' . date('Y-m-d'));
            $width = (int) ($request->input('width', 1080));
            $height = (int) ($request->input('height', 1080));
            $preset = $request->input('preset');

            $startTime = microtime(true);
            try {
                $result = $this->canvaService->createDesign($token, $title, $width, $height, $preset);
            } catch (\Throwable $apiEx) {
                // If access token was invalid or expired, attempt one auto-refresh and retry
                if (str_contains($apiEx->getMessage(), 'invalid_access_token') || str_contains($apiEx->getMessage(), '401')) {
                    $token = $this->getValidAccessToken($userId, true);
                    if ($token) {
                        $result = $this->canvaService->createDesign($token, $title, $width, $height, $preset);
                    } else {
                        $this->clearTokens($userId);
                        throw new \RuntimeException('Canva session expired. Please click "Connect Canva" to re-authorize.');
                    }
                } else {
                    throw $apiEx;
                }
            }

            $design = $result['design'] ?? [];
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_CANVA,
                "Created new Canva design '{$title}' ({$width}x{$height})",
                \App\Models\SystemActivityLog::STATUS_SUCCESS,
                ['title' => $title, 'preset' => $preset, 'width' => $width, 'height' => $height],
                ['design_id' => $design['id'] ?? null, 'title' => $design['title'] ?? $title],
                $durationMs,
                null,
                $userId !== 'guest' ? $userId : null
            );

            return response()->json([
                'success'   => true,
                'design_id' => $design['id'] ?? null,
                'title'     => $design['title'] ?? $title,
                'edit_url'  => $design['urls']['edit_url'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Canva Create Design Controller Error: ' . $e->getMessage());

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_CANVA,
                "Failed to create Canva design",
                \App\Models\SystemActivityLog::STATUS_FAILED,
                ['preset' => $request->input('preset')],
                $e->getMessage(),
                null,
                null,
                $userId !== 'guest' ? $userId : null
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export and download a completed Canva design into SocialManager.
     */
    public function exportDesign(Request $request): JsonResponse
    {
        $request->validate([
            'design_id' => 'required|string',
            'format'    => 'nullable|string|in:png,jpg',
        ]);

        $userId = auth()->id() ?? 'guest';
        $token = $this->getValidAccessToken($userId);

        if (empty($token)) {
            return response()->json([
                'success'      => false,
                'is_connected' => false,
                'message'      => 'Canva session expired. Please reconnect your account.',
            ], 401);
        }

        try {
            $designId = $request->input('design_id');
            $format = $request->input('format', 'jpg');

            $startTime = microtime(true);
            try {
                $downloadResult = $this->canvaService->exportAndDownloadDesign($token, $designId, $format);
            } catch (\Throwable $apiEx) {
                // If token was invalid, refresh and retry once
                if (str_contains($apiEx->getMessage(), 'invalid_access_token') || str_contains($apiEx->getMessage(), '401')) {
                    $token = $this->getValidAccessToken($userId, true);
                    if ($token) {
                        $downloadResult = $this->canvaService->exportAndDownloadDesign($token, $designId, $format);
                    } else {
                        $this->clearTokens($userId);
                        throw new \RuntimeException('Canva session expired. Please reconnect your Canva account.');
                    }
                } else {
                    throw $apiEx;
                }
            }

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_CANVA,
                "Exported Canva design '{$downloadResult['filename']}' for post thumbnail",
                \App\Models\SystemActivityLog::STATUS_SUCCESS,
                ['design_id' => $designId, 'format' => $format],
                [
                    'filename' => $downloadResult['filename'],
                    'size'     => $downloadResult['size'],
                    'url'      => $downloadResult['url'],
                ],
                $durationMs,
                null,
                $userId !== 'guest' ? $userId : null
            );

            return response()->json([
                'success'       => true,
                'image_url'     => $downloadResult['url'],
                'relative_path' => $downloadResult['relative_path'],
                'filename'      => $downloadResult['filename'],
                'size'          => $downloadResult['size'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Canva Export Controller Error: ' . $e->getMessage());

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_CANVA,
                "Failed to export Canva design #{$request->input('design_id')}",
                \App\Models\SystemActivityLog::STATUS_FAILED,
                ['design_id' => $request->input('design_id')],
                $e->getMessage(),
                null,
                null,
                $userId !== 'guest' ? $userId : null
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect Canva account.
     */
    public function disconnect(Request $request): JsonResponse
    {
        $user = auth()->user();
        $userId = $user ? $user->id : 'guest';

        $this->clearTokens($user, $userId);

        \App\Services\ActivityLogger::log(
            \App\Models\SystemActivityLog::EVENT_CANVA,
            "Disconnected Canva account",
            \App\Models\SystemActivityLog::STATUS_SUCCESS,
            null,
            null,
            null,
            null,
            $user ? $user->id : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Canva disconnected successfully.',
        ]);
    }
}
