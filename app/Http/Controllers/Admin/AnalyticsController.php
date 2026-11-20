<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the Political Analytics (Politics Analyses) Dashboard.
     * Pure SQL & Meta/YouTube aggregations — Zero AI/LLM cost or latency.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // ── 1. Scoped Accounts & Platform Filter ──────────────────────
        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }

        $allUserAccounts = (clone $accountsQuery)->get();
        $userAccountIds = $allUserAccounts->pluck('id')->toArray();

        $selectedPlatform = $request->input('platform', 'all');
        if ($selectedPlatform === 'facebook') {
            $accountsQuery->where('account_type', Account::TYPE_FACEBOOK);
        } elseif ($selectedPlatform === 'instagram') {
            $accountsQuery->where('account_type', Account::TYPE_INSTAGRAM);
        } elseif ($selectedPlatform === 'youtube') {
            $accountsQuery->where('account_type', Account::TYPE_YOUTUBE);
        }

        $scopedAccountIds = $accountsQuery->pluck('id')->toArray();

        // ── 2. Time Range Filter ──────────────────────────────────────
        $timeRange = $request->input('range', '30d');
        $customDateFrom = $request->input('date_from');
        $customDateTo = $request->input('date_to');

        $endDate = Carbon::now()->endOfDay();
        $startDate = match ($timeRange) {
            '7d'     => Carbon::now()->subDays(6)->startOfDay(),
            '90d'    => Carbon::now()->subDays(89)->startOfDay(),
            'custom' => !empty($customDateFrom) ? Carbon::parse($customDateFrom)->startOfDay() : Carbon::now()->subDays(29)->startOfDay(),
            'all'    => null,
            default  => Carbon::now()->subDays(29)->startOfDay(), // 30d default
        };

        if ($timeRange === 'custom' && !empty($customDateTo)) {
            $endDate = Carbon::parse($customDateTo)->endOfDay();
        }

        // ── 3. Base Query Builders ────────────────────────────────────
        $commentBase = Comment::whereIn('account_id', $scopedAccountIds);
        $postBase = Post::whereIn('account_id', $scopedAccountIds);

        if ($startDate) {
            $commentBase->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('commented_at', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('commented_at')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });

            $postBase->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('published_at', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('published_at')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });
        }

        // ── 4. Core Political & Sentiment Metrics ─────────────────────
        $totalComments = (clone $commentBase)->count();
        $favorCount = (clone $commentBase)->where('is_favor', true)->count();
        $againstCount = (clone $commentBase)->where('is_against', true)->count();
        $complaintCount = (clone $commentBase)->where('is_complaint', true)->count();
        $trollsCount = (clone $commentBase)->where('is_abusive', true)->count();

        $positiveSentimentCount = (clone $commentBase)->where('sentiment', 'positive')->count();
        $neutralSentimentCount  = (clone $commentBase)->where('sentiment', 'neutral')->count();
        $negativeSentimentCount = (clone $commentBase)->where('sentiment', 'negative')->count();

        $positivePercent = $totalComments > 0 ? round(($positiveSentimentCount / $totalComments) * 100, 1) : 0;
        $negativePercent = $totalComments > 0 ? round(($negativeSentimentCount / $totalComments) * 100, 1) : 0;
        $neutralPercent = $totalComments > 0 ? round(($neutralSentimentCount / $totalComments) * 100, 1) : 0;

        $repliedCount = (clone $commentBase)->where('status', Comment::STATUS_REPLIED)->count();
        $responseRate = $totalComments > 0 ? round(($repliedCount / $totalComments) * 100, 1) : 0;

        // Overall Favourability / Positive Sentiment Ratio
        $knownPoliticalPulse = $favorCount + $againstCount;
        $favorRatio = $totalComments > 0 
            ? round(($positiveSentimentCount / $totalComments) * 100, 1)
            : ($knownPoliticalPulse > 0 ? round(($favorCount / $knownPoliticalPulse) * 100, 1) : 0);

        // ── 5. Total Public Reach & Engagement on Posts ───────────────
        $totalPosts = (clone $postBase)->count();
        $totalLikes = (clone $postBase)->sum('likes_count') ?? 0;
        $totalPostComments = (clone $postBase)->sum('comments_count') ?? 0;
        $totalShares = (clone $postBase)->sum('shares_count') ?? 0;
        $totalViews = (clone $postBase)->sum('views_count') ?? 0;

        $totalAudience = $allUserAccounts->sum(function ($acc) {
            return $acc->isYouTube() ? (int) $acc->subscriber_count : (int) $acc->followers_count;
        });

        // ── 6. Daily Timeline Trend Chart (ApexCharts Data) ───────────
        $timelineStartDate = $startDate ?: Carbon::now()->subDays(29)->startOfDay();
        $timelineEndDate = $endDate ?: Carbon::now()->endOfDay();

        // Safe ANSI grouping for both SQLite and MySQL
        $dailyCommentsGrouped = (clone $commentBase)
            ->where(function ($q) use ($timelineStartDate, $timelineEndDate) {
                $q->whereBetween('commented_at', [$timelineStartDate, $timelineEndDate])
                  ->orWhere(function ($sub) use ($timelineStartDate, $timelineEndDate) {
                      $sub->whereNull('commented_at')->whereBetween('created_at', [$timelineStartDate, $timelineEndDate]);
                  });
            })
            ->selectRaw('
                DATE(COALESCE(commented_at, created_at)) as comment_date,
                SUM(CASE WHEN sentiment = "positive" OR is_favor = 1 THEN 1 ELSE 0 END) as positive_count,
                SUM(CASE WHEN sentiment = "negative" OR is_against = 1 THEN 1 ELSE 0 END) as negative_count,
                SUM(CASE WHEN sentiment = "neutral" AND is_favor = 0 AND is_against = 0 THEN 1 ELSE 0 END) as neutral_count,
                SUM(CASE WHEN is_complaint = 1 THEN 1 ELSE 0 END) as complaint_count
            ')
            ->groupBy('comment_date')
            ->get()
            ->keyBy('comment_date');

        $chartDates = [];
        $chartPositiveSeries = [];
        $chartNegativeSeries = [];
        $chartComplaintSeries = [];

        for ($d = clone $timelineStartDate; $d->lte($timelineEndDate); $d->addDay()) {
            $dStr = $d->format('Y-m-d');
            $chartDates[] = $d->format('d M');
            $row = $dailyCommentsGrouped->get($dStr);

            $chartPositiveSeries[] = (int) ($row?->positive_count ?? 0);
            $chartNegativeSeries[] = (int) ($row?->negative_count ?? 0);
            $chartComplaintSeries[] = (int) ($row?->complaint_count ?? 0);
        }

        // ── 7. Category Distribution ──────────────────────────────────
        $categoryBreakdown = [
            'Appreciation & Praise' => (clone $commentBase)->where('category', 'appreciation')->count(),
            'Questions & Inquiries' => (clone $commentBase)->whereIn('category', ['question', 'inquiry'])->count(),
            'Feedback & Suggestions' => (clone $commentBase)->whereIn('category', ['feedback', 'suggestion'])->count(),
            'Issues & Concerns' => (clone $commentBase)->where(function ($q) {
                $q->where('category', 'complaint')->orWhere('is_complaint', true);
            })->count(),
            'Spam & Flagged' => (clone $commentBase)->where(function ($q) {
                $q->whereIn('category', ['troll', 'spam', 'hate_speech'])->orWhere('is_abusive', true);
            })->count(),
        ];

        // ── 8. Platform Distribution Comparison ───────────────────────
        $platformBreakdown = [];
        foreach ([Account::TYPE_FACEBOOK => 'Facebook', Account::TYPE_INSTAGRAM => 'Instagram', Account::TYPE_YOUTUBE => 'YouTube'] as $type => $label) {
            $pAccIds = $allUserAccounts->where('account_type', $type)->pluck('id')->toArray();
            if (!empty($pAccIds)) {
                $pComments = Comment::whereIn('account_id', $pAccIds)->count();
                $pPos = Comment::whereIn('account_id', $pAccIds)->where('sentiment', 'positive')->count();
                $pAudience = $allUserAccounts->where('account_type', $type)->sum(function ($a) use ($type) {
                    return $type === Account::TYPE_YOUTUBE ? (int) $a->subscriber_count : (int) $a->followers_count;
                });
                $platformBreakdown[$label] = [
                    'comments' => $pComments,
                    'positive_rate' => $pComments > 0 ? round(($pPos / $pComments) * 100, 1) : 0,
                    'audience' => $pAudience,
                ];
            }
        }

        // ── 9. Top 5 Most Engaged Public Statements / Posts ───────────
        $topPosts = (clone $postBase)
            ->with('account')
            ->orderByRaw('(likes_count + comments_count * 2 + shares_count * 3) DESC')
            ->take(5)
            ->get();

        // ── 10. Top Active Constituents (Frequent Commenters) ──────────
        $topConstituents = (clone $commentBase)
            ->whereNotNull('commenter_name')
            ->where('commenter_name', 'not like', '%(Page)%')
            ->where('commenter_name', 'not like', '%User%')
            ->select(
                'commenter_name',
                'commenter_photo_url',
                DB::raw('COUNT(*) as total_comments'),
                DB::raw('SUM(CASE WHEN sentiment = "positive" OR is_favor = 1 THEN 1 ELSE 0 END) as positive_count'),
                DB::raw('SUM(CASE WHEN sentiment = "negative" OR is_against = 1 OR is_complaint = 1 THEN 1 ELSE 0 END) as critical_count')
            )
            ->groupBy('commenter_name', 'commenter_photo_url')
            ->orderByDesc('total_comments')
            ->take(5)
            ->get();

        return view('admin.analytics.index', compact(
            'allUserAccounts',
            'selectedPlatform',
            'timeRange',
            'customDateFrom',
            'customDateTo',
            'totalComments',
            'favorCount',
            'againstCount',
            'complaintCount',
            'trollsCount',
            'positiveSentimentCount',
            'neutralSentimentCount',
            'negativeSentimentCount',
            'positivePercent',
            'negativePercent',
            'neutralPercent',
            'repliedCount',
            'responseRate',
            'favorRatio',
            'totalPosts',
            'totalLikes',
            'totalPostComments',
            'totalShares',
            'totalViews',
            'totalAudience',
            'chartDates',
            'chartPositiveSeries',
            'chartNegativeSeries',
            'chartComplaintSeries',
            'categoryBreakdown',
            'platformBreakdown',
            'topPosts',
            'topConstituents'
        ));
    }
}
