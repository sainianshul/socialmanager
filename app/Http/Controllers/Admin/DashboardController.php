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

        if (!$user->isAdmin()) {
            // Scope all metrics and feeds to authenticated user's own accounts
            $userAccountIds = Account::where('user_id', $user->id)->pluck('id')->toArray();

            $channelsCount = count($userAccountIds);
            $totalPosts = Post::whereIn('account_id', $userAccountIds)->count();
            $totalComments = Comment::whereIn('account_id', $userAccountIds)->count();
            $scheduledCount = ScheduledPost::where('user_id', $user->id)->where('status', ScheduledPost::STATUS_SCHEDULED)->count();
            $totalReach = Account::where('user_id', $user->id)->sum('followers_count') ?: 0;
            $commentsToday = Comment::whereIn('account_id', $userAccountIds)->whereDate('created_at', Carbon::today())->count();
            $repliedCount = Comment::whereIn('account_id', $userAccountIds)->where('status', Comment::STATUS_REPLIED)->count();
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
        } else {
            // Admin sees global system overview across all connected accounts
            $channelsCount = Account::count();
            $totalPosts = Post::count();
            $totalComments = Comment::count();
            $scheduledCount = ScheduledPost::where('status', ScheduledPost::STATUS_SCHEDULED)->count();
            $totalReach = Account::sum('followers_count') ?: 0;
            $commentsToday = Comment::whereDate('created_at', Carbon::today())->count();
            $repliedCount = Comment::where('status', Comment::STATUS_REPLIED)->count();
            $newPostsMonth = Post::where('published_at', '>=', Carbon::now()->subDays(30))->count();

            $accounts = Account::withCount(['posts', 'comments'])->get();
            $recentPosts = Post::with('account')->latest('published_at')->take(6)->get();
            $recentComments = Comment::with(['post', 'account'])->latest('commented_at')->take(5)->get();

            $postsGrouped = Post::whereBetween('published_at', [$startDate, $endDate])
                ->selectRaw('DATE(published_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $commentsGrouped = Comment::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');
        }

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
            'totalReach',
            'commentsToday',
            'repliedCount',
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
            ]);
        }

        return response()->json([
            'total_users' => User::users()->count(),
            'channels_count' => Account::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
        ]);
    }
}
