@extends('admin.layouts.app')

@section('title', 'Edit Festival Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
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
    $footerStyle = $layoutConfig['footer']['style'] ?? ($festivalTemplate->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
    $footerFixed = !empty($layoutConfig['footer']['is_fixed']);
    $headerLayout = $layoutConfig['header']['layout'] ?? ($layoutConfig['header']['mode'] ?? 'full_width_strip');
    if (isset($layoutConfig['header']['enabled']) && !$layoutConfig['header']['enabled']) {
        $headerLayout = 'none';
    }
    $headerFixed = !empty($layoutConfig['header']['is_fixed']);
    $leaderShape = $layoutConfig['header']['chip_shape'] ?? 'circle';
    $leaderBorder = $layoutConfig['header']['chip_border_color'] ?? '#FF9933';
@endphp

<form action="{{ route('admin.festival-templates.update', $festivalTemplate) }}" method="POST" enctype="multipart/form-data" id="template-form">
    @csrf
    @method('PUT')

    <input type="hidden" id="field-template-id" value="{{ $festivalTemplate->id }}">
    <input type="hidden" name="preset_key" id="field-preset" value="{{ old('preset_key', $festivalTemplate->preset_key ?: 'classic_political_right') }}">
    <input type="hidden" name="show_top_strip" id="field-show-strip" value="{{ $headerLayout === 'none' ? '0' : '1' }}">

    <div class="row g-4">
        {{-- Left Column: Single Clean Card like Political Leader Form --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Festival</label>
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
                            <label class="form-label">Political Party</label>
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
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $festivalTemplate->title) }}" placeholder="Template Title" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Artwork Image</label>
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-light rounded border">
                                <img src="{{ $festivalTemplate->image_url }}" alt="Current Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <div class="text-muted small">Current artwork in use. Upload new file only if replacing.</div>
                            </div>
                            <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                            @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label required">Branding Frame Layer (Footer Style)</label>
                            <select name="footer_style" class="form-select @error('footer_style') is-invalid @enderror" id="field-footer-style">
                                <option value="dual_ribbon_badge" {{ $footerStyle === 'dual_ribbon_badge' ? 'selected' : '' }}>Layer 1: Dual Ribbon Badge (Orange Box Over Photo + Green Party Strip) - [Bhai Dooj Style]</option>
                                <option value="solid_white_banner" {{ $footerStyle === 'solid_white_banner' ? 'selected' : '' }}>Layer 2: Solid White Banner (Ethnic Pattern + Saffron Text) - [Milkha Singh Style]</option>
                                <option value="floating_tricolor_badge" {{ $footerStyle === 'floating_tricolor_badge' ? 'selected' : '' }}>Layer 3: Floating Tricolor Badge (Minimalist + Tiranga Divider) - [Diwali Style]</option>
                                <option value="royal_gold_dock" {{ $footerStyle === 'royal_gold_dock' ? 'selected' : '' }}>Layer 4: Royal Gold Banner (Deep Midnight Card + Gold Borders) - [Ceremonial Style]</option>
                                <option value="candidate_left_ribbon" {{ $footerStyle === 'candidate_left_ribbon' ? 'selected' : '' }}>Layer 5: Candidate Left Ribbon (Mirrored Ribbon for Right-Focus Artwork)</option>
                                <option value="modern_capsule_dock" {{ $footerStyle === 'modern_capsule_dock' ? 'selected' : '' }}>Layer 6: Modern Capsule Dock (Floating Rounded Pill Card + Inset Socials)</option>
                                <option value="circular_medallion_badge" {{ $footerStyle === 'circular_medallion_badge' ? 'selected' : '' }}>Layer 7: Circular Medallion Badge (Framed Portrait & Dual-Tier Card)</option>
                                <option value="saffron_wave_banner" {{ $footerStyle === 'saffron_wave_banner' ? 'selected' : '' }}>Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave + Tricolor Accent)</option>
                                <option value="tricolor_angled_blade" {{ $footerStyle === 'tricolor_angled_blade' ? 'selected' : '' }}>Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)</option>
                                <option value="royal_maroon_heritage" {{ $footerStyle === 'royal_maroon_heritage' ? 'selected' : '' }}>Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)</option>
                            </select>
                            <div class="mt-1 d-flex align-items-center justify-content-between">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="footer_fixed" value="1" id="field-footer-fixed" {{ old('footer_fixed', $footerFixed) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="field-footer-fixed">
                                        <i class="ti ti-lock me-1 text-danger"></i><strong>Make this Strip Fixed</strong> (Locked on poster, user cannot change)
                                    </label>
                                </div>
                                <span class="text-muted small">Default: Changeable by user</span>
                            </div>
                            @error('footer_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Top Leadership Header (Header Style)</label>
                            <select name="header_layout" class="form-select @error('header_layout') is-invalid @enderror" id="field-header-layout">
                                <option value="full_width_strip" {{ $headerLayout === 'full_width_strip' ? 'selected' : '' }}>Header 1: Full-Width Strip (6-7 Leaders Across Top)</option>
                                <option value="top_left_cluster" {{ $headerLayout === 'top_left_cluster' ? 'selected' : '' }}>Header 2: Top-Left Cluster (3-4 Leaders + Right Party Logo)</option>
                                <option value="top_right_cluster" {{ $headerLayout === 'top_right_cluster' ? 'selected' : '' }}>Header 3: Top-Right Cluster (3-4 Leaders + Left Party Logo)</option>
                                <option value="split_wings" {{ $headerLayout === 'split_wings' ? 'selected' : '' }}>Header 4: Split Wings (3 Left + 3 Right, Center Grand Opening)</option>
                                <option value="vertical_capsule_strip" {{ $headerLayout === 'vertical_capsule_strip' ? 'selected' : '' }}>Header 5: Vertical Capsule Strip (Tall Rounded Cards + Party Pill Badge)</option>
                                <option value="party_group_cutout" {{ $headerLayout === 'party_group_cutout' ? 'selected' : '' }}>Header 6: Joint Group Cutout (Official High-Command Standing Cutout + Logo)</option>
                                <option value="frosted_glass_dock" {{ $headerLayout === 'frosted_glass_dock' ? 'selected' : '' }}>Header 7: Modern Frosted Glass Dock (Translucent Pill Card with Saffron Accent)</option>
                                <option value="tricolor_ribbon_canopy" {{ $headerLayout === 'tricolor_ribbon_canopy' ? 'selected' : '' }}>Header 8: Tricolor Ribbon Canopy (Patriotic Indian Tiranga Top Arch & Gold Rings)</option>
                                <option value="none" {{ $headerLayout === 'none' ? 'selected' : '' }}>None: Hide Leadership Strip (Artwork Only)</option>
                            </select>
                            <div class="mt-1 d-flex align-items-center justify-content-between">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="header_fixed" value="1" id="field-header-fixed" {{ old('header_fixed', $headerFixed) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="field-header-fixed">
                                        <i class="ti ti-lock me-1 text-danger"></i><strong>Make this Header Fixed</strong> (Locked on poster, user cannot change)
                                    </label>
                                </div>
                                <span class="text-muted small">Default: Changeable by user</span>
                            </div>
                            @error('header_layout')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Candidate Photo Position</label>
                            <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                                <option value="bottom-right" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-right' ? 'selected' : '' }}>Right Side (Standard)</option>
                                <option value="bottom-left" {{ old('photo_position', $festivalTemplate->photo_position) == 'bottom-left' ? 'selected' : '' }}>Left Side (When Artwork Is On Right)</option>
                            </select>
                            @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Advanced Options Collapsible --}}
                        <div class="col-md-12">
                            <div class="border rounded p-3 bg-light">
                                <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold" data-bs-toggle="collapse" href="#advanced-leader-settings" role="button" aria-expanded="false" aria-controls="advanced-leader-settings">
                                    <span class="d-flex align-items-center gap-1.5">
                                        <i class="ti ti-adjustments-alt text-primary"></i> Advanced: Leader Shape & Border Color
                                    </span>
                                    <span class="badge bg-secondary-lt small">Click to Expand <i class="ti ti-chevron-down ms-1"></i></span>
                                </a>
                                <div class="collapse mt-3" id="advanced-leader-settings">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Leader Avatar Shape</label>
                                            <select name="leader_shape" class="form-select form-select-sm" id="field-leader-shape">
                                                <option value="circle" {{ old('leader_shape', $leaderShape) === 'circle' ? 'selected' : '' }}>Circle (गोल - Standard)</option>
                                                <option value="rounded_square" {{ old('leader_shape', $leaderShape) === 'rounded_square' ? 'selected' : '' }}>Rounded Square (घुमावदार चौकोर)</option>
                                                <option value="square" {{ old('leader_shape', $leaderShape) === 'square' ? 'selected' : '' }}>Square (चौकोर / Boxed)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Leader Ring / Border Color</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="color" name="leader_border_color" id="field-leader-border" class="form-control form-control-color form-control-sm" value="{{ old('leader_border_color', $leaderBorder) }}" title="Choose Custom Color">
                                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                                    <button type="button" class="btn btn-outline-secondary px-2 py-0.5 btn-color-preset" data-color="#FF9933">Saffron</button>
                                                    <button type="button" class="btn btn-outline-secondary px-2 py-0.5 btn-color-preset" data-color="#FFFFFF">White</button>
                                                    <button type="button" class="btn btn-outline-secondary px-2 py-0.5 btn-color-preset" data-color="#138808">Green</button>
                                                    <button type="button" class="btn btn-outline-secondary px-2 py-0.5 btn-color-preset" data-color="#D4AF37">Gold</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="field-is-active" {{ old('is_active', $festivalTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="field-is-active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Update Festival Template
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Column: Minimal Clean Live Preview --}}
        <div class="col-lg-5">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title m-0">Live Preview</h3>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-manual-refresh">
                        <i class="ti ti-refresh me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="position-relative w-100 rounded overflow-hidden border bg-dark" style="aspect-ratio: 1 / 1;" id="preview-wrapper">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-50 text-white z-3 d-none" id="preview-loading">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <span class="small fw-semibold">Rendering Live Preview...</span>
                        </div>

                        <img src="" alt="Template Live Preview" class="w-100 h-100 d-block" style="object-fit: contain;" id="preview-image">
                    </div>

                    <div class="mt-3">
                        <a href="#" id="btn-download-preview" class="btn btn-success w-100 fw-bold py-2" download="preview-poster.jpg">
                            <i class="ti ti-download me-1.5"></i> Download Full Image (1080x1080)
                        </a>
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
    const fieldFooterStyle = document.getElementById('field-footer-style');
    const fieldHeaderLayout = document.getElementById('field-header-layout');
    const fieldPreset = document.getElementById('field-preset');
    const fieldParty = document.getElementById('field-party');
    const fieldPhotoPos = document.getElementById('field-photo-position');
    const fieldLeaderShape = document.getElementById('field-leader-shape');
    const fieldLeaderBorder = document.getElementById('field-leader-border');
    const fieldShowStrip = document.getElementById('field-show-strip');

    const previewImg = document.getElementById('preview-image');
    const previewLoading = document.getElementById('preview-loading');
    const btnRefresh = document.getElementById('btn-manual-refresh');
    const btnDownload = document.getElementById('btn-download-preview');

    // Auto sync candidate position & preset
    if (fieldFooterStyle && fieldPhotoPos) {
        fieldFooterStyle.addEventListener('change', function () {
            if (fieldFooterStyle.value === 'candidate_left_ribbon') {
                fieldPhotoPos.value = 'bottom-left';
            }
            syncPresetKey();
        });
    }

    if (fieldPhotoPos) {
        fieldPhotoPos.addEventListener('change', syncPresetKey);
    }

    function syncPresetKey() {
        if (!fieldPreset || !fieldPhotoPos) return;
        if (fieldPhotoPos.value === 'bottom-left') {
            fieldPreset.value = 'classic_political_left';
        } else {
            fieldPreset.value = 'classic_political_right';
        }
    }

    function syncHeaderStrip() {
        if (!fieldShowStrip || !fieldHeaderLayout) return;
        fieldShowStrip.value = (fieldHeaderLayout.value === 'none') ? '0' : '1';
    }

    let debounceTimer = null;

    async function updatePreview() {
        if (previewLoading) previewLoading.classList.remove('d-none');

        syncPresetKey();
        syncHeaderStrip();

        const formData = new FormData();
        formData.append('_token', csrfToken);
        if (templateId) formData.append('template_id', templateId);
        if (fieldFooterStyle) formData.append('footer_style', fieldFooterStyle.value);
        if (fieldHeaderLayout) formData.append('header_layout', fieldHeaderLayout.value);
        if (fieldPreset) formData.append('preset_key', fieldPreset.value);
        if (fieldParty && fieldParty.value) formData.append('party_id', fieldParty.value);
        if (fieldPhotoPos) formData.append('photo_position', fieldPhotoPos.value);
        if (fieldLeaderShape) formData.append('leader_shape', fieldLeaderShape.value);
        if (fieldLeaderBorder) formData.append('leader_border_color', fieldLeaderBorder.value);
        if (fieldShowStrip) formData.append('show_top_strip', fieldShowStrip.value);

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
                if (btnDownload) {
                    btnDownload.href = data.preview_url;
                    const titleVal = document.querySelector('input[name="title"]')?.value || 'festival-poster-preview';
                    btnDownload.download = titleVal.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '.jpg';
                }
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

    [fieldFooterStyle, fieldHeaderLayout, fieldParty, fieldPhotoPos, fieldLeaderShape, fieldLeaderBorder].forEach(el => {
        if (el) el.addEventListener('change', schedulePreview);
    });

    document.querySelectorAll('.btn-color-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            const color = this.dataset.color;
            if (fieldLeaderBorder) {
                fieldLeaderBorder.value = color;
                schedulePreview();
            }
        });
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
