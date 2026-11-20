@extends('admin.layouts.app')

@section('title', 'Sentiment Analytics - SocialManager')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle text-secondary">Audience Sentiment & Engagement Intelligence</div>
                <h2 class="page-title text-dark fw-bold">Sentiment Analytics</h2>
            </div>
            <div class="col-auto">
                <form action="{{ route('admin.analytics.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    {{-- Channel Filter (Normal Select) --}}
                    <div style="min-width: 150px;">
                        <select name="platform" class="form-select form-select-sm shadow-none" onchange="this.form.submit()">
                            <option value="all" {{ $selectedPlatform === 'all' ? 'selected' : '' }}>All Channels</option>
                            <option value="facebook" {{ $selectedPlatform === 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="instagram" {{ $selectedPlatform === 'instagram' ? 'selected' : '' }}>Instagram</option>
                            <option value="youtube" {{ $selectedPlatform === 'youtube' ? 'selected' : '' }}>YouTube</option>
                        </select>
                    </div>

                    {{-- Time Range Filter (Normal Select) --}}
                    <div style="min-width: 140px;">
                        <select name="range" class="form-select form-select-sm shadow-none" onchange="this.form.submit()">
                            <option value="7d" {{ $timeRange === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30d" {{ $timeRange === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="90d" {{ $timeRange === '90d' ? 'selected' : '' }}>Last 90 Days</option>
                            <option value="all" {{ $timeRange === 'all' ? 'selected' : '' }}>All Time</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Top Row: 4 High-Impact Sentiment KPI Cards --}}
    <div class="row row-deck row-cards mb-4">

        {{-- 1. Sentiment Favourability --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">FAVOURABILITY</span>
                        <span class="badge bg-{{ $favorRatio >= 50 ? 'success' : 'warning' }}-lt fw-semibold fs-8 px-2 py-1 rounded-pill">
                            <i class="ti ti-thumb-up me-1"></i>{{ $favorRatio >= 50 ? 'Positive' : 'Cautious' }}
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h1 mb-0 fw-bold text-dark" style="font-size: 2rem; letter-spacing: -0.03em;">{{ $favorRatio }}%</div>
                    </div>
                    <div class="text-secondary small d-flex align-items-center gap-1.5">
                        <span class="badge bg-success p-1 rounded-circle" style="width: 6px; height: 6px;"></span>
                        <span>{{ number_format($favorCount) }} pro</span>
                        <span class="text-muted">&bull;</span>
                        <span>{{ number_format($againstCount) }} critical</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Grievances (Complaints) --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">GRIEVANCES</span>
                        <span class="badge bg-warning-lt fw-semibold fs-8 px-2 py-1 rounded-pill">
                            <i class="ti ti-alert-triangle me-1"></i>Actionable
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h1 mb-0 fw-bold text-dark" style="font-size: 2rem; letter-spacing: -0.03em;">{{ number_format($complaintCount) }}</div>
                        @if($totalComments > 0)
                            <span class="text-secondary small fw-medium">({{ round(($complaintCount / $totalComments) * 100, 1) }}%)</span>
                        @endif
                    </div>
                    <div class="text-secondary small d-flex align-items-center gap-1.5">
                        <span class="badge bg-warning p-1 rounded-circle" style="width: 6px; height: 6px;"></span>
                        <span>{{ number_format($trollsCount) }} trolls auto-filtered</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Total Comments --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">TOTAL COMMENTS</span>
                        <span class="badge bg-primary-lt fw-semibold fs-8 px-2 py-1 rounded-pill">
                            <i class="ti ti-messages me-1"></i>{{ number_format($repliedCount) }} Replied
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h1 mb-0 fw-bold text-dark" style="font-size: 2rem; letter-spacing: -0.03em;">{{ number_format($totalComments) }}</div>
                    </div>
                    <div class="text-secondary small d-flex align-items-center gap-1.5">
                        <span class="badge bg-primary p-1 rounded-circle" style="width: 6px; height: 6px;"></span>
                        <span>Response Rate: <strong class="text-dark">{{ $responseRate }}%</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Total Audience --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">TOTAL AUDIENCE</span>
                        <span class="badge bg-azure-lt fw-semibold fs-8 px-2 py-1 rounded-pill">
                            <i class="ti ti-users me-1"></i>Reach
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h1 mb-0 fw-bold text-dark" style="font-size: 2rem; letter-spacing: -0.03em;">{{ number_format($totalAudience) }}</div>
                    </div>
                    <div class="text-secondary small d-flex align-items-center gap-1.5">
                        <span class="badge bg-azure p-1 rounded-circle" style="width: 6px; height: 6px;"></span>
                        <span>{{ number_format($totalPosts) }} broadcasts published</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 2: Charts (Daily Sentiment Timeline & Sentiment Donut) --}}
    <div class="row row-deck row-cards mb-4">

        {{-- Left: Daily Sentiment Timeline Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Constituent Sentiment Timeline</h3>
                        <div class="text-secondary small">Daily trend of positive feedback, criticism & citizen complaints</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-success p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Positive</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-danger p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Critical</span>
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
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Public Sentiment Pulse</h3>
                        <div class="text-secondary small">Overall constituent tone breakdown</div>
                    </div>
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

    {{-- Row 3: Interaction Category Breakdown & Platform Matrix --}}
    <div class="row row-deck row-cards mb-4">

        {{-- Left: Interaction Category Breakdown --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Constituent Interaction Breakdown</h3>
                        <div class="text-secondary small">Categorized by intent from citizen comments</div>
                    </div>
                </div>
                <div class="card-body p-3.5">
                    <div class="d-flex flex-column gap-3.5">
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
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <span class="fw-semibold text-dark small">{{ $catName }}</span>
                                    <span class="text-secondary small">
                                        <strong class="text-dark">{{ number_format($count) }}</strong> <span class="text-muted">({{ $percent }}%)</span>
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 999px; background: #f1f5f9;">
                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $barWidth }}%; border-radius: 999px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Platform Performance Comparison Matrix --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Platform Channel Matrix</h3>
                        <div class="text-secondary small">Constituent reach and sentiment across platforms</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-uppercase fs-7 text-secondary fw-semibold">Channel</th>
                                    <th class="text-uppercase fs-7 text-secondary fw-semibold">Followers</th>
                                    <th class="text-uppercase fs-7 text-secondary fw-semibold">Comments</th>
                                    <th class="text-uppercase fs-7 text-secondary fw-semibold" style="min-width: 140px;">Positive %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($platformBreakdown as $pName => $pData)
                                    @php
                                        $iconClass = match($pName) {
                                            'Facebook' => 'ti ti-brand-facebook text-primary',
                                            'Instagram' => 'ti ti-brand-instagram text-pink',
                                            'YouTube' => 'ti ti-brand-youtube text-danger',
                                            default => 'ti ti-device-desktop',
                                        };
                                        $bgClass = match($pName) {
                                            'Facebook' => 'bg-primary-lt',
                                            'Instagram' => 'bg-pink-lt',
                                            'YouTube' => 'bg-danger-lt',
                                            default => 'bg-secondary-lt',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-2 {{ $bgClass }}">
                                                    <i class="{{ $iconClass }} fs-3"></i>
                                                </span>
                                                <span class="fw-semibold text-dark">{{ $pName }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ number_format($pData['audience']) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ number_format($pData['comments']) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 999px; background: #f1f5f9; min-width: 60px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $pData['positive_rate'] }}%; border-radius: 999px;"></div>
                                                </div>
                                                <span class="text-dark fw-bold small" style="min-width: 40px; text-align: right;">{{ $pData['positive_rate'] }}%</span>
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
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Top Engaging Public Statements</h3>
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
                            <div class="list-group-item px-3.5 py-2.5">
                                <div class="d-flex align-items-center">
                                    @if($thumb)
                                        <span class="avatar avatar-md rounded-2 me-3 flex-shrink-0" style="background-image: url('{{ $thumb }}'); background-size: cover;"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-2 bg-light text-primary me-3 flex-shrink-0">
                                            <i class="{{ $pIcon }} fs-2"></i>
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-2">
                                        <div class="fw-semibold text-dark text-truncate small" title="{{ e($postText) }}">
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
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Key Active Constituents</h3>
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
                            <div class="list-group-item px-3.5 py-2.5">
                                <div class="d-flex align-items-center">
                                    @if($tc->commenter_photo_url)
                                        <span class="avatar avatar-md rounded-circle me-3 flex-shrink-0" style="background-image: url('{{ $tc->commenter_photo_url }}')"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-circle bg-body-secondary text-dark fw-bold border me-3 flex-shrink-0">
                                            {{ $initial }}
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-2">
                                        <div class="fw-semibold text-dark text-truncate small">{{ $tc->commenter_name }}</div>
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
