@extends('admin.layouts.app')

@section('title', 'Politics Analyses & Constituent Intelligence')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle text-secondary">Political Pulse & Public Intelligence</div>
                <h2 class="page-title text-body">Politics Analyses</h2>
            </div>
            <div class="col-auto d-flex align-items-center gap-2 flex-wrap">
                {{-- Platform Filter Buttons --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['platform' => 'all'])) }}"
                        class="btn btn-sm {{ $selectedPlatform === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        All Channels
                    </a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['platform' => 'facebook'])) }}"
                        class="btn btn-sm {{ $selectedPlatform === 'facebook' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="ti ti-brand-facebook text-facebook me-1"></i>Facebook
                    </a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['platform' => 'instagram'])) }}"
                        class="btn btn-sm {{ $selectedPlatform === 'instagram' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="ti ti-brand-instagram text-pink me-1"></i>Instagram
                    </a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['platform' => 'youtube'])) }}"
                        class="btn btn-sm {{ $selectedPlatform === 'youtube' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="ti ti-brand-youtube text-danger me-1"></i>YouTube
                    </a>
                </div>

                {{-- Time Range Filter Buttons --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['range' => '7d'])) }}"
                        class="btn btn-sm {{ $timeRange === '7d' ? 'btn-dark' : 'btn-outline-secondary' }}">7D</a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['range' => '30d'])) }}"
                        class="btn btn-sm {{ $timeRange === '30d' ? 'btn-dark' : 'btn-outline-secondary' }}">30D</a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['range' => '90d'])) }}"
                        class="btn btn-sm {{ $timeRange === '90d' ? 'btn-dark' : 'btn-outline-secondary' }}">90D</a>
                    <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['range' => 'all'])) }}"
                        class="btn btn-sm {{ $timeRange === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">All Time</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Row: 4 High-Impact Political KPI Cards --}}
    <div class="row row-deck row-cards mb-3">

        {{-- 1. Political Favourability --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                                <i class="ti ti-thumb-up fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">FAVOURABILITY</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ $favorRatio }}%</div>
                            <div class="text-secondary small mt-0.5">
                                {{ number_format($favorCount) }} pro • {{ number_format($againstCount) }} critical
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-{{ $favorRatio >= 50 ? 'success' : 'warning' }}-lt small fw-semibold">
                                {{ $favorRatio >= 50 ? 'Positive' : 'Cautious' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Constituent Grievances (Complaints) --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-warning-lt text-warning">
                                <i class="ti ti-alert-triangle fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">GRIEVANCES</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($complaintCount) }}</div>
                            <div class="text-secondary small mt-0.5">
                                {{ number_format($trollsCount) }} trolls auto-filtered
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-warning-lt small fw-semibold">
                                <i class="ti ti-flag me-0.5"></i>Actionable
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Public Interactions (Comments & Response Rate) --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                                <i class="ti ti-messages fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL COMMENTS</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalComments) }}</div>
                            <div class="text-secondary small mt-0.5">
                                Response Rate: {{ $responseRate }}%
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-primary-lt small fw-semibold">
                                {{ number_format($repliedCount) }} Replied
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Total Constituent Reach --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                                <i class="ti ti-users fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL AUDIENCE</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalAudience) }}</div>
                            <div class="text-secondary small mt-0.5">
                                {{ number_format($totalPosts) }} broadcasts published
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-azure-lt small fw-semibold">
                                +{{ number_format($totalLikes + $totalShares) }} Reacts
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 2: Charts (Daily Sentiment Timeline & Sentiment Donut) --}}
    <div class="row row-deck row-cards mb-3">

        {{-- Left: Daily Sentiment Timeline Chart --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-body mb-0">Constituent Sentiment Timeline</h3>
                        <div class="text-secondary small">Daily trend of positive feedback, criticism & citizen complaints</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-success p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Pro / Positive</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-danger p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Critical / Against</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-warning p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Grievances</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-sentiment-trend" style="min-height: 290px;"></div>
                </div>
            </div>
        </div>

        {{-- Right: Sentiment Breakdown Donut --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title fw-bold text-body mb-0">Public Sentiment Pulse</h3>
                    <div class="text-secondary small">Overall constituent tone breakdown</div>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="chart-sentiment-donut" style="min-height: 240px;"></div>
                    <div class="d-flex justify-content-around text-center pt-2 border-top mt-2">
                        <div>
                            <div class="text-secondary small">Positive</div>
                            <div class="h3 fw-bold text-success mb-0">{{ number_format($positiveSentimentCount) }}</div>
                        </div>
                        <div>
                            <div class="text-secondary small">Neutral</div>
                            <div class="h3 fw-bold text-secondary mb-0">{{ number_format($neutralSentimentCount) }}</div>
                        </div>
                        <div>
                            <div class="text-secondary small">Negative</div>
                            <div class="h3 fw-bold text-danger mb-0">{{ number_format($negativeSentimentCount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 3: Citizen Grievances / Category Breakdown & Platform Matrix --}}
    <div class="row row-deck row-cards mb-3">

        {{-- Left: Interaction Category Breakdown --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title fw-bold text-body mb-0">Constituent Interaction Breakdown</h3>
                    <div class="text-secondary small">Categorized by intent from citizen comments</div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        @php
                            $maxCount = max(1, max($categoryBreakdown));
                            $categoryColors = [
                                'Appreciation' => 'success',
                                'Grievance / Issue' => 'warning',
                                'Inquiry / Question' => 'primary',
                                'Feedback / Suggestion' => 'azure',
                                'Troll / Spam' => 'danger',
                            ];
                        @endphp
                        @foreach($categoryBreakdown as $catName => $count)
                            @php
                                $percent = $totalComments > 0 ? round(($count / $totalComments) * 100, 1) : 0;
                                $barWidth = round(($count / $maxCount) * 100, 1);
                                $color = $categoryColors[$catName] ?? 'secondary';
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium text-body small">{{ $catName }}</span>
                                    <span class="text-secondary small">
                                        <strong class="text-body">{{ number_format($count) }}</strong> ({{ $percent }}%)
                                    </span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Platform Performance Comparison Matrix --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title fw-bold text-body mb-0">Platform Channel Matrix</h3>
                    <div class="text-secondary small">Constituent reach and sentiment across platforms</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Channel</th>
                                    <th>Followers</th>
                                    <th>Comments</th>
                                    <th>Positive %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($platformBreakdown as $pName => $pData)
                                    @php
                                        $iconClass = match($pName) {
                                            'Facebook' => 'ti ti-brand-facebook text-facebook',
                                            'Instagram' => 'ti ti-brand-instagram text-pink',
                                            'YouTube' => 'ti ti-brand-youtube text-danger',
                                            default => 'ti ti-device-desktop',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="{{ $iconClass }} fs-2"></i>
                                                <span class="fw-semibold text-body">{{ $pName }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-body">{{ number_format($pData['audience']) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-body">{{ number_format($pData['comments']) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress progress-xs flex-grow-1" style="min-width: 60px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $pData['positive_rate'] }}%"></div>
                                                </div>
                                                <span class="text-secondary small">{{ $pData['positive_rate'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No active channel data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 4: Top Performing Statements & Active Commenters Leaderboard --}}
    <div class="row row-deck row-cards">

        {{-- Left: Top Performing Statements / Posts --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-body mb-0">Top Engaging Public Statements</h3>
                        <div class="text-secondary small">Broadcasts that generated the highest constituent interaction</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topPosts as $tp)
                            @php
                                $thumb = $tp->thumbnail_url ?: $tp->media_url;
                                $postText = !empty($tp->message) ? $tp->message : (!empty($tp->title) ? $tp->title : 'Media Post');
                                $pIcon = $tp->account?->isYouTube() ? 'ti ti-brand-youtube text-danger' : ($tp->account?->isInstagram() ? 'ti ti-brand-instagram text-pink' : 'ti ti-brand-facebook text-primary');
                            @endphp
                            <div class="list-group-item px-3 py-2.5">
                                <div class="d-flex align-items-center">
                                    @if($thumb)
                                        <span class="avatar avatar-md rounded-2 me-3 flex-shrink-0" style="background-image: url('{{ $thumb }}'); background-size: cover;"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-2 bg-light text-primary me-3 flex-shrink-0">
                                            <i class="{{ $pIcon }} fs-2"></i>
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-2">
                                        <div class="fw-semibold text-body text-truncate small" title="{{ e($postText) }}">
                                            {{ Str::limit($postText, 55) }}
                                        </div>
                                        <div class="text-secondary fs-7 mt-0.5">
                                            <span>{{ $tp->account?->name ?: 'Channel' }}</span> • 
                                            <span>{{ $tp->published_at ? $tp->published_at->format('d M Y') : 'Recent' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-secondary small flex-shrink-0">
                                        <span class="d-inline-flex align-items-center text-danger" title="Likes">
                                            <i class="ti ti-heart me-1"></i>{{ number_format($tp->likes_count) }}
                                        </span>
                                        <span class="d-inline-flex align-items-center text-primary" title="Comments">
                                            <i class="ti ti-message-circle me-1"></i>{{ number_format($tp->comments_count) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary small">
                                No published posts found for the selected timeframe.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Most Active Constituents (Frequent Commenters) --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-body mb-0">Key Active Constituents</h3>
                        <div class="text-secondary small">Frequent citizen participants in public discussions</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topConstituents as $tc)
                            @php
                                $initial = strtoupper(substr($tc->commenter_name, 0, 1));
                                $isPositiveLean = ($tc->positive_count >= $tc->critical_count);
                            @endphp
                            <div class="list-group-item px-3 py-2.5">
                                <div class="d-flex align-items-center">
                                    @if($tc->commenter_photo_url)
                                        <span class="avatar avatar-md rounded-circle me-3 flex-shrink-0" style="background-image: url('{{ $tc->commenter_photo_url }}')"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-circle bg-body-secondary text-body fw-bold border me-3 flex-shrink-0">
                                            {{ $initial }}
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-2">
                                        <div class="fw-semibold text-body text-truncate small">{{ $tc->commenter_name }}</div>
                                        <div class="text-secondary fs-7 mt-0.5">
                                            <span>{{ number_format($tc->total_comments) }} contributions</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($isPositiveLean)
                                            <span class="badge bg-success-lt text-success small">
                                                <i class="ti ti-thumb-up me-0.5"></i>Supporter
                                            </span>
                                        @else
                                            <span class="badge bg-warning-lt text-warning small">
                                                <i class="ti ti-alert-circle me-0.5"></i>Critical / Grievance
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary small">
                                No constituent comment history found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
{{-- ApexCharts for Interactive Trend and Donut Visualization --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const isDarkMode = function() {
        return (document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.body.getAttribute('data-bs-theme') === 'dark');
    };

    let dark = isDarkMode();

    // ── 1. Daily Sentiment Timeline Line/Area Chart ──────────────────
    const trendDates = {!! json_encode($chartDates) !!};
    const positiveSeries = {!! json_encode($chartPositiveSeries) !!};
    const negativeSeries = {!! json_encode($chartNegativeSeries) !!};
    const complaintSeries = {!! json_encode($chartComplaintSeries) !!};

    const trendOptions = {
        chart: {
            type: "area",
            fontFamily: 'inherit',
            height: 290,
            parentHeightOffset: 0,
            toolbar: { show: false },
            animations: { enabled: true },
        },
        dataLabels: { enabled: false },
        stroke: {
            width: [2.5, 2.5, 2.5],
            curve: "smooth",
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.25,
                opacityTo: 0.04,
                stops: [0, 95, 100]
            }
        },
        series: [
            { name: "Pro / Positive", data: positiveSeries },
            { name: "Critical / Against", data: negativeSeries },
            { name: "Grievances", data: complaintSeries }
        ],
        colors: ["#2fb344", "#d63939", "#f59f00"],
        xaxis: {
            categories: trendDates,
            labels: {
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '12px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '12px' }
            }
        },
        grid: {
            strokeDashArray: 3,
            borderColor: dark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9',
            padding: { top: -20, right: 0, left: -4, bottom: -4 }
        },
        legend: { show: false },
        tooltip: { theme: dark ? 'dark' : 'light' }
    };

    const trendEl = document.querySelector("#chart-sentiment-trend");
    let trendChart = null;
    if (trendEl) {
        trendChart = new ApexCharts(trendEl, trendOptions);
        trendChart.render();
    }

    // ── 2. Sentiment Donut Chart ──────────────────────────────────────
    const donutOptions = {
        chart: {
            type: "donut",
            fontFamily: 'inherit',
            height: 240,
            animations: { enabled: true },
        },
        series: [
            {{ $positiveSentimentCount }},
            {{ $neutralSentimentCount }},
            {{ $negativeSentimentCount }}
        ],
        labels: ["Positive", "Neutral", "Negative"],
        colors: ["#2fb344", "#94a3b8", "#d63939"],
        legend: {
            position: 'bottom',
            labels: {
                colors: dark ? '#cbd5e1' : '#334155'
            }
        },
        dataLabels: {
            enabled: true,
            dropShadow: { enabled: false }
        },
        stroke: {
            width: 2,
            colors: [dark ? '#111a2d' : '#ffffff']
        },
        tooltip: {
            theme: dark ? 'dark' : 'light'
        }
    };

    const donutEl = document.querySelector("#chart-sentiment-donut");
    let donutChart = null;
    if (donutEl) {
        donutChart = new ApexCharts(donutEl, donutOptions);
        donutChart.render();
    }

    // ── 3. Theme Toggle Reactive Listener ────────────────────────────
    window.addEventListener('themeChanged', function (e) {
        const nextDark = (e.detail?.theme === 'dark');

        if (trendChart) {
            trendChart.updateOptions({
                xaxis: { labels: { style: { colors: nextDark ? '#8896ab' : '#64748b' } } },
                yaxis: { labels: { style: { colors: nextDark ? '#8896ab' : '#64748b' } } },
                grid: { borderColor: nextDark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9' },
                tooltip: { theme: nextDark ? 'dark' : 'light' }
            });
        }

        if (donutChart) {
            donutChart.updateOptions({
                legend: { labels: { colors: nextDark ? '#cbd5e1' : '#334155' } },
                stroke: { colors: [nextDark ? '#111a2d' : '#ffffff'] },
                tooltip: { theme: nextDark ? 'dark' : 'light' }
            });
        }
    });
});
</script>
@endpush
