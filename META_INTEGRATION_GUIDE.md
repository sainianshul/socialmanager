# 🚀 Political AI SaaS: Meta (Facebook & Instagram) Complete Integration Guide

> **Project Target:** `SocialManager` (Laravel 12 + Tabler UI + Docker)  
> **Status:** Meta App Credentials, Permissions & Live Page API **Tested & 100% Verified** ✅

---

## 1. Verified Live Credentials & Accounts

| Entity                  | Details / Verified Value                                   |
| :---------------------- | :--------------------------------------------------------- |
| **Meta App Name**       | `DevApp`                                                   |
| **Meta App ID**         | `1658585039195864`                                         |
| **Meta App Key/Secret** | Present in `.env` (`FACEBOOK_APP_KEY`)                     |
| **Tested Page Name**    | `Healthiiiii` (Category: Health/beauty)                    |
| **Tested Page ID**      | `1404772002715952`                                         |
| **Tested Live Post ID** | `1404772002715952_122097279687532143`                      |
| **Live Post Link**      | https://www.facebook.com/photo.php?fbid=122097279687532143 |

---

## 2. Meta App Configuration (Meta Developer Portal)

### A. Active Use Cases Added:

- ✅ `Manage everything on your Page` (Flag icon 🏴)
- ✅ `Manage messaging & content on Instagram` (Instagram icon 📸)

### B. Required Scopes / Permissions:

- `pages_show_list` (Client ke pages ki list nikalne ke liye)
- `pages_read_engagement` (Posts, photos, aur reactions read karne ke liye)
- `pages_manage_posts` (Post publish aur schedule karne ke liye)
- `pages_manage_engagement` (Comments par reply karne aur auto-hide karne ke liye)
- `pages_messaging` (Direct DMs aur Comment-to-DM ke liye)

### C. OAuth Redirect URI:

- Go to: **Facebook Login for Business -> Settings**
- Set **Valid OAuth Redirect URIs**:
    ```
    http://localhost:8201/auth/facebook/callback
    ```
    _(Production ke time apna domain daalna: `https://yourdomain.com/auth/facebook/callback`)_

---

## 3. Database Architecture (`accounts` Table)

Laravel migration banayein: `php artisan make:migration create_accounts_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {



            // AI Bot Customization for this page
            $table->string('ai_tone')->default('Humble & Respectful');
            $table->string('ai_language')->default('Hindi + Hinglish');
            $table->boolean('auto_reply_mode')->default(true); // true = Full Auto, false = Approval
            $table->boolean('auto_hide_trolls')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
```

---

## 4. Routes Setup (`routes/web.php`)

```php
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\MetaWebhookController;

Route::middleware(['auth'])->group(function () {
    // 1. Connect Account Page
    Route::get('/dashboard/accounts', [FacebookAuthController::class, 'index'])->name('accounts.index');

    // 2. Redirect to Meta OAuth
    Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirect'])->name('facebook.redirect');

    // 3. Meta OAuth Callback
    Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback'])->name('facebook.callback');
});

// 4. Meta Webhook URL (Public route - CSRF exempt in bootstrap/app.php)
Route::match(['GET', 'POST'], '/webhook/meta', [MetaWebhookController::class, 'handle']);
```

---

## 5. Facebook OAuth Controller (`app/Http/Controllers/FacebookAuthController.php`)

Yeh controller **Client ke 'Connect Facebook' button dabane par automatically token nikalega aur save karega**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
{
    private string $appId;
    private string $appSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->appId = env('FACEBOOK_APP_ID');
        $this->appSecret = env('FACEBOOK_APP_KEY');
        $this->redirectUri = env('APP_URL') . '/auth/facebook/callback';
    }

    // Step 1: User clicks "Connect Facebook Page" -> Send to Meta OAuth
    public function redirect()
    {
        $scopes = implode(',', [
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'pages_manage_engagement',
            'pages_messaging',
            'public_profile'
        ]);

        $loginUrl = "https://www.facebook.com/v20.0/dialog/oauth?" . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $scopes,
            'response_type' => 'code'
        ]);

        return redirect()->away($loginUrl);
    }

    // Step 2: Meta redirects back with authorization code -> Exchange for Page Tokens
    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('accounts.index')->with('error', 'Facebook authentication failed!');
        }

        // A. Exchange code for User Access Token
        $tokenResponse = Http::get("https://graph.facebook.com/v20.0/oauth/access_token", [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ])->json();

        $userAccessToken = $tokenResponse['access_token'] ?? null;
        if (!$userAccessToken) {
            return redirect()->route('accounts.index')->with('error', 'Failed to retrieve user token.');
        }

        // B. Fetch all Pages managed by this user
        $pagesResponse = Http::get("https://graph.facebook.com/v20.0/me/accounts", [
            'fields' => 'id,name,username,picture{url},category,fan_count,access_token',
            'access_token' => $userAccessToken
        ])->json();

        $pages = $pagesResponse['data'] ?? [];

        foreach ($pages as $page) {
            $pageId = $page['id'];
            $pageAccessToken = $page['access_token'];

            // C. Save or Update Page in DB
            $account = Account::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'meta_account_id' => $pageId
                ],
                [
                    'id' => Str::uuid(),
                    'platform' => 'FACEBOOK',
                    'name' => $page['name'],
                    'username' => $page['username'] ?? null,
                    'category' => $page['category'] ?? 'Public Figure',
                    'followers_count' => $page['fan_count'] ?? 0,
                    'profile_pic_url' => $page['picture']['data']['url'] ?? null,
                    'access_token' => $pageAccessToken,
                    'is_active' => true,
                    'webhook_active' => true,
                ]
            );

            // D. AUTOMATIC WEBHOOK SUBSCRIPTION VIA API
            // Meta ko bolna ki is page ke comments hamare webhook par bhejein
            Http::post("https://graph.facebook.com/v20.0/{$pageId}/subscribed_apps", [
                'subscribed_fields' => ['feed', 'mention'],
                'access_token' => $pageAccessToken
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Facebook Page successfully connected!');
    }
}
```

---

## 6. Meta Graph API Service (`app/Services/MetaGraphService.php`)

Comments read karna, reply karna, aur troll hide karne ke reusable functions:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaGraphService
{
    private string $baseUrl = 'https://graph.facebook.com/v20.0';

    // 1. Post Comments Read Karna
    public function getPostComments(string $postId, string $pageToken): array
    {
        $response = Http::get("{$this->baseUrl}/{$postId}/comments", [
            'fields' => 'id,message,from,created_time',
            'access_token' => $pageToken
        ]);

        return $response->json()['data'] ?? [];
    }

    // 2. AI Reply Post Karna
    public function replyToComment(string $commentId, string $replyText, string $pageToken): bool
    {
        $response = Http::post("{$this->baseUrl}/{$commentId}/comments", [
            'message' => $replyText,
            'access_token' => $pageToken
        ]);

        return isset($response->json()['id']);
    }

    // 3. Troll Comment Ko Auto-Hide Karna (Janta ko na dikhe)
    public function hideComment(string $commentId, string $pageToken): bool
    {
        $response = Http::post("{$this->baseUrl}/{$commentId}", [
            'is_hidden' => true,
            'access_token' => $pageToken
        ]);

        return $response->json()['success'] ?? false;
    }

    // 4. Comment-to-DM Private Message Bhejna
    public function sendPrivateReply(string $commentId, string $message, string $pageToken): bool
    {
        $response = Http::post("{$this->baseUrl}/{$commentId}/private_replies", [
            'message' => $message,
            'access_token' => $pageToken
        ]);

        return isset($response->json()['id']);
    }
}
```

---

## 7. Webhook Handler Controller (`app/Http/Controllers/MetaWebhookController.php`)

Meta jab bhi naya comment aayega, hamare is URL par ping karega:

```php
<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCommentWithAIJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    // Meta Webhook Verification (One-time check by Meta)
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            $verifyToken = env('META_VERIFY_TOKEN', 'warroom_secret_token_123');

            if ($request->get('hub_mode') === 'subscribe' && $request->get('hub_verify_token') === $verifyToken) {
                return response($request->get('hub_challenge'), 200);
            }
            return response('Unauthorized', 403);
        }

        // Meta Post Event (When a comment or post occurs)
        $payload = $request->all();

        // Instant 200 OK to Meta (< 200ms) taaki Meta block na kare
        // Aur background queue mein job push kar dena
        dispatch(new ProcessCommentWithAIJob($payload));

        return response('EVENT_RECEIVED', 200);
    }
}
```

---

## 8. Tabler UI View Snippet (`resources/views/accounts/index.blade.php`)

Client ko screen par dikhane ke liye Tabler UI button aur card:

```html
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Connected Social Accounts</h2>
            </div>
            <div class="col-auto ms-auto">
                <!-- 1-Click Connect Button -->
                <a
                    href="{{ route('facebook.redirect') }}"
                    class="btn btn-primary"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-brand-facebook"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"
                        />
                    </svg>
                    Connect Facebook Page
                </a>
            </div>
        </div>
    </div>
</div>
```

---

## 9. Live Tested Curl Commands For Reference

### A. Page Details Fetching:

```bash
curl -X GET "https://graph.facebook.com/v20.0/1404772002715952?fields=id,name,category,fan_count&access_token=PAGE_TOKEN"
```

### B. Read Posts:

```bash
curl -X GET "https://graph.facebook.com/v20.0/1404772002715952/published_posts?access_token=PAGE_TOKEN"
```

### C. Reply to Comment:

```bash
curl -X POST "https://graph.facebook.com/v20.0/{COMMENT_ID}/comments" \
  -d "message=Pranam Ramesh ji! Dhanyawad aapke sahyog ke liye. 🙏" \
  -d "access_token=PAGE_TOKEN"
```

### D. Hide Troll Comment:

```bash
curl -X POST "https://graph.facebook.com/v20.0/{COMMENT_ID}" \
  -d "is_hidden=true" \
  -d "access_token=PAGE_TOKEN"
```

================================================================================
END OF INTEGRATION GUIDE
================================================================================
