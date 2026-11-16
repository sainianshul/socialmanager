<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Comments\CommentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Services\MetaCommentSyncService;
use App\Services\YouTubeCommentSyncService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Dedicated Instagram Comments & Auto-Moderation Page
     */
    public function instagram(Request $request, CommentDataTable $dataTable)
    {
        $user = auth()->user();

        $accountsQuery = Account::query()->instagram();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // Calculate summary metrics for Instagram comments
        $baseCommentsQuery = Comment::whereIn('account_id', $accountIds);
        $totalComments = (clone $baseCommentsQuery)->count();
        $pendingReview = (clone $baseCommentsQuery)->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count();
        $repliedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_REPLIED)->count();
        $hiddenCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_HIDDEN)->count();
        $flaggedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_FLAGGED)->count();

        // Fetch recent Instagram posts for the manual composer
        $posts = Post::whereIn('account_id', $accountIds)
            ->with('account')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $statusList = Comment::getStatusList();

        return view('admin.comments.instagram', compact(
            'accounts',
            'statusList',
            'posts',
            'totalComments',
            'pendingReview',
            'repliedCount',
            'hiddenCount',
            'flaggedCount'
        ));
    }

    /**
     * Data endpoint for server-side Yajra DataTable scoped to Instagram
     */
    public function instagramData(CommentDataTable $dataTable)
    {
        request()->merge(['account_type' => Account::TYPE_INSTAGRAM]);
        return $dataTable->ajax();
    }

    /**
     * Dedicated Facebook Comments & Auto-Moderation Page
     */
    public function facebook(Request $request, CommentDataTable $dataTable)
    {
        $user = auth()->user();

        $accountsQuery = Account::query()->facebook();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // Calculate summary metrics for Facebook comments
        $baseCommentsQuery = Comment::whereIn('account_id', $accountIds);
        $totalComments = (clone $baseCommentsQuery)->count();
        $pendingReview = (clone $baseCommentsQuery)->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count();
        $repliedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_REPLIED)->count();
        $hiddenCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_HIDDEN)->count();
        $flaggedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_FLAGGED)->count();

        // Fetch recent Facebook posts for the manual composer
        $posts = Post::whereIn('account_id', $accountIds)
            ->with('account')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $statusList = Comment::getStatusList();

        return view('admin.comments.facebook', compact(
            'accounts',
            'statusList',
            'posts',
            'totalComments',
            'pendingReview',
            'repliedCount',
            'hiddenCount',
            'flaggedCount'
        ));
    }

    /**
     * Data endpoint for server-side Yajra DataTable scoped to Facebook
     */
    public function facebookData(CommentDataTable $dataTable)
    {
        request()->merge(['account_type' => Account::TYPE_FACEBOOK]);
        return $dataTable->ajax();
    }

    /**
     * Dedicated YouTube Comments & Auto-Moderation Page
     */
    public function youtube(Request $request, CommentDataTable $dataTable)
    {
        $user = auth()->user();

        $accountsQuery = Account::query()->youtube();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // Calculate summary metrics for YouTube comments
        $baseCommentsQuery = Comment::whereIn('account_id', $accountIds);
        $totalComments = (clone $baseCommentsQuery)->count();
        $pendingReview = (clone $baseCommentsQuery)->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count();
        $repliedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_REPLIED)->count();
        $hiddenCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_HIDDEN)->count();
        $flaggedCount = (clone $baseCommentsQuery)->where('status', Comment::STATUS_FLAGGED)->count();

        // Fetch recent YouTube videos for the manual composer
        $posts = Post::whereIn('account_id', $accountIds)
            ->with('account')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $statusList = Comment::getStatusList();

        return view('admin.comments.youtube', compact(
            'accounts',
            'statusList',
            'posts',
            'totalComments',
            'pendingReview',
            'repliedCount',
            'hiddenCount',
            'flaggedCount'
        ));
    }

    /**
     * Data endpoint for server-side Yajra DataTable scoped to YouTube
     */
    public function youtubeData(CommentDataTable $dataTable)
    {
        request()->merge(['account_type' => Account::TYPE_YOUTUBE]);
        return $dataTable->ajax();
    }

    /**
     * Display comments using server-side Yajra DataTable
     */
    public function index(CommentDataTable $dataTable)
    {
        $user = auth()->user();

        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();
        $statusList = Comment::getStatusList();

        // Fetch recent posts for the manual comment composer
        $posts = Post::whereIn('account_id', $accountIds)
            ->with('account')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        return $dataTable->render('admin.comments.index', compact(
            'accounts',
            'statusList',
            'posts'
        ));
    }

    /**
     * Post a manual top-level comment on a Post (Published to Meta or YouTube)
     */
    public function storeManual(Request $request, MetaCommentSyncService $syncService, YouTubeCommentSyncService $ytSyncService)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'message' => 'required|string|max:2000',
        ]);

        $post = Post::with('account')->findOrFail($validated['post_id']);
        $user = auth()->user();

        if (!$user->isAdmin() && $post->account->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this post.');
        }

        if ($post->account?->isYouTube()) {
            $result = $ytSyncService->publishCommentOnVideo($post, $post->account, $validated['message']);
            $platformName = 'YouTube';
        } else {
            $result = $syncService->publishCommentOnPost($post, $post->account, $validated['message']);
            $platformName = $post->account?->isInstagram() ? 'Instagram' : 'Facebook';
        }

        if (!$result['success']) {
            return redirect()->back()->with('error', "Could not post to {$platformName}: " . $result['message']);
        }

        return redirect()->back()->with('success', "Manual comment successfully published on {$platformName}.");
    }

    /**
     * Reply directly to a constituent comment (Published to Meta or YouTube)
     */
    public function reply(Request $request, Comment $comment, MetaCommentSyncService $syncService, YouTubeCommentSyncService $ytSyncService)
    {
        $validated = $request->validate([
            'reply_message' => 'required|string|max:2000',
        ]);

        $this->authorizeComment($comment);

        if ($comment->account?->isYouTube()) {
            $result = $ytSyncService->replyToComment($comment, $validated['reply_message']);
            $platformName = 'YouTube';
        } else {
            $result = $syncService->replyToComment($comment, $validated['reply_message']);
            $platformName = $comment->account?->isInstagram() ? 'Instagram' : 'Facebook';
        }

        if (!$result['success']) {
            return redirect()->back()->with('error', "Could not post reply to {$platformName}: " . $result['message']);
        }

        return redirect()->back()->with('success', "Reply successfully published on {$platformName}.");
    }

    /**
     * Data endpoint for server-side Yajra DataTable
     */
    public function data(CommentDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    /**
     * Trigger manual sync of comments from Meta Graph API or YouTube Data API
     */
    public function sync(Request $request, MetaCommentSyncService $syncService, YouTubeCommentSyncService $ytSyncService)
    {
        $user = auth()->user();
        $selectedAccountId = $request->input('account_id');

        $accountsQuery = Account::where('is_active', true);
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }

        if (!empty($selectedAccountId)) {
            $accountsQuery->where('id', $selectedAccountId);
        }

        $accountsToSync = $accountsQuery->get();

        if ($accountsToSync->isEmpty()) {
            return redirect()->back()->with('warning', 'No active channels found to synchronize comments.');
        }

        $totalSynced = 0;
        $errorMessages = [];

        foreach ($accountsToSync as $account) {
            if ($account->account_type == Account::TYPE_YOUTUBE) {
                $result = $ytSyncService->syncAccountComments($account, 10, 25);
            } else {
                $result = $syncService->syncAccountComments($account, 10, 25);
            }

            if ($result['success']) {
                $totalSynced += $result['synced_count'];
            } else {
                $errorMessages[] = "{$account->name}: {$result['message']}";
            }
        }

        if (!empty($errorMessages) && $totalSynced === 0) {
            return redirect()->back()->with('error', 'Comment sync notice: ' . implode(' | ', $errorMessages));
        }

        $msg = "Synchronized {$totalSynced} comment(s) across " . $accountsToSync->count() . " channel(s).";
        if (!empty($errorMessages)) {
            $msg .= ' (Some notices: ' . implode('; ', $errorMessages) . ')';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Approve and mark an AI draft reply as ready or replied
     */
    public function approve(Request $request, Comment $comment)
    {
        $this->authorizeComment($comment);

        $updateData = [
            'status' => Comment::STATUS_REPLIED,
            'replied_at' => now(),
        ];

        if ($request->filled('ai_reply')) {
            $updateData['ai_reply'] = trim($request->input('ai_reply'));
        }

        $comment->update($updateData);

        return redirect()->back()->with('success', 'Comment marked as reviewed & replied.');
    }

    /**
     * Toggle or set comment status to Hidden
     */
    public function toggleHide(Request $request, Comment $comment)
    {
        $this->authorizeComment($comment);

        if ($comment->status === Comment::STATUS_HIDDEN) {
            $comment->update([
                'status' => Comment::STATUS_PENDING_REVIEW,
                'hidden_at' => null,
            ]);
            $msg = 'Comment unhidden and returned to review.';
        } else {
            $comment->update([
                'status' => Comment::STATUS_HIDDEN,
                'hidden_at' => now(),
            ]);
            $msg = 'Comment marked as Hidden from public.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Authorize that the current user owns this comment's account
     */
    protected function authorizeComment(Comment $comment): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return;
        }

        if ($comment->account->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this comment.');
        }
    }
}

