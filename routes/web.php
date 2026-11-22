<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('landing');
})->name('home');

Route::prefix('admin')->group(function () {
    // Auth Routes
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::name('admin.')->group(function () {
        Route::middleware(['auth'])->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'stats'])->name('dashboard.stats');

            // Profile
            Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
            Route::post('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

            // Political Analytics (Politics Analyses)
            Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

            // Festival Studio (Auto Poster & Scheduler)
            Route::prefix('festival-studio')->name('festival-studio.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\FestivalStudioController::class, 'index'])->name('index');
                Route::post('/quick-schedule', [\App\Http\Controllers\Admin\FestivalStudioController::class, 'quickSchedule'])->name('quick-schedule');
                Route::post('/compose', [\App\Http\Controllers\Admin\PosterComposerController::class, 'compose'])->name('compose');
                Route::get('/presets', [\App\Http\Controllers\Admin\PosterComposerController::class, 'presets'])->name('presets');
            });

            // Branding Studio (Leader/Business Branding Strip Profile)
            Route::prefix('branding')->name('branding.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\BrandingController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\BrandingController::class, 'update'])->name('update');
            });

            // Political Leadership Strips API & Management
            Route::prefix('leader-strips')->name('leader-strips.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\LeaderStripController::class, 'index'])->name('index');
                Route::get('leaders', [\App\Http\Controllers\Admin\LeaderStripController::class, 'leaders'])->name('leaders');
                Route::post('/', [\App\Http\Controllers\Admin\LeaderStripController::class, 'store'])->name('store');
                Route::get('{strip}', [\App\Http\Controllers\Admin\LeaderStripController::class, 'show'])->name('show');
                Route::put('{strip}', [\App\Http\Controllers\Admin\LeaderStripController::class, 'update'])->name('update');
                Route::post('{strip}/default', [\App\Http\Controllers\Admin\LeaderStripController::class, 'makeDefault'])->name('default');
                Route::delete('{strip}', [\App\Http\Controllers\Admin\LeaderStripController::class, 'destroy'])->name('destroy');
            });

            // Connected Social Accounts (Meta / Facebook & Instagram)
            Route::prefix('accounts')->name('accounts.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('index');
                Route::post('{account}/settings', [\App\Http\Controllers\Admin\AccountController::class, 'updateSettings'])->name('update-settings');
                Route::post('{account}/toggle-status', [\App\Http\Controllers\Admin\AccountController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('{account}/sync', [\App\Http\Controllers\Admin\AccountController::class, 'syncAccount'])->name('sync');
                Route::delete('{account}', [\App\Http\Controllers\Admin\AccountController::class, 'destroy'])->name('destroy');
            });

            // Social Posts (Synced Feed, Gallery & Engagement)
            Route::prefix('posts')->name('posts.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('index');
                Route::get('instagram', [\App\Http\Controllers\Admin\PostController::class, 'instagram'])->name('instagram');
                Route::get('facebook', [\App\Http\Controllers\Admin\PostController::class, 'facebook'])->name('facebook');
                Route::get('youtube', [\App\Http\Controllers\Admin\PostController::class, 'youtube'])->name('youtube');
                Route::post('sync', [\App\Http\Controllers\Admin\PostController::class, 'sync'])->name('sync');

                // Content Calendar & Scheduled Publishing
                Route::get('create', [\App\Http\Controllers\Admin\SchedulePostController::class, 'create'])->name('create');
                Route::get('schedule', [\App\Http\Controllers\Admin\SchedulePostController::class, 'index'])->name('schedule');
                Route::get('schedule/events', [\App\Http\Controllers\Admin\SchedulePostController::class, 'events'])->name('schedule.events');
                Route::post('schedule/store', [\App\Http\Controllers\Admin\SchedulePostController::class, 'store'])->name('schedule.store');
                Route::get('schedule/{scheduledPost}', [\App\Http\Controllers\Admin\SchedulePostController::class, 'show'])->name('schedule.show');
                Route::get('schedule/{scheduledPost}/edit', [\App\Http\Controllers\Admin\SchedulePostController::class, 'edit'])->name('schedule.edit');
                Route::match(['put', 'post'], 'schedule/{scheduledPost}', [\App\Http\Controllers\Admin\SchedulePostController::class, 'update'])->name('schedule.update');
                Route::post('schedule/{scheduledPost}/publish-now', [\App\Http\Controllers\Admin\SchedulePostController::class, 'publishNow'])->name('schedule.publish-now');
                Route::delete('schedule/{scheduledPost}', [\App\Http\Controllers\Admin\SchedulePostController::class, 'destroy'])->name('schedule.destroy');
                Route::post('generate-ai-description', [\App\Http\Controllers\Admin\SchedulePostController::class, 'generateAiDescription'])->name('generate-ai-description');
                Route::post('generate-ai-caption', [\App\Http\Controllers\Admin\SchedulePostController::class, 'generateAiCaption'])->name('generate-ai-caption');
            });

            // Canva Connect Integration
            Route::prefix('canva')->name('canva.')->group(function () {
                Route::get('auth', [\App\Http\Controllers\Admin\CanvaController::class, 'auth'])->name('auth');
                Route::get('callback', [\App\Http\Controllers\Admin\CanvaController::class, 'callback'])->name('callback');
                Route::get('status', [\App\Http\Controllers\Admin\CanvaController::class, 'status'])->name('status');
                Route::post('create-design', [\App\Http\Controllers\Admin\CanvaController::class, 'createDesign'])->name('create-design');
                Route::post('export-design', [\App\Http\Controllers\Admin\CanvaController::class, 'exportDesign'])->name('export-design');
                Route::post('disconnect', [\App\Http\Controllers\Admin\CanvaController::class, 'disconnect'])->name('disconnect');
            });

            // Auto DM (Comment-to-Inbox Automation)
            Route::prefix('auto-dm')->name('auto-dm.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\AutoDmController::class, 'index'])->name('index');
                Route::get('create', [\App\Http\Controllers\Admin\AutoDmController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\AutoDmController::class, 'store'])->name('store');
                Route::get('{autoDmRule}/edit', [\App\Http\Controllers\Admin\AutoDmController::class, 'edit'])->name('edit');
                Route::put('{autoDmRule}', [\App\Http\Controllers\Admin\AutoDmController::class, 'update'])->name('update');
                Route::post('{autoDmRule}/toggle', [\App\Http\Controllers\Admin\AutoDmController::class, 'toggle'])->name('toggle');
                Route::delete('{autoDmRule}', [\App\Http\Controllers\Admin\AutoDmController::class, 'destroy'])->name('destroy');
                Route::get('accounts/{account}/posts', [\App\Http\Controllers\Admin\AutoDmController::class, 'getPosts'])->name('account-posts');
            });

            // Webhook Testing & Activity Logs
            Route::prefix('webhook-logs')->name('webhook-logs.')->group(function () {
                Route::get('/', [\App\Http\Controllers\MetaWebhookController::class, 'viewLogs'])->name('index');
                Route::post('/clear', [\App\Http\Controllers\MetaWebhookController::class, 'clearLogs'])->name('clear');
            });

            // Constituent Comments & AI Moderation
            Route::prefix('comments')->name('comments.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
                Route::get('instagram', [\App\Http\Controllers\Admin\CommentController::class, 'instagram'])->name('instagram');
                Route::get('instagram/data', [\App\Http\Controllers\Admin\CommentController::class, 'instagramData'])->name('instagram.data');
                Route::get('facebook', [\App\Http\Controllers\Admin\CommentController::class, 'facebook'])->name('facebook');
                Route::get('facebook/data', [\App\Http\Controllers\Admin\CommentController::class, 'facebookData'])->name('facebook.data');
                Route::get('youtube', [\App\Http\Controllers\Admin\CommentController::class, 'youtube'])->name('youtube');
                Route::get('youtube/data', [\App\Http\Controllers\Admin\CommentController::class, 'youtubeData'])->name('youtube.data');
                Route::get('data', [\App\Http\Controllers\Admin\CommentController::class, 'data'])->name('data');
                Route::post('sync', [\App\Http\Controllers\Admin\CommentController::class, 'sync'])->name('sync');
                Route::post('store-manual', [\App\Http\Controllers\Admin\CommentController::class, 'storeManual'])->name('store-manual');
                Route::post('{comment}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('reply');
                Route::post('{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('approve');
                Route::post('{comment}/toggle-hide', [\App\Http\Controllers\Admin\CommentController::class, 'toggleHide'])->name('toggle-hide');
            });

            // Help Center & Support Tickets
            Route::prefix('help')->name('help.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('index');
                Route::get('data', [\App\Http\Controllers\Admin\TicketController::class, 'data'])->name('data');
                Route::post('/', [\App\Http\Controllers\Admin\TicketController::class, 'store'])->name('store');
                Route::get('{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('show');
                Route::patch('{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('update-status');
            });

            // Users CRUD & Actions (Admin & Manager only)
            Route::middleware(['admin.manager'])->group(function () {
                Route::prefix('users')->name('users.')->group(function () {
                    Route::get('data', [\App\Http\Controllers\Admin\UserController::class, 'data'])->name('data');
                    Route::get('search', [\App\Http\Controllers\Admin\UserController::class, 'search'])->name('search');
                    Route::get('blocked', [\App\Http\Controllers\Admin\UserController::class, 'blocked'])->name('blocked');
                    Route::get('blocked/data', [\App\Http\Controllers\Admin\UserController::class, 'blockedData'])->name('blocked.data');
                    Route::post('{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('unblock');
                    Route::get('deleted', [\App\Http\Controllers\Admin\UserController::class, 'deleted'])->name('deleted');
                    Route::get('deleted/data', [\App\Http\Controllers\Admin\UserController::class, 'deletedData'])->name('deleted.data');
                    Route::post('{user}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('restore');
                    Route::post('{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('update-status');
                    Route::post('{user}/revoke-token', [\App\Http\Controllers\Admin\UserController::class, 'revokeToken'])->name('revoke-token');
                });
                Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

                // Political Management (Parties, Leaders)
                Route::resource('political-parties', \App\Http\Controllers\Admin\PoliticalPartyController::class)->names('political-parties');
                Route::resource('political-leaders', \App\Http\Controllers\Admin\PoliticalLeaderController::class)->names('political-leaders');

                // Poster & Card Templates Management
                Route::resource('festival-templates', \App\Http\Controllers\Admin\FestivalTemplateController::class)->names('festival-templates');
                Route::resource('card-templates', \App\Http\Controllers\Admin\CardTemplateController::class)->names('card-templates');
            });
        });
    });
});

// Meta (Facebook & Instagram) OAuth Routes (Matches Registered Redirect URI in Meta Developer Portal)
Route::middleware(['auth'])->group(function () {
    Route::get('/auth/facebook/redirect', [\App\Http\Controllers\Admin\FacebookAuthController::class, 'redirect'])->name('facebook.redirect');
    Route::get('/auth/instagram/redirect', [\App\Http\Controllers\Admin\InstagramAuthController::class, 'redirect'])->name('instagram.redirect');
    Route::get('/auth/youtube/redirect', [\App\Http\Controllers\Admin\YouTubeAuthController::class, 'redirect'])->name('youtube.redirect');
});

// OAuth Callback routes (Protected by cryptographic state token & auto-restores user session)
Route::get('/auth/facebook/callback', [\App\Http\Controllers\Admin\FacebookAuthController::class, 'callback'])->name('facebook.callback');
Route::get('/auth/instagram/callback', [\App\Http\Controllers\Admin\InstagramAuthController::class, 'callback'])->name('instagram.callback');
Route::get('/auth/youtube/callback', [\App\Http\Controllers\Admin\YouTubeAuthController::class, 'callback'])->name('youtube.callback');

// Meta Webhook Endpoint (Challenge Verification & Live Event Receiver)
Route::match(['GET', 'POST'], '/webhook/meta', [\App\Http\Controllers\MetaWebhookController::class, 'handle'])->name('meta.webhook');

// Public Legal & Meta Compliance Pages (Required for Meta App Review & Live Mode)
Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
Route::match(['GET', 'POST'], '/data-deletion', [\App\Http\Controllers\LegalController::class, 'dataDeletion'])->name('legal.data-deletion');
