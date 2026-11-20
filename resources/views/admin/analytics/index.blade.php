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
    <div class="row row-deck row-cards mb-4 dashboard-stat-grid">

        {{-- 1. Positive Sentiment --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3 p-md-3.5">
                    <div class="row g-2 align-items-center stat-row">
                        <div class="col-auto stat-icon-col">
                            <span class="avatar avatar-md rounded-3 bg-success-lt text-success">
                                <i class="ti ti-thumb-up fs-2"></i>
                            </span>
                        </div>
                        <div class="col stat-content-col">
                            <div class="text-uppercase text-secondary fw-bold stat-label">POSITIVE SENTIMENT</div>
                            <div class="d-flex align-items-baseline gap-1.5">
                                <span class="h1 mb-0 fw-bold text-success stat-value" style="letter-spacing: -0.03em;">{{ number_format($positiveSentimentCount) }}</span>
                                <span class="text-secondary small fw-medium">({{ $positivePercent }}%)</span>
                            </div>
                            <div class="text-secondary small d-flex align-items-center gap-1 mt-1 text-truncate d-none d-sm-flex">
                                <span class="badge bg-success p-1 rounded-circle" style="width: 5px; height: 5px;"></span>
                                <span class="text-truncate">Appreciation & praise</span>
                            </div>
                        </div>
                        <div class="col-auto text-end stat-badge-col">
                            <span class="badge bg-success-lt fw-semibold stat-badge">
                                <i class="ti ti-thumb-up me-1 d-none d-sm-inline"></i>Positive
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Negative Sentiment --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3 p-md-3.5">
                    <div class="row g-2 align-items-center stat-row">
                        <div class="col-auto stat-icon-col">
                            <span class="avatar avatar-md rounded-3 bg-danger-lt text-danger">
                                <i class="ti ti-thumb-down fs-2"></i>
                            </span>
                        </div>
                        <div class="col stat-content-col">
                            <div class="text-uppercase text-secondary fw-bold stat-label">NEGATIVE SENTIMENT</div>
                            <div class="d-flex align-items-baseline gap-1.5">
                                <span class="h1 mb-0 fw-bold text-danger stat-value" style="letter-spacing: -0.03em;">{{ number_format($negativeSentimentCount) }}</span>
                                <span class="text-secondary small fw-medium">({{ $negativePercent }}%)</span>
                            </div>
                            <div class="text-secondary small d-flex align-items-center gap-1 mt-1 text-truncate d-none d-sm-flex">
                                <span class="badge bg-danger p-1 rounded-circle" style="width: 5px; height: 5px;"></span>
                                <span class="text-truncate">Critical & issues</span>
                            </div>
                        </div>
                        <div class="col-auto text-end stat-badge-col">
                            <span class="badge bg-danger-lt fw-semibold stat-badge">
                                <i class="ti ti-thumb-down me-1 d-none d-sm-inline"></i>Negative
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Neutral & Inquiries --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3 p-md-3.5">
                    <div class="row g-2 align-items-center stat-row">
                        <div class="col-auto stat-icon-col">
                            <span class="avatar avatar-md rounded-3 bg-azure-lt text-azure">
                                <i class="ti ti-message-dots fs-2"></i>
                            </span>
                        </div>
                        <div class="col stat-content-col">
                            <div class="text-uppercase text-secondary fw-bold stat-label">NEUTRAL & INQUIRIES</div>
                            <div class="d-flex align-items-baseline gap-1.5">
                                <span class="h1 mb-0 fw-bold text-azure stat-value" style="letter-spacing: -0.03em;">{{ number_format($neutralSentimentCount) }}</span>
                                <span class="text-secondary small fw-medium">({{ $neutralPercent }}%)</span>
                            </div>
                            <div class="text-secondary small d-flex align-items-center gap-1 mt-1 text-truncate d-none d-sm-flex">
                                <span class="badge bg-azure p-1 rounded-circle" style="width: 5px; height: 5px;"></span>
                                <span class="text-truncate">Questions & feedback</span>
                            </div>
                        </div>
                        <div class="col-auto text-end stat-badge-col">
                            <span class="badge bg-azure-lt fw-semibold stat-badge">
                                <i class="ti ti-message-dots me-1 d-none d-sm-inline"></i>Neutral
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Overall Sentiment Pulse --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body p-3 p-md-3.5">
                    <div class="row g-2 align-items-center stat-row">
                        <div class="col-auto stat-icon-col">
                            <span class="avatar avatar-md rounded-3 bg-{{ $favorRatio >= 50 ? 'success' : 'warning' }}-lt text-{{ $favorRatio >= 50 ? 'success' : 'warning' }}">
                                <i class="ti ti-activity fs-2"></i>
                            </span>
                        </div>
                        <div class="col stat-content-col">
                            <div class="text-uppercase text-secondary fw-bold stat-label">OVERALL SENTIMENT</div>
                            <div class="d-flex align-items-baseline gap-1.5">
                                <span class="h1 mb-0 fw-bold text-dark stat-value" style="letter-spacing: -0.03em;">{{ $favorRatio }}%</span>
                                <span class="text-secondary small fw-medium">Pulse</span>
                            </div>
                            <div class="text-secondary small d-flex align-items-center gap-1 mt-1 text-truncate d-none d-sm-flex">
                                <span class="badge bg-primary p-1 rounded-circle" style="width: 5px; height: 5px;"></span>
                                <span class="text-truncate">{{ number_format($totalComments) }} total comments</span>
                            </div>
                        </div>
                        <div class="col-auto text-end stat-badge-col">
                            <span class="badge bg-{{ $favorRatio >= 50 ? 'success' : 'warning' }}-lt fw-semibold stat-badge">
                                <i class="ti ti-heart-rate-monitor me-1 d-none d-sm-inline"></i>{{ $favorRatio >= 50 ? 'Positive' : 'Caution' }}
                            </span>
                        </div>
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
                        <h3 class="card-title fw-bold text-dark mb-1">Audience Sentiment Timeline</h3>
                        <div class="text-secondary small">Daily trend of positive feedback, criticism & general inquiries</div>
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
                            <span class="badge bg-azure p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Inquiries</span>
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
                        <h3 class="card-title fw-bold text-dark mb-1">Audience Sentiment Pulse</h3>
                        <div class="text-secondary small">Overall sentiment tone breakdown across comments</div>
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

        {{-- Left: Audience Intent Breakdown --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3.5 px-4 bg-white" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title fw-bold text-dark mb-0.5" style="letter-spacing: -0.01em;">Audience Intent Breakdown</h3>
                            <div class="text-secondary small">Categorized by AI intent across all audience comments</div>
                        </div>
                        <span class="badge bg-light text-secondary rounded-pill px-2.5 py-1 fs-8 fw-semibold border border-light-subtle">
                            {{ number_format($totalComments) }} Total
                        </span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4 bg-white">
                    <div class="d-flex flex-column">
                        @php
                            $categoryMeta = [
                                'Appreciation & Praise' => [
                                    'color' => 'success',
                                    'icon' => 'ti ti-heart-handshake',
                                    'desc' => 'Positive praise, thanks & appreciation',
                                ],
                                'Questions & Inquiries' => [
                                    'color' => 'azure',
                                    'icon' => 'ti ti-help-circle',
                                    'desc' => 'Direct questions & informational requests',
                                ],
                                'Feedback & Suggestions' => [
                                    'color' => 'primary',
                                    'icon' => 'ti ti-bulb',
                                    'desc' => 'Constructive suggestions & feature ideas',
                                ],
                                'Issues & Concerns' => [
                                    'color' => 'warning',
                                    'icon' => 'ti ti-alert-triangle',
                                    'desc' => 'User complaints, bug reports & issues',
                                ],
                                'Spam & Flagged' => [
                                    'color' => 'danger',
                                    'icon' => 'ti ti-shield-x',
                                    'desc' => 'Hate speech, spam & auto-filtered trolls',
                                ],
                            ];
                        @endphp
                        @foreach($categoryBreakdown as $catName => $count)
                            @php
                                $percent = $totalComments > 0 ? round(($count / $totalComments) * 100, 1) : 0;
                                $meta = $categoryMeta[$catName] ?? ['color' => 'secondary', 'icon' => 'ti ti-message', 'desc' => 'Audience interactions'];
                                $color = $meta['color'];
                            @endphp
                            <div class="intent-row py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2.5 min-w-0 pe-2">
                                        <span class="avatar avatar-sm bg-{{ $color }}-lt text-{{ $color }} flex-shrink-0" style="width: 34px; height: 34px; border-radius: 9px;">
                                            <i class="{{ $meta['icon'] }} fs-2"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 0.875rem;">{{ $catName }}</span>
                                            <span class="text-secondary fs-8 text-truncate d-block mt-0.5">{{ $meta['desc'] }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ps-2">
                                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ number_format($count) }}</span>
                                        <span class="badge bg-{{ $color }}-lt text-{{ $color }} ms-1.5 fs-8 fw-semibold px-2 py-0.5 rounded-pill">{{ $percent }}%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 999px; background: #f1f5f9;">
                                    <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percent }}%; border-radius: 999px;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
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
                        <div class="text-secondary small">Audience reach and positive engagement across platforms</div>
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
                        <h3 class="card-title fw-bold text-dark mb-1">Top Performing Content</h3>
                        <div class="text-secondary small">Broadcasts and posts that generated the highest audience engagement</div>
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
                                            <span>{{ $tp->account?->name ?: 'Channel' }}</span> &bull; 
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

        {{-- Right: Most Active Community Members --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column">
                        <h3 class="card-title fw-bold text-dark mb-1">Most Active Community Members</h3>
                        <div class="text-secondary small">Top audience members interacting across your channels</div>
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
                                            <span>{{ number_format($tc->total_comments) }} interactions</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($isPositiveLean)
                                            <span class="badge bg-success-lt text-success small">
                                                <i class="ti ti-thumb-up me-0.5"></i>Positive Contributor
                                            </span>
                                        @else
                                            <span class="badge bg-azure-lt text-azure small">
                                                <i class="ti ti-message-circle me-0.5"></i>Feedback Contributor
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary small">
                                No audience interactions found yet.
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
            { name: "Positive", data: positiveSeries },
            { name: "Critical", data: negativeSeries },
            { name: "Inquiries", data: complaintSeries }
        ],
        colors: ["#2fb344", "#d63939", "#0284c7"],
        xaxis: {
            categories: trendDates,
            tickAmount: window.innerWidth < 768 ? 5 : 12,
            labels: {
                rotate: -35,
                rotateAlways: false,
                hideOverlappingLabels: true,
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '11px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: { colors: dark ? '#8896ab' : '#64748b', fontSize: '11px' }
            }
        },
        grid: {
            strokeDashArray: 3,
            borderColor: dark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9',
            padding: { top: -20, right: 0, left: -4, bottom: -4 }
        },
        legend: { show: false },
        tooltip: { theme: dark ? 'dark' : 'light' },
        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: { height: 240 },
                    xaxis: {
                        tickAmount: 5,
                        labels: { rotate: -30, hideOverlappingLabels: true, style: { fontSize: '10px' } }
                    },
                    yaxis: { tickAmount: 3 }
                }
            }
        ]
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
