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
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $politicalLeader->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Designation / Role</label>
                    <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $politicalLeader->designation) }}" placeholder="e.g. माननीय प्रधानमंत्री / राष्ट्रीय अध्यक्ष">
                    @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">State / Region</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $politicalLeader->state) }}">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Leader Photo</label>
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                        <img src="{{ $politicalLeader->image_url }}" alt="{{ $politicalLeader->name }}" class="avatar avatar-lg rounded-circle border shadow-sm" style="object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ $politicalLeader->name }}</div>
                            <div class="text-muted small">Current photo preview</div>
                        </div>
                    </div>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <small class="form-hint">Upload new image to replace current photo.</small>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $politicalLeader->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active leader (available in strips & posters)</span>
                    </label>
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
