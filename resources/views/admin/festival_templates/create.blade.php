@extends('admin.layouts.app')

@section('title', 'Add Festival Template')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Festival Studio</div>
            <h2 class="page-title">Add Festival Template</h2>
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

    <div class="row g-4">
        {{-- Left Column: Clean Configuration Controls --}}
        <div class="col-lg-7">
            {{-- 1. Festival & Template Info --}}
            <div class="card mb-3 shadow-xs">
                <div class="card-header py-3 bg-light-subtle">
                    <h3 class="card-title text-dark fw-bold">
                        <i class="ti ti-calendar-event me-1.5 text-primary"></i> 1. Festival & Template Info
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Festival Selection with sleek inline + Add New toggle --}}
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required m-0">Festival</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="toggle-new-festival">
                                    <i class="ti ti-plus" id="new-fest-icon"></i> <span id="new-fest-text">New Festival</span>
                                </button>
                            </div>
                            <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="field-festival">
                                <option value="">-- Choose Existing Festival --</option>
                                @foreach($festivals as $fest)
                                    <option value="{{ $fest->id }}" {{ old('festival_id') == $fest->id ? 'selected' : '' }}>
                                        {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M Y') : '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('festival_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            {{-- Collapsible inline new festival title box --}}
                            <div id="new-festival-box" class="mt-2 p-2.5 bg-light rounded-2 border d-none">
                                <label class="form-label small fw-bold text-primary mb-1">New Festival Title</label>
                                <input type="text" name="new_festival_title" id="field-new-festival-title" class="form-control form-control-sm" value="{{ old('new_festival_title') }}" placeholder="e.g. महाशिवरात्रि / गणतंत्र दिवस">
                            </div>
                        </div>

                        {{-- Target Political Party --}}
                        <div class="col-md-6">
                            <label class="form-label mb-1">Target Political Party</label>
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

                        {{-- Template Title --}}
                        <div class="col-md-12">
                            <label class="form-label required">Template Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Diwali Greeting 2026 Special Poster" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Artwork Image Upload --}}
                        <div class="col-md-12">
                            <label class="form-label required">Artwork Background Image (1080x1080)</label>
                            <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                            <small class="form-hint">Square 1080x1080 JPG or PNG. The Live Preview updates immediately on selection.</small>
                            @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Active Switch --}}
                        <div class="col-md-12">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="field-is-active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="field-is-active">Active in Festival Studio</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Design & Branding Frame Selection --}}
            <div class="card mb-3 shadow-xs">
                <div class="card-header py-3 bg-light-subtle">
                    <h3 class="card-title text-dark fw-bold">
                        <i class="ti ti-palette me-1.5 text-primary"></i> 2. Design & Branding Frame
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Branding Frame Layer (Footer Style) --}}
                        <div class="col-md-12">
                            <label class="form-label required fw-bold text-primary">
                                <i class="ti ti-layout-bottombar me-1"></i> Branding Frame Layer (Footer Style)
                            </label>
                            <select name="footer_style" class="form-select border-primary fw-semibold" id="field-footer-style">
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
                        </div>

                        {{-- Top Leadership Layout (Header Style) --}}
                        <div class="col-md-7">
                            <label class="form-label required fw-bold text-primary">
                                <i class="ti ti-crown me-1"></i> Top Leadership Header (Header Style)
                            </label>
                            <select name="header_layout" class="form-select border-primary fw-semibold" id="field-header-layout">
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
                        </div>

                        {{-- Candidate Photo Placement --}}
                        <div class="col-md-5">
                            <label class="form-label required fw-bold">Candidate Photo Position</label>
                            <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                                <option value="bottom-right" {{ old('photo_position', 'bottom-right') == 'bottom-right' ? 'selected' : '' }}>Right Side (Standard)</option>
                                <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Left Side (For Right-Focus Art)</option>
                            </select>
                            @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Optional Collapsed Customization for Avatars --}}
                        <div class="col-md-12">
                            <details class="border rounded-2 p-2.5 bg-light-subtle">
                                <summary class="fw-semibold text-secondary small" style="cursor: pointer;">
                                    <i class="ti ti-adjustments me-1 text-primary"></i> Optional: Customize Leader Avatar Shape & Border
                                </summary>
                                <div class="row g-2 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Avatar Shape</label>
                                        <select name="leader_shape" class="form-select form-select-sm" id="field-leader-shape">
                                            <option value="circle" selected>Circular Avatar</option>
                                            <option value="rounded_square">Rounded Square</option>
                                            <option value="square">Square</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Ring / Border Color</label>
                                        <select name="leader_border_color" class="form-select form-select-sm" id="field-leader-border">
                                            <option value="#FF9933" selected>Saffron Orange (#FF9933)</option>
                                            <option value="#D4AF37">Royal Gold (#D4AF37)</option>
                                            <option value="#ffffff">Clean White (#FFFFFF)</option>
                                            <option value="none">No Border</option>
                                        </select>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2 bg-white">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="ti ti-check me-1"></i> Save Festival Template
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

                    {{-- Download & Actions --}}
                    <div class="mt-3">
                        <a href="#" id="btn-download-preview" class="btn btn-success w-100 fw-bold py-2 shadow-xs" download="preview-poster.jpg">
                            <i class="ti ti-download me-1.5"></i> Download Full Image (1080x1080)
                        </a>
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

    // Auto sync show_top_strip hidden field with header layout
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

    [fieldFooterStyle, fieldHeaderLayout, fieldParty, fieldPhotoPos, fieldLeaderShape, fieldLeaderBorder].forEach(el => {
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
