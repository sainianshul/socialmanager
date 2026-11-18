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

{{-- Top Row: 4 Primary SaaS Metric Cards --}}
<div class="row row-deck row-cards mb-3">

    {{-- 1. TOTAL REGISTERED USERS --}}
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('admin.users.index') }}" class="card card-sm card-link text-decoration-none" title="Manage Users">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                            <i class="ti ti-users fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL USERS</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalUsers) }}</div>
                        <div class="text-secondary small mt-0.5">
                            {{ $activeUsersCount }} Active • {{ $blockedUsersCount }} Blocked
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-primary-lt small fw-semibold">
                            +{{ $newUsersThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- 2. TOTAL AI REPLIES DISPATCHED --}}
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
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">AI REPLIES</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalAiReplies) }}</div>
                        <div class="text-secondary small mt-0.5">
                            {{ number_format($aiRepliesToday) }} generated today
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-teal-lt small fw-semibold">
                            +{{ $aiRepliesThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. CONNECTED CHANNELS ACROSS SYSTEM --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                            <i class="ti ti-plug-connected fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">CHANNELS CONNECTED</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalAccounts) }}</div>
                        <div class="text-secondary small mt-0.5">
                            {{ $fbAccountsCount }} FB • {{ $igAccountsCount }} IG • {{ $ytAccountsCount }} YT
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-azure-lt small fw-semibold">
                            System Wide
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TOTAL BROADCASTS & POSTS --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                            <i class="ti ti-broadcast fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL POSTS</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalPosts) }}</div>
                        <div class="text-secondary small mt-0.5">
                            {{ $scheduledCount }} Queued • {{ $publishedScheduledCount }} Published
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-success-lt small fw-semibold">
                            +{{ $postsThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Second Row: 4 Operational Health & Moderation Cards --}}
<div class="row row-deck row-cards mb-3">

    {{-- 5. TOTAL COMMENTS PROCESSED --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-2 bg-indigo-lt text-indigo">
                            <i class="ti ti-messages fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">COMMENTS PROCESSED</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($totalComments) }}</div>
                        <div class="text-secondary small mt-0.5">
                            {{ number_format($commentsToday) }} ingested today
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-indigo-lt small fw-semibold">
                            +{{ $commentsThisMonth }} this mo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. TROLLS & ABUSE BLOCKED --}}
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
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TROLLS / ABUSE BLOCKED</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($hiddenTrollsCount) }}</div>
                        <div class="text-secondary small mt-0.5">
                            Auto-moderated & shielded
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-danger-lt small fw-semibold">
                            Auto-Shield
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 7. CONSTITUENT GRIEVANCES --}}
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
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">GRIEVANCES FLAGGED</div>
                        <div class="h2 mb-0 fw-bold text-body">{{ number_format($grievancesCount) }}</div>
                        <div class="text-secondary small mt-0.5">
                            Priority citizen feedback
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-warning-lt small fw-semibold">
                            Actionable
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 8. SYSTEM RUNTIME --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded-2 bg-green-lt text-green">
                            <i class="ti ti-cpu fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">SYSTEM ENGINE</div>
                        <div class="h2 mb-0 fw-bold text-body">100% Active</div>
                        <div class="text-secondary small mt-0.5">
                            Cron worker & APIs healthy
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <span class="badge bg-green-lt small fw-semibold">
                            Healthy
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Middle Row: 30-Day Activity Trends & Platform Infrastructure Breakdown --}}
<div class="row row-deck row-cards mb-3">

    {{-- Left: 30-Day Activity Trends Line Chart --}}
    <div class="col-lg-8">
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

    {{-- Right: Platform Infrastructure & AI Efficiency --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title fw-bold text-body mb-0">Platform Infrastructure</h3>
            </div>
            <div class="card-body">
                
                {{-- AI Automation Rate --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-body small">AI Automation Efficiency</span>
                        <span class="fw-bold text-teal small">{{ $totalComments > 0 ? round(($totalAiReplies / $totalComments) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-teal" style="width: {{ $totalComments > 0 ? min(100, round(($totalAiReplies / $totalComments) * 100, 1)) : 0 }}%"></div>
                    </div>
                    <div class="text-secondary fs-7 mt-1">Percentage of constituent interactions handled by AI engine.</div>
                </div>

                {{-- Connected Channels Breakdown --}}
                <div class="mb-3">
                    <div class="text-uppercase text-secondary fw-semibold fs-7 mb-2" style="letter-spacing: 0.05em;">CHANNEL BREAKDOWN</div>
                    
                    {{-- Facebook --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-brand-facebook text-facebook fs-2"></i>
                            <span class="text-body small">Facebook Pages</span>
                        </div>
                        <span class="badge bg-primary-lt fw-bold">{{ $fbAccountsCount }}</span>
                    </div>

                    {{-- Instagram --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-brand-instagram text-pink fs-2"></i>
                            <span class="text-body small">Instagram Business</span>
                        </div>
                        <span class="badge bg-pink-lt fw-bold">{{ $igAccountsCount }}</span>
                    </div>

                    {{-- YouTube --}}
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-brand-youtube text-danger fs-2"></i>
                            <span class="text-body small">YouTube Channels</span>
                        </div>
                        <span class="badge bg-danger-lt fw-bold">{{ $ytAccountsCount }}</span>
                    </div>
                </div>

                <hr class="my-3">

                {{-- User Status Breakdown --}}
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-body small">Active User Ratio</span>
                        <span class="text-secondary small">{{ $activeUsersCount }} of {{ $totalUsers }} active</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: {{ $totalUsers > 0 ? round(($activeUsersCount / $totalUsers) * 100, 1) : 0 }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ $totalUsers > 0 ? round(($blockedUsersCount / $totalUsers) * 100, 1) : 0 }}%"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Bottom Row: Recently Registered Users & Live Interactions Feed --}}
<div class="row row-deck row-cards">

    {{-- Left: Recently Registered Users Table --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title fw-bold text-body mb-0">Recent Registered Users</h3>
                    <div class="text-secondary small">Latest clients and political managers enrolled in system</div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-users me-1"></i>View All ({{ $totalUsers }})
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Channels</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="w-1 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {!! $u->avatar_html !!}
                                        <div class="ms-2.5">
                                            <div class="fw-semibold text-body">{{ $u->name }}</div>
                                            <div class="text-secondary fs-7">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt fw-semibold">
                                        <i class="ti ti-plug me-1"></i>{{ $u->accounts_count }} Channels
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $u->status_color }}-lt fw-semibold">
                                        <i class="{{ $u->status_icon }} me-1"></i>{{ $u->status_name }}
                                    </span>
                                </td>
                                <td class="text-secondary small">
                                    {{ $u->created_at ? $u->created_at->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-ghost-secondary">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-secondary small">
                                    <i class="ti ti-users-minus fs-1 text-muted d-block mb-2"></i>
                                    No registered clients yet. Click "+ Add New User" above to create client accounts.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Live Comment & AI Interaction Feed --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title fw-bold text-body mb-0">Live AI & Constituent Stream</h3>
                    <div class="text-secondary small">Real-time interactions across all client accounts</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentComments as $c)
                        <div class="list-group-item px-3 py-3">
                            <div class="d-flex align-items-start">
                                <span class="avatar avatar-sm rounded-circle me-2.5 flex-shrink-0 bg-primary-lt text-primary">
                                    <i class="ti {{ $c->account?->account_type == 2 ? 'ti-brand-instagram text-pink' : ($c->account?->account_type == 3 ? 'ti-brand-youtube text-danger' : 'ti-brand-facebook text-facebook') }}"></i>
                                </span>
                                <div class="flex-grow-1 text-truncate pe-2">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-semibold text-body small text-truncate">{{ $c->commenter_name ?: 'Constituent' }}</span>
                                        <span class="text-secondary fs-7">{{ $c->created_at ? $c->created_at->diffForHumans() : 'Just now' }}</span>
                                    </div>
                                    <div class="text-secondary small text-truncate mb-1">
                                        "{{ Str::limit($c->message, 70) }}"
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-secondary-lt fs-7">
                                            {{ $c->account?->name ?? 'Channel' }}
                                        </span>
                                        @if($c->account?->user)
                                            <span class="badge bg-primary-lt fs-7">
                                                User: {{ $c->account->user->name }}
                                            </span>
                                        @endif
                                        @if($c->status === \App\Models\Comment::STATUS_REPLIED || !empty($c->ai_reply))
                                            <span class="badge bg-teal-lt fs-7">
                                                <i class="ti ti-robot me-0.5"></i>AI Replied
                                            </span>
                                        @elseif($c->is_abusive || $c->status === \App\Models\Comment::STATUS_HIDDEN)
                                            <span class="badge bg-danger-lt fs-7">
                                                <i class="ti ti-shield me-0.5"></i>Troll Hidden
                                            </span>
                                        @elseif($c->is_complaint)
                                            <span class="badge bg-warning-lt fs-7">
                                                <i class="ti ti-alert-triangle me-0.5"></i>Grievance
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-secondary small">
                            <i class="ti ti-messages-off fs-1 text-muted d-block mb-2"></i>
                            No constituent comments ingested yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
