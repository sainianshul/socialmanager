@extends('admin.layouts.app')

@section('title', 'Add Political Leader')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Add New Political Leader</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-leaders.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Leaders
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.political-leaders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror" required>
                        <option value="">Select Party</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', request('party_id')) == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }} ({{ $party->hindi_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Leader Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Full Name" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Designation / Role</label>
                    <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" placeholder="Designation">
                    @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">State / Region</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', 'National') }}" placeholder="State / Region">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Leader Photo (Avatar / Cutout)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Active Leader</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.political-leaders.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Political Leader
            </button>
        </div>
    </form>
</div>
@endsection
