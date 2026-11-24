@extends('admin.layouts.app')

@section('title', 'Add User')

@section('content')

    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Add New User</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.users.index') }}" class="btn">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label required">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter full name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="user@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave empty to auto-generate random password">
                        <small class="form-hint">Minimum 6 characters. Leave blank for auto-generated password.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number (Optional)</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 9876543210">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required">Client / Account Type</label>
                        <select name="client_type" class="form-select @error('client_type') is-invalid @enderror" required>
                            @foreach (\App\Models\User::getClientTypeList() as $val => $label)
                                <option value="{{ $val }}" {{ old('client_type', \App\Models\User::CLIENT_TYPE_POLITICIAN) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="form-hint">Defines branding and festival templates profile.</small>
                        @error('client_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach (\App\Models\User::getRoleList() as $val => $label)
                                @if ($val !== \App\Models\User::ROLE_ADMIN)
                                    <option value="{{ $val }}" {{ old('role', \App\Models\User::ROLE_USER) == $val ? 'selected' : '' }}>{{ $label }}</option>
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
                                <option value="{{ $val }}" {{ old('status', \App\Models\User::STATUS_ACTIVE) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PLAN FEATURES & AI TOKEN QUOTA --}}
                    <div class="col-12 mt-4">
                        <div class="card bg-light-lt border shadow-none">
                            <div class="card-header bg-transparent py-2.5">
                                <h4 class="card-title text-dark mb-0 d-flex align-items-center">
                                    <i class="ti ti-toggle-right me-2 text-primary fs-3"></i>Plan Features & AI Quota
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-check form-switch cursor-pointer mb-1">
                                            <input class="form-check-input" type="checkbox" name="can_schedule_posts" value="1" {{ old('can_schedule_posts', '1') ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold text-dark">Post Scheduling</span>
                                        </label>
                                        <small class="form-hint text-muted d-block">Multi-account scheduling & publishing</small>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-check form-switch cursor-pointer mb-1">
                                            <input class="form-check-input" type="checkbox" name="can_use_festival_studio" value="1" {{ old('can_use_festival_studio', '1') ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold text-dark">Festival Studio</span>
                                        </label>
                                        <small class="form-hint text-muted d-block">Branded festival posters & templates</small>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-check form-switch cursor-pointer mb-1">
                                            <input class="form-check-input" type="checkbox" name="can_use_auto_dm" value="1" {{ old('can_use_auto_dm', '1') ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold text-dark">Auto DM</span>
                                        </label>
                                        <small class="form-hint text-muted d-block">Comment-to-DM automated reply</small>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-check form-switch cursor-pointer mb-1">
                                            <input class="form-check-input" type="checkbox" name="can_use_ai" value="1" {{ old('can_use_ai', '1') ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold text-dark">AI Features</span>
                                        </label>
                                        <small class="form-hint text-muted d-block">Sentiment & caption generators</small>
                                    </div>
                                    <div class="col-12 col-md-6 mt-3">
                                        <label class="form-label fw-bold text-dark">Monthly AI Token Quota</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-cpu"></i></span>
                                            <input type="number" name="ai_token_quota" class="form-control" value="{{ old('ai_token_quota', 100000) }}" placeholder="e.g. 100000 (leave blank for unlimited)" min="0" step="5000">
                                            <span class="input-group-text">Tokens / mo</span>
                                        </div>
                                        <small class="form-hint text-muted">100,000 tokens ≈ 300 comments/captions. Leave empty for unlimited.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Save User
                </button>
            </div>
        </form>
    </div>

@endsection
