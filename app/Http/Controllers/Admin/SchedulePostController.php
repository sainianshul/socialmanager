<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ScheduledPost;
use App\Services\MetaPublishingService;
use App\Services\YouTubePublishingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchedulePostController extends Controller
{
    /**
     * Show form to create and schedule a new post
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user && !$user->canSchedulePosts()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Post scheduling is not enabled on your plan. Please contact admin to upgrade.');
        }

        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        if ($selectedAccountId && !$user->isAdmin() && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        $selectedPlatform = $request->filled('platform') ? (int) $request->get('platform') : Account::TYPE_FACEBOOK;
        if (!in_array($selectedPlatform, [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM, Account::TYPE_YOUTUBE])) {
            $selectedPlatform = Account::TYPE_FACEBOOK;
        }

        $prefillDate = $request->filled('date') ? $request->get('date') : now()->addHours(2)->format('Y-m-d\TH:i');

        $selectedAccountIds = [];
        if ($request->filled('account_ids')) {
            $selectedAccountIds = array_map('intval', (array) $request->get('account_ids'));
        } elseif ($selectedAccountId) {
            $selectedAccountIds = [$selectedAccountId];
        }

        return view('admin.posts.create', compact('accounts', 'selectedAccountId', 'selectedAccountIds', 'selectedPlatform', 'prefillDate'));
    }

    /**
     * Display content calendar and scheduled posts queue
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // Selected channel filter
        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        if ($selectedAccountId && !$user->isAdmin() && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        // Selected platform filter
        $selectedPlatform = $request->filled('platform') ? (int) $request->get('platform') : null;
        if ($selectedPlatform && !in_array($selectedPlatform, [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM, Account::TYPE_YOUTUBE])) {
            $selectedPlatform = null;
        }

        // View mode: calendar (default) or list
        $viewMode = $request->get('view', 'calendar');

        // Upcoming posts query
        $upcomingQuery = ScheduledPost::with(['account', 'user'])
            ->whereIn('account_id', $accountIds);

        if ($selectedAccountId) {
            $upcomingQuery->where('account_id', $selectedAccountId);
        }

        if ($selectedPlatform) {
            $upcomingQuery->where(function ($q) use ($selectedPlatform) {
                $q->where('platform', $selectedPlatform)
                  ->orWhereHas('account', function ($aq) use ($selectedPlatform) {
                      $aq->where('account_type', $selectedPlatform);
                  });
            });
        }

        $upcomingPosts = (clone $upcomingQuery)
            ->whereIn('status', [ScheduledPost::STATUS_SCHEDULED, ScheduledPost::STATUS_DRAFT])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15);

        // Stats summary
        $statsQuery = ScheduledPost::whereIn('account_id', $accountIds);
        if ($selectedAccountId) {
            $statsQuery->where('account_id', $selectedAccountId);
        }
        if ($selectedPlatform) {
            $statsQuery->where(function ($q) use ($selectedPlatform) {
                $q->where('platform', $selectedPlatform)
                  ->orWhereHas('account', function ($aq) use ($selectedPlatform) {
                      $aq->where('account_type', $selectedPlatform);
                  });
            });
        }

        $stats = (clone $statsQuery)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as scheduled_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed_count
            ", [ScheduledPost::STATUS_SCHEDULED, ScheduledPost::STATUS_PUBLISHED, ScheduledPost::STATUS_FAILED])
            ->first();

        // Platform counts for interactive calendar filter tabs
        $platformCounts = [
            'all' => ScheduledPost::whereIn('account_id', $accountIds)->count(),
            'fb'  => ScheduledPost::whereIn('account_id', $accountIds)->where(function($q) { $q->where('platform', Account::TYPE_FACEBOOK)->orWhereHas('account', fn($a) => $a->where('account_type', Account::TYPE_FACEBOOK)); })->count(),
            'ig'  => ScheduledPost::whereIn('account_id', $accountIds)->where(function($q) { $q->where('platform', Account::TYPE_INSTAGRAM)->orWhereHas('account', fn($a) => $a->where('account_type', Account::TYPE_INSTAGRAM)); })->count(),
            'yt'  => ScheduledPost::whereIn('account_id', $accountIds)->where(function($q) { $q->where('platform', Account::TYPE_YOUTUBE)->orWhereHas('account', fn($a) => $a->where('account_type', Account::TYPE_YOUTUBE)); })->count(),
        ];

        return view('admin.posts.schedule', compact(
            'accounts',
            'selectedAccountId',
            'selectedPlatform',
            'platformCounts',
            'upcomingPosts',
            'stats',
            'viewMode'
        ));
    }

    /**
     * Return scheduled posts events formatted for FullCalendar
     */
    public function events(Request $request)
    {
        $user = auth()->user();
        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accountIds = $accountsQuery->pluck('id')->toArray();

        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        $selectedPlatform = $request->filled('platform') ? (int) $request->get('platform') : null;

        $query = ScheduledPost::with('account')
            ->whereIn('account_id', $accountIds);

        if ($selectedAccountId) {
            $query->where('account_id', $selectedAccountId);
        }

        if ($selectedPlatform) {
            $query->where(function ($q) use ($selectedPlatform) {
                $q->where('platform', $selectedPlatform)
                  ->orWhereHas('account', function ($aq) use ($selectedPlatform) {
                      $aq->where('account_type', $selectedPlatform);
                  });
            });
        }

        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->get('start'));
            $end = Carbon::parse($request->get('end'));
            $query->whereBetween('scheduled_at', [$start, $end]);
        }

        $posts = $query->get();

        $events = $posts->map(function (ScheduledPost $post) {
            $account = $post->account;
            $platformType = $post->platform ?: $account->account_type;

            $platformColor = match ($platformType) {
                Account::TYPE_INSTAGRAM => '#e1306c',
                Account::TYPE_YOUTUBE   => '#e02424',
                default                 => '#1877f2',
            };

            $title = !empty($post->title)
                ? Str::limit($post->title, 28)
                : (!empty($post->content) ? Str::limit($post->content, 28) : ucfirst($post->post_type) . ' post');

            $thumbnail = $post->thumbnail_path ? Storage::url($post->thumbnail_path) : $post->first_media_url;

            // Status-driven colors for high contrast and readability
            $eventBgColor = match ($post->status) {
                ScheduledPost::STATUS_PUBLISHED  => '#ecfdf5',
                ScheduledPost::STATUS_FAILED     => '#fef2f2',
                ScheduledPost::STATUS_PUBLISHING => '#fffbeb',
                ScheduledPost::STATUS_DRAFT      => '#f8fafc',
                default                          => '#eff6ff', // Scheduled
            };

            $eventBorderColor = match ($post->status) {
                ScheduledPost::STATUS_PUBLISHED  => '#86efac',
                ScheduledPost::STATUS_FAILED     => '#fca5a5',
                ScheduledPost::STATUS_PUBLISHING => '#fde68a',
                ScheduledPost::STATUS_DRAFT      => '#cbd5e1',
                default                          => '#93c5fd', // Scheduled
            };

            $eventTextColor = match ($post->status) {
                ScheduledPost::STATUS_PUBLISHED  => '#064e3b', // Rich Dark Green
                ScheduledPost::STATUS_FAILED     => '#7f1d1d', // Deep Dark Red
                ScheduledPost::STATUS_PUBLISHING => '#92400e', // Dark Amber
                ScheduledPost::STATUS_DRAFT      => '#475569', // Dark Slate
                default                          => '#0f2d59', // Deep Dark Blue (Scheduled)
            };

            return [
                'id'              => (string) $post->id,
                'title'           => $title,
                'start'           => $post->scheduled_at->toIso8601String(),
                'backgroundColor' => $platformColor,
                'borderColor'     => $platformColor,
                'textColor'       => $eventTextColor,
                'extendedProps'   => [
                    'account_name'    => $account->name,
                    'account_type'    => $account->account_type_name,
                    'platform'        => (int) $platformType,
                    'platform_name'   => $post->platform_name,
                    'platform_icon'   => $account->platform_icon,
                    'platform_badge'  => $post->platform_badge,
                    'post_type'       => $post->post_type,
                    'post_type_badge' => $post->post_type_badge,
                    'status'          => (int) $post->status,
                    'status_name'     => $post->status_name,
                    'status_badge'    => $post->status_badge,
                    'can_publish_now' => $post->canBePublishedNow(),
                    'is_published'    => $post->status === ScheduledPost::STATUS_PUBLISHED,
                    'title'           => $post->title,
                    'content'         => $post->content,
                    'description'     => $post->description,
                    'tags'            => $post->tags,
                    'privacy_status'  => $post->privacy_status,
                    'thumbnail'       => $thumbnail,
                    'scheduled_at'    => $post->scheduled_at->format('d M Y, h:i A'),
                    'show_url'        => route('admin.posts.schedule.show', $post->id),
                    'edit_url'        => route('admin.posts.schedule.edit', $post->id),
                    'publish_url'     => route('admin.posts.schedule.publish-now', $post->id),
                    'delete_url'      => route('admin.posts.schedule.destroy', $post->id),
                    'is_editable'     => $post->isEditable(),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Store and schedule a new post
     */
    public function store(
        Request $request,
        MetaPublishingService $publishingService,
        YouTubePublishingService $youtubePublishingService
    ) {
        $accountIds = $request->input('account_ids', []);
        if (!is_array($accountIds)) {
            $accountIds = $accountIds ? [(int) $accountIds] : [];
        }

        if (empty($accountIds) && $request->filled('account_id')) {
            $accountIds = [(int) $request->input('account_id')];
        }

        $accountIds = array_values(array_unique(array_filter(array_map('intval', $accountIds))));

        if (empty($accountIds)) {
            return redirect()->back()->with('error', 'Please select at least one social channel account.')->withInput();
        }

        $user = auth()->user();
        $accounts = Account::whereIn('id', $accountIds)->get();

        if ($accounts->isEmpty()) {
            return redirect()->back()->with('error', 'Selected account(s) not found.')->withInput();
        }

        if (!$user->isAdmin()) {
            foreach ($accounts as $account) {
                if ($account->user_id !== $user->id) {
                    abort(403, 'Unauthorized channel selection.');
                }
            }
        }

        $accountPlatforms = $accounts->pluck('account_type')->map(fn($t) => (int) $t)->unique()->toArray();
        $inputPlatform = $request->filled('platform') ? (int) $request->input('platform') : null;

        $isYouTube = false;
        if (count($accountPlatforms) === 1 && $accountPlatforms[0] === Account::TYPE_YOUTUBE) {
            $isYouTube = true;
        } elseif ($inputPlatform === Account::TYPE_YOUTUBE && in_array(Account::TYPE_YOUTUBE, $accountPlatforms)) {
            $isYouTube = true;
        }

        $publishMode = $request->input('publish_mode', 'schedule');
        if (!in_array($publishMode, ['now', 'schedule', 'draft'])) {
            return redirect()->back()->with('error', 'Invalid publishing mode.')->withInput();
        }

        $mediaUrls = [];
        $thumbnailPath = null;
        $title = null;
        $description = null;
        $tags = null;
        $privacyStatus = 'public';
        $content = '';
        $postType = 'photo';
        $firstComment = $request->input('first_comment');

        if ($isYouTube) {
            $validated = $request->validate([
                'title'          => 'required|string|max:100',
                'description'    => 'nullable|string|max:5000',
                'first_comment'  => 'nullable|string|max:2200',
                'video_file'     => 'nullable|file|mimes:mp4,mov,webm,avi,mkv|max:102400',
                'thumbnail'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
                'tags'           => 'nullable|string|max:500',
                'privacy_status' => 'required|in:public,unlisted,private',
                'publish_mode'   => 'required|in:now,schedule,draft',
                'scheduled_at'   => 'nullable|date',
            ]);

            if ($publishMode !== 'draft' && !$request->hasFile('video_file')) {
                return redirect()->back()
                    ->withErrors(['video_file' => 'A video file (MP4, MOV, or WebM) is required for YouTube uploads.'])
                    ->withInput();
            }

            if ($request->hasFile('video_file')) {
                $file = $request->file('video_file');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/youtube_videos', $filename, 'public');
                $mediaUrls[] = $path;
            }

            if ($request->hasFile('thumbnail')) {
                $thumb = $request->file('thumbnail');
                $thumbName = time() . '_thumb_' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();
                $thumbnailPath = $thumb->storeAs('uploads/thumbnails', $thumbName, 'public');
            } elseif ($request->filled('canva_thumbnail')) {
                $canvaThumb = $this->sanitizeCanvaPath($request->input('canva_thumbnail'));
                if ($canvaThumb) {
                    $thumbnailPath = $canvaThumb;
                }
            }

            if ($request->filled('tags')) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $request->input('tags')))));
            }

            $title = $validated['title'];
            $description = $validated['description'] ?? null;
            $content = $description ?: $title;
            $postType = ScheduledPost::TYPE_VIDEO;
            $privacyStatus = $validated['privacy_status'];
        } elseif (count($accountPlatforms) === 1 && $accountPlatforms[0] === Account::TYPE_INSTAGRAM) {
            $validated = $request->validate([
                'post_type'     => 'required|in:photo,video,reel,carousel',
                'content'       => 'nullable|string|max:2200',
                'first_comment' => 'nullable|string|max:2200',
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            if ($request->filled('canva_media_paths')) {
                foreach ((array) $request->input('canva_media_paths') as $cPath) {
                    $clean = $this->sanitizeCanvaPath($cPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($request->filled('readymade_poster_paths')) {
                foreach ((array) $request->input('readymade_poster_paths') as $rPath) {
                    $clean = $this->sanitizeReadymadePosterPath($rPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($publishMode !== 'draft' && !$request->hasFile('media_files') && empty($mediaUrls)) {
                return redirect()->back()
                    ->withErrors(['media_files' => 'At least one photo or video file is required for Instagram posts.'])
                    ->withInput();
            }

            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            $postType = $validated['post_type'];
            $content = $validated['content'] ?? '';
        } else {
            // Facebook or cross-platform Meta (Facebook + Instagram)
            $validated = $request->validate([
                'post_type'     => 'nullable|in:text,photo,video,reel,carousel',
                'content'       => 'nullable|string|max:5000',
                'first_comment' => 'nullable|string|max:2200',
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            $postType = $validated['post_type'] ?? 'photo';

            if ($request->filled('canva_media_paths')) {
                foreach ((array) $request->input('canva_media_paths') as $cPath) {
                    $clean = $this->sanitizeCanvaPath($cPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($request->filled('readymade_poster_paths')) {
                foreach ((array) $request->input('readymade_poster_paths') as $rPath) {
                    $clean = $this->sanitizeReadymadePosterPath($rPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if (in_array(Account::TYPE_INSTAGRAM, $accountPlatforms) && $publishMode !== 'draft' && !$request->hasFile('media_files') && empty($mediaUrls)) {
                return redirect()->back()
                    ->withErrors(['media_files' => 'At least one photo or video file is required because one or more Instagram accounts are selected.'])
                    ->withInput();
            }

            if ($publishMode !== 'draft' && $postType === 'text' && empty(trim($validated['content'] ?? ''))) {
                return redirect()->back()
                    ->withErrors(['content' => 'Post message cannot be empty for text posts.'])
                    ->withInput();
            }

            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            $content = $validated['content'] ?? '';
        }

        // PostSafetyGuard Check 1: Block hazardous URL shorteners & locker links
        $blockedDomain = \App\Services\PostSafetyGuard::checkBlockedDomains($content, $firstComment, $description, $title);
        if ($blockedDomain) {
            return redirect()->back()
                ->withErrors(['content' => "Link shorteners/lockers like '{$blockedDomain}' are not allowed for account safety."])
                ->withInput();
        }

        // PostSafetyGuard Check 2: Max 20 hashtags across content & first comment
        $hashtagCount = \App\Services\PostSafetyGuard::countHashtags($content, $firstComment, $description);
        if ($hashtagCount > \App\Services\PostSafetyGuard::MAX_HASHTAGS) {
            return redirect()->back()
                ->withErrors(['content' => "Too many hashtags ({$hashtagCount}). Maximum " . \App\Services\PostSafetyGuard::MAX_HASHTAGS . " hashtags allowed per post."])
                ->withInput();
        }

        // Determine scheduled time
        $scheduledAt = now();
        $status = ScheduledPost::STATUS_SCHEDULED;

        if ($publishMode === 'schedule') {
            if (empty($validated['scheduled_at'])) {
                return redirect()->back()->with('error', 'Please choose a valid date & time for scheduling.')->withInput();
            }
            $scheduledAt = Carbon::parse($validated['scheduled_at']);
            $status = ScheduledPost::STATUS_SCHEDULED;

            // PostSafetyGuard Check 3 & 4: Daily Limit (<= 20/day) & Collision (10 min gap) per selected account
            foreach ($accounts as $account) {
                $dailyCount = \App\Services\PostSafetyGuard::checkDailyLimit($account->id, $scheduledAt);
                if ($dailyCount !== null) {
                    return redirect()->back()
                        ->with('error', "Daily limit reached for account '{$account->name}': 20 Posts Allowed per day ({$dailyCount} posts already scheduled/published).")
                        ->withInput();
                }

                $collision = \App\Services\PostSafetyGuard::checkCollision($account->id, $scheduledAt);
                if ($collision) {
                    $existingTime = $collision->scheduled_at->format('h:i A');
                    return redirect()->back()
                        ->with('error', "Time collision for account '{$account->name}': There is already a post scheduled at {$existingTime}. Please keep at least a 10-minute gap.")
                        ->withInput();
                }
            }
        } elseif ($publishMode === 'draft') {
            $status = ScheduledPost::STATUS_DRAFT;
        }

        $createdPosts = [];
        foreach ($accounts as $account) {
            $accountPlatform = (int) $account->account_type;
            $accountPostType = $postType;

            if ($accountPlatform === Account::TYPE_INSTAGRAM && $accountPostType === 'text') {
                $accountPostType = 'photo';
            }
            if ($accountPlatform === Account::TYPE_YOUTUBE) {
                $accountPostType = ScheduledPost::TYPE_VIDEO;
            }

            $scheduledPost = ScheduledPost::create([
                'user_id'        => $user->id,
                'account_id'     => $account->id,
                'platform'       => $accountPlatform,
                'post_type'      => $accountPostType,
                'title'          => $title,
                'description'    => $description,
                'content'        => $content,
                'first_comment'  => $firstComment,
                'media_urls'     => $mediaUrls,
                'thumbnail_path' => $thumbnailPath,
                'tags'           => $tags,
                'privacy_status' => $privacyStatus,
                'scheduled_at'   => $scheduledAt,
                'status'         => $status,
            ]);

            $createdPosts[] = $scheduledPost;
        }

        if ($request->filled('readymade_poster_id')) {
            \App\Models\Poster::where('id', $request->input('readymade_poster_id'))->increment('usage_count');
        }

        // If Publish Now chosen, dispatch immediately to each account
        if ($publishMode === 'now') {
            $successfulAccounts = [];
            $failedAccounts = [];

            foreach ($createdPosts as $post) {
                if ($post->platform === Account::TYPE_YOUTUBE) {
                    $result = $youtubePublishingService->publish($post);
                } else {
                    $result = $publishingService->publish($post);
                }

                if ($result['success']) {
                    $successfulAccounts[] = $post->account->name;
                } else {
                    $failedAccounts[] = $post->account->name . ' (' . ($result['message'] ?? 'Publish failed') . ')';
                }
            }

            if (empty($failedAccounts)) {
                $successMsg = count($createdPosts) === 1
                    ? 'Post published live to ' . $createdPosts[0]->account->name . ' successfully!'
                    : 'Post published live to all ' . count($createdPosts) . ' accounts successfully!';

                return redirect()->route('admin.posts.schedule')->with('success', $successMsg);
            } elseif (empty($successfulAccounts)) {
                return redirect()->route('admin.posts.schedule')
                    ->with('warning', 'Post saved, but publishing failed: ' . implode(', ', $failedAccounts));
            } else {
                return redirect()->route('admin.posts.schedule')
                    ->with('warning', 'Published to ' . implode(', ', $successfulAccounts) . ', but failed on: ' . implode(', ', $failedAccounts));
            }
        }

        foreach ($createdPosts as $p) {
            $postTitle = $p->title ?: ($p->content ? \Illuminate\Support\Str::limit($p->content, 40) : 'Post #' . $p->id);
            $actionLabel = $status === ScheduledPost::STATUS_DRAFT
                ? "Saved draft post for {$p->account->name}"
                : "Scheduled post '{$postTitle}' for {$p->account->name} on " . $scheduledAt->format('d M, h:i A');

            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_POST_PUBLISH,
                $actionLabel,
                \App\Models\SystemActivityLog::STATUS_SUCCESS,
                [
                    'scheduled_post_id' => $p->id,
                    'platform'          => $p->platform,
                    'post_type'         => $p->post_type,
                    'scheduled_at'      => $p->scheduled_at,
                    'status'            => $status,
                ],
                ['title' => $p->title],
                null,
                $p->account_id,
                $p->user_id
            );
        }

        if (count($createdPosts) === 1) {
            $msg = $status === ScheduledPost::STATUS_DRAFT
                ? 'Post saved as draft.'
                : 'Post successfully scheduled for ' . $scheduledAt->format('d M Y, h:i A');
        } else {
            $msg = $status === ScheduledPost::STATUS_DRAFT
                ? count($createdPosts) . ' posts saved as draft.'
                : 'Post successfully scheduled across ' . count($createdPosts) . ' accounts for ' . $scheduledAt->format('d M Y, h:i A');
        }

        return redirect()->route('admin.posts.schedule')->with('success', $msg);
    }

    /**
     * Show single scheduled post details (JSON endpoint for drawer / modal)
     */
    public function show(ScheduledPost $scheduledPost)
    {
        $this->authorizePost($scheduledPost);

        $scheduledPost->load(['account', 'user']);

        $thumbnail = $scheduledPost->thumbnail_path
            ? Storage::url($scheduledPost->thumbnail_path)
            : $scheduledPost->first_media_url;

        return response()->json([
            'id'              => $scheduledPost->id,
            'platform'        => $scheduledPost->platform,
            'account_name'    => $scheduledPost->account->name,
            'account_type'    => $scheduledPost->account->account_type_name,
            'platform_icon'   => $scheduledPost->account->platform_icon,
            'platform_badge'  => $scheduledPost->platform_badge,
            'post_type'       => $scheduledPost->post_type,
            'post_type_badge' => $scheduledPost->post_type_badge,
            'title'           => $scheduledPost->title,
            'description'     => $scheduledPost->description,
            'tags'            => $scheduledPost->tags,
            'privacy_status'  => $scheduledPost->privacy_status,
            'content'         => $scheduledPost->content,
            'media_urls'      => $scheduledPost->media_urls ?? [],
            'first_media'     => $scheduledPost->first_media_url,
            'thumbnail'       => $thumbnail,
            'status'          => $scheduledPost->status,
            'status_name'     => $scheduledPost->status_name,
            'status_badge'    => $scheduledPost->status_badge,
            'can_publish_now' => $scheduledPost->canBePublishedNow(),
            'is_published'    => $scheduledPost->status === ScheduledPost::STATUS_PUBLISHED,
            'is_editable'     => $scheduledPost->isEditable(),
            'edit_url'        => route('admin.posts.schedule.edit', $scheduledPost->id),
            'scheduled_at'    => $scheduledPost->scheduled_at->format('d M Y, h:i A'),
            'meta_post_id'    => $scheduledPost->meta_post_id,
            'error_message'   => $scheduledPost->error_message,
            'publish_url'     => route('admin.posts.schedule.publish-now', $scheduledPost->id),
            'delete_url'      => route('admin.posts.schedule.destroy', $scheduledPost->id),
        ]);
    }

    /**
     * Show form to edit an unpublished scheduled post
     */
    public function edit(ScheduledPost $scheduledPost)
    {
        $this->authorizePost($scheduledPost);

        if (!$scheduledPost->isEditable()) {
            return redirect()->route('admin.posts.schedule')
                ->with('error', 'Published or currently publishing posts cannot be edited.');
        }

        $scheduledPost->load(['account', 'user']);
        $platform = (int) ($scheduledPost->platform ?: $scheduledPost->account->account_type);

        return view('admin.posts.edit', compact('scheduledPost', 'platform'));
    }

    /**
     * Update an unpublished scheduled post
     */
    public function update(
        Request $request,
        ScheduledPost $scheduledPost,
        MetaPublishingService $publishingService,
        YouTubePublishingService $youtubePublishingService
    ) {
        $this->authorizePost($scheduledPost);

        if (!$scheduledPost->isEditable()) {
            return redirect()->route('admin.posts.schedule')
                ->with('error', 'Published or currently publishing posts cannot be edited.');
        }

        // STRICT CONSTRAINT: Platform & Account are locked to prevent cross-platform schema mismatch
        $platform = (int) ($scheduledPost->platform ?: $scheduledPost->account->account_type);

        $publishMode = $request->input('publish_mode', 'schedule');
        if (!in_array($publishMode, ['now', 'schedule', 'draft'])) {
            return redirect()->back()->with('error', 'Invalid publishing mode.')->withInput();
        }

        $mediaUrls = is_array($scheduledPost->media_urls) ? $scheduledPost->media_urls : [];
        $thumbnailPath = $scheduledPost->thumbnail_path;

        if ($platform === Account::TYPE_YOUTUBE) {
            $validated = $request->validate([
                'title'          => 'required|string|max:100',
                'description'    => 'nullable|string|max:5000',
                'first_comment'  => 'nullable|string|max:2200',
                'video_file'     => 'nullable|file|mimes:mp4,mov,webm,avi,mkv|max:102400',
                'thumbnail'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
                'tags'           => 'nullable|string|max:500',
                'privacy_status' => 'required|in:public,unlisted,private',
                'publish_mode'   => 'required|in:now,schedule,draft',
                'scheduled_at'   => 'nullable|date',
            ]);

            // If user uploads a new video file, safely delete old user video and save new
            if ($request->hasFile('video_file')) {
                if (!empty($mediaUrls)) {
                    foreach ($mediaUrls as $oldFile) {
                        $this->safelyDeleteMediaFile($oldFile);
                    }
                }
                $file = $request->file('video_file');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/youtube_videos', $filename, 'public');
                $mediaUrls = [$path];
            } elseif ($publishMode !== 'draft' && empty($mediaUrls)) {
                return redirect()->back()
                    ->withErrors(['video_file' => 'A video file (MP4, MOV, or WebM) is required for YouTube uploads.'])
                    ->withInput();
            }

            // If user uploads a new thumbnail, safely delete old thumbnail and save new
            if ($request->hasFile('thumbnail')) {
                if ($thumbnailPath) {
                    $this->safelyDeleteThumbnailFile($thumbnailPath);
                }
                $thumb = $request->file('thumbnail');
                $thumbName = time() . '_thumb_' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();
                $thumbnailPath = $thumb->storeAs('uploads/thumbnails', $thumbName, 'public');
            } elseif ($request->filled('canva_thumbnail')) {
                $canvaThumb = $this->sanitizeCanvaPath($request->input('canva_thumbnail'));
                if ($canvaThumb) {
                    $thumbnailPath = $canvaThumb;
                }
            }

            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $request->input('tags')))));
            }

            $scheduledPost->title = $validated['title'];
            $scheduledPost->description = $validated['description'] ?? null;
            $scheduledPost->content = $scheduledPost->description ?: $scheduledPost->title;
            $scheduledPost->post_type = ScheduledPost::TYPE_VIDEO;
            $scheduledPost->privacy_status = $validated['privacy_status'];
            $scheduledPost->tags = $tags;
            $scheduledPost->media_urls = $mediaUrls;
            $scheduledPost->thumbnail_path = $thumbnailPath;

        } elseif ($platform === Account::TYPE_INSTAGRAM) {
            $validated = $request->validate([
                'post_type'     => 'required|in:photo,video,reel,carousel',
                'content'       => 'nullable|string|max:2200',
                'first_comment' => 'nullable|string|max:2200',
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            // If new media files uploaded, replace old ones
            if ($request->hasFile('media_files')) {
                if (!empty($mediaUrls)) {
                    foreach ($mediaUrls as $oldFile) {
                        $this->safelyDeleteMediaFile($oldFile);
                    }
                }
                $mediaUrls = [];
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            if ($request->filled('canva_media_paths')) {
                foreach ((array) $request->input('canva_media_paths') as $cPath) {
                    $clean = $this->sanitizeCanvaPath($cPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($request->filled('readymade_poster_paths')) {
                foreach ((array) $request->input('readymade_poster_paths') as $rPath) {
                    $clean = $this->sanitizeReadymadePosterPath($rPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($publishMode !== 'draft' && empty($mediaUrls)) {
                return redirect()->back()
                    ->withErrors(['media_files' => 'At least one photo or video file is required for Instagram posts.'])
                    ->withInput();
            }

            $scheduledPost->post_type = $validated['post_type'];
            $scheduledPost->content = $validated['content'] ?? '';
            $scheduledPost->media_urls = $mediaUrls;

        } else {
            // Facebook
            $validated = $request->validate([
                'post_type'     => 'required|in:text,photo,video,reel,carousel',
                'content'       => 'nullable|string|max:5000',
                'first_comment' => 'nullable|string|max:2200',
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            if ($publishMode !== 'draft' && $validated['post_type'] === 'text' && empty(trim($validated['content'] ?? ''))) {
                return redirect()->back()
                    ->withErrors(['content' => 'Post message cannot be empty for text posts.'])
                    ->withInput();
            }

            if ($request->hasFile('media_files')) {
                if (!empty($mediaUrls)) {
                    foreach ($mediaUrls as $oldFile) {
                        $this->safelyDeleteMediaFile($oldFile);
                    }
                }
                $mediaUrls = [];
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            if ($request->filled('canva_media_paths')) {
                foreach ((array) $request->input('canva_media_paths') as $cPath) {
                    $clean = $this->sanitizeCanvaPath($cPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            if ($request->filled('readymade_poster_paths')) {
                foreach ((array) $request->input('readymade_poster_paths') as $rPath) {
                    $clean = $this->sanitizeReadymadePosterPath($rPath);
                    if ($clean) {
                        $mediaUrls[] = $clean;
                    }
                }
            }

            $scheduledPost->post_type = $validated['post_type'];
            $scheduledPost->content = $validated['content'] ?? '';
            $scheduledPost->media_urls = $mediaUrls;
        }

        $scheduledPost->first_comment = $request->input('first_comment');

        // PostSafetyGuard Check 1: Block hazardous URL shorteners & locker links
        $blockedDomain = \App\Services\PostSafetyGuard::checkBlockedDomains(
            $scheduledPost->content,
            $scheduledPost->first_comment,
            $scheduledPost->description,
            $scheduledPost->title
        );
        if ($blockedDomain) {
            return redirect()->back()
                ->withErrors(['content' => "Link shorteners/lockers like '{$blockedDomain}' are not allowed for account safety."])
                ->withInput();
        }

        // PostSafetyGuard Check 2: Max 20 hashtags
        $hashtagCount = \App\Services\PostSafetyGuard::countHashtags(
            $scheduledPost->content,
            $scheduledPost->first_comment,
            $scheduledPost->description
        );
        if ($hashtagCount > \App\Services\PostSafetyGuard::MAX_HASHTAGS) {
            return redirect()->back()
                ->withErrors(['content' => "Too many hashtags ({$hashtagCount}). Maximum " . \App\Services\PostSafetyGuard::MAX_HASHTAGS . " hashtags allowed per post."])
                ->withInput();
        }

        // Determine schedule time & status
        if ($publishMode === 'schedule') {
            if (empty($validated['scheduled_at'])) {
                return redirect()->back()->with('error', 'Please choose a valid date & time for scheduling.')->withInput();
            }
            $targetScheduledAt = Carbon::parse($validated['scheduled_at']);

            // PostSafetyGuard Check 3: Daily Limit (<= 20/day)
            $dailyCount = \App\Services\PostSafetyGuard::checkDailyLimit($scheduledPost->account_id, $targetScheduledAt, $scheduledPost->id);
            if ($dailyCount !== null) {
                return redirect()->back()
                    ->with('error', "Daily limit reached for this account: 20 Posts Allowed per day ({$dailyCount} posts on this day).")
                    ->withInput();
            }

            // PostSafetyGuard Check 4: Collision (10 min gap)
            $collision = \App\Services\PostSafetyGuard::checkCollision($scheduledPost->account_id, $targetScheduledAt, $scheduledPost->id);
            if ($collision) {
                $existingTime = $collision->scheduled_at->format('h:i A');
                return redirect()->back()
                    ->with('error', "Time collision: There is already a post scheduled at {$existingTime} for this account. Please keep at least a 10-minute gap.")
                    ->withInput();
            }

            $scheduledPost->scheduled_at = $targetScheduledAt;
            $scheduledPost->status = ScheduledPost::STATUS_SCHEDULED;
        } elseif ($publishMode === 'draft') {
            $scheduledPost->status = ScheduledPost::STATUS_DRAFT;
            if (!empty($validated['scheduled_at'])) {
                $scheduledPost->scheduled_at = Carbon::parse($validated['scheduled_at']);
            }
        }

        $scheduledPost->save();

        // If Publish Now chosen, dispatch immediately
        if ($publishMode === 'now') {
            $scheduledPost->scheduled_at = now();
            $scheduledPost->save();

            if ($platform === Account::TYPE_YOUTUBE) {
                $result = $youtubePublishingService->publish($scheduledPost);
            } else {
                $result = $publishingService->publish($scheduledPost);
            }

            if ($result['success']) {
                return redirect()->route('admin.posts.schedule')
                    ->with('success', 'Post updated and published live successfully!');
            } else {
                return redirect()->route('admin.posts.schedule')
                    ->with('warning', 'Post updated, but publishing service returned an error: ' . $result['message']);
            }
        }

        $msg = $scheduledPost->status === ScheduledPost::STATUS_DRAFT
            ? 'Post updated and saved as draft.'
            : 'Scheduled post updated successfully for ' . $scheduledPost->scheduled_at->format('d M Y, h:i A');

        return redirect()->route('admin.posts.schedule')->with('success', $msg);
    }

    /**
     * Instantly publish a scheduled post now
     */
    public function publishNow(
        ScheduledPost $scheduledPost,
        MetaPublishingService $publishingService,
        YouTubePublishingService $youtubePublishingService
    ) {
        $this->authorizePost($scheduledPost);

        if ($scheduledPost->isYouTube() || $scheduledPost->account->isYouTube()) {
            $result = $youtubePublishingService->publish($scheduledPost);
        } else {
            $result = $publishingService->publish($scheduledPost);
        }

        if ($result['success']) {
            \App\Services\ActivityLogger::log(
                \App\Models\SystemActivityLog::EVENT_POST_PUBLISH,
                "Published live post #{$scheduledPost->id} to {$scheduledPost->account->name}",
                \App\Models\SystemActivityLog::STATUS_SUCCESS,
                ['post_id' => $scheduledPost->id, 'type' => $scheduledPost->post_type],
                $result,
                null,
                $scheduledPost->account_id,
                $scheduledPost->user_id
            );
            return redirect()->back()->with('success', 'Post published live successfully!');
        }

        \App\Services\ActivityLogger::log(
            \App\Models\SystemActivityLog::EVENT_POST_PUBLISH,
            "Failed publishing post #{$scheduledPost->id} to {$scheduledPost->account->name}",
            \App\Models\SystemActivityLog::STATUS_FAILED,
            ['post_id' => $scheduledPost->id],
            $result['message'],
            null,
            $scheduledPost->account_id,
            $scheduledPost->user_id
        );
        return redirect()->back()->with('error', 'Failed to publish post: ' . $result['message']);
    }

    /**
     * Delete or cancel a scheduled post
     */
    public function destroy(ScheduledPost $scheduledPost)
    {
        $this->authorizePost($scheduledPost);

        // Safely remove user-uploaded media files from storage without touching shared templates/posters
        if (is_array($scheduledPost->media_urls)) {
            foreach ($scheduledPost->media_urls as $path) {
                $this->safelyDeleteMediaFile($path);
            }
        }

        // Safely remove user thumbnail if present
        if ($scheduledPost->thumbnail_path) {
            $this->safelyDeleteThumbnailFile($scheduledPost->thumbnail_path);
        }

        $scheduledPost->delete();

        return redirect()->route('admin.posts.schedule')->with('success', 'Scheduled post cancelled and removed.');
    }

    /**
     * Authorize user access to post
     */
    protected function authorizePost(ScheduledPost $post): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return;
        }

        if ($post->user_id !== $user->id && $post->account->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this scheduled post.');
        }
    }

    /**
     * Safely validate and sanitize a Canva-exported media path against traversal.
     */
    private function sanitizeCanvaPath(?string $path): ?string
    {
        if (empty($path) || !is_string($path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($path));
        if (str_contains($path, '..') || !str_starts_with($path, 'posts/canva/')) {
            return null;
        }
        return Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Safely validate and sanitize a readymade poster media path against traversal.
     */
    private function sanitizeReadymadePosterPath(?string $path): ?string
    {
        if (empty($path) || !is_string($path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($path));
        if (str_contains($path, '..') || !str_starts_with($path, 'posters/')) {
            return null;
        }
        return Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Safely delete a user-uploaded post media file from storage without touching shared assets.
     */
    private function safelyDeleteMediaFile(?string $path): void
    {
        if (empty($path) || !is_string($path)) {
            return;
        }
        $path = str_replace('\\', '/', trim($path));
        if (str_contains($path, '..')) {
            return;
        }
        // Never delete shared templates or readymade posters
        $isDisposableUserMedia = str_starts_with($path, 'uploads/scheduled_posts/')
            || str_starts_with($path, 'uploads/youtube_videos/')
            || str_starts_with($path, 'posts/canva/');

        if ($isDisposableUserMedia && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Safely delete a user thumbnail file from storage without touching shared assets.
     */
    private function safelyDeleteThumbnailFile(?string $path): void
    {
        if (empty($path) || !is_string($path)) {
            return;
        }
        $path = str_replace('\\', '/', trim($path));
        if (str_contains($path, '..')) {
            return;
        }
        $isDisposableThumbnail = str_starts_with($path, 'uploads/thumbnails/')
            || str_starts_with($path, 'posts/canva/');

        if ($isDisposableThumbnail && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Generate SEO-friendly YouTube video description using AI from entered Title.
     */
    public function generateAiDescription(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->canUseAi()) {
            return response()->json([
                'success' => false,
                'message' => 'AI features are not enabled on your plan. Please contact admin to upgrade.',
            ], 403);
        }

        if ($user && $user->hasExceededAiQuota()) {
            return response()->json([
                'success' => false,
                'message' => 'Monthly AI token quota exceeded. Please upgrade your plan or wait for next billing cycle.',
            ], 429);
        }

        $request->validate([
            'title' => 'required|string|min:3|max:100',
        ]);

        $title = trim($request->input('title'));

        try {
            $aiService = app(\App\Contracts\AIServiceInterface::class);
            $description = null;

            if ($aiService->isConfigured()) {
                $description = $aiService->generateVideoDescription($title, [
                    'platform' => 'youtube',
                ]);
            }

            if (empty($description)) {
                $description = $this->generateFallbackDescription($title);
            } elseif (!str_contains($description, '#')) {
                $cleanTitle = trim($title);
                $words = array_filter(explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $cleanTitle)));
                $hashtags = [];
                foreach (array_slice($words, 0, 4) as $w) {
                    if (strlen($w) > 2) {
                        $hashtags[] = '#' . ucfirst(strtolower($w));
                    }
                }
                $hashtags[] = '#YouTube';
                $hashtags[] = '#Updates';
                $description .= "\n\n" . implode(' ', array_unique($hashtags));
            }

            if ($user && !empty($description)) {
                $user->recordAiTokenUsage(400);
            }

            return response()->json([
                'success'         => true,
                'description'     => $description,
                'character_count' => mb_strlen($description),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AI Description Generation error: ' . $e->getMessage());

            $fallback = $this->generateFallbackDescription($title);
            return response()->json([
                'success'         => true,
                'description'     => $fallback,
                'character_count' => mb_strlen($fallback),
            ]);
        }
    }

    /**
     * Rule-based smart SEO fallback description when external AI is offline or unconfigured.
     */
    protected function generateFallbackDescription(string $title): string
    {
        $cleanTitle = trim($title);
        $words = array_filter(explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $cleanTitle)));
        $hashtags = [];
        foreach (array_slice($words, 0, 4) as $w) {
            if (strlen($w) > 2) {
                $hashtags[] = '#' . ucfirst(strtolower($w));
            }
        }
        $hashtags[] = '#YouTube';
        $hashtags[] = '#Updates';
        $tagStr = implode(' ', array_unique($hashtags));

        return "Welcome to this official video on: {$cleanTitle}\n\n"
            . "In this video, we provide an in-depth breakdown and comprehensive updates regarding {$cleanTitle}. Watch till the end for complete insights, important announcements, and key details.\n\n"
            . "What You Will Learn:\n"
            . "- Complete overview and critical highlights\n"
            . "- In-depth explanation of {$cleanTitle}\n"
            . "- Actionable guidance and official updates\n\n"
            . "If you found this video helpful, please LIKE, SHARE, and SUBSCRIBE for more timely updates!\n"
            . "Drop your questions and valuable feedback in the comments section below.\n\n"
            . "{$tagStr}";
    }

    /**
     * Generate engaging AI social captions and trending hashtags.
     */
    public function generateAiCaption(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->canUseAi()) {
            return response()->json([
                'success' => false,
                'message' => 'AI features are not enabled on your plan. Please contact admin to upgrade.',
            ], 403);
        }

        if ($user && $user->hasExceededAiQuota()) {
            return response()->json([
                'success' => false,
                'message' => 'Monthly AI token quota exceeded. Please upgrade your plan or wait for next billing cycle.',
            ], 429);
        }

        $request->validate([
            'topic'    => 'required|string|min:2|max:500',
            'platform' => 'nullable|string|in:facebook,instagram,youtube,all',
            'tone'     => 'nullable|string|max:50',
            'language' => 'nullable|string|max:50',
        ]);

        $topic = trim($request->input('topic'));
        $options = [
            'platform' => $request->input('platform', 'all'),
            'tone'     => $request->input('tone', 'engaging'),
            'language' => $request->input('language', 'English'),
        ];

        try {
            $aiService = app(\App\Contracts\AIServiceInterface::class);
            $result = null;

            if ($aiService->isConfigured()) {
                $result = $aiService->generateSocialCaption($topic, $options);
            }

            if (empty($result) || empty($result['caption'])) {
                $result = $this->generateFallbackCaption($topic, $options);
            }

            if ($user && !empty($result)) {
                $tokens = (int) ($result['tokens_used'] ?? 800);
                $user->recordAiTokenUsage($tokens);
            }

            return response()->json([
                'success'    => true,
                'hook'       => $result['hook'] ?? '',
                'caption'    => $result['caption'] ?? '',
                'cta'        => $result['call_to_action'] ?? '',
                'hashtags'   => $result['hashtags'] ?? [],
                'variations' => $result['variations'] ?? [],
                'full_text'  => $result['full_text'] ?? '',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AI Caption Generation error: ' . $e->getMessage());
            $fallback = $this->generateFallbackCaption($topic, $options);

            return response()->json([
                'success'    => true,
                'hook'       => $fallback['hook'],
                'caption'    => $fallback['caption'],
                'cta'        => $fallback['call_to_action'],
                'hashtags'   => $fallback['hashtags'],
                'variations' => $fallback['variations'],
                'full_text'  => $fallback['full_text'],
            ]);
        }
    }

    /**
     * Rule-based intelligent fallback social caption and hashtags generator.
     */
    protected function generateFallbackCaption(string $topic, array $options = []): array
    {
        $platform = strtolower($options['platform'] ?? 'all');
        $tone = strtolower($options['tone'] ?? 'engaging');
        $cleanTopic = trim($topic);

        // Extract key words for hashtag generation
        $words = preg_split('/[\s,\.\?!;:\-]+/', strtolower($cleanTopic), -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = ['the', 'is', 'at', 'which', 'on', 'and', 'a', 'an', 'in', 'to', 'for', 'with', 'of', 'from', 'our', 'new', 'this', 'that', 'all', 'we', 'are', 'you', 'your'];
        $keywords = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopWords));
        $keywordTags = array_map(fn($w) => '#' . ucfirst($w), array_slice(array_unique($keywords), 0, 5));

        $defaultTags = ['#SocialManager', '#Updates', '#Growth', '#Trending', '#ContentCreator', '#Community'];
        $mergedHashtags = array_values(array_unique(array_merge($keywordTags, $defaultTags)));
        $hashtagsString = implode(' ', $mergedHashtags);

        $hook = "Excited to share this with everyone: {$cleanTopic}!";
        $caption = "{$hook}\n\nWe are always working to bring you the best experience, and this is another step forward. Whether you are following along or just getting started, we would love to have you with us on this journey.\n\nDrop a comment below with your thoughts or questions!";

        $variations = [
            [
                'title' => 'Engaging Story',
                'text'  => "{$hook}\n\nEvery milestone is an opportunity to connect and grow together. We couldn't have achieved this without our incredible community.\n\nWhat are your thoughts on this? Let us know below!",
            ],
            [
                'title' => 'Short & Punchy',
                'text'  => "{$cleanTopic}.\n\nBig things are happening. Stay tuned for what's coming next!",
            ],
            [
                'title' => 'Community Question',
                'text'  => "What do you think about {$cleanTopic}?\n\nWe'd love to hear your honest feedback. Join the conversation in the comments below!",
            ],
        ];

        return [
            'hook'           => $hook,
            'caption'        => $caption,
            'call_to_action' => 'Drop a comment below with your thoughts!',
            'hashtags'       => $mergedHashtags,
            'variations'     => $variations,
            'full_text'      => $caption . "\n\n" . $hashtagsString,
        ];
    }

    /**
     * Real-time client-side safety guard verification endpoint (Collision & Daily limit check)
     */
    public function checkSafetyGuard(Request $request): \Illuminate\Http\JsonResponse
    {
        $accountIds = $request->input('account_ids', []);
        if (!is_array($accountIds)) {
            $accountIds = $accountIds ? [(int) $accountIds] : [];
        }
        if ($request->filled('account_id')) {
            $accountIds[] = (int) $request->input('account_id');
        }
        $accountIds = array_values(array_unique(array_filter(array_map('intval', $accountIds))));

        $scheduledAtStr = $request->input('scheduled_at');
        $excludeId = $request->filled('exclude_id') ? (int) $request->input('exclude_id') : null;
        $content = $request->input('content', '');
        $firstComment = $request->input('first_comment', '');
        $title = $request->input('title', '');
        $description = $request->input('description', '');

        // 1. Check blocked domain
        $blockedDomain = \App\Services\PostSafetyGuard::checkBlockedDomains($content, $firstComment, $description, $title);

        // 2. Count hashtags
        $hashtagCount = \App\Services\PostSafetyGuard::countHashtags($content, $firstComment, $description);

        $warnings = [];
        $errors = [];

        if ($blockedDomain) {
            $errors[] = "Link shorteners/lockers like '{$blockedDomain}' are not allowed.";
        }

        if ($hashtagCount > \App\Services\PostSafetyGuard::MAX_HASHTAGS) {
            $errors[] = "Maximum " . \App\Services\PostSafetyGuard::MAX_HASHTAGS . " hashtags allowed ({$hashtagCount} detected).";
        }

        if (!empty($scheduledAtStr) && !empty($accountIds)) {
            try {
                $scheduledAt = Carbon::parse($scheduledAtStr);
                $accounts = Account::whereIn('id', $accountIds)->get();

                foreach ($accounts as $account) {
                    // Daily limit check
                    $dailyCount = \App\Services\PostSafetyGuard::checkDailyLimit($account->id, $scheduledAt, $excludeId);
                    if ($dailyCount !== null) {
                        $errors[] = "Daily limit reached for '{$account->name}': 20 Posts Allowed per day ({$dailyCount} posts on this day).";
                    }

                    // Collision check
                    $collision = \App\Services\PostSafetyGuard::checkCollision($account->id, $scheduledAt, $excludeId);
                    if ($collision) {
                        $timeStr = $collision->scheduled_at->format('h:i A');
                        $warnings[] = "There is already a post scheduled at {$timeStr} for '{$account->name}'. Please keep at least a 10-minute gap.";
                    }
                }
            } catch (\Throwable $e) {
                // Invalid date parse
            }
        }

        return response()->json([
            'valid'         => empty($errors),
            'has_collision' => !empty($warnings),
            'errors'        => $errors,
            'warnings'      => $warnings,
            'hashtag_count' => $hashtagCount,
            'max_hashtags'  => \App\Services\PostSafetyGuard::MAX_HASHTAGS,
            'blocked_domain'=> $blockedDomain,
        ]);
    }
}
