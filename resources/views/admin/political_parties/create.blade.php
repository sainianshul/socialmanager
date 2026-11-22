@extends('admin.layouts.app')

@section('title', 'Add Political Party')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Add New Political Party</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-parties.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Parties
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.political-parties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Party Name (English)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Bharatiya Janata Party" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Party Name (Hindi / Regional)</label>
                    <input type="text" name="hindi_name" class="form-control @error('hindi_name') is-invalid @enderror" value="{{ old('hindi_name') }}" placeholder="e.g. भारतीय जनता पार्टी">
                    <small class="form-hint">Used on poster footers and regional banners.</small>
                    @error('hindi_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Short Code</label>
                    <input type="text" name="short_code" class="form-control @error('short_code') is-invalid @enderror" value="{{ old('short_code') }}" placeholder="e.g. BJP" maxlength="20" required style="text-transform: uppercase;">
                    @error('short_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Primary Brand Color</label>
                    <div class="input-group">
                        <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', '#FF9933') }}" title="Choose primary color">
                        <input type="text" class="form-control" id="primary_color_text" value="{{ old('primary_color', '#FF9933') }}" oninput="document.querySelector('[name=primary_color]').value = this.value">
                    </div>
                    @error('primary_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Secondary Brand Color</label>
                    <div class="input-group">
                        <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', '#138808') }}" title="Choose secondary color">
                        <input type="text" class="form-control" id="secondary_color_text" value="{{ old('secondary_color', '#138808') }}" oninput="document.querySelector('[name=secondary_color]').value = this.value">
                    </div>
                    @error('secondary_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Party Symbol / Logo</label>
                    <input type="file" name="symbol_logo" class="form-control @error('symbol_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    <small class="form-hint">Clean transparent PNG symbol recommended (e.g. Lotus, Hand, Cycle, Elephant). Shown in footer bar.</small>
                    @error('symbol_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Group Cutout Image (Transparent Header)</label>
                    <input type="file" name="group_cutout_image" class="form-control @error('group_cutout_image') is-invalid @enderror" accept="image/png,image/webp">
                    <small class="form-hint">Pre-arranged 6-7 top party leaders transparent cutout used at top banner. (PNG with transparent background)</small>
                    @error('group_cutout_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Top Leaders Strip Image (Alternative Strip)</label>
                    <input type="file" name="top_leaders_strip" class="form-control @error('top_leaders_strip') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <small class="form-hint">Optional wide strip (1080x120px) if party uses a pre-made top banner strip.</small>
                    @error('top_leaders_strip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active in Studio & Poster Generator</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.political-parties.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Political Party
            </button>
        </div>
    </form>
</div>
@endsection
