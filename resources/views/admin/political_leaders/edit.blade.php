@extends('admin.layouts.app')

@section('title', 'Edit Political Leader')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Edit Political Leader: {{ $politicalLeader->name }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-leaders.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Leaders
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.political-leaders.update', $politicalLeader) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror" required>
                        <option value="">Select Party</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', $politicalLeader->party_id) == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }} ({{ $party->hindi_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Leader Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $politicalLeader->name) }}" placeholder="Full Name" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Designation / Role</label>
                    <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $politicalLeader->designation) }}" placeholder="Designation">
                    @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">State / Region</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $politicalLeader->state) }}" placeholder="State / Region">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Leader Photo</label>
                    <div class="d-flex align-items-center gap-2 mb-2 p-1 bg-light rounded border" style="width: fit-content;">
                        <img src="{{ $politicalLeader->image_url }}" alt="{{ $politicalLeader->name }}" class="avatar avatar-md rounded-circle border shadow-sm" style="object-fit: cover;">
                        <span class="text-body small fw-medium">{{ $politicalLeader->name }}</span>
                    </div>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Active Leader</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', $politicalLeader->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_active', $politicalLeader->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.political-leaders.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Update Political Leader
            </button>
        </div>
    </form>
</div>
@endsection
