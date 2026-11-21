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

            $userId = auth()->id() ?? 'guest';
            $ttl = $tokens['expires_in'] ?? 14400;

            session([
                'canva_access_token'  => $tokens['access_token'],
                'canva_refresh_token' => $tokens['refresh_token'] ?? null,
                'canva_connected'     => true,
            ]);

            Cache::put('canva_token_' . $userId, $tokens['access_token'], now()->addSeconds($ttl));
            if (!empty($tokens['refresh_token'])) {
                Cache::put('canva_refresh_token_' . $userId, $tokens['refresh_token'], now()->addDays(30));
            }

            return redirect()->route('admin.posts.create')
                ->with('success', 'Canva successfully connected! You can now create designs and auto-import them into your posts.');
        } catch (\Throwable $e) {
            Log::error('Canva OAuth Callback Exception: ' . $e->getMessage());

            return redirect()->route('admin.posts.create')
                ->with('error', 'Failed to complete Canva authentication: ' . $e->getMessage());
        }
    }

    /**
     * Get Canva connection status & available templates.
     */
    public function status(Request $request): JsonResponse
    {
        $userId = auth()->id() ?? 'guest';
        $token = session('canva_access_token') ?? Cache::get('canva_token_' . $userId);

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
        ]);

        $userId = auth()->id() ?? 'guest';
        $token = session('canva_access_token') ?? Cache::get('canva_token_' . $userId);

        if (empty($token)) {
            return response()->json([
                'success'      => false,
                'is_connected' => false,
                'message'      => 'Please connect your Canva account first.',
                'auth_url'     => route('admin.canva.auth'),
            ], 401);
        }

        try {
            $title = $request->input('title', 'Social Post - ' . date('Y-m-d'));
            $width = (int) ($request->input('width', 1080));
            $height = (int) ($request->input('height', 1080));

            $result = $this->canvaService->createDesign($token, $title, $width, $height);

            $design = $result['design'] ?? [];

            return response()->json([
                'success'   => true,
                'design_id' => $design['id'] ?? null,
                'title'     => $design['title'] ?? $title,
                'edit_url'  => $design['urls']['edit_url'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Canva Create Design Controller Error: ' . $e->getMessage());

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
        $token = session('canva_access_token') ?? Cache::get('canva_token_' . $userId);

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

            $downloadResult = $this->canvaService->exportAndDownloadDesign($token, $designId, $format);

            return response()->json([
                'success'       => true,
                'image_url'     => $downloadResult['url'],
                'relative_path' => $downloadResult['relative_path'],
                'filename'      => $downloadResult['filename'],
                'size'          => $downloadResult['size'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Canva Export Controller Error: ' . $e->getMessage());

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
        $userId = auth()->id() ?? 'guest';

        session()->forget(['canva_access_token', 'canva_refresh_token', 'canva_connected']);
        Cache::forget('canva_token_' . $userId);
        Cache::forget('canva_refresh_token_' . $userId);

        return response()->json([
            'success' => true,
            'message' => 'Canva disconnected successfully.',
        ]);
    }
}
