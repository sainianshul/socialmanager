@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => route('admin.users.index'), 'label' => 'Users'],
        ['label' => 'Edit User'],
    ]" />
@endsection

@section('content')

    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Edit User: {{ $user->name ?? $user->email }}</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.users.index') }}" class="btn">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label required">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="user@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave empty to keep existing password">
                        <small class="form-hint">Leave blank if you don't want to change the password.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number (Optional)</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 9876543210">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                        $selectedRole = (int) old('role', $user->role);
                        $isUserRole = $selectedRole === \App\Models\User::ROLE_USER;
                    @endphp

                    <div class="col-md-4">
                        <label class="form-label required">Role</label>
                        <select name="role" id="user-role-select" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach (\App\Models\User::getRoleList() as $val => $label)
                                @if ($val !== \App\Models\User::ROLE_ADMIN || auth()->user()?->isAdmin())
                                    <option value="{{ $val }}" {{ $selectedRole == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (\App\Models\User::getStatusList() as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $user->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 {{ $isUserRole ? '' : 'd-none' }}" id="client-type-wrapper">
                        <label class="form-label required d-flex align-items-center">
                            <span>Client / Account Type</span>
                            <i class="ti ti-info-circle text-muted ms-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Defines branding and festival templates profile."></i>
                        </label>
                        <select name="client_type" id="client-type-select" class="form-select @error('client_type') is-invalid @enderror" {{ $isUserRole ? 'required' : 'disabled' }}>
                            @foreach (\App\Models\User::getClientTypeList() as $val => $label)
                                <option value="{{ $val }}" {{ old('client_type', $user->client_type ?? \App\Models\User::CLIENT_TYPE_POLITICIAN) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('client_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.users.partials._plan_features', ['user' => $user])
                    @include('admin.users.partials._manager_permissions', ['user' => $user])

                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Update User
                </button>
            </div>
        </form>
    </div>

@endsection

@include('admin.users.partials._role_toggle_script')
