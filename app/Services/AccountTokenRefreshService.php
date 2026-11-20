<?php

namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountTokenRefreshService
{
    protected string $googleClientId;
    protected string $googleClientSecret;

    public function __construct()
    {
        $this->googleClientId = (string) (config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID', ''));
        $this->googleClientSecret = (string) (config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET', ''));
    }

    /**
     * Proactively refresh a YouTube account's access token using its google_refresh_token
     *
     * @param Account $account
     * @param bool $force Force refresh even if token is still valid
     * @return array ['success' => bool, 'refreshed' => bool, 'message' => string, 'token' => ?string]
     */
    public function refreshYouTubeToken(Account $account, bool $force = false): array
    {
        if (!$account->isYouTube()) {
            return [
                'success' => false,
                'refreshed' => false,
                'message' => 'Account is not a YouTube channel.',
                'token' => null,
            ];
        }

        $refreshToken = $account->google_refresh_token ?: $account->refresh_token;

        if (empty($refreshToken)) {
            return [
                'success' => false,
                'refreshed' => false,
                'message' => 'No refresh token available for this account. Re-authorization required.',
                'token' => null,
            ];
        }

        // If not forcing and token is still valid for at least 15 more minutes, skip refreshing
        if (!$force && $account->token_expires_at && Carbon::parse($account->token_expires_at)->gt(now()->addMinutes(15))) {
            return [
                'success' => true,
                'refreshed' => false,
                'message' => 'Access token is still valid (expires at ' . $account->token_expires_at->toDateTimeString() . ').',
                'token' => $account->access_token,
            ];
        }

        try {
            $response = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $this->googleClientId,
                'client_secret' => $this->googleClientSecret,
                'refresh_token' => $refreshToken,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $newAccessToken = $data['access_token'] ?? null;
                $expiresIn = (int) ($data['expires_in'] ?? 3600);

                if ($newAccessToken) {
                    $expiresAt = now()->addSeconds($expiresIn);
                    $account->update([
                        'access_token'     => $newAccessToken,
                        'token_expires_at' => $expiresAt,
                    ]);

                    Log::info("YouTube access token refreshed successfully for account #{$account->id} ({$account->name}). Expires at {$expiresAt}.");

                    return [
                        'success'          => true,
                        'refreshed'        => true,
                        'message'          => "Token refreshed successfully. Valid until {$expiresAt}.",
                        'token'            => $newAccessToken,
                        'token_expires_at' => $expiresAt,
                    ];
                }
            }

            $errorData = $response->json() ?? [];
            $error = $errorData['error'] ?? 'unknown_error';
            $errorDescription = $errorData['error_description'] ?? $response->body();

            Log::warning("YouTube token refresh failed for account #{$account->id} ({$account->name}): [{$error}] {$errorDescription}");

            return [
                'success'   => false,
                'refreshed' => false,
                'message'   => "Google OAuth error: {$errorDescription}",
                'token'     => null,
                'revoked'   => ($error === 'invalid_grant'),
            ];

        } catch (\Throwable $e) {
            Log::error("Exception while refreshing YouTube token for account #{$account->id}: {$e->getMessage()}");

            return [
                'success'   => false,
                'refreshed' => false,
                'message'   => $e->getMessage(),
                'token'     => null,
            ];
        }
    }

    /**
     * Proactively refresh all active YouTube accounts that need refreshing
     *
     * @param bool $force
     * @return array
     */
    public function refreshAllExpiringYouTubeTokens(bool $force = false): array
    {
        $query = Account::where('account_type', Account::TYPE_YOUTUBE)
            ->where('is_active', true)
            ->whereNotNull('google_refresh_token');

        if (!$force) {
            // Refresh if token_expires_at is null, past, or expiring within 30 minutes
            $query->where(function ($q) {
                $q->whereNull('token_expires_at')
                  ->orWhere('token_expires_at', '<=', now()->addMinutes(30));
            });
        }

        $accounts = $query->get();
        $results = [
            'total'     => $accounts->count(),
            'refreshed' => 0,
            'skipped'   => 0,
            'failed'    => 0,
            'details'   => [],
        ];

        foreach ($accounts as $account) {
            $res = $this->refreshYouTubeToken($account, $force);
            $results['details'][$account->id] = [
                'name'    => $account->name,
                'result'  => $res,
            ];

            if ($res['success']) {
                if ($res['refreshed']) {
                    $results['refreshed']++;
                } else {
                    $results['skipped']++;
                }
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }
}
