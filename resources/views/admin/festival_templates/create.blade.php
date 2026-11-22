@extends('admin.layouts.app')

@section('title', 'Add Festival Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Templates</div>
            <h2 class="page-title">Add New Festival Template</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Templates
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.festival-templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Select Festival</label>
                    <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="festival-select">
                        <option value="">-- Choose Existing Festival --</option>
                        @foreach($festivals as $fest)
                            <option value="{{ $fest->id }}" {{ old('festival_id') == $fest->id ? 'selected' : '' }}>
                                {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M Y') : '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('festival_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Or Add New Festival Name</label>
                    <input type="text" name="new_festival_title" class="form-control" value="{{ old('new_festival_title') }}" placeholder="e.g. महाशिवरात्रि / गणतंत्र दिवस">
                    <small class="form-hint">Leave blank if selecting an existing festival above.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Festival Date (If adding new)</label>
                    <input type="date" name="new_festival_date" class="form-control" value="{{ old('new_festival_date') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Target Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror">
                        <option value="">All Parties (Universal Template)</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-hint">Select a specific party if template has party-specific colors or themes.</small>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label required">Template Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Tulsi Vivah Shubhkaamnaayein Poster 1" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Poster Composition Preset</label>
                    <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" required>
                        @foreach($presets as $p)
                            <option value="{{ $p['key'] }}" {{ old('preset_key', 'classic_political_right') == $p['key'] ? 'selected' : '' }}>
                                {{ $p['name'] }} ({{ $p['badge'] ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-hint">Controls leadership strip at top, candidate photo placement, and footer styling.</small>
                    @error('preset_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Candidate Photo Position</label>
                    <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" required>
                        <option value="bottom-right" {{ old('photo_position') == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Standard Dr. Dharam Singh Style)</option>
                        <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Bottom Left (When deity/artwork is on right)</option>
                        <option value="footer-strip" {{ old('photo_position') == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                    </select>
                    @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label required">Template Background Image (1080x1080 px)</label>
                    <input type="file" name="template_image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                    <small class="form-hint">Square 1080x1080 JPG/PNG graphic with festival background and message text. Leader strips and user cutout will be composed dynamically over this.</small>
                    @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active and available in Festival Studio</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Festival Template
            </button>
        </div>
    </form>
</div>
@endsection
