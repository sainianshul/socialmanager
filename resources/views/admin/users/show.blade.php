@extends('admin.layouts.app')

@section('title', ($user->name ?? $user->phone) . ' — User Profile')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => 'Users', 'url' => route('admin.users.index')],
        ['label' => $user->name ?? $user->phone],
    ]" />
@endsection

@section('content')

    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $user->name ?? $user->phone }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                    <i class="ti ti-pencil me-1"></i>Edit User
                </a>

                {{-- Status Change Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-settings me-1"></i>Status
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Change Account Status</h6></li>
                        @foreach (\App\Models\User::getStatusList() as $value => $label)
                            @if($user->status != $value)
                                <li>
                                    <a class="dropdown-item status-change-btn" href="#"
                                       data-id="{{ $user->id }}" data-status="{{ $value }}">
                                        @if($value == \App\Models\User::STATUS_ACTIVE)
                                            <i class="ti ti-check me-2 text-success"></i>
                                        @elseif($value == \App\Models\User::STATUS_BLOCKED)
                                            <i class="ti ti-ban me-2 text-danger"></i>
                                        @elseif($value == \App\Models\User::STATUS_SUSPENDED)
                                            <i class="ti ti-alert-triangle me-2 text-warning"></i>
                                        @endif
                                        {{ $label }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Header Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($user->profile_photo)
                        <span class="avatar avatar-xl rounded-circle" style="background-image: url({{ asset('storage/' . $user->profile_photo) }})"></span>
                    @else
                        <span class="avatar avatar-xl rounded-circle bg-primary-lt fs-2 fw-bold">
                            {{ mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)) }}
                        </span>
                    @endif
                </div>
                <div class="col">
                    <div class="d-flex align-items-center mb-1 flex-wrap gap-2">
                        <h2 class="mb-0 me-1">{{ $user->name ?? 'Unknown User' }}</h2>
                        <span class="badge bg-{{ $user->status_color }}-lt">
                            <i class="{{ $user->status_icon }} me-1"></i>{{ $user->status_name }}
                        </span>
                        @if($user->phone_verified_at)
                            <span class="badge bg-blue-lt"><i class="ti ti-circle-check me-1"></i>Phone Verified</span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-secondary small mt-1">
                        <span><i class="ti ti-phone me-1"></i>{{ $user->phone ?? 'N/A' }}</span>
                        @if($user->email)
                            <span><i class="ti ti-mail me-1"></i>{{ $user->email }}</span>
                        @endif
                        <span><i class="ti ti-map-pin me-1"></i>{{ $user->city ?? 'No City' }}@if($user->pincode), {{ $user->pincode }}@endif</span>
                        <span><i class="ti ti-clock me-1"></i>Joined {{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Card --}}
    <div class="card mb-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-overview" role="tab">
                        <i class="ti ti-user me-1"></i>Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-accounts" role="tab">
                        <i class="ti ti-plug-connected me-1"></i>Connected Accounts
                        <span class="badge border border-secondary bg-transparent text-secondary ms-1">{{ $accountsCount ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-posts" role="tab">
                        <i class="ti ti-photo me-1"></i>Posts & Content
                        <span class="badge border border-secondary bg-transparent text-secondary ms-1">{{ $postsCount ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-activity" role="tab">
                        <i class="ti ti-activity me-1"></i>User Activity
                        <span class="badge border border-primary bg-transparent text-primary ms-1">{{ $activityCount ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-devices" role="tab">
                        <i class="ti ti-devices me-1"></i>Devices & Sessions
                        <span class="badge border border-secondary bg-transparent text-secondary ms-1">{{ $devicesCount ?? 0 }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">

                {{-- Tab: Overview --}}
                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <h3 class="mb-3">Personal Details</h3>
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Full Name</div>
                                    <div class="datagrid-content">{{ $user->name ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Phone Number</div>
                                    <div class="datagrid-content">
                                        {{ $user->phone ?? 'N/A' }}
                                        @if($user->phone_verified_at)
                                            <span class="badge bg-green-lt ms-1"><i class="ti ti-check me-1"></i>Verified</span>
                                        @else
                                            <span class="badge bg-red-lt ms-1">Unverified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email Address</div>
                                    <div class="datagrid-content">{{ $user->email ?? 'Not provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Role</div>
                                    <div class="datagrid-content">
                                        <span class="badge badge-outline text-{{ $user->role_color }}">{{ $user->role_name }}</span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Account Status</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-{{ $user->status_color }}-lt">
                                            <i class="{{ $user->status_icon }} me-1"></i>{{ $user->status_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Created By</div>
                                    <div class="datagrid-content">
                                        @if(empty($user->created_by))
                                            Self Registered (OTP)
                                        @else
                                            Admin
                                        @endif
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Joined On</div>
                                    <div class="datagrid-content">{{ $user->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Last Login</div>
                                    <div class="datagrid-content">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h3 class="mb-3">Location Details</h3>
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Address</div>
                                    <div class="datagrid-content">{{ $user->address ?? 'Not provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">City</div>
                                    <div class="datagrid-content">{{ $user->city ?? 'Not provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">State</div>
                                    <div class="datagrid-content">{{ $user->state ?? 'Not provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Pincode</div>
                                    <div class="datagrid-content">{{ $user->pincode ?? 'Not provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Coordinates</div>
                                    <div class="datagrid-content">
                                        @if($user->latitude && $user->longitude)
                                            <code>{{ $user->latitude }}, {{ $user->longitude }}</code>
                                        @else
                                            Not provided
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PLAN FEATURES & TOKEN QUOTA CARD --}}
                        <div class="col-12 mt-4">
                            <div class="card border shadow-sm">
                                <div class="card-header bg-light py-2.5 d-flex justify-content-between align-items-center">
                                    <h4 class="card-title text-dark mb-0 d-flex align-items-center">
                                        <i class="ti ti-crown me-2 text-warning fs-3"></i>Plan Features & AI Usage
                                    </h4>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-edit me-1"></i>Manage Plan
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-7">
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge {{ $user->canSchedulePosts() ? 'bg-success-lt border border-success' : 'bg-secondary-lt border border-secondary text-muted' }} p-2">
                                                    <i class="ti {{ $user->canSchedulePosts() ? 'ti-check' : 'ti-x' }} me-1"></i>Post Scheduling
                                                </span>
                                                <span class="badge {{ $user->canUseFestivalStudio() ? 'bg-success-lt border border-success' : 'bg-secondary-lt border border-secondary text-muted' }} p-2">
                                                    <i class="ti {{ $user->canUseFestivalStudio() ? 'ti-check' : 'ti-x' }} me-1"></i>Festival Studio
                                                </span>
                                                <span class="badge {{ $user->canUseAutoDm() ? 'bg-success-lt border border-success' : 'bg-secondary-lt border border-secondary text-muted' }} p-2">
                                                    <i class="ti {{ $user->canUseAutoDm() ? 'ti-check' : 'ti-x' }} me-1"></i>Auto DM
                                                </span>
                                                <span class="badge {{ $user->canUseAi() ? 'bg-success-lt border border-success' : 'bg-secondary-lt border border-secondary text-muted' }} p-2">
                                                    <i class="ti {{ $user->canUseAi() ? 'ti-check' : 'ti-x' }} me-1"></i>AI Features
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small fw-bold text-dark">
                                                    <i class="ti ti-cpu me-1 text-primary"></i>AI Tokens: {{ number_format($user->ai_tokens_used ?? 0) }} / {{ $user->ai_token_quota ? number_format($user->ai_token_quota) : 'Unlimited' }}
                                                </span>
                                                <span class="badge bg-azure-lt">Est. ₹{{ number_format($user->estimatedAiCostInr(), 2) }}</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar {{ $user->aiQuotaPercentage() > 80 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $user->aiQuotaPercentage() }}%" role="progressbar"></div>
                                            </div>
                                            <div class="d-flex justify-content-between small text-muted mt-1">
                                                <span>{{ $user->aiQuotaPercentage() }}% Used</span>
                                                <span>Resets: {{ $user->ai_quota_resets_at ? $user->ai_quota_resets_at->format('d M') : '1st of month' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Connected Accounts --}}
                <div class="tab-pane fade" id="tab-accounts" role="tabpanel">
                    <div class="table-responsive py-3">
                        <table id="user-accounts-table" class="table table-vcenter table-hover w-100">
                            <thead>
                                <tr>
                                    <th class="w-1">S.No</th>
                                    <th>Channel / Page</th>
                                    <th>Followers</th>
                                    <th>Connection</th>
                                    <th>Token Status</th>
                                    <th>Last Synced</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: Posts & Content --}}
                <div class="tab-pane fade" id="tab-posts" role="tabpanel">
                    <div class="table-responsive py-3">
                        <table id="user-posts-table" class="table table-vcenter table-hover w-100">
                            <thead>
                                <tr>
                                    <th class="w-1">S.No</th>
                                    <th style="width: 50px;">Media</th>
                                    <th>Content / Title</th>
                                    <th style="width: 120px;">Platform</th>
                                    <th style="width: 90px;">Type</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 140px;">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: User Activity Datatable --}}
                <div class="tab-pane fade" id="tab-activity" role="tabpanel">
                    <div class="table-responsive py-3">
                        <table id="user-activity-table" class="table table-vcenter table-hover w-100">
                            <thead>
                                <tr>
                                    <th class="w-1">S.No</th>
                                    <th style="width: 140px;">Timestamp</th>
                                    <th style="width: 130px;">Event</th>
                                    <th>Action & Description</th>
                                    <th style="width: 150px;">Target Channel</th>
                                    <th style="width: 80px;">Duration</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 60px;" class="text-end">Details</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: Devices (Sanctum tokens) --}}
                <div class="tab-pane fade" id="tab-devices" role="tabpanel">
                    @php
                        $tokens = $user->tokens()->latest()->get();
                    @endphp
                    @if($tokens->count() > 0)
                        <div class="table-responsive py-4">
                            <table class="table table-sm table-vcenter" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>IP Address</th>
                                        <th>FCM Token</th>
                                        <th>Last Used</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tokens as $token)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm bg-secondary-lt rounded me-2">
                                                        <i class="ti ti-device-mobile"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-semibold">{{ $token->device_name ?? $token->name ?? 'Unknown Device' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">{{ $token->ip_address ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($token->fcm_token)
                                                    <span class="badge bg-green-lt border-transparent"><i class="ti ti-bell me-1"></i>Active</span>
                                                @else
                                                    <span class="badge bg-secondary-lt border-transparent">Not Set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-secondary">{{ $token->last_used_at ? $token->last_used_at->format('d M Y, H:i') : 'Never' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary">{{ $token->created_at->format('d M Y, H:i') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-revoke-token"
                                                    data-token-id="{{ $token->id }}"
                                                    data-bs-toggle="tooltip" title="Revoke Session">
                                                    <i class="ti ti-x me-1"></i> Revoke
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty py-5">
                            <div class="empty-icon">
                                <i class="ti ti-devices text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <p class="empty-title">No Active Devices</p>
                            <p class="empty-subtitle text-secondary">
                                This user does not have any active sessions / logged-in devices.
                            </p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Activity Log Detail Inspector Modal --}}
    <div class="modal modal-blur fade" id="userLogDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalUserLogAction">Activity Details</h5>
                        <div class="text-muted small" id="modalUserLogSubtitle"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-light-lt p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span id="modalUserLogStatusBadge"></span>
                            <span class="text-muted small">Duration: <strong id="modalUserLogDuration"></strong></span>
                            <span class="text-muted small">Channel: <strong id="modalUserLogAccount"></strong></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="copyUserLogBtn">
                            <i class="ti ti-copy me-1"></i> Copy Response
                        </button>
                    </div>
                    <div class="p-3">
                        <div class="mb-3" id="userPayloadSection">
                            <label class="form-label fw-bold small text-muted text-uppercase">Request / Payload Data</label>
                            <pre class="bg-dark text-white p-3 rounded font-monospace small mb-0" style="max-height: 220px; overflow-y: auto;" id="modalUserLogPayload"></pre>
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-muted text-uppercase">Response Output</label>
                            <pre class="bg-dark text-white p-3 rounded font-monospace small mb-0" style="max-height: 280px; overflow-y: auto;" id="modalUserLogResponse"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('datatables_css')
    @include('admin.layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('admin.layouts.partials._datatable-cdn-js')
    <script>
        $(function () {
            let accountsTable = null;
            let postsTable = null;
            let activityTable = null;

            function initAccountsTable() {
                if (accountsTable) return;
                accountsTable = $('#user-accounts-table').DataTable({
                    serverSide: true,
                    processing: false,
                    ajax: '{{ route('admin.users.accounts-data', $user->id) }}',
                    columns: [
                        { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                        { data: 'channel', name: 'name' },
                        { data: 'followers', name: 'followers_count' },
                        { data: 'status', name: 'is_active', orderable: false, searchable: false },
                        { data: 'token_status', name: 'token_expires_at', orderable: false, searchable: false },
                        { data: 'last_synced', name: 'last_synced_at' },
                    ],
                    order: [[5, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50], [10, 25, 50]],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ accounts',
                        paginate: { previous: '<i class="ti ti-chevron-left"></i>', next: '<i class="ti ti-chevron-right"></i>' },
                        emptyTable: 'No social accounts connected by this user.'
                    }
                });
            }

            function initPostsTable() {
                if (postsTable) return;
                postsTable = $('#user-posts-table').DataTable({
                    serverSide: true,
                    processing: false,
                    ajax: '{{ route('admin.users.posts-data', $user->id) }}',
                    columns: [
                        { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                        { data: 'media', name: 'media', orderable: false, searchable: false },
                        { data: 'content_preview', name: 'title' },
                        { data: 'platform', name: 'platform', orderable: false },
                        { data: 'type', name: 'post_type' },
                        { data: 'status', name: 'status' },
                        { data: 'time', name: 'created_at' },
                    ],
                    order: [[6, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50], [10, 25, 50]],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ posts',
                        paginate: { previous: '<i class="ti ti-chevron-left"></i>', next: '<i class="ti ti-chevron-right"></i>' },
                        emptyTable: 'No posts created by this user.'
                    }
                });
            }

            function initActivityTable() {
                if (activityTable) return;
                activityTable = $('#user-activity-table').DataTable({
                    serverSide: true,
                    processing: false,
                    ajax: '{{ route('admin.users.activity-data', $user->id) }}',
                    columns: [
                        { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'event_type', name: 'event_type' },
                        { data: 'action', name: 'action' },
                        { data: 'target', name: 'target', orderable: false, searchable: false },
                        { data: 'duration_ms', name: 'duration_ms' },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
                    ],
                    order: [[1, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50], [10, 25, 50]],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ activity logs',
                        paginate: { previous: '<i class="ti ti-chevron-left"></i>', next: '<i class="ti ti-chevron-right"></i>' },
                        emptyTable: 'No activity recorded for this user yet.'
                    }
                });
            }

            // Tab switch handler for responsive table drawing & lazy loading
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                let target = $(e.target).attr('href');
                if (target === '#tab-accounts') {
                    initAccountsTable();
                } else if (target === '#tab-posts') {
                    initPostsTable();
                } else if (target === '#tab-activity') {
                    initActivityTable();
                }
                setTimeout(function () {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                }, 50);
            });

            // Activity Log Inspector Modal
            let currentLogResponse = '';

            function populateUserLogModal(btn) {
                if (!btn) return;
                const logId = btn.getAttribute('data-log-id') || $(btn).data('log-id');
                const action = btn.getAttribute('data-action') || $(btn).data('action') || 'Activity Details';
                const event = btn.getAttribute('data-event') || $(btn).data('event') || '';
                const status = btn.getAttribute('data-status') || $(btn).data('status') || '';
                const time = btn.getAttribute('data-time') || $(btn).data('time') || '';
                const duration = btn.getAttribute('data-duration') || $(btn).data('duration') || 'N/A';
                const account = btn.getAttribute('data-account') || $(btn).data('account') || 'System';

                const payloadEl = document.getElementById('user-log-raw-payload-' + logId);
                const responseEl = document.getElementById('user-log-raw-response-' + logId);
                const payload = payloadEl ? payloadEl.textContent.trim() : '';
                const response = responseEl ? responseEl.textContent.trim() : '';

                $('#modalUserLogAction').text(action);
                $('#modalUserLogSubtitle').text(`${event} • ${time}`);
                $('#modalUserLogDuration').text(duration);
                $('#modalUserLogAccount').text(account);

                const statusBadge = document.getElementById('modalUserLogStatusBadge');
                if (statusBadge) {
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
                }

                if (payload && payload !== 'null' && payload !== '[]') {
                    $('#modalUserLogPayload').text(payload);
                    $('#userPayloadSection').show();
                } else {
                    $('#userPayloadSection').hide();
                }

                currentLogResponse = response || 'No response data recorded.';
                $('#modalUserLogResponse').text(currentLogResponse);
            }

            $(document).on('click', '.view-user-log-btn', function (e) {
                e.preventDefault();
                populateUserLogModal(this);
                const modalEl = document.getElementById('userLogDetailModal');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    inst.show();
                }
            });

            $('#copyUserLogBtn').on('click', function () {
                if (!currentLogResponse) return;
                navigator.clipboard.writeText(currentLogResponse).then(() => {
                    const btn = $(this);
                    const orig = btn.html();
                    btn.html('<i class="ti ti-check me-1"></i> Copied!');
                    setTimeout(() => btn.html(orig), 2000);
                });
            });
        });
    </script>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Status Change
        $('.status-change-btn').on('click', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            const status = $(this).data('status');
            
            Swal.fire({
                title: 'Change Status?',
                text: 'Are you sure you want to change this user status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/admin/users/${userId}/status`, {
                        _token: '{{ csrf_token() }}',
                        status: status
                    }).done(function(res) {
                        Swal.fire('Success', res.message || 'Status updated', 'success')
                            .then(() => window.location.reload());
                    }).fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update status', 'error');
                    });
                }
            });
        });

        // Revoke Device Token
        $('.btn-revoke-token').on('click', function(e) {
            e.preventDefault();
            const tokenId = $(this).data('token-id');

            Swal.fire({
                title: 'Revoke Device Session?',
                text: 'The user will be logged out on that device.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d63939',
                confirmButtonText: 'Yes, Revoke',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('admin.users.revoke-token', $user->id) }}', {
                        _token: '{{ csrf_token() }}',
                        token_id: tokenId
                    }).done(function (res) {
                        Swal.fire('Revoked!', res.message, 'success').then(() => window.location.reload());
                    }).fail(function (xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                    });
                }
            });
        });
    });
</script>
@endpush
