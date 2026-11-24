@extends('admin.layouts.app')

@section('title', 'System Activity & Queue Monitor')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Infrastructure & Diagnostics</div>
            <h2 class="page-title d-flex align-items-center gap-2">
                <i class="ti ti-activity text-primary"></i>
                System Activity & Queue Monitor
            </h2>
        </div>
        <div class="col-auto ms-auto d-flex align-items-center gap-2">
            <a href="{{ request()->fullUrl() }}" class="btn btn-outline-secondary">
                <i class="ti ti-refresh me-1"></i> Refresh
            </a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#pruneLogsModal">
                <i class="ti ti-trash me-1"></i> Clean Logs
            </button>
        </div>
    </div>
</div>

{{-- Flash Feedback Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-2 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-2 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Top 4 Metric Overview Cards --}}
<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-azure-lt text-azure avatar">
                            <i class="ti ti-clock-play fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Pending Queue Jobs</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <div class="h2 mb-0">{{ number_format($stats['pending_jobs']) }}</div>
                            <span class="badge border border-info bg-transparent text-info font-weight-normal text-uppercase" style="font-size: 0.72rem;">
                                {{ $stats['driver'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-{{ $stats['failed_jobs'] > 0 ? 'danger' : 'success' }}-lt text-{{ $stats['failed_jobs'] > 0 ? 'danger' : 'success' }} avatar">
                            <i class="ti ti-alert-triangle fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Failed Queue Jobs</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <div class="h2 mb-0 text-{{ $stats['failed_jobs'] > 0 ? 'danger' : 'success' }}">
                                {{ number_format($stats['failed_jobs']) }}
                            </div>
                            @if($stats['failed_jobs'] > 0)
                                <span class="badge border border-danger bg-transparent text-danger">Action Needed</span>
                            @else
                                <span class="badge border border-success bg-transparent text-success">Healthy</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-teal-lt text-teal avatar">
                            <i class="ti ti-activity fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Activity Events (Today)</div>
                        <div class="h2 mb-0 text-teal">{{ number_format($stats['activity_today']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-purple-lt text-purple avatar">
                            <i class="ti ti-refresh-dot fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Account Syncs (Today)</div>
                        <div class="h2 mb-0 text-purple">{{ number_format($stats['sync_today']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Unified Tabbed Navigation: Activity Logs & Queue Monitor --}}
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#tab-logs" class="nav-link {{ $activeTab === 'logs' ? 'active' : '' }} d-flex align-items-center gap-2" data-bs-toggle="tab" role="tab" aria-selected="{{ $activeTab === 'logs' ? 'true' : 'false' }}">
                    <i class="ti ti-list-details fs-2"></i>
                    <span>Activity & Sync Logs</span>
                    <span class="badge border border-secondary bg-transparent text-muted ms-1">{{ $logs->total() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#tab-queue" class="nav-link {{ $activeTab === 'queue' ? 'active' : '' }} d-flex align-items-center gap-2" data-bs-toggle="tab" role="tab" aria-selected="{{ $activeTab === 'queue' ? 'true' : 'false' }}">
                    <i class="ti ti-cpu fs-2"></i>
                    <span>Queue Monitor</span>
                    @if($stats['failed_jobs'] > 0)
                        <span class="badge bg-danger text-white ms-1">{{ $stats['failed_jobs'] }}</span>
                    @else
                        <span class="badge border border-success bg-transparent text-success ms-1">0</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        {{-- ======================================================== --}}
        {{-- TAB 1: SYSTEM ACTIVITY & SYNC LOGS                       --}}
        {{-- ======================================================== --}}
        <div class="tab-pane {{ $activeTab === 'logs' ? 'active show' : '' }}" id="tab-logs" role="tabpanel">
            {{-- Search & Filtering Toolbar --}}
            <div class="card-body border-bottom py-3">
                <form action="{{ route('admin.system-activity.index') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="logs">
                    
                    <div class="col-12 col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search action, response, user or account..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <select name="event_type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Event Types</option>
                            @foreach($eventTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('event_type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        @if(request('search') || request('event_type') || request('status') || request('account_id'))
                            <a href="{{ route('admin.system-activity.index', ['tab' => 'logs']) }}" class="btn btn-outline-secondary" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Activity Logs Table --}}
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                    <thead>
                        <tr>
                            <th style="width: 170px;">Timestamp</th>
                            <th style="width: 140px;">Event</th>
                            <th>Action & Description</th>
                            <th>Target / Account</th>
                            <th style="width: 90px;">Duration</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 90px;" class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-muted small">
                                    <div>{{ $log->created_at->diffForHumans() }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem;">{{ $log->created_at->format('d M Y, h:i A') }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match($log->event_type) {
                                            'account_sync'    => 'primary',
                                            'post_publish'    => 'success',
                                            'canva'           => 'cyan',
                                            'auto_dm'         => 'purple',
                                            'comment_process' => 'teal',
                                            'webhook'         => 'azure',
                                            'queue_job'       => 'warning',
                                            default           => 'secondary',
                                        };
                                        $eventLabel = $eventTypes[$log->event_type] ?? ucwords(str_replace('_', ' ', $log->event_type));
                                    @endphp
                                    <span class="badge border border-{{ $badgeColor }} bg-transparent text-{{ $badgeColor }}">
                                        {{ $eventLabel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 320px;" title="{{ $log->action }}">
                                        {{ $log->action }}
                                    </div>
                                    @if($log->status === 'failed' && $log->response)
                                        <div class="text-danger small text-truncate" style="max-width: 320px;">
                                            <i class="ti ti-alert-circle me-1"></i>{{ Str::limit(strip_tags($log->response), 80) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($log->account)
                                        <div class="d-flex align-items-center gap-1">
                                            @if($log->account->isFacebook())
                                                <i class="ti ti-brand-facebook text-facebook"></i>
                                            @elseif($log->account->isInstagram())
                                                <i class="ti ti-brand-instagram text-pink"></i>
                                            @elseif($log->account->isYouTube())
                                                <i class="ti ti-brand-youtube text-danger"></i>
                                            @else
                                                <i class="ti ti-plug text-muted"></i>
                                            @endif
                                            <span class="fw-medium text-truncate" style="max-width: 160px;" title="{{ $log->account->name }}">
                                                {{ $log->account->name }}
                                            </span>
                                        </div>
                                    @elseif($log->user)
                                        <div class="text-muted small">
                                            <i class="ti ti-user me-1"></i>{{ $log->user->name }}
                                        </div>
                                    @else
                                        <span class="text-muted small">System Worker</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->duration_ms)
                                        <span class="badge border border-secondary bg-transparent text-muted">
                                            {{ $log->duration_ms }}ms
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="badge border border-success bg-transparent text-success">
                                            <i class="ti ti-check me-1"></i> Success
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge border border-danger bg-transparent text-danger">
                                            <i class="ti ti-x me-1"></i> Failed
                                        </span>
                                    @else
                                        <span class="badge border border-warning bg-transparent text-warning">
                                            <i class="ti ti-clock me-1"></i> {{ ucfirst($log->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-ghost-secondary px-2 view-log-btn"
                                            data-action="{{ $log->action }}"
                                            data-event="{{ $eventLabel }}"
                                            data-status="{{ $log->status }}"
                                            data-time="{{ $log->created_at->format('d M Y, h:i:s A') }}"
                                            data-duration="{{ $log->duration_ms ? $log->duration_ms . ' ms' : 'N/A' }}"
                                            data-account="{{ $log->account->name ?? 'System' }}"
                                            data-ip="{{ $log->ip_address ?? 'N/A' }}"
                                            data-payload="{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}"
                                            data-response="{{ $log->response }}"
                                            title="View Details">
                                        <i class="ti ti-code fs-3"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-clipboard-list fs-1 d-block mb-2 text-secondary"></i>
                                    <div>No activity logs found matching the filter criteria.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <p class="m-0 text-muted small">
                        Showing <span>{{ $logs->firstItem() ?? 0 }}</span> to <span>{{ $logs->lastItem() ?? 0 }}</span> of <span>{{ $logs->total() }}</span> entries
                    </p>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ======================================================== --}}
        {{-- TAB 2: QUEUE MONITOR & FAILED JOBS                       --}}
        {{-- ======================================================== --}}
        <div class="tab-pane {{ $activeTab === 'queue' ? 'active show' : '' }}" id="tab-queue" role="tabpanel">
            {{-- Queue Diagnostics Header Bar --}}
            <div class="card-body border-bottom bg-light-lt py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div>
                            <span class="text-muted small d-block">Queue Driver</span>
                            <span class="fw-bold text-uppercase">{{ $stats['driver'] }}</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Connection Status</span>
                            @if($stats['connection_status'] === 'online' || $stats['connection_status'] === 'sync_mode')
                                <span class="badge border border-success bg-transparent text-success">
                                    <i class="ti ti-circle-filled text-success me-1" style="font-size: 8px;"></i> Online
                                </span>
                            @elseif($stats['connection_status'] === 'extension_missing')
                                <span class="badge border border-warning bg-transparent text-warning">
                                    <i class="ti ti-alert-circle me-1"></i> PHP Redis Not Loaded (Local Dev)
                                </span>
                            @else
                                <span class="badge border border-danger bg-transparent text-danger">
                                    <i class="ti ti-alert-triangle me-1"></i> {{ $stats['connection_status'] }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted small d-block">Monitored Queues</span>
                            <span class="fw-medium">{{ implode(', ', $stats['active_queues']) }}</span>
                        </div>
                    </div>

                    @if($stats['failed_jobs'] > 0)
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.system-activity.retry-all') }}" method="POST" onsubmit="return confirm('Retry all {{ $stats['failed_jobs'] }} failed jobs now?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-rotate-clockwise me-1"></i> Retry All ({{ $stats['failed_jobs'] }})
                                </button>
                            </form>
                            <form action="{{ route('admin.system-activity.clear-failed') }}" method="POST" onsubmit="return confirm('Clear and purge ALL failed jobs permanently?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="ti ti-trash me-1"></i> Flush All Failed
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Failed Jobs Table --}}
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Job Handler</th>
                            <th style="width: 120px;">Queue</th>
                            <th>Failure Reason</th>
                            <th style="width: 160px;">Failed At</th>
                            <th style="width: 160px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($failedJobs as $job)
                            <tr>
                                <td class="text-muted font-monospace">#{{ $job->id }}</td>
                                <td>
                                    <div class="fw-semibold font-monospace small" title="{{ $job->display_name }}">
                                        {{ class_basename($job->display_name) }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        UUID: {{ Str::limit($job->uuid, 16) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge border border-secondary bg-transparent text-secondary">
                                        {{ $job->queue }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-danger small font-monospace text-truncate" style="max-width: 420px;" title="{{ $job->exception_headline }}">
                                        {{ $job->exception_headline }}
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        {{-- View Exception Modal Trigger --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-ghost-secondary px-2 view-job-btn"
                                                data-id="{{ $job->id }}"
                                                data-uuid="{{ $job->uuid }}"
                                                data-name="{{ $job->display_name }}"
                                                data-queue="{{ $job->queue }}"
                                                data-failed-at="{{ $job->failed_at }}"
                                                data-payload="{{ json_encode($job->payload_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}"
                                                data-exception="{{ $job->exception }}"
                                                title="View Error Details">
                                            <i class="ti ti-eye fs-3"></i>
                                        </button>

                                        {{-- Retry Single Job --}}
                                        <form action="{{ route('admin.system-activity.retry', $job->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-ghost-primary px-2" title="Retry this job">
                                                <i class="ti ti-rotate-clockwise fs-3"></i>
                                            </button>
                                        </form>

                                        {{-- Delete Single Job --}}
                                        <form action="{{ route('admin.system-activity.delete-failed', $job->id) }}" method="POST" onsubmit="return confirm('Delete failed job #{{ $job->id }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost-danger px-2" title="Delete job">
                                                <i class="ti ti-trash fs-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-success mb-2">
                                        <i class="ti ti-circle-check fs-1"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1">Queue is Running Cleanly!</h4>
                                    <p class="text-muted mb-0">No failed jobs recorded. Background workers are processing tasks smoothly.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($failedJobs->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <p class="m-0 text-muted small">
                        Showing <span>{{ $failedJobs->firstItem() ?? 0 }}</span> to <span>{{ $failedJobs->lastItem() ?? 0 }}</span> of <span>{{ $failedJobs->total() }}</span> failed jobs
                    </p>
                    <div>
                        {{ $failedJobs->appends(['tab' => 'queue'])->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL 1: ACTIVITY LOG PAYLOAD & RESPONSE INSPECTOR           --}}
{{-- ============================================================ --}}
<div class="modal modal-blur fade" id="logDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="modalLogAction">Log Details</h5>
                    <div class="text-muted small" id="modalLogSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light-lt p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <span id="modalLogStatusBadge"></span>
                        <span class="text-muted small">Duration: <strong id="modalLogDuration"></strong></span>
                        <span class="text-muted small">Account: <strong id="modalLogAccount"></strong></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="copyLogBtn">
                        <i class="ti ti-copy me-1"></i> Copy Response
                    </button>
                </div>

                <div class="p-3">
                    <div class="mb-3" id="payloadSection">
                        <label class="form-label fw-bold small text-muted text-uppercase">Request / Payload Data</label>
                        <pre class="bg-dark text-white p-3 rounded font-monospace small mb-0" style="max-height: 220px; overflow-y: auto;" id="modalLogPayload"></pre>
                    </div>

                    <div>
                        <label class="form-label fw-bold small text-muted text-uppercase">Response / Error Output</label>
                        <pre class="bg-dark text-white p-3 rounded font-monospace small mb-0" style="max-height: 300px; overflow-y: auto;" id="modalLogResponse"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL 2: FAILED QUEUE JOB EXCEPTION & TRACE INSPECTOR        --}}
{{-- ============================================================ --}}
<div class="modal modal-blur fade" id="jobDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="modalJobName">Failed Job</h5>
                    <div class="text-muted small" id="modalJobMeta"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Exception & Stack Trace</label>
                    <pre class="bg-dark text-danger p-3 rounded font-monospace small mb-0" style="max-height: 340px; overflow-y: auto; white-space: pre-wrap;" id="modalJobException"></pre>
                </div>
                <div>
                    <label class="form-label fw-bold small text-muted text-uppercase">Serialized Job Payload</label>
                    <pre class="bg-dark text-white p-3 rounded font-monospace small mb-0" style="max-height: 200px; overflow-y: auto;" id="modalJobPayload"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL 3: PRUNE ACTIVITY LOGS MODAL                           --}}
{{-- ============================================================ --}}
<div class="modal modal-blur fade" id="pruneLogsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.system-activity.clear-logs') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="ti ti-trash me-1"></i> Clean System Activity Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    Regularly pruning old activity logs keeps database indices compact and lightning-fast. Select your desired retention period:
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Retention Period</label>
                    <select name="days" class="form-select">
                        <option value="30" selected>Delete logs older than 30 days (Recommended)</option>
                        <option value="14">Delete logs older than 14 days</option>
                        <option value="7">Delete logs older than 7 days</option>
                        <option value="0" class="text-danger">Purge ALL logs (Wipe entirely)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm & Clean</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document-ready: document.addEventListener('DOMContentLoaded', function () {
    // 1. Activity Log Detail Inspector
    const logModalEl = document.getElementById('logDetailModal');
    const logModal = logModalEl ? new bootstrap.Modal(logModalEl) : null;
    let currentResponseText = '';

    document.querySelectorAll('.view-log-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const event = this.dataset.event;
            const status = this.dataset.status;
            const time = this.dataset.time;
            const duration = this.dataset.duration;
            const account = this.dataset.account;
            const payload = this.dataset.payload;
            const response = this.dataset.response;

            document.getElementById('modalLogAction').textContent = action;
            document.getElementById('modalLogSubtitle').textContent = `${event} • ${time}`;
            document.getElementById('modalLogDuration').textContent = duration;
            document.getElementById('modalLogAccount').textContent = account;

            // Status Badge
            const statusBadge = document.getElementById('modalLogStatusBadge');
            if (status === 'success') {
                statusBadge.className = 'badge border border-success bg-transparent text-success';
                statusBadge.innerHTML = '<i class="ti ti-check me-1"></i> Success';
            } else if (status === 'failed') {
                statusBadge.className = 'badge border border-danger bg-transparent text-danger';
                statusBadge.innerHTML = '<i class="ti ti-x me-1"></i> Failed';
            } else {
                statusBadge.className = 'badge border border-warning bg-transparent text-warning';
                statusBadge.innerHTML = status;
            }

            // Payload
            const payloadSection = document.getElementById('payloadSection');
            const payloadPre = document.getElementById('modalLogPayload');
            if (payload && payload !== 'null' && payload !== '[]') {
                payloadPre.textContent = payload;
                payloadSection.style.display = 'block';
            } else {
                payloadSection.style.display = 'none';
            }

            // Response
            currentResponseText = response || 'No response data recorded.';
            document.getElementById('modalLogResponse').textContent = currentResponseText;

            if (logModal) logModal.show();
        });
    });

    // Copy Response text
    const copyBtn = document.getElementById('copyLogBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            if (!currentResponseText) return;
            navigator.clipboard.writeText(currentResponseText).then(() => {
                const orig = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="ti ti-check me-1"></i> Copied!';
                setTimeout(() => copyBtn.innerHTML = orig, 2000);
            });
        });
    }

    // 2. Failed Queue Job Inspector
    const jobModalEl = document.getElementById('jobDetailModal');
    const jobModal = jobModalEl ? new bootstrap.Modal(jobModalEl) : null;

    document.querySelectorAll('.view-job-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const uuid = this.dataset.uuid;
            const name = this.dataset.name;
            const queue = this.dataset.queue;
            const failedAt = this.dataset.failedAt;
            const payload = this.dataset.payload;
            const exception = this.dataset.exception;

            document.getElementById('modalJobName').textContent = name;
            document.getElementById('modalJobMeta').textContent = `Job #${id} • Queue: ${queue} • UUID: ${uuid} • Failed: ${failedAt}`;
            document.getElementById('modalJobException').textContent = exception || 'No stack trace available.';
            document.getElementById('modalJobPayload').textContent = payload || 'No payload data.';

            if (jobModal) jobModal.show();
        });
    });
});
</script>
@endpush
@endsection
