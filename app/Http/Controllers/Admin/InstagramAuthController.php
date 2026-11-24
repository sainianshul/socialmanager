<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Instagram Direct Login (via Instagram Graph API)
 *
 * This controller implements the "Instagram Login for Business" OAuth flow,
 * which lets users connect an Instagram Professional (Business or Creator)
 * account directly — WITHOUT requiring a linked Facebook Page.
 *
 * This is the same approach used by Buffer, Hootsuite, Later, etc.
 *
 * Requirements:
 *  - Instagram account must be Professional (Business or Creator).
 *  - Meta Developer Console must have "Instagram" product added with
 *    "Instagram Login for Business" use case configured.
 *
 * Flow:
 *  1. User clicks "Connect Instagram" → redirect to Instagram OAuth dialog
 *  2. User authorizes → Instagram redirects back with ?code=…
 *  3. Exchange code → short-lived token → long-lived token (60 days)
 *  4. Fetch profile info → save Account record
 *
 * @see https://developers.facebook.com/docs/instagram-platform/instagram-api-with-instagram-login
 */
class InstagramAuthController extends Controller
{
    private string $appId;
    private string $appSecret;
    private string $redirectUri;

    public function __construct()
    {
        // Instagram Login for Business uses the same Meta App but with
        // Instagram-specific App ID & Secret from the "Instagram > Basic Display"
        // or "Instagram > API Setup" section. Falls back to Facebook App credentials
        // since they share the same Meta App in most setups.
        $this->appId = (string) config('services.instagram.client_id',
            config('services.facebook.client_id', env('INSTAGRAM_APP_ID', env('FACEBOOK_APP_ID')))
        );
        $this->appSecret = (string) config('services.instagram.client_secret',
            config('services.facebook.client_secret', env('INSTAGRAM_APP_SECRET', env('FACEBOOK_APP_KEY')))
        );
        $this->redirectUri = url('/auth/instagram/callback');
    }

    /**
     * Step 1: Redirect user to Instagram Authorization Dialog
     *
     * Uses the new "Instagram Login for Business" scopes (instagram_business_*).
     * These scopes allow direct Instagram login without needing a Facebook Page.
     */
    public function redirect()
    {
        // New Instagram Login for Business scopes (2024+)
        // These replace the old instagram_basic, instagram_content_publish etc.
        $defaultScopes = 'instagram_business_basic,instagram_business_manage_messages,instagram_business_manage_comments,instagram_business_content_publish';
        $scopes = env('INSTAGRAM_SCOPES', $defaultScopes);

        $userId = auth()->id();
        $state = bin2hex(random_bytes(16));
        session([
            'instagram_oauth_state' => $state,
            'instagram_user_id' => $userId,
        ]);
        \Illuminate\Support\Facades\Cache::put('instagram_oauth_state_' . $state, [
            'user_id' => $userId,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(15));

        $loginUrl = 'https://www.instagram.com/oauth/authorize?' . http_build_query([
            'enable_fb_login'      => 0,           // Don't show "Continue with Facebook" button
            'force_authentication' => 1,           // Always show login form (don't auto-login)
            'client_id'            => $this->appId,
            'redirect_uri'         => $this->redirectUri,
            'response_type'        => 'code',
            'scope'                => $scopes,
            'state'                => $state,
        ]);

        return redirect()->away($loginUrl);
    }

    /**
     * Step 2: Handle OAuth callback from Instagram
     *
     * Exchange authorization code → short-lived token → long-lived token → fetch profile → save
     */
    public function callback(Request $request)
    {
        // Handle user cancellation or denial
        if ($request->has('error')) {
            $errorDescription = $request->get('error_description', 'Instagram authorization was cancelled or denied.');
            Log::warning('Instagram OAuth denied: ' . $errorDescription);
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Instagram connection failed: ' . $errorDescription);
        }

        // Validate CSRF state token
        $incomingState = (string) $request->get('state');
        $cachedData = !empty($incomingState) ? \Illuminate\Support\Facades\Cache::pull('instagram_oauth_state_' . $incomingState) : null;
        $expectedSessionState = session('instagram_oauth_state');
        $sessionUserId = session('instagram_user_id');
        session()->forget(['instagram_oauth_state', 'instagram_user_id']);

        $isValidState = false;
        $userId = auth()->id();

        if ($cachedData && !empty($cachedData['user_id'])) {
            $isValidState = true;
            $userId = $cachedData['user_id'];
        } elseif (!empty($expectedSessionState) && hash_equals((string)$expectedSessionState, $incomingState)) {
            $isValidState = true;
            $userId = $sessionUserId ?: auth()->id();
        }

        // Local dev / test harness bypass if state cookie was dropped on redirect
        if (!$isValidState && (app()->isLocal() || app()->runningUnitTests() || config('app.debug'))) {
            Log::warning('Instagram OAuth CSRF state mismatch bypassed in local dev/testing', [
                'expected_session' => $expectedSessionState,
                'has_cache' => !empty($cachedData),
                'received' => $incomingState,
            ]);
            $isValidState = true;
            $userId = $userId ?: auth()->id();
        }

        if (!$isValidState) {
            Log::warning('Instagram OAuth CSRF state mismatch', ['state' => $incomingState]);
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Security check failed (OAuth state mismatch). Please try connecting again.');
        }

        if (!auth()->check() && $userId) {
            auth()->loginUsingId($userId);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Authorization code not received from Instagram.');
        }

        try {
            // ── Step 2a: Exchange code for Short-Lived Access Token ──
            $tokenResponse = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id'     => $this->appId,
                'client_secret' => $this->appSecret,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->redirectUri,
                'code'          => $code,
            ])->json();

            $shortLivedToken = $tokenResponse['access_token'] ?? null;
            $igUserId = isset($tokenResponse['user_id']) ? (string) $tokenResponse['user_id'] : null;

            if (!$shortLivedToken || !$igUserId) {
                $errMsg = $tokenResponse['error_message'] ?? ($tokenResponse['error']['message'] ?? 'Failed to retrieve Instagram access token.');
                Log::error('Instagram token exchange failed', $tokenResponse);
                return redirect()->route('admin.accounts.index')
                    ->with('error', 'Instagram Token Error: ' . $errMsg);
            }

            // ── Step 2b: Exchange Short-Lived Token for Long-Lived Token (60 days) ──
            $longLivedResponse = Http::get('https://graph.instagram.com/access_token', [
                'grant_type'    => 'ig_exchange_token',
                'client_secret' => $this->appSecret,
                'access_token'  => $shortLivedToken,
            ])->json();

            $longLivedToken = $longLivedResponse['access_token'] ?? $shortLivedToken;
            $expiresIn = $longLivedResponse['expires_in'] ?? null; // seconds (typically 5184000 = 60 days)

            // ── Step 2c: Fetch Instagram User Profile ──
            $profileResponse = Http::get("https://graph.instagram.com/v20.0/me", [
                'fields'       => 'user_id,username,name,account_type,profile_picture_url,followers_count,follows_count,media_count',
                'access_token' => $longLivedToken,
            ])->json();

            if (isset($profileResponse['error'])) {
                $errMsg = $profileResponse['error']['message'] ?? 'Failed to fetch Instagram profile.';
                Log::error('Instagram profile fetch failed', $profileResponse);
                return redirect()->route('admin.accounts.index')
                    ->with('error', 'Instagram API Error: ' . $errMsg);
            }

            // Use user_id from profile response, fall back to token exchange response
            $instagramId = (string) ($profileResponse['user_id'] ?? $profileResponse['id'] ?? $igUserId);
            $username = $profileResponse['username'] ?? null;
            $displayName = $profileResponse['name'] ?? ($username ?? 'Instagram Account');
            $profilePicUrl = $profileResponse['profile_picture_url'] ?? null;
            $followersCount = (int) ($profileResponse['followers_count'] ?? 0);
            $followingCount = (int) ($profileResponse['follows_count'] ?? 0);
            $accountType = $profileResponse['account_type'] ?? 'BUSINESS'; // BUSINESS or CREATOR

            // ── Step 2d: Save or Update Instagram Account ──
            $userId = auth()->id();
            $tokenExpiresAt = $expiresIn ? now()->addSeconds($expiresIn) : now()->addDays(60);

            Account::updateOrCreate(
                [
                    'user_id'          => $userId,
                    'account_type'     => Account::TYPE_INSTAGRAM,
                    'meta_account_id'  => $instagramId,
                ],
                [
                    'instagram_business_account_id' => $instagramId, // self-reference for direct login
                    'name'              => $displayName,
                    'username'          => $username,
                    'category'          => ($accountType === 'CREATOR') ? 'Creator' : 'Instagram Business',
                    'followers_count'   => $followersCount,
                    'following_count'   => $followingCount,
                    'profile_pic_url'   => $profilePicUrl,
                    'access_token'      => $longLivedToken,
                    'token_expires_at'  => $tokenExpiresAt,
                    'is_active'         => true,
                    'webhook_active'    => true,
                    'last_synced_at'    => now(),
                ]
            );

            Log::info("Instagram account connected: @{$username} (ID: {$instagramId}) for user #{$userId}");

            return redirect()->route('admin.accounts.index')
                ->with('success', "Instagram account @{$username} connected successfully!");

        } catch (\Throwable $e) {
            Log::error('Instagram OAuth callback exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Instagram connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh a Long-Lived Token (can be called before expiry to extend 60 more days)
     *
     * Long-lived tokens can be refreshed as long as they are at least 24 hours old
     * and not expired. Each refresh gives a new 60-day token.
     */
    public static function refreshLongLivedToken(Account $account): bool
    {
        if (empty($account->access_token)) {
            return false;
        }

        try {
            $response = Http::get('https://graph.instagram.com/refresh_access_token', [
                'grant_type'   => 'ig_refresh_token',
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['access_token'])) {
                $expiresIn = $response['expires_in'] ?? 5184000; // 60 days default
                $account->update([
                    'access_token'     => $response['access_token'],
                    'token_expires_at' => now()->addSeconds($expiresIn),
                ]);

                Log::info("Instagram token refreshed for account #{$account->id} (@{$account->username})");
                return true;
            }

            Log::warning("Instagram token refresh failed for account #{$account->id}", $response);
            return false;

        } catch (\Throwable $e) {
            Log::error("Instagram token refresh exception for account #{$account->id}: " . $e->getMessage());
            return false;
        }
    }
}

