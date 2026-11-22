@extends('admin.layouts.app')

@section('title', 'Edit Card Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Templates</div>
            <h2 class="page-title">Edit Card Template: {{ $cardTemplate->title }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.card-templates.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Card Templates
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.card-templates.update', $cardTemplate) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Template Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $cardTemplate->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Card Category</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ old('category', $cardTemplate->category) == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror">
                        <option value="">All Parties (Universal)</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', $cardTemplate->party_id) == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Composition Preset</label>
                    <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" required>
                        @foreach($presets as $p)
                            <option value="{{ $p['key'] }}" {{ old('preset_key', $cardTemplate->preset_key) == $p['key'] ? 'selected' : '' }}>
                                {{ $p['name'] }} ({{ $p['badge'] ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('preset_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Candidate Photo Position</label>
                    <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" required>
                        <option value="bottom-right" {{ old('photo_position', $cardTemplate->photo_position) == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Standard Dr. Dharam Singh Style)</option>
                        <option value="bottom-left" {{ old('photo_position', $cardTemplate->photo_position) == 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                        <option value="footer-strip" {{ old('photo_position', $cardTemplate->photo_position) == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                    </select>
                    @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Card Background Graphic</label>
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                        <img src="{{ $cardTemplate->image_url }}" alt="Current Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                        <div>
                            <div class="fw-bold">{{ $cardTemplate->title }}</div>
                            <div class="text-muted small">Current 1080x1080 graphic</div>
                        </div>
                    </div>
                    <input type="file" name="template_image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <small class="form-hint">Upload new image to replace current graphic.</small>
                    @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $cardTemplate->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active and available for users</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.card-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Update Card Template
            </button>
        </div>
    </form>
</div>
@endsection
