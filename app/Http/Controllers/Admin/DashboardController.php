<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ScheduledPost;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        if ($user->isAdmin()) {
            // ── Super Admin System Overview Metrics ───────────────────
            $totalUsers = User::where('role', '!=', User::ROLE_ADMIN)->count();
            $newUsersThisMonth = User::where('role', '!=', User::ROLE_ADMIN)->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
            $activeUsersCount = User::where('role', '!=', User::ROLE_ADMIN)->where('status', User::STATUS_ACTIVE)->count();
            $blockedUsersCount = User::where('role', '!=', User::ROLE_ADMIN)->where('status', User::STATUS_BLOCKED)->count();

            $totalAccounts = Account::count();
            $fbAccountsCount = Account::where('account_type', Account::TYPE_FACEBOOK)->count();
            $igAccountsCount = Account::where('account_type', Account::TYPE_INSTAGRAM)->count();
            $ytAccountsCount = Account::where('account_type', Account::TYPE_YOUTUBE)->count();

            $totalPosts = Post::count();
            $postsThisMonth = Post::where('published_at', '>=', Carbon::now()->startOfMonth())->count();

            $totalComments = Comment::count();
            $commentsThisMonth = Comment::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
            $commentsToday = Comment::whereDate('created_at', Carbon::today())->count();

            $totalAiReplies = Comment::where(function ($q) {
                $q->whereNotNull('ai_reply')->orWhere('status', Comment::STATUS_REPLIED);
            })->count();

            $aiRepliesThisMonth = Comment::where(function ($q) {
                $q->whereNotNull('ai_reply')->orWhere('status', Comment::STATUS_REPLIED);
            })->where('created_at', '>=', Carbon::now()->startOfMonth())->count();

            $aiRepliesToday = Comment::where(function ($q) {
                $q->whereNotNull('ai_reply')->orWhere('status', Comment::STATUS_REPLIED);
            })->whereDate('created_at', Carbon::today())->count();

            $hiddenTrollsCount = Comment::where(function ($q) {
                $q->where('is_abusive', true)->orWhere('status', Comment::STATUS_HIDDEN);
            })->count();

            $grievancesCount = Comment::where('is_complaint', true)->count();
            $scheduledCount = ScheduledPost::where('status', ScheduledPost::STATUS_SCHEDULED)->count();
            $publishedScheduledCount = ScheduledPost::where('status', ScheduledPost::STATUS_PUBLISHED)->count();

            // Recent registered users with accounts count
            $recentUsers = User::where('role', '!=', User::ROLE_ADMIN)->withCount('accounts')->latest()->take(6)->get();

            // Recent system-wide comments stream
            $recentComments = Comment::with(['post', 'account.user'])->latest('created_at')->take(6)->get();

            // 30 Days Activity Chart Data for Super Admin
            $commentsGrouped = Comment::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $aiRepliesGrouped = Comment::where(function ($q) {
                $q->whereNotNull('ai_reply')->orWhere('status', Comment::STATUS_REPLIED);
            })->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $usersGrouped = User::where('role', '!=', User::ROLE_ADMIN)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $chartDates = [];
            $commentCounts = [];
            $aiReplyCounts = [];
            $userCounts = [];

            for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
                $dStr = $date->format('Y-m-d');
                $chartDates[] = $date->format('d M');
                $commentCounts[] = (int) ($commentsGrouped[$dStr] ?? 0);
                $aiReplyCounts[] = (int) ($aiRepliesGrouped[$dStr] ?? 0);
                $userCounts[] = (int) ($usersGrouped[$dStr] ?? 0);
            }

            return view('admin.dashboard', compact(
                'totalUsers',
                'newUsersThisMonth',
                'activeUsersCount',
                'blockedUsersCount',
                'totalAccounts',
                'fbAccountsCount',
                'igAccountsCount',
                'ytAccountsCount',
                'totalPosts',
                'postsThisMonth',
                'totalComments',
                'commentsThisMonth',
                'commentsToday',
                'totalAiReplies',
                'aiRepliesThisMonth',
                'aiRepliesToday',
                'hiddenTrollsCount',
                'grievancesCount',
                'scheduledCount',
                'publishedScheduledCount',
                'recentUsers',
                'recentComments',
                'chartDates',
                'commentCounts',
                'aiReplyCounts',
                'userCounts'
            ));
        }

        // Scope all metrics and feeds to authenticated user's own accounts
        $userAccountIds = Account::where('user_id', $user->id)->pluck('id')->toArray();

        $channelsCount = count($userAccountIds);
        $totalPosts = Post::whereIn('account_id', $userAccountIds)->count();
        $totalComments = Comment::whereIn('account_id', $userAccountIds)->count();
        $scheduledCount = ScheduledPost::where('user_id', $user->id)->where('status', ScheduledPost::STATUS_SCHEDULED)->count();
        $publishedScheduledCount = ScheduledPost::where('user_id', $user->id)->where('status', ScheduledPost::STATUS_PUBLISHED)->count();
        $commentsToday = Comment::whereIn('account_id', $userAccountIds)->whereDate('created_at', Carbon::today())->count();
        $repliedCount = Comment::whereIn('account_id', $userAccountIds)->where('status', Comment::STATUS_REPLIED)->count();
        $pendingReviewCount = Comment::whereIn('account_id', $userAccountIds)->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count();
        $hiddenCount = Comment::whereIn('account_id', $userAccountIds)->where('status', Comment::STATUS_HIDDEN)->count();
        $newPostsMonth = Post::whereIn('account_id', $userAccountIds)->where('published_at', '>=', Carbon::now()->subDays(30))->count();

        $accounts = Account::where('user_id', $user->id)->withCount(['posts', 'comments'])->get();
        $recentPosts = Post::whereIn('account_id', $userAccountIds)->with('account')->latest('published_at')->take(6)->get();
        $recentComments = Comment::whereIn('account_id', $userAccountIds)->with(['post', 'account'])->latest('commented_at')->take(5)->get();

        $postsGrouped = Post::whereIn('account_id', $userAccountIds)
            ->whereBetween('published_at', [$startDate, $endDate])
            ->selectRaw('DATE(published_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $commentsGrouped = Comment::whereIn('account_id', $userAccountIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // 30 Days Activity Chart Data
        $chartDates = [];
        $postCounts = [];
        $commentCounts = [];

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $dStr = $date->format('Y-m-d');
            $chartDates[] = $date->format('d M');
            $postCounts[] = (int) ($postsGrouped[$dStr] ?? 0);
            $commentCounts[] = (int) ($commentsGrouped[$dStr] ?? 0);
        }

        return view('admin.dashboard', compact(
            'channelsCount',
            'totalPosts',
            'totalComments',
            'scheduledCount',
            'publishedScheduledCount',
            'commentsToday',
            'repliedCount',
            'pendingReviewCount',
            'hiddenCount',
            'newPostsMonth',
            'accounts',
            'recentPosts',
            'recentComments',
            'chartDates',
            'postCounts',
            'commentCounts'
        ));
    }

    public function stats()
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            $userAccountIds = Account::where('user_id', $user->id)->pluck('id')->toArray();
            return response()->json([
                'channels_count' => count($userAccountIds),
                'total_posts' => Post::whereIn('account_id', $userAccountIds)->count(),
                'total_comments' => Comment::whereIn('account_id', $userAccountIds)->count(),
                'scheduled_count' => ScheduledPost::where('user_id', $user->id)->where('status', ScheduledPost::STATUS_SCHEDULED)->count(),
                'published_scheduled_count' => ScheduledPost::where('user_id', $user->id)->where('status', ScheduledPost::STATUS_PUBLISHED)->count(),
                'replied_count' => Comment::whereIn('account_id', $userAccountIds)->where('status', Comment::STATUS_REPLIED)->count(),
                'pending_review_count' => Comment::whereIn('account_id', $userAccountIds)->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count(),
                'hidden_count' => Comment::whereIn('account_id', $userAccountIds)->where('status', Comment::STATUS_HIDDEN)->count(),
            ]);
        }

        return response()->json([
            'total_users' => User::users()->count(),
            'channels_count' => Account::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'scheduled_count' => ScheduledPost::where('status', ScheduledPost::STATUS_SCHEDULED)->count(),
            'published_scheduled_count' => ScheduledPost::where('status', ScheduledPost::STATUS_PUBLISHED)->count(),
            'replied_count' => Comment::where('status', Comment::STATUS_REPLIED)->count(),
            'pending_review_count' => Comment::whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW])->count(),
            'hidden_count' => Comment::where('status', Comment::STATUS_HIDDEN)->count(),
        ]);
    }
}
