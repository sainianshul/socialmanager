<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Post;
use App\Services\MetaPostSyncService;
use App\Services\YouTubeVideoSyncService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a paginated visual gallery & table of synced social posts
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Get accounts accessible to current user
        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // 2. Filter states
        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        $search = $request->get('q');
        $selectedDate = $request->get('date');
        $viewMode = $request->get('view', 'gallery'); // 'gallery' or 'table'

        // Validate selected account belongs to user (unless admin)
        if ($selectedAccountId && !$user->isAdmin() && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        // 3. Posts Query
        $postsQuery = Post::with('account');

        if ($selectedAccountId) {
            $postsQuery->where('account_id', $selectedAccountId);
        } else {
            $postsQuery->whereIn('account_id', $accountIds);
        }

        if (!empty($search)) {
            $postsQuery->where('message', 'like', "%{$search}%");
        }

        if ($request->filled('date')) {
            $postsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $posts = $postsQuery->orderBy('published_at', 'desc')->paginate(12)->withQueryString();

        // 4. Quick aggregate metrics for header counters
        $statsQuery = Post::query();
        if ($selectedAccountId) {
            $statsQuery->where('account_id', $selectedAccountId);
        } else {
            $statsQuery->whereIn('account_id', $accountIds);
        }

        if ($request->filled('date')) {
            $statsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $totalPosts = (clone $statsQuery)->count();
        $totalLikes = (clone $statsQuery)->sum('likes_count');
        $totalComments = (clone $statsQuery)->sum('comments_count');

        $selectedAccount = $selectedAccountId ? $accounts->firstWhere('id', $selectedAccountId) : null;

        return view('admin.posts.index', compact(
            'posts',
            'accounts',
            'selectedAccountId',
            'selectedAccount',
            'selectedDate',
            'search',
            'viewMode',
            'totalPosts',
            'totalLikes',
            'totalComments'
        ));
    }

    /**
     * Dedicated Instagram Publications Stream & Visual Gallery
     */
    public function instagram(Request $request)
    {
        $user = auth()->user();

        // 1. Get Instagram accounts accessible to current user
        $accountsQuery = Account::query()->instagram();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // 2. Filter states
        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        $search = $request->get('q');
        $selectedDate = $request->get('date');
        $viewMode = $request->get('view', 'gallery');

        if ($selectedAccountId && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        // 3. Instagram Posts Query
        $postsQuery = Post::query()->instagram()->with('account');

        if ($selectedAccountId) {
            $postsQuery->where('account_id', $selectedAccountId);
        } else {
            $postsQuery->whereIn('account_id', $accountIds);
        }

        if (!empty($search)) {
            $postsQuery->where('message', 'like', "%{$search}%");
        }

        if ($request->filled('date')) {
            $postsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $posts = $postsQuery->orderBy('published_at', 'desc')->paginate(12)->withQueryString();

        // 4. Quick aggregate metrics
        $statsQuery = Post::query()->instagram();
        if ($selectedAccountId) {
            $statsQuery->where('account_id', $selectedAccountId);
        } else {
            $statsQuery->whereIn('account_id', $accountIds);
        }

        if ($request->filled('date')) {
            $statsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $totalPosts = (clone $statsQuery)->count();
        $totalLikes = (clone $statsQuery)->sum('likes_count');
        $totalComments = (clone $statsQuery)->sum('comments_count');

        $selectedAccount = $selectedAccountId ? $accounts->firstWhere('id', $selectedAccountId) : null;

        return view('admin.posts.instagram', compact(
            'posts',
            'accounts',
            'selectedAccountId',
            'selectedAccount',
            'selectedDate',
            'search',
            'viewMode',
            'totalPosts',
            'totalLikes',
            'totalComments'
        ));
    }

    /**
     * Dedicated Facebook Publications Stream & Feed
     */
    public function facebook(Request $request)
    {
        $user = auth()->user();

        // 1. Get Facebook accounts accessible to current user
        $accountsQuery = Account::query()->facebook();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // 2. Filter states
        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        $search = $request->get('q');
        $selectedDate = $request->get('date');
        $viewMode = $request->get('view', 'gallery');

        if ($selectedAccountId && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        // 3. Facebook Posts Query
        $postsQuery = Post::query()->facebook()->with('account');

        if ($selectedAccountId) {
            $postsQuery->where('account_id', $selectedAccountId);
        } else {
            $postsQuery->whereIn('account_id', $accountIds);
        }

        if (!empty($search)) {
            $postsQuery->where('message', 'like', "%{$search}%");
        }

        if ($request->filled('date')) {
            $postsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $posts = $postsQuery->orderBy('published_at', 'desc')->paginate(12)->withQueryString();

        // 4. Quick aggregate metrics
        $statsQuery = Post::query()->facebook();
        if ($selectedAccountId) {
            $statsQuery->where('account_id', $selectedAccountId);
        } else {
            $statsQuery->whereIn('account_id', $accountIds);
        }

        if ($request->filled('date')) {
            $statsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $totalPosts = (clone $statsQuery)->count();
        $totalLikes = (clone $statsQuery)->sum('likes_count');
        $totalComments = (clone $statsQuery)->sum('comments_count');
        $totalShares = (clone $statsQuery)->sum('shares_count');

        $selectedAccount = $selectedAccountId ? $accounts->firstWhere('id', $selectedAccountId) : null;

        return view('admin.posts.facebook', compact(
            'posts',
            'accounts',
            'selectedAccountId',
            'selectedAccount',
            'selectedDate',
            'search',
            'viewMode',
            'totalPosts',
            'totalLikes',
            'totalComments',
            'totalShares'
        ));
    }

    /**
     * Dedicated YouTube Videos Stream & Content Manager
     */
    public function youtube(Request $request)
    {
        $user = auth()->user();

        // 1. Get YouTube channel accounts accessible to current user
        $accountsQuery = Account::query()->youtube();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();
        $accountIds = $accounts->pluck('id')->toArray();

        // 2. Filter states
        $selectedAccountId = $request->filled('account_id') ? (int) $request->get('account_id') : null;
        $search = $request->get('q');
        $selectedDate = $request->get('date');
        $viewMode = $request->get('view', 'gallery');

        if ($selectedAccountId && !in_array($selectedAccountId, $accountIds)) {
            $selectedAccountId = null;
        }

        // 3. YouTube Posts Query
        $postsQuery = Post::query()->youtube()->with('account');

        if ($selectedAccountId) {
            $postsQuery->where('account_id', $selectedAccountId);
        } else {
            $postsQuery->whereIn('account_id', $accountIds);
        }

        if (!empty($search)) {
            $postsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $postsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $posts = $postsQuery->orderBy('published_at', 'desc')->paginate(12)->withQueryString();

        // 4. Quick aggregate metrics
        $statsQuery = Post::query()->youtube();
        if ($selectedAccountId) {
            $statsQuery->where('account_id', $selectedAccountId);
        } else {
            $statsQuery->whereIn('account_id', $accountIds);
        }

        if ($request->filled('date')) {
            $statsQuery->where(function ($q) use ($selectedDate) {
                $q->whereDate('published_at', $selectedDate)
                  ->orWhere(function ($sub) use ($selectedDate) {
                      $sub->whereNull('published_at')->whereDate('created_at', $selectedDate);
                  });
            });
        }

        $totalPosts = (clone $statsQuery)->count();
        $totalViews = (clone $statsQuery)->sum('views_count');
        $totalLikes = (clone $statsQuery)->sum('likes_count');
        $totalComments = (clone $statsQuery)->sum('comments_count');

        $selectedAccount = $selectedAccountId ? $accounts->firstWhere('id', $selectedAccountId) : null;

        return view('admin.posts.youtube', compact(
            'posts',
            'accounts',
            'selectedAccountId',
            'selectedAccount',
            'selectedDate',
            'search',
            'viewMode',
            'totalPosts',
            'totalViews',
            'totalLikes',
            'totalComments'
        ));
    }

    /**
     * Trigger on-demand sync of posts from Meta Graph API or YouTube Data API
     */
    public function sync(Request $request, MetaPostSyncService $syncService, YouTubeVideoSyncService $ytSyncService)
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
            return redirect()->back()->with('warning', 'No active channels found to synchronize.');
        }

        $totalSynced = 0;
        $errorMessages = [];

        foreach ($accountsToSync as $account) {
            if ($account->account_type == Account::TYPE_YOUTUBE) {
                $result = $ytSyncService->syncChannelVideos($account, 25);
            } else {
                $result = $syncService->syncAccountPosts($account, 25);
            }

            if ($result['success']) {
                $totalSynced += $result['synced_count'];
                \App\Services\ActivityLogger::log(
                    \App\Models\SystemActivityLog::EVENT_ACCOUNT_SYNC,
                    "Synced {$result['synced_count']} posts from {$account->name}",
                    \App\Models\SystemActivityLog::STATUS_SUCCESS,
                    ['account_id' => $account->id, 'type' => $account->account_type],
                    $result,
                    null,
                    $account->id,
                    $account->user_id
                );
            } else {
                $errorMessages[] = "{$account->name}: {$result['message']}";
                \App\Services\ActivityLogger::log(
                    \App\Models\SystemActivityLog::EVENT_ACCOUNT_SYNC,
                    "Failed syncing posts from {$account->name}",
                    \App\Models\SystemActivityLog::STATUS_FAILED,
                    ['account_id' => $account->id],
                    $result['message'],
                    null,
                    $account->id,
                    $account->user_id
                );
            }
        }

        if (!empty($errorMessages) && $totalSynced === 0) {
            return redirect()->back()->with('error', 'Sync failed: ' . implode(' | ', $errorMessages));
        }

        $msg = "Synchronized {$totalSynced} posts/videos across " . $accountsToSync->count() . " channel(s).";
        if (!empty($errorMessages)) {
            $msg .= ' (Some channels had notices: ' . implode('; ', $errorMessages) . ')';
        }

        return redirect()->back()->with('success', $msg);
    }
}

