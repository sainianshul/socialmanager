<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class YouTubeAuthController extends Controller
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = (string) (config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = (string) (config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET', ''));
        $this->redirectUri = url('/auth/youtube/callback');
    }

    /**
     * Redirect user to Google OAuth 2.0 Consent Screen with YouTube scopes
     */
    public function redirect(Request $request)
    {
        $this->redirectUri = url('/auth/youtube/callback');
        $state = Str::random(40);
        session(['youtube_oauth_state' => $state]);

        $scopes = [
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ];

        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
            'access_type'   => 'offline',   // Essential for permanent Google refresh_token
            'prompt'        => 'consent',   // Force consent prompt to guarantee refresh_token
            'state'         => $state,
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        Log::info('YouTube OAuth redirect initiated', [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'full_auth_url' => $authUrl,
        ]);

        return redirect()->away($authUrl);
    }

    /**
     * Handle OAuth Callback from Google
     */
    public function callback(Request $request)
    {
        $this->redirectUri = url('/auth/youtube/callback');

        // 1. Check for cancellation or error from Google
        if ($request->has('error')) {
            $errorDescription = $request->get('error_description', $request->get('error'));
            return redirect()->route('admin.accounts.index')->with('error', 'Google authorization cancelled: ' . $errorDescription);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('admin.accounts.index')->with('error', 'Authorization code not received from Google.');
        }

        // 2. Validate CSRF state token
        $storedState = session()->pull('youtube_oauth_state');
        if ($storedState && $request->filled('state') && !hash_equals($storedState, (string) $request->get('state'))) {
            return redirect()->route('admin.accounts.index')->with('error', 'Invalid OAuth state token. Please try connecting again.');
        }

        try {
            // 3. Exchange authorization code for tokens
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code'          => $code,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri'  => $this->redirectUri,
                'grant_type'    => 'authorization_code',
            ])->json();

            $accessToken = $tokenResponse['access_token'] ?? null;
            if (!$accessToken) {
                $errMsg = $tokenResponse['error_description'] ?? ($tokenResponse['error'] ?? 'Unable to obtain access token from Google.');
                return redirect()->route('admin.accounts.index')->with('error', 'Google Token Error: ' . $errMsg);
            }

            $refreshToken = $tokenResponse['refresh_token'] ?? null;
            $expiresIn = (int) ($tokenResponse['expires_in'] ?? 3600);

            // 4. Fetch authenticated user's YouTube channels
            $channelsResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'snippet,statistics,brandingSettings',
                'mine' => 'true',
            ])->json();

            $items = $channelsResponse['data'] ?? ($channelsResponse['items'] ?? []);

            if (empty($items)) {
                return redirect()->route('admin.accounts.index')->with('warning', 'Google login successful, but no YouTube Channel was found on this Google account.');
            }

            $userId = auth()->id();
            $connectedCount = 0;

            foreach ($items as $channel) {
                $channelId = (string) $channel['id'];
                $snippet = $channel['snippet'] ?? [];
                $statistics = $channel['statistics'] ?? [];

                $name = $snippet['title'] ?? 'YouTube Channel';
                $username = !empty($snippet['customUrl']) ? ltrim($snippet['customUrl'], '@') : null;
                $profilePic = $snippet['thumbnails']['medium']['url'] ?? ($snippet['thumbnails']['default']['url'] ?? null);
                $subsCount = (int) ($statistics['subscriberCount'] ?? 0);
                $videoCount = (int) ($statistics['videoCount'] ?? 0);

                Account::updateOrCreate(
                    [
                        'user_id'            => $userId,
                        'account_type'       => Account::TYPE_YOUTUBE,
                        'youtube_channel_id' => $channelId,
                    ],
                    [
                        'name'                 => $name,
                        'username'             => $username,
                        'category'             => 'YouTube Channel',
                        'profile_pic_url'      => $profilePic,
                        'subscriber_count'     => $subsCount,
                        'video_count'          => $videoCount,
                        'followers_count'      => $subsCount,
                        'access_token'         => $accessToken,
                        'google_refresh_token' => $refreshToken,
                        'token_expires_at'     => now()->addSeconds($expiresIn),
                        'is_active'            => true,
                        'last_synced_at'       => now(),
                        'platform_data'        => [
                            'view_count'    => (int) ($statistics['viewCount'] ?? 0),
                            'comment_count' => (int) ($statistics['commentCount'] ?? 0),
                        ],
                    ]
                );

                $connectedCount++;
            }

            return redirect()->route('admin.accounts.index')->with('success', "{$connectedCount} YouTube Channel(s) connected and synced successfully!");

        } catch (\Throwable $e) {
            Log::error('YouTube OAuth Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.accounts.index')->with('error', 'YouTube connection failed: ' . $e->getMessage());
        }
    }
}

