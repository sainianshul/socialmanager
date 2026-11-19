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

        return view('admin.posts.create', compact('accounts', 'selectedAccountId', 'selectedPlatform', 'prefillDate'));
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

            return [
                'id'              => (string) $post->id,
                'title'           => $title,
                'start'           => $post->scheduled_at->toIso8601String(),
                'backgroundColor' => $platformColor,
                'borderColor'     => $platformColor,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'account_name'    => $account->name,
                    'account_type'    => $account->account_type_name,
                    'platform'        => $platformType,
                    'platform_name'   => $post->platform_name,
                    'platform_icon'   => $account->platform_icon,
                    'platform_badge'  => $post->platform_badge,
                    'post_type'       => $post->post_type,
                    'post_type_badge' => $post->post_type_badge,
                    'status'          => $post->status,
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
        $accountId = $request->input('account_id');
        if (!$accountId) {
            return redirect()->back()->with('error', 'Please select a social channel account.')->withInput();
        }

        $user = auth()->user();
        $account = Account::findOrFail($accountId);

        if (!$user->isAdmin() && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized channel selection.');
        }

        // Determine platform: from input, or fallback to account's native platform
        $platform = (int) ($request->input('platform') ?: $account->account_type);

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

        if ($platform === Account::TYPE_YOUTUBE) {
            $validated = $request->validate([
                'account_id'     => 'required|exists:accounts,id',
                'title'          => 'required|string|max:100',
                'description'    => 'nullable|string|max:5000',
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
            }

            if ($request->filled('tags')) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $request->input('tags')))));
            }

            $title = $validated['title'];
            $description = $validated['description'] ?? null;
            $content = $description ?: $title;
            $postType = ScheduledPost::TYPE_VIDEO;
            $privacyStatus = $validated['privacy_status'];
        } elseif ($platform === Account::TYPE_INSTAGRAM) {
            $validated = $request->validate([
                'account_id'    => 'required|exists:accounts,id',
                'post_type'     => 'required|in:photo,video,reel,carousel',
                'content'       => 'nullable|string|max:2200',
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            if ($publishMode !== 'draft' && !$request->hasFile('media_files')) {
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
            // Facebook
            $validated = $request->validate([
                'account_id'    => 'required|exists:accounts,id',
                'post_type'     => 'required|in:text,photo,video,reel,carousel',
                'content'       => 'nullable|string|max:5000',
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
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            $postType = $validated['post_type'];
            $content = $validated['content'] ?? '';
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
        } elseif ($publishMode === 'draft') {
            $status = ScheduledPost::STATUS_DRAFT;
        }

        $scheduledPost = ScheduledPost::create([
            'user_id'        => $user->id,
            'account_id'     => $account->id,
            'platform'       => $platform,
            'post_type'      => $postType,
            'title'          => $title,
            'description'    => $description,
            'content'        => $content,
            'media_urls'     => $mediaUrls,
            'thumbnail_path' => $thumbnailPath,
            'tags'           => $tags,
            'privacy_status' => $privacyStatus,
            'scheduled_at'   => $scheduledAt,
            'status'         => $status,
        ]);

        // If Publish Now chosen, dispatch immediately
        if ($publishMode === 'now') {
            if ($platform === Account::TYPE_YOUTUBE) {
                $result = $youtubePublishingService->publish($scheduledPost);
            } else {
                $result = $publishingService->publish($scheduledPost);
            }

            if ($result['success']) {
                return redirect()->route('admin.posts.schedule')
                    ->with('success', 'Post published live to ' . $account->name . ' successfully!');
            } else {
                return redirect()->route('admin.posts.schedule')
                    ->with('warning', 'Post saved, but publishing service returned an error: ' . $result['message']);
            }
        }

        $msg = $status === ScheduledPost::STATUS_DRAFT
            ? 'Post saved as draft.'
            : 'Post successfully scheduled for ' . $scheduledAt->format('d M Y, h:i A');

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
                'video_file'     => 'nullable|file|mimes:mp4,mov,webm,avi,mkv|max:102400',
                'thumbnail'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
                'tags'           => 'nullable|string|max:500',
                'privacy_status' => 'required|in:public,unlisted,private',
                'publish_mode'   => 'required|in:now,schedule,draft',
                'scheduled_at'   => 'nullable|date',
            ]);

            // If user uploads a new video file, delete old video and save new
            if ($request->hasFile('video_file')) {
                if (!empty($mediaUrls)) {
                    foreach ($mediaUrls as $oldFile) {
                        if (Storage::disk('public')->exists($oldFile)) {
                            Storage::disk('public')->delete($oldFile);
                        }
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

            // If user uploads a new thumbnail, delete old thumbnail and save new
            if ($request->hasFile('thumbnail')) {
                if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $thumb = $request->file('thumbnail');
                $thumbName = time() . '_thumb_' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();
                $thumbnailPath = $thumb->storeAs('uploads/thumbnails', $thumbName, 'public');
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
                'media_files'   => 'nullable|array',
                'media_files.*' => 'file|mimes:jpeg,jpg,png,webp,mp4,mov|max:51200',
                'publish_mode'  => 'required|in:now,schedule,draft',
                'scheduled_at'  => 'nullable|date',
            ]);

            // If new media files uploaded, replace old ones
            if ($request->hasFile('media_files')) {
                if (!empty($mediaUrls)) {
                    foreach ($mediaUrls as $oldFile) {
                        if (Storage::disk('public')->exists($oldFile)) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                }
                $mediaUrls = [];
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            } elseif ($publishMode !== 'draft' && empty($mediaUrls)) {
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
                        if (Storage::disk('public')->exists($oldFile)) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                }
                $mediaUrls = [];
                foreach ($request->file('media_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/scheduled_posts', $filename, 'public');
                    $mediaUrls[] = $path;
                }
            }

            $scheduledPost->post_type = $validated['post_type'];
            $scheduledPost->content = $validated['content'] ?? '';
            $scheduledPost->media_urls = $mediaUrls;
        }

        // Determine schedule time & status
        if ($publishMode === 'schedule') {
            if (empty($validated['scheduled_at'])) {
                return redirect()->back()->with('error', 'Please choose a valid date & time for scheduling.')->withInput();
            }
            $scheduledPost->scheduled_at = Carbon::parse($validated['scheduled_at']);
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
            return redirect()->back()->with('success', 'Post published live successfully!');
        }

        return redirect()->back()->with('error', 'Failed to publish post: ' . $result['message']);
    }

    /**
     * Delete or cancel a scheduled post
     */
    public function destroy(ScheduledPost $scheduledPost)
    {
        $this->authorizePost($scheduledPost);

        // Remove media files from storage if present
        if (is_array($scheduledPost->media_urls)) {
            foreach ($scheduledPost->media_urls as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        // Remove thumbnail if present
        if ($scheduledPost->thumbnail_path && Storage::disk('public')->exists($scheduledPost->thumbnail_path)) {
            Storage::disk('public')->delete($scheduledPost->thumbnail_path);
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
}
