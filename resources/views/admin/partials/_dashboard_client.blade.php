{{-- Page Header --}}
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Social Dashboard</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-brand-meta me-1"></i>Channels ({{ $channelsCount }})
                </a>
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-plus me-1"></i>Schedule Post
                </a>
            </div>
        </div>
    </div>
</div>

{{-- 8 Real Operational Metric Cards --}}
<div class="row row-deck row-cards mb-3 dashboard-stat-grid">
    
    {{-- 1. CONNECTED CHANNELS --}}
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.accounts.index') }}" class="card card-sm card-link text-decoration-none" title="View Connected Accounts">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                            <i class="ti ti-plug-connected fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">CHANNELS</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($channelsCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-{{ $channelsCount > 0 ? 'success' : 'warning' }}-lt small fw-semibold stat-badge">
                            {{ $channelsCount > 0 ? 'Connected' : 'Setup' }}
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- 2. TOTAL PUBLISHED POSTS --}}
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.posts.facebook') }}" class="card card-sm card-link text-decoration-none" title="View Facebook Posts">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                            <i class="ti ti-article fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">TOTAL POSTS</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($totalPosts) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-success-lt small fw-semibold stat-badge">
                            +{{ $newPostsMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- 3. SCHEDULED POSTS (QUEUED) --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-purple-lt text-purple">
                            <i class="ti ti-calendar-time fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">SCHEDULED</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($scheduledCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-purple-lt small fw-semibold stat-badge">Queued</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. AUTO-PUBLISHED BY CRON --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                            <i class="ti ti-circle-check fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">AUTO-PUBLISHED</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($publishedScheduledCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-azure-lt small fw-semibold stat-badge">Cron Live</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. TOTAL COMMENTS SYNCED --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-warning-lt text-warning">
                            <i class="ti ti-message-circle fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">TOTAL COMMENTS</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($totalComments) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-warning-lt small fw-semibold stat-badge">
                            +{{ $commentsToday }} today
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. AI REPLIED --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                            <i class="ti ti-robot fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">AI REPLIED</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($repliedCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-teal-lt small fw-semibold stat-badge">Auto-pilot</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 7. PENDING REVIEW COMMENTS --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-info-lt text-info">
                            <i class="ti ti-clock-edit fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">PENDING REVIEW</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($pendingReviewCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-{{ $pendingReviewCount > 0 ? 'info' : 'secondary' }}-lt small fw-semibold stat-badge">
                            {{ $pendingReviewCount > 0 ? 'Action' : 'Clear' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 8. HIDDEN / MODERATED TROLLS --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row g-2 align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-danger-lt text-danger">
                            <i class="ti ti-shield-check fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label">HIDDEN TROLLS</div>
                        <div class="h2 mb-0 fw-bold text-dark stat-value">{{ number_format($hiddenCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-danger-lt small fw-semibold stat-badge">
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
                <div class="d-flex align-items-center justify-content-between mb-3 chart-header-row">
                    <div class="chart-title-wrap">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">30-DAY PUBLISHING & COMMENT ACTIVITY</div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <span class="h1 fw-bold text-dark mb-0 chart-title-headline">{{ number_format($totalPosts) }} Posts &bull; {{ number_format($totalComments) }} Comments</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 chart-legend-wrap">
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
                    <h3 class="card-title fw-bold text-body mb-0">Connected Channels</h3>
                    <div class="text-secondary small">Meta & Social accounts status</div>
                </div>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
                    <i class="ti ti-plus me-1"></i>Manage
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush channel-list-group">
                    @forelse($accounts as $acc)
                        <div class="list-group-item px-3 py-3 channel-list-item">
                            <div class="d-flex align-items-center">
                                @if($acc->profile_pic_url)
                                    <span class="avatar avatar-md rounded-circle me-3 channel-avatar" style="background-image: url('{{ $acc->profile_pic_url }}')"></span>
                                @else
                                    <span class="avatar avatar-md rounded-circle bg-primary-lt text-primary me-3 channel-avatar">
                                        <i class="ti {{ $acc->account_type == 2 ? 'ti-brand-instagram' : 'ti-brand-facebook' }} fs-2"></i>
                                    </span>
                                @endif
                                <div class="flex-grow-1 text-truncate pe-2">
                                    <div class="fw-semibold text-body text-truncate channel-name">{{ $acc->name }}</div>
                                    <div class="text-secondary small channel-sub">
                                        <span>{{ $acc->account_type == 2 ? 'Instagram' : 'Facebook Page' }}</span>
                                        @if($acc->followers_count)
                                            • {{ number_format($acc->followers_count) }} followers
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge bg-{{ $acc->is_active ? 'success' : 'secondary' }}-lt channel-status-badge">
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
                            <h4 class="fw-bold text-body mb-1">No Channels Connected</h4>
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
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-1">
                <div>
                    <h3 class="card-title fw-bold text-body mb-0">Recent Published Posts & Activity</h3>
                    <div class="text-secondary small">Latest multi-channel broadcasts and synced interactions</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush post-feed-list">
                    @forelse($recentPosts as $p)
                        <div class="list-group-item px-3 py-3 post-feed-item">
                            <div class="d-flex align-items-center post-feed-row">
                                @if($p->media_url)
                                    <span class="avatar avatar-md rounded-2 me-3 post-feed-thumb" style="background-image: url('{{ $p->media_url }}')"></span>
                                @else
                                    <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary me-3 post-feed-thumb">
                                        <i class="ti ti-article fs-2"></i>
                                    </span>
                                @endif
                                <div class="flex-grow-1 text-truncate pe-md-3 post-feed-content">
                                    <div class="fw-semibold text-body text-truncate small post-feed-title">
                                        {{ $p->message ?: 'New media published to ' . ($p->account ? $p->account->name : 'Channel') }}
                                    </div>
                                    <div class="text-secondary fs-7 mt-1 post-feed-meta">
                                        <span>{{ $p->account ? $p->account->name : 'Social Channel' }}</span> &bull; 
                                        <span>{{ $p->published_at ? $p->published_at->diffForHumans() : 'Recently' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 text-secondary small flex-shrink-0 post-feed-stats">
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
