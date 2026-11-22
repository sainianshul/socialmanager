@extends('admin.layouts.app')

@section('title', 'Edit Festival Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Templates</div>
            <h2 class="page-title">Edit Festival Template: {{ $festivalTemplate->title }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Templates
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.festival-templates.update', $festivalTemplate) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Select Festival</label>
                    <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" required>
                        @foreach($festivals as $fest)
                            <option value="{{ $fest->id }}" {{ old('festival_id', $festivalTemplate->festival_id) == $fest->id ? 'selected' : '' }}>
                                {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M Y') : '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('festival_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Target Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror">
                        <option value="">All Parties (Universal Template)</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', $festivalTemplate->party_id) == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label required">Template Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $festivalTemplate->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Poster Composition Preset</label>
                    <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" required>
                        @foreach($presets as $p)
                            <option value="{{ $p['key'] }}" {{ old('preset_key', $festivalTemplate->preset_key) == $p['key'] ? 'selected' : '' }}>
                                {{ $p['name'] }} ({{ $p['badge'] ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('preset_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Candidate Photo Position</label>
                    <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" required>
                        <option value="bottom-right" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Standard Dr. Dharam Singh Style)</option>
                        <option value="bottom-left" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-left' ? 'selected' : '' }}>Bottom Left (When deity/artwork is on right)</option>
                        <option value="footer-strip" {{ old('photo_position', $festivalTemplate->photo_position) == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                    </select>
                    @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Template Background Image</label>
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                        <img src="{{ $festivalTemplate->image_url }}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
                        <div>
                            <div class="fw-bold">{{ $festivalTemplate->title }}</div>
                            <div class="text-muted small">Current 1080x1080 template artwork</div>
                        </div>
                    </div>
                    <input type="file" name="template_image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <small class="form-hint">Upload new image only if you wish to replace the current template artwork.</small>
                    @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $festivalTemplate->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active and available in Festival Studio</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Update Festival Template
            </button>
        </div>
    </form>
</div>
@endsection
