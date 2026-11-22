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

@php
    $layoutConfig = $festivalTemplate->layout_config ?? [];
    $leaderShape = $layoutConfig['header']['chip_shape'] ?? 'circle';
    $leaderBorder = $layoutConfig['header']['chip_border_color'] ?? '#FF9933';
    $showStrip = isset($layoutConfig['header']['enabled']) ? ($layoutConfig['header']['enabled'] ? '1' : '0') : '1';
    $nameColor = $layoutConfig['footer']['name_color'] ?? '#c0392b';
    $footerBg = $layoutConfig['footer']['main_bar_bg'] ?? '#f8f5ee';
@endphp

<form action="{{ route('admin.festival-templates.update', $festivalTemplate) }}" method="POST" enctype="multipart/form-data" id="template-form">
    @csrf
    @method('PUT')
    <input type="hidden" id="field-template-id" value="{{ $festivalTemplate->id }}">

    <div class="row g-4">
        {{-- Left Column: Configuration Controls --}}
        <div class="col-lg-7">
            {{-- 1. General & Festival Details --}}
            <div class="card mb-3 shadow-xs">
                <div class="card-header py-3 bg-light-subtle">
                    <h3 class="card-title text-dark fw-bold">
                        <i class="ti ti-calendar-event me-1.5 text-primary"></i> 1. Festival & Template Info
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Select Festival</label>
                            <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="field-festival" required>
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
                            <select name="party_id" class="form-select @error('party_id') is-invalid @enderror" id="field-party">
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
                    </div>
                </div>
            </div>

            {{-- 2. Styling & Layout Customization --}}
            <div class="card mb-3 shadow-xs">
                <div class="card-header py-3 bg-light-subtle">
                    <h3 class="card-title text-dark fw-bold">
                        <i class="ti ti-palette me-1.5 text-primary"></i> 2. Layout & Branding Customization
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Base Poster Preset</label>
                            <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" id="field-preset" required>
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
                            <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                                <option value="bottom-right" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Classic Dr. Dharam Singh)</option>
                                <option value="bottom-left" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-left' ? 'selected' : '' }}>Bottom Left (When artwork is on right)</option>
                                <option value="footer-strip" {{ old('photo_position', $festivalTemplate->photo_position) == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                            </select>
                            @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Top Leaders Shape</label>
                            <select name="leader_shape" class="form-select" id="field-leader-shape">
                                <option value="circle" {{ $leaderShape == 'circle' ? 'selected' : '' }}>Circular Avatar</option>
                                <option value="rounded_square" {{ $leaderShape == 'rounded_square' ? 'selected' : '' }}>Rounded Square</option>
                                <option value="square" {{ $leaderShape == 'square' ? 'selected' : '' }}>Square</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Leader Border Color</label>
                            <select name="leader_border_color" class="form-select" id="field-leader-border">
                                <option value="#FF9933" {{ $leaderBorder == '#FF9933' ? 'selected' : '' }}>Saffron Orange (#FF9933)</option>
                                <option value="#ffffff" {{ $leaderBorder == '#ffffff' ? 'selected' : '' }}>Clean White (#FFFFFF)</option>
                                <option value="#D4AF37" {{ $leaderBorder == '#D4AF37' ? 'selected' : '' }}>Royal Gold (#D4AF37)</option>
                                <option value="none" {{ $leaderBorder == 'none' ? 'selected' : '' }}>No Border</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Top Leadership Strip</label>
                            <select name="show_top_strip" class="form-select" id="field-show-strip">
                                <option value="1" {{ $showStrip == '1' ? 'selected' : '' }}>Yes (Show Leaders)</option>
                                <option value="0" {{ $showStrip == '0' ? 'selected' : '' }}>No (Hide Strip)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Politician Name Color</label>
                            <select name="name_color" class="form-select" id="field-name-color">
                                <option value="#c0392b" {{ $nameColor == '#c0392b' ? 'selected' : '' }}>Deep Red (#c0392b - Classic)</option>
                                <option value="#E65100" {{ $nameColor == '#E65100' ? 'selected' : '' }}>Saffron Deep (#E65100)</option>
                                <option value="#1a365d" {{ $nameColor == '#1a365d' ? 'selected' : '' }}>Dark Navy (#1a365d)</option>
                                <option value="#2d3748" {{ $nameColor == '#2d3748' ? 'selected' : '' }}>Dark Charcoal (#2d3748)</option>
                                <option value="#ffffff" {{ $nameColor == '#ffffff' ? 'selected' : '' }}>Pure White (#ffffff)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Footer Bar Background</label>
                            <select name="footer_bg" class="form-select" id="field-footer-bg">
                                <option value="#f8f5ee" {{ $footerBg == '#f8f5ee' ? 'selected' : '' }}>Warm Light Cream (#f8f5ee)</option>
                                <option value="#ffffff" {{ $footerBg == '#ffffff' ? 'selected' : '' }}>Pure White (#ffffff)</option>
                                <option value="#1a202c" {{ $footerBg == '#1a202c' ? 'selected' : '' }}>Dark Midnight (#1a202c)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Graphic Background Artwork --}}
            <div class="card mb-3 shadow-xs">
                <div class="card-header py-3 bg-light-subtle">
                    <h3 class="card-title text-dark fw-bold">
                        <i class="ti ti-photo me-1.5 text-primary"></i> 3. Template Artwork Image (1080x1080)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-light rounded border">
                        <img src="{{ $festivalTemplate->image_url }}" alt="Current Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                        <div>
                            <div class="fw-bold small">{{ $festivalTemplate->title }}</div>
                            <div class="text-muted" style="font-size: 11px;">Current 1080x1080 artwork in use. Upload below only if replacing.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                        <small class="form-hint">Leave empty to keep existing background. If a new file is chosen, the Live Preview updates immediately.</small>
                        @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $festivalTemplate->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active in Festival Studio</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2 bg-white">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-check me-1"></i> Update Festival Template
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Column: Live Realistic Preview --}}
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header py-2.5 px-3 d-flex align-items-center justify-content-between bg-white border-bottom">
                    <div>
                        <h4 class="card-title m-0 fw-bold text-dark">
                            <i class="ti ti-eye me-1.5 text-teal"></i> Realistic Live Preview
                        </h4>
                        <div class="text-secondary small" style="font-size: 11px;">Sample politician overlay (Auto-Fit)</div>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5" id="btn-manual-refresh" title="Refresh Preview">
                        <i class="ti ti-refresh me-1"></i>Refresh
                    </button>
                </div>
                <div class="card-body p-3">
                    {{-- Preview Container --}}
                    <div class="position-relative w-100 rounded-3 overflow-hidden border shadow-xs bg-dark" style="aspect-ratio: 1 / 1;" id="preview-wrapper">
                        {{-- Loading Spinner Overlay --}}
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-50 text-white z-3 d-none" id="preview-loading">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <span class="small fw-semibold">Rendering Live Preview...</span>
                        </div>

                        {{-- Composed Preview Image --}}
                        <img src="" alt="Template Live Preview" class="w-100 h-100 d-block" style="object-fit: contain;" id="preview-image">
                    </div>

                    {{-- Helpful explanation notes --}}
                    <div class="mt-2.5 p-2 bg-light rounded-2 border">
                        <div class="d-flex align-items-center gap-1.5 text-secondary small" style="font-size: 11px;">
                            <i class="ti ti-sparkles text-warning fs-3"></i>
                            <span>Text size auto-scales cleanly for names of any length without clipping.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const previewUrl = "{{ route('admin.festival-templates.preview') }}";
    const csrfToken = "{{ csrf_token() }}";

    const templateId = document.getElementById('field-template-id') ? document.getElementById('field-template-id').value : '';
    const fieldImage = document.getElementById('field-image');
    const fieldPreset = document.getElementById('field-preset');
    const fieldParty = document.getElementById('field-party');
    const fieldPhotoPos = document.getElementById('field-photo-position');
    const fieldLeaderShape = document.getElementById('field-leader-shape');
    const fieldLeaderBorder = document.getElementById('field-leader-border');
    const fieldShowStrip = document.getElementById('field-show-strip');
    const fieldNameColor = document.getElementById('field-name-color');
    const fieldFooterBg = document.getElementById('field-footer-bg');

    const previewImg = document.getElementById('preview-image');
    const previewLoading = document.getElementById('preview-loading');
    const btnRefresh = document.getElementById('btn-manual-refresh');

    let debounceTimer = null;

    async function updatePreview() {
        if (previewLoading) previewLoading.classList.remove('d-none');

        const formData = new FormData();
        formData.append('_token', csrfToken);
        if (templateId) formData.append('template_id', templateId);
        formData.append('preset_key', fieldPreset.value);
        if (fieldParty.value) formData.append('party_id', fieldParty.value);
        formData.append('photo_position', fieldPhotoPos.value);
        formData.append('leader_shape', fieldLeaderShape.value);
        formData.append('leader_border_color', fieldLeaderBorder.value);
        formData.append('show_top_strip', fieldShowStrip.value);
        formData.append('name_color', fieldNameColor.value);
        formData.append('footer_bg', fieldFooterBg.value);

        if (fieldImage && fieldImage.files && fieldImage.files[0]) {
            formData.append('template_image', fieldImage.files[0]);
        }

        try {
            const res = await fetch(previewUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success && data.preview_url) {
                previewImg.src = data.preview_url;
            }
        } catch (err) {
            console.error('Preview error:', err);
        } finally {
            if (previewLoading) previewLoading.classList.add('d-none');
        }
    }

    function schedulePreview() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updatePreview, 300);
    }

    [fieldPreset, fieldParty, fieldPhotoPos, fieldLeaderShape, fieldLeaderBorder, fieldShowStrip, fieldNameColor, fieldFooterBg].forEach(el => {
        if (el) el.addEventListener('change', schedulePreview);
    });

    if (fieldImage) {
        fieldImage.addEventListener('change', updatePreview);
    }

    if (btnRefresh) {
        btnRefresh.addEventListener('click', updatePreview);
    }

    // Trigger initial preview on page load
    updatePreview();
});
</script>
@endpush
