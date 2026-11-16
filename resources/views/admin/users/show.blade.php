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
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-devices" role="tab">
                        <i class="ti ti-devices me-1"></i>Devices & Sessions
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
                                        <span class="badge bg-blue-lt">{{ $user->role_name }}</span>
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

@endsection

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
