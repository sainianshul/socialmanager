{{-- Super Admin Page Header --}}
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Super Admin Console</div>
            <h2 class="page-title text-body">System Overview & Administration</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-users me-1"></i>All Users ({{ $totalUsers }})
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-user-plus me-1"></i>Add New User
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Top Row: Core Platform Stats (4 Cards) --}}
<div class="row row-deck row-cards mb-3 dashboard-stat-grid">

    {{-- 1. TOTAL USERS --}}
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.users.index') }}" class="card card-sm card-link text-decoration-none" title="Manage Users">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                            <i class="ti ti-users fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">TOTAL USERS</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($totalUsers) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-primary-lt small fw-semibold stat-badge">
                            +{{ $newUsersThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- 2. AI REPLIES --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                            <i class="ti ti-robot fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">AI REPLIES</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($totalAiReplies) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-teal-lt small fw-semibold stat-badge">
                            +{{ $aiRepliesThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TOTAL POSTS --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                            <i class="ti ti-article fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">TOTAL POSTS</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($totalPosts) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-success-lt small fw-semibold stat-badge">
                            +{{ $postsThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TOTAL COMMENTS --}}
    <div class="col-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-indigo-lt text-indigo">
                            <i class="ti ti-messages fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">TOTAL COMMENTS</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($totalComments) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-indigo-lt small fw-semibold stat-badge">
                            +{{ $commentsThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Second Row: Connected Accounts by Platform (3 Cards) --}}
<div class="row row-deck row-cards mb-3 dashboard-stat-grid">

    {{-- 1. TOTAL FB ACCOUNTS CONNECTED --}}
    <div class="col-6 col-md-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-primary-lt text-facebook">
                            <i class="ti ti-brand-facebook fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">FACEBOOK CONNECTED</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($fbAccountsCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-primary-lt small fw-semibold stat-badge">Facebook Pages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TOTAL INSTA CONNECTED --}}
    <div class="col-6 col-md-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-pink-lt text-pink">
                            <i class="ti ti-brand-instagram fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">INSTAGRAM CONNECTED</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($igAccountsCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-pink-lt small fw-semibold stat-badge">Instagram Business</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TOTAL YOUTUBE CONNECTED --}}
    <div class="col-12 col-md-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center stat-row">
                    <div class="col-auto stat-icon-col">
                        <span class="avatar avatar-md rounded-2 bg-danger-lt text-danger">
                            <i class="ti ti-brand-youtube fs-2"></i>
                        </span>
                    </div>
                    <div class="col stat-content-col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7 stat-label" style="letter-spacing: 0.05em;">YOUTUBE CONNECTED</div>
                        <div class="h2 mb-0 fw-bold text-body stat-value">{{ number_format($ytAccountsCount) }}</div>
                    </div>
                    <div class="col-auto text-end stat-badge-col">
                        <span class="badge bg-danger-lt small fw-semibold stat-badge">YouTube Channels</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Full-Width 30-Day Activity Trends Chart --}}
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">30-DAY SYSTEM THROUGHPUT & AI AUTOMATION</div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <span class="h2 fw-bold text-body mb-0">Platform Processing Trends</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-azure p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">Comments Ingested</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-teal p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">AI Replies</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-indigo p-1 rounded-circle" style="width: 8px; height: 8px;"></span>
                            <span class="text-secondary small">New Users</span>
                        </div>
                    </div>
                </div>
                <div id="chart-superadmin-activity" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>
</div>
