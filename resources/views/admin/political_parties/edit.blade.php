@extends('admin.layouts.app')

@section('title', 'Edit Political Party')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Edit Political Party: {{ $politicalParty->name }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-parties.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Parties
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.political-parties.update', $politicalParty) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Party Name (English)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $politicalParty->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Party Name (Hindi / Regional)</label>
                    <input type="text" name="hindi_name" class="form-control @error('hindi_name') is-invalid @enderror" value="{{ old('hindi_name', $politicalParty->hindi_name) }}" placeholder="e.g. भारतीय जनता पार्टी">
                    @error('hindi_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Short Code</label>
                    <input type="text" name="short_code" class="form-control @error('short_code') is-invalid @enderror" value="{{ old('short_code', $politicalParty->short_code) }}" required style="text-transform: uppercase;">
                    @error('short_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Primary Brand Color</label>
                    <div class="input-group">
                        <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $politicalParty->primary_color) }}">
                        <input type="text" class="form-control" value="{{ old('primary_color', $politicalParty->primary_color) }}" oninput="document.querySelector('[name=primary_color]').value = this.value">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Secondary Brand Color</label>
                    <div class="input-group">
                        <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $politicalParty->secondary_color ?? '#ffffff') }}">
                        <input type="text" class="form-control" value="{{ old('secondary_color', $politicalParty->secondary_color ?? '#ffffff') }}" oninput="document.querySelector('[name=secondary_color]').value = this.value">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Party Symbol / Logo</label>
                    @if($politicalParty->symbol_logo_url)
                        <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                            <img src="{{ $politicalParty->symbol_logo_url }}" alt="Current Logo" style="width: 50px; height: 50px; object-fit: contain;">
                            <span class="text-muted small">Current Party Symbol</span>
                        </div>
                    @endif
                    <input type="file" name="symbol_logo" class="form-control @error('symbol_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    <small class="form-hint">Upload new image to replace current symbol.</small>
                    @error('symbol_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Group Cutout Image (Transparent Header)</label>
                    @if($politicalParty->group_cutout_url)
                        <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                            <img src="{{ $politicalParty->group_cutout_url }}" alt="Current Cutout" style="height: 50px; max-width: 200px; object-fit: contain;">
                            <span class="text-muted small">Current Group Cutout</span>
                        </div>
                    @endif
                    <input type="file" name="group_cutout_image" class="form-control @error('group_cutout_image') is-invalid @enderror" accept="image/png,image/webp">
                    <small class="form-hint">Upload new transparent PNG to replace current group cutout.</small>
                    @error('group_cutout_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Top Leaders Strip Image</label>
                    @if($politicalParty->top_leaders_strip_url)
                        <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                            <img src="{{ $politicalParty->top_leaders_strip_url }}" alt="Current Strip" style="height: 40px; max-width: 300px; object-fit: contain;">
                            <span class="text-muted small">Current Strip Image</span>
                        </div>
                    @endif
                    <input type="file" name="top_leaders_strip" class="form-control @error('top_leaders_strip') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    @error('top_leaders_strip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $politicalParty->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active in Studio & Poster Generator</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.political-parties.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Update Political Party
            </button>
        </div>
    </form>
</div>
@endsection
