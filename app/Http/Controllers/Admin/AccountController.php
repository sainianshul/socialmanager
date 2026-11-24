<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\SystemActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Display a listing of connected social accounts (Buffer / Hootsuite style)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Account::with('user')->latest();

        // If not Admin, restrict to logged-in user's accounts
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Platform filter
        if ($request->filled('platform')) {
            $query->where('account_type', $request->platform);
        }

        $accounts = $query->paginate(12)->withQueryString();

        // Proactively renew YouTube accounts whose access tokens expired while offline
        foreach ($accounts as $acc) {
            if ($acc->isYouTube() && !empty($acc->google_refresh_token)) {
                if ($acc->token_expires_at === null || $acc->token_expires_at->isPast()) {
                    app(\App\Services\AccountTokenRefreshService::class)->refreshYouTubeToken($acc);
                }
            }
        }

        // Calculate summary stats
        $statsQuery = Account::query();
        if (!$user->isAdmin()) {
            $statsQuery->where('user_id', $user->id);
        }

        $totalAccounts = (clone $statsQuery)->count();
        $fbCount = (clone $statsQuery)->where('account_type', Account::TYPE_FACEBOOK)->count();
        $igCount = (clone $statsQuery)->where('account_type', Account::TYPE_INSTAGRAM)->count();
        $ytCount = (clone $statsQuery)->where('account_type', Account::TYPE_YOUTUBE)->count();
        $totalFollowers = (clone $statsQuery)->sum('followers_count');
        $totalSubscribers = (clone $statsQuery)->sum('subscriber_count');
        $activeBots = (clone $statsQuery)->where('is_active', true)->where('auto_reply_mode', '!=', Account::AUTO_REPLY_OFF)->count();

        $aiTones = [
            'Humble & Respectful' => 'Humble & Respectful',
            'Patriotic & Inspiring' => 'Patriotic & Inspiring',
            'Assertive & Firm' => 'Assertive & Firm',
            'Formal & Official' => 'Formal & Official',
        ];

        $aiLanguages = [
            'Hindi + Hinglish' => 'Hindi + Hinglish',
            'Pure Hindi' => 'Pure Hindi',
            'English' => 'English',
            'Bilingual (Hindi + English)' => 'Bilingual (Hindi + English)',
        ];

        $autoReplyModes = Account::getAutoReplyModeList();

        return view('admin.accounts.index', compact(
            'accounts',
            'totalAccounts',
            'fbCount',
            'igCount',
            'ytCount',
            'totalFollowers',
            'totalSubscribers',
            'activeBots',
            'aiTones',
            'aiLanguages',
            'autoReplyModes'
        ));
    }

    /**
     * Update AI Bot and automation settings for an account
     */
    public function updateSettings(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'ai_tone' => ['required', 'string', 'max:100'],
            'ai_language' => ['required', 'string', 'max:100'],
            'auto_reply_mode' => ['required', 'integer', 'in:0,1,2'],
            'auto_hide_trolls' => ['nullable', 'boolean'],
            'comment_to_dm' => ['nullable', 'boolean'],
        ]);

        $account->update([
            'ai_tone' => $validated['ai_tone'],
            'ai_language' => $validated['ai_language'],
            'auto_reply_mode' => (int) $validated['auto_reply_mode'],
            'auto_hide_trolls' => $request->boolean('auto_hide_trolls'),
            'comment_to_dm' => $request->boolean('comment_to_dm'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'AI Bot settings updated successfully.',
                'account' => $account,
            ]);
        }

        return redirect()->back()->with('success', 'Account automation settings updated successfully for ' . $account->name);
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $account->is_active = !$account->is_active;
        $account->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $account->is_active,
                'message' => $account->is_active ? 'Channel activated.' : 'Channel paused.',
            ]);
        }

        return redirect()->back()->with('success', $account->is_active ? 'Channel activated.' : 'Channel paused.');
    }

    /**
     * Re-sync live details (followers, name, pic) from Meta Graph API
     */
    public function syncAccount(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        if (empty($account->access_token)) {
            return redirect()->back()->with('error', 'No active access token found for this account. Please reconnect.');
        }

        $startTime = microtime(true);
        try {
            // Instagram accounts use the Instagram Graph API endpoint
            if ($account->isInstagram()) {
                return $this->syncInstagramAccount($account);
            }

            // Facebook accounts use the Facebook Graph API endpoint
            $url = "https://graph.facebook.com/v20.0/{$account->meta_account_id}";
            $response = Http::get($url, [
                'fields' => 'id,name,category,fan_count,picture{url},instagram_business_account{id,username,name,profile_picture_url,followers_count,follows_count},connected_instagram_account{id,username,name,profile_picture_url}',
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['id'])) {
                $account->name = $response['name'] ?? $account->name;
                $account->category = $response['category'] ?? $account->category;
                $account->followers_count = (int) ($response['fan_count'] ?? $account->followers_count);
                if (isset($response['picture']['data']['url'])) {
                    $account->profile_pic_url = $response['picture']['data']['url'];
                }

                // Check for linked Instagram account (dual check)
                $igData = $response['instagram_business_account'] ?? ($response['connected_instagram_account'] ?? null);
                $igId = isset($igData['id']) ? (string) $igData['id'] : null;

                if ($igId) {
                    $account->instagram_business_account_id = $igId;

                    // Fetch rich profile for Instagram if missing
                    if (empty($igData['profile_picture_url'])) {
                        try {
                            $directIg = Http::get("https://graph.facebook.com/v20.0/{$igId}", [
                                'fields' => 'id,username,name,profile_picture_url,followers_count,follows_count,media_count',
                                'access_token' => $account->access_token,
                            ])->json();
                            if (!empty($directIg['id'])) {
                                $igData = array_merge($igData, $directIg);
                            }
                        } catch (\Throwable $igErr) {
                            Log::debug("IG direct fetch during FB sync notice: " . $igErr->getMessage());
                        }
                    }

                    // Save or update the linked Instagram account
                    Account::updateOrCreate(
                        [
                            'user_id' => $account->user_id,
                            'account_type' => Account::TYPE_INSTAGRAM,
                            'meta_account_id' => $igId,
                        ],
                        [
                            'instagram_business_account_id' => (string) $account->meta_account_id,
                            'name' => $igData['name'] ?? ($igData['username'] ?? 'Instagram Account'),
                            'username' => $igData['username'] ?? null,
                            'category' => 'Instagram Professional',
                            'followers_count' => (int) ($igData['followers_count'] ?? 0),
                            'following_count' => (int) ($igData['follows_count'] ?? 0),
                            'profile_pic_url' => $igData['profile_picture_url'] ?? $account->profile_pic_url,
                            'access_token' => $account->access_token,
                            'token_expires_at' => null,
                            'is_active' => true,
                            'webhook_active' => true,
                            'last_synced_at' => now(),
                        ]
                    );
                }

                $account->last_synced_at = now();
                $account->save();

                $durationMs = (int) round((microtime(true) - $startTime) * 1000);
                ActivityLogger::log(
                    SystemActivityLog::EVENT_ACCOUNT_SYNC,
                    "Synced Facebook Page '{$account->name}'",
                    SystemActivityLog::STATUS_SUCCESS,
                    ['meta_account_id' => $account->meta_account_id, 'linked_ig' => $igId],
                    ['name' => $account->name, 'followers' => $account->followers_count, 'category' => $account->category],
                    $durationMs,
                    $account->id,
                    $account->user_id
                );

                $msg = $igId
                    ? "{$account->name} and linked Instagram account (@" . ($igData['username'] ?? 'Instagram') . ") synced successfully!"
                    : "{$account->name} synced successfully with Meta!";

                return redirect()->back()->with('success', $msg);
            }

            $errMsg = $response['error']['message'] ?? 'Unable to fetch updated page details.';
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            ActivityLogger::log(
                SystemActivityLog::EVENT_ACCOUNT_SYNC,
                "Failed syncing Facebook Page '{$account->name}'",
                SystemActivityLog::STATUS_FAILED,
                ['meta_account_id' => $account->meta_account_id],
                $errMsg,
                $durationMs,
                $account->id,
                $account->user_id
            );

            return redirect()->back()->with('error', 'Meta Sync Error: ' . $errMsg);

        } catch (\Throwable $e) {
            Log::error('Account sync error: ' . $e->getMessage());
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            ActivityLogger::log(
                SystemActivityLog::EVENT_ACCOUNT_SYNC,
                "Exception while syncing account '{$account->name}'",
                SystemActivityLog::STATUS_FAILED,
                ['meta_account_id' => $account->meta_account_id],
                $e->getMessage(),
                $durationMs,
                $account->id,
                $account->user_id
            );
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync Instagram account details from Meta Graph API
     *
     * Supports both connection methods:
     *  - Via Facebook Page (instagram_business_account_id = FB Page ID) → uses graph.facebook.com
     *  - Direct Instagram Login (instagram_business_account_id = meta_account_id) → uses graph.instagram.com
     */
    private function syncInstagramAccount(Account $account)
    {
        $startTime = microtime(true);
        try {
            // Detect connection method:
            // FB-linked: instagram_business_account_id holds the parent FB Page ID (different from meta_account_id)
            // Direct login: instagram_business_account_id == meta_account_id (self-reference)
            $isDirectLogin = $account->instagram_business_account_id === $account->meta_account_id;

            if ($isDirectLogin) {
                // Direct Instagram Login — use Instagram Graph API
                $response = Http::get("https://graph.instagram.com/v20.0/me", [
                    'fields' => 'user_id,username,name,account_type,profile_picture_url,followers_count,follows_count,media_count',
                    'access_token' => $account->access_token,
                ])->json();
            } else {
                // Connected via Facebook Page — use Facebook Graph API
                $igId = $account->meta_account_id;
                $response = Http::get("https://graph.facebook.com/v20.0/{$igId}", [
                    'fields' => 'id,username,name,profile_picture_url,followers_count,follows_count,media_count',
                    'access_token' => $account->access_token,
                ])->json();
            }

            if (isset($response['error'])) {
                // If token expired, attempt refresh (only for direct-login tokens)
                if ($isDirectLogin && ($response['error']['code'] ?? 0) === 190) {
                    $refreshed = \App\Http\Controllers\Admin\InstagramAuthController::refreshLongLivedToken($account);
                    if ($refreshed) {
                        $account->refresh();
                        return $this->syncInstagramAccount($account);
                    }
                    $durationMs = (int) round((microtime(true) - $startTime) * 1000);
                    ActivityLogger::log(
                        SystemActivityLog::EVENT_ACCOUNT_SYNC,
                        "Instagram token expired for '@{$account->username}'",
                        SystemActivityLog::STATUS_FAILED,
                        ['meta_account_id' => $account->meta_account_id],
                        $response['error'],
                        $durationMs,
                        $account->id,
                        $account->user_id
                    );
                    return redirect()->back()->with('error', 'Instagram token expired. Please reconnect your account.');
                }

                $errMsg = $response['error']['message'] ?? 'Failed to fetch Instagram profile.';
                $durationMs = (int) round((microtime(true) - $startTime) * 1000);
                ActivityLogger::log(
                    SystemActivityLog::EVENT_ACCOUNT_SYNC,
                    "Failed syncing Instagram Profile '@{$account->username}'",
                    SystemActivityLog::STATUS_FAILED,
                    ['meta_account_id' => $account->meta_account_id],
                    $errMsg,
                    $durationMs,
                    $account->id,
                    $account->user_id
                );
                return redirect()->back()->with('error', 'Instagram Sync Error: ' . $errMsg);
            }

            $account->name = $response['name'] ?? ($response['username'] ?? $account->name);
            $account->username = $response['username'] ?? $account->username;
            $account->followers_count = (int) ($response['followers_count'] ?? $account->followers_count);
            $account->following_count = (int) ($response['follows_count'] ?? $account->following_count);
            $account->profile_pic_url = $response['profile_picture_url'] ?? $account->profile_pic_url;
            $account->last_synced_at = now();
            $account->save();

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            ActivityLogger::log(
                SystemActivityLog::EVENT_ACCOUNT_SYNC,
                "Synced Instagram Profile '@{$account->username}'",
                SystemActivityLog::STATUS_SUCCESS,
                ['meta_account_id' => $account->meta_account_id, 'is_direct' => $isDirectLogin],
                ['username' => $account->username, 'followers' => $account->followers_count],
                $durationMs,
                $account->id,
                $account->user_id
            );

            return redirect()->back()->with('success', "@{$account->username} synced successfully with Instagram!");

        } catch (\Throwable $e) {
            Log::error('Instagram sync error: ' . $e->getMessage());
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            ActivityLogger::log(
                SystemActivityLog::EVENT_ACCOUNT_SYNC,
                "Exception syncing Instagram Profile '@{$account->username}'",
                SystemActivityLog::STATUS_FAILED,
                ['meta_account_id' => $account->meta_account_id],
                $e->getMessage(),
                $durationMs,
                $account->id,
                $account->user_id
            );
            return redirect()->back()->with('error', 'Instagram sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect / Delete an account
     */
    public function destroy(Account $account)
    {
        $this->authorizeAccount($account);

        $accountName = $account->name;
        $account->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Account {$accountName} disconnected.",
            ]);
        }

        return redirect()->route('admin.accounts.index')->with('success', "Account {$accountName} successfully disconnected.");
    }

    /**
     * Check authorization: Only account owner or Admin/Manager can manage
     */
    private function authorizeAccount(Account $account): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this social account.');
        }
    }
}

