@extends('admin.layouts.app')

@section('title', 'Add Festival Template')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Add New Festival Template</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Templates
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.festival-templates.store') }}" method="POST" enctype="multipart/form-data" id="template-form">
    @csrf

    {{-- Hidden technical fields --}}
    <input type="hidden" name="preset_key" id="field-preset" value="{{ old('preset_key', 'classic_political_right') }}">
    <input type="hidden" name="show_top_strip" id="field-show-strip" value="{{ old('show_top_strip', '1') }}">
    <input type="hidden" name="leader_shape" id="field-leader-shape" value="circle">
    <input type="hidden" name="leader_border_color" id="field-leader-border" value="#FF9933">

    <div class="row g-4">
        {{-- Left Column: Single Clean Card like Political Leader Form --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required m-0">Festival</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="toggle-new-festival">
                                    <i class="ti ti-plus" id="new-fest-icon"></i> <span id="new-fest-text">New Festival</span>
                                </button>
                            </div>
                            <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="field-festival">
                                <option value="">Select Festival</option>
                                @foreach($festivals as $fest)
                                    <option value="{{ $fest->id }}" {{ old('festival_id') == $fest->id ? 'selected' : '' }}>
                                        {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M Y') : '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('festival_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            <div id="new-festival-box" class="mt-2 p-2 bg-light rounded border d-none">
                                <label class="form-label small mb-1">New Festival Title</label>
                                <input type="text" name="new_festival_title" id="field-new-festival-title" class="form-control form-control-sm" value="{{ old('new_festival_title') }}" placeholder="Festival Title">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Political Party</label>
                            <select name="party_id" class="form-select @error('party_id') is-invalid @enderror" id="field-party">
                                <option value="">All Parties (Universal Template)</option>
                                @foreach($parties as $party)
                                    <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                        {{ $party->short_code }} - {{ $party->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label required">Template Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Template Title" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label required">Artwork Image</label>
                            <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                            @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label required">Branding Frame Layer (Footer Style)</label>
                            <select name="footer_style" class="form-select @error('footer_style') is-invalid @enderror" id="field-footer-style">
                                <option value="dual_ribbon_badge" {{ old('footer_style', 'dual_ribbon_badge') == 'dual_ribbon_badge' ? 'selected' : '' }}>Layer 1: Dual Ribbon Badge (Orange Box Over Photo + Green Party Strip) - [Bhai Dooj Style]</option>
                                <option value="solid_white_banner" {{ old('footer_style') == 'solid_white_banner' ? 'selected' : '' }}>Layer 2: Solid White Banner (Ethnic Pattern + Saffron Text) - [Milkha Singh Style]</option>
                                <option value="floating_tricolor_badge" {{ old('footer_style') == 'floating_tricolor_badge' ? 'selected' : '' }}>Layer 3: Floating Tricolor Badge (Minimalist + Tiranga Divider) - [Diwali Style]</option>
                                <option value="royal_gold_dock" {{ old('footer_style') == 'royal_gold_dock' ? 'selected' : '' }}>Layer 4: Royal Gold Banner (Deep Midnight Card + Gold Borders) - [Ceremonial Style]</option>
                                <option value="candidate_left_ribbon" {{ old('footer_style') == 'candidate_left_ribbon' ? 'selected' : '' }}>Layer 5: Candidate Left Ribbon (Mirrored Ribbon for Right-Focus Artwork)</option>
                                <option value="modern_capsule_dock" {{ old('footer_style') == 'modern_capsule_dock' ? 'selected' : '' }}>Layer 6: Modern Capsule Dock (Floating Rounded Pill Card + Inset Socials)</option>
                                <option value="circular_medallion_badge" {{ old('footer_style') == 'circular_medallion_badge' ? 'selected' : '' }}>Layer 7: Circular Medallion Badge (Framed Portrait & Dual-Tier Card)</option>
                                <option value="saffron_wave_banner" {{ old('footer_style') == 'saffron_wave_banner' ? 'selected' : '' }}>Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave + Tricolor Accent)</option>
                                <option value="tricolor_angled_blade" {{ old('footer_style') == 'tricolor_angled_blade' ? 'selected' : '' }}>Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)</option>
                                <option value="royal_maroon_heritage" {{ old('footer_style') == 'royal_maroon_heritage' ? 'selected' : '' }}>Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)</option>
                            </select>
                            @error('footer_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Top Leadership Header (Header Style)</label>
                            <select name="header_layout" class="form-select @error('header_layout') is-invalid @enderror" id="field-header-layout">
                                <option value="full_width_strip" {{ old('header_layout', 'full_width_strip') == 'full_width_strip' ? 'selected' : '' }}>Header 1: Full-Width Strip (6-7 Leaders Across Top)</option>
                                <option value="top_left_cluster" {{ old('header_layout') == 'top_left_cluster' ? 'selected' : '' }}>Header 2: Top-Left Cluster (3-4 Leaders + Right Party Logo)</option>
                                <option value="top_right_cluster" {{ old('header_layout') == 'top_right_cluster' ? 'selected' : '' }}>Header 3: Top-Right Cluster (3-4 Leaders + Left Party Logo)</option>
                                <option value="split_wings" {{ old('header_layout') == 'split_wings' ? 'selected' : '' }}>Header 4: Split Wings (3 Left + 3 Right, Center Grand Opening)</option>
                                <option value="vertical_capsule_strip" {{ old('header_layout') == 'vertical_capsule_strip' ? 'selected' : '' }}>Header 5: Vertical Capsule Strip (Tall Rounded Cards + Party Pill Badge)</option>
                                <option value="party_group_cutout" {{ old('header_layout') == 'party_group_cutout' ? 'selected' : '' }}>Header 6: Joint Group Cutout (Official High-Command Standing Cutout + Logo)</option>
                                <option value="frosted_glass_dock" {{ old('header_layout') == 'frosted_glass_dock' ? 'selected' : '' }}>Header 7: Modern Frosted Glass Dock (Translucent Pill Card with Saffron Accent)</option>
                                <option value="tricolor_ribbon_canopy" {{ old('header_layout') == 'tricolor_ribbon_canopy' ? 'selected' : '' }}>Header 8: Tricolor Ribbon Canopy (Patriotic Indian Tiranga Top Arch & Gold Rings)</option>
                                <option value="none" {{ old('header_layout') == 'none' ? 'selected' : '' }}>None: Hide Leadership Strip (Artwork Only)</option>
                            </select>
                            @error('header_layout')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Candidate Photo Position</label>
                            <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                                <option value="bottom-right" {{ old('photo_position', 'bottom-right') == 'bottom-right' ? 'selected' : '' }}>Right Side (Standard)</option>
                                <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Left Side (When Artwork Is On Right)</option>
                            </select>
                            @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="field-is-active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="field-is-active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Save Festival Template
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

    // Inline + Add New Festival Toggle
    const toggleNewFestBtn = document.getElementById('toggle-new-festival');
    const newFestBox = document.getElementById('new-festival-box');
    const newFestText = document.getElementById('new-fest-text');
    const newFestIcon = document.getElementById('new-fest-icon');
    const fieldFestivalSelect = document.getElementById('field-festival');
    const fieldNewFestTitle = document.getElementById('field-new-festival-title');

    if (toggleNewFestBtn && newFestBox) {
        toggleNewFestBtn.addEventListener('click', function () {
            const isHidden = newFestBox.classList.contains('d-none');
            if (isHidden) {
                newFestBox.classList.remove('d-none');
                newFestText.textContent = 'Cancel';
                newFestIcon.className = 'ti ti-x';
                if (fieldFestivalSelect) fieldFestivalSelect.value = '';
                if (fieldNewFestTitle) fieldNewFestTitle.focus();
            } else {
                newFestBox.classList.add('d-none');
                newFestText.textContent = 'New Festival';
                newFestIcon.className = 'ti ti-plus';
                if (fieldNewFestTitle) fieldNewFestTitle.value = '';
            }
        });
    }

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

    [fieldFooterStyle, fieldHeaderLayout, fieldParty, fieldPhotoPos].forEach(el => {
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
