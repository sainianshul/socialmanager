@extends('admin.layouts.app')

@section('title', 'Add Card Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Templates</div>
            <h2 class="page-title">Add New Card Template</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.card-templates.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Card Templates
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.card-templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Template Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. जन्मदिन की हार्दिक शुभकामनाएं / जयंती पर सादर नमन" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Card Category</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror">
                        <option value="">All Parties (Universal)</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-hint">Assign if card contains party-specific color themes or branding.</small>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Composition Preset</label>
                    <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" required>
                        @foreach($presets as $p)
                            <option value="{{ $p['key'] }}" {{ old('preset_key', 'classic_political_right') == $p['key'] ? 'selected' : '' }}>
                                {{ $p['name'] }} ({{ $p['badge'] ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('preset_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Candidate Photo Position</label>
                    <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" required>
                        <option value="bottom-right" {{ old('photo_position') == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Standard Dr. Dharam Singh Style)</option>
                        <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Bottom Left (When dignitary is on right)</option>
                        <option value="footer-strip" {{ old('photo_position') == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                    </select>
                    @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Card Background Graphic (1080x1080 px)</label>
                    <input type="file" name="template_image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                    <small class="form-hint">Square 1080x1080 image with card design and text.</small>
                    @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active and available for users</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.card-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Card Template
            </button>
        </div>
    </form>
</div>
@endsection
