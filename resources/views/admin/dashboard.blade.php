@extends('admin.layouts.app')
@section('title', 'Social Dashboard')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Social Dashboard</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex align-items-center gap-2">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-users me-1"></i>Manage Users
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-user-plus me-1"></i>Add User
                        </a>
                    @else
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-brand-meta me-1"></i>Channels ({{ $channelsCount }})
                        </a>
                        <a href="{{ route('admin.posts.schedule') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>Schedule Post
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 8 Real Operational Metric Cards --}}
    <div class="row row-deck row-cards mb-3">
        
        {{-- 1. CONNECTED CHANNELS --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                                <i class="ti ti-plug-connected fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">CHANNELS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($channelsCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-{{ $channelsCount > 0 ? 'success' : 'warning' }}-lt small fw-semibold">
                                {{ $channelsCount > 0 ? 'Connected' : 'Setup' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TOTAL PUBLISHED POSTS --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                                <i class="ti ti-article fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL POSTS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalPosts) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-success-lt small fw-semibold">
                                +{{ $newPostsMonth }} this mo
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. SCHEDULED POSTS (QUEUED) --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-purple-lt text-purple">
                                <i class="ti ti-calendar-time fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">SCHEDULED</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($scheduledCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-purple-lt small fw-semibold">Queued</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. AUTO-PUBLISHED BY CRON --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                                <i class="ti ti-circle-check fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">AUTO-PUBLISHED</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($publishedScheduledCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-azure-lt small fw-semibold">Cron Live</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. TOTAL COMMENTS SYNCED --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-warning-lt text-warning">
                                <i class="ti ti-message-circle fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL COMMENTS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalComments) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-warning-lt small fw-semibold">
                                +{{ $commentsToday }} today
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. AI REPLIED --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-robot fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">AI REPLIED</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($repliedCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-teal-lt small fw-semibold">Auto-pilot</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. PENDING REVIEW COMMENTS --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-info-lt text-info">
                                <i class="ti ti-clock-edit fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">PENDING REVIEW</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($pendingReviewCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-{{ $pendingReviewCount > 0 ? 'info' : 'secondary' }}-lt small fw-semibold">
                                {{ $pendingReviewCount > 0 ? 'Action' : 'Clear' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 8. HIDDEN / MODERATED TROLLS --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-danger-lt text-danger">
                                <i class="ti ti-shield-check fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">HIDDEN TROLLS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($hiddenCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-danger-lt small fw-semibold">
                                {{ $hiddenCount > 0 ? 'Filtered' : 'Clean' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Main Row: 30-Day Activity Trends & Connected Channels Performance --}}
    <div class="row row-deck row-cards mb-3">
        
        {{-- Left: Publishing & Comments Activity Line Chart --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">30-DAY PUBLISHING & COMMENT ACTIVITY</div>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <span class="h1 fw-bold text-dark mb-0">{{ number_format($totalPosts) }} Posts &bull; {{ number_format($totalComments) }} Comments</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-primary p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                                <span class="text-secondary small">Posts</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-azure p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                                <span class="text-secondary small">Comments</span>
                            </div>
                        </div>
                    </div>
                    <div id="chart-social-activity" style="min-height: 290px;"></div>
                </div>
            </div>
        </div>

        {{-- Right: Connected Channels & Accounts --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">Connected Channels</h3>
                        <div class="text-secondary small">Meta & Social accounts status</div>
                    </div>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-plus me-1"></i>Manage
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($accounts as $acc)
                            <div class="list-group-item px-3 py-3">
                                <div class="d-flex align-items-center">
                                    @if($acc->profile_pic_url)
                                        <span class="avatar avatar-md rounded-circle me-3" style="background-image: url('{{ $acc->profile_pic_url }}')"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-circle bg-primary-lt text-primary me-3">
                                            <i class="ti {{ $acc->account_type == 2 ? 'ti-brand-instagram' : 'ti-brand-facebook' }} fs-2"></i>
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-2">
                                        <div class="fw-semibold text-dark text-truncate">{{ $acc->name }}</div>
                                        <div class="text-secondary small">
                                            <span>{{ $acc->account_type == 2 ? 'Instagram' : 'Facebook Page' }}</span>
                                            @if($acc->followers_count)
                                                • {{ number_format($acc->followers_count) }} followers
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <span class="badge bg-{{ $acc->is_active ? 'success' : 'secondary' }}-lt">
                                            {{ $acc->is_active ? 'Active' : 'Paused' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center">
                                <span class="avatar avatar-xl rounded-circle bg-azure-lt text-azure mb-3">
                                    <i class="ti ti-brand-meta fs-1"></i>
                                </span>
                                <h4 class="fw-bold text-dark mb-1">No Channels Connected</h4>
                                <p class="text-secondary small mb-3">Connect your Facebook Pages or Instagram Business accounts to start syncing posts & automated comments.</p>
                                <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-plug me-1"></i>Connect Channel
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Bottom Row: Recent Published Posts & Activity Feed --}}
    <div class="row row-deck row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">Recent Published Posts & Activity</h3>
                        <div class="text-secondary small">Latest multi-channel broadcasts and synced interactions</div>
                    </div>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary">
                        View all posts <i class="ti ti-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentPosts as $p)
                            <div class="list-group-item px-3 py-3">
                                <div class="d-flex align-items-center">
                                    @if($p->media_url)
                                        <span class="avatar avatar-md rounded-2 me-3" style="background-image: url('{{ $p->media_url }}')"></span>
                                    @else
                                        <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary me-3">
                                            <i class="ti ti-article fs-2"></i>
                                        </span>
                                    @endif
                                    <div class="flex-grow-1 text-truncate pe-3">
                                        <div class="fw-semibold text-dark text-truncate small">
                                            {{ $p->message ?: 'New media published to ' . ($p->account ? $p->account->name : 'Channel') }}
                                        </div>
                                        <div class="text-secondary fs-7 mt-1">
                                            <span>{{ $p->account ? $p->account->name : 'Social Channel' }}</span> • 
                                            <span>{{ $p->published_at ? $p->published_at->diffForHumans() : 'Recently' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-secondary small flex-shrink-0">
                                        @if(!empty($p->views_count))
                                            <span class="d-inline-flex align-items-center" title="Views"><i class="ti ti-eye me-1"></i> {{ number_format($p->views_count) }}</span>
                                        @endif
                                        <span class="d-inline-flex align-items-center text-danger" title="Likes"><i class="ti ti-heart me-1"></i> {{ number_format($p->likes_count ?? 0) }}</span>
                                        <span class="d-inline-flex align-items-center text-primary" title="Comments"><i class="ti ti-message-circle me-1"></i> {{ number_format($p->comments_count ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary small">
                                <i class="ti ti-article-off fs-1 text-muted d-block mb-2"></i>
                                No posts synced yet. Once you connect an account or publish a post, activity will appear here.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- ApexCharts for Social Activity Trends --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const categories = {!! json_encode($chartDates ?? []) !!};
    const postSeries = {!! json_encode($postCounts ?? []) !!};
    const commentSeries = {!! json_encode($commentCounts ?? []) !!};

    const options = {
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
            width: [2.5, 2.5],
            curve: "smooth",
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.25,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        series: [
            {
                name: "Posts Published",
                data: postSeries
            },
            {
                name: "Comments Synced",
                data: commentSeries
            }
        ],
        colors: ["#1db8ab", "#0284c7"],
        xaxis: {
            categories: categories,
            labels: {
                style: { colors: '#64748b', fontSize: '12px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: { colors: '#64748b', fontSize: '12px' }
            }
        },
        grid: {
            strokeDashArray: 3,
            borderColor: '#f1f5f9',
            padding: { top: -20, right: 0, left: -4, bottom: -4 }
        },
        legend: { show: false },
        tooltip: { theme: 'light' }
    };

    const chartEl = document.querySelector("#chart-social-activity");
    if (chartEl) {
        const chart = new ApexCharts(chartEl, options);
        chart.render();
    }
});
</script>
@endpush
