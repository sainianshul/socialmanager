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

<form action="{{ route('admin.festival-templates.store') }}" method="POST" enctype="multipart/form-data" id="template-form">
    @csrf
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
                            <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="field-festival">
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
                            <label class="form-label">Or Add New Festival</label>
                            <input type="text" name="new_festival_title" class="form-control" value="{{ old('new_festival_title') }}" placeholder="e.g. महाशिवरात्रि / गणतंत्र दिवस">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Target Political Party</label>
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

                        <div class="col-md-6">
                            <label class="form-label required">Template Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', 'Devotthan Ekadashi Sacred Frame') }}" placeholder="e.g. Devotthan Ekadashi Sacred Frame" required>
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
                        <div class="col-md-12">
                            <label class="form-label required fw-bold text-primary">
                                <i class="ti ti-layout-bottombar me-1"></i> Branding Frame Layer (Footer Style)
                            </label>
                            <select name="footer_style" class="form-select border-primary fw-bold" id="field-footer-style">
                                <option value="dual_ribbon_badge" selected>Layer 1: Dual Ribbon Badge (Orange Box Over Photo + Green Party Strip) - [Bhai Dooj Style]</option>
                                <option value="solid_white_banner">Layer 2: Solid White Banner (Ethnic Pattern + Saffron Text) - [Milkha Singh Style]</option>
                                <option value="floating_tricolor_badge">Layer 3: Floating Tricolor Badge (Minimalist + Tiranga Divider) - [Diwali Style]</option>
                                <option value="royal_gold_dock">Layer 4: Royal Gold Banner (Deep Midnight Card + Gold Borders) - [Ceremonial Style]</option>
                                <option value="candidate_left_ribbon">Layer 5: Candidate Left Ribbon (Mirrored Ribbon for Right-Focus Artwork)</option>
                                <option value="modern_capsule_dock">Layer 6: Modern Capsule Dock (Floating Rounded Pill Card + Inset Socials)</option>
                                <option value="circular_medallion_badge">Layer 7: Circular Medallion Badge (Framed Portrait & Dual-Tier Card)</option>
                                <option value="saffron_wave_banner">Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave + Tricolor Accent)</option>
                                <option value="tricolor_angled_blade">Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)</option>
                                <option value="royal_maroon_heritage">Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)</option>
                            </select>
                            <small class="form-hint">Choose among 10 professional branding layouts (Ribbon badges, Ethnic cards, Floating minimalist, Royal midnight, Left-aligned, Capsule dock, Medallion frame, Saffron wave, Tricolor blade, or Maroon heritage).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Base Poster Preset</label>
                            <select name="preset_key" class="form-select @error('preset_key') is-invalid @enderror" id="field-preset" required>
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
                            <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                                <option value="bottom-right" {{ old('photo_position') == 'bottom-right' ? 'selected' : '' }}>Bottom Right (Classic Dr. Dharam Singh)</option>
                                <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Bottom Left (When artwork is on right)</option>
                                <option value="footer-strip" {{ old('photo_position') == 'footer-strip' ? 'selected' : '' }}>Footer Strip Avatar</option>
                            </select>
                            @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-bold text-primary">
                                <i class="ti ti-crown me-1"></i> Top Leadership Layout (Header Style)
                            </label>
                            <select name="header_layout" class="form-select border-primary fw-bold" id="field-header-layout">
                                <option value="full_width_strip" selected>Header 1: Full-Width Strip (6-7 Leaders Across Top)</option>
                                <option value="top_left_cluster">Header 2: Top-Left Cluster (3-4 Leaders + Right Party Logo)</option>
                                <option value="top_right_cluster">Header 3: Top-Right Cluster (3-4 Leaders + Left Party Logo)</option>
                                <option value="split_wings">Header 4: Split Wings (3 Left + 3 Right, Center Grand Opening)</option>
                                <option value="vertical_capsule_strip">Header 5: Vertical Capsule Strip (Tall Rounded Cards + Party Pill Badge)</option>
                                <option value="party_group_cutout">Header 6: Joint Group Cutout (Official High-Command Standing Cutout + Logo)</option>
                            </select>
                            <small class="form-hint">Placement of top national/state leadership across canvas.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Top Leaders Shape</label>
                            <select name="leader_shape" class="form-select" id="field-leader-shape">
                                <option value="circle" selected>Circular Avatar</option>
                                <option value="rounded_square">Rounded Square</option>
                                <option value="square">Square</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Leader Border Color</label>
                            <select name="leader_border_color" class="form-select" id="field-leader-border">
                                <option value="#FF9933" selected>Saffron Orange (#FF9933)</option>
                                <option value="#ffffff">Clean White (#FFFFFF)</option>
                                <option value="#D4AF37">Royal Gold (#D4AF37)</option>
                                <option value="none">No Border</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Top Leadership Strip</label>
                            <select name="show_top_strip" class="form-select" id="field-show-strip">
                                <option value="1" selected>Yes (Show Leaders)</option>
                                <option value="0">No (Hide Strip)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Politician Name Color</label>
                            <select name="name_color" class="form-select" id="field-name-color">
                                <option value="#c0392b" selected>Deep Red (#c0392b - Classic)</option>
                                <option value="#E65100">Saffron Deep (#E65100)</option>
                                <option value="#1a365d">Dark Navy (#1a365d)</option>
                                <option value="#2d3748">Dark Charcoal (#2d3748)</option>
                                <option value="#ffffff">Pure White (#ffffff)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Footer Bar Background</label>
                            <select name="footer_bg" class="form-select" id="field-footer-bg">
                                <option value="#f8f5ee" selected>Warm Light Cream (#f8f5ee)</option>
                                <option value="#ffffff">Pure White (#ffffff)</option>
                                <option value="#1a202c">Dark Midnight (#1a202c)</option>
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
                    <div class="mb-3">
                        <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                        <small class="form-hint">Square 1080x1080 JPG or PNG. As soon as you select an image, the Live Preview on the right updates instantly.</small>
                        @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Active in Festival Studio</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2 bg-white">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
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
    const fieldNameColor = document.getElementById('field-name-color');
    const fieldFooterBg = document.getElementById('field-footer-bg');

    const previewImg = document.getElementById('preview-image');
    const previewLoading = document.getElementById('preview-loading');
    const btnRefresh = document.getElementById('btn-manual-refresh');
    const btnDownload = document.getElementById('btn-download-preview');

    let debounceTimer = null;

    async function updatePreview() {
        if (previewLoading) previewLoading.classList.remove('d-none');

        const formData = new FormData();
        formData.append('_token', csrfToken);
        if (fieldFooterStyle) formData.append('footer_style', fieldFooterStyle.value);
        if (fieldHeaderLayout) formData.append('header_layout', fieldHeaderLayout.value);
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

    [fieldFooterStyle, fieldHeaderLayout, fieldPreset, fieldParty, fieldPhotoPos, fieldLeaderShape, fieldLeaderBorder, fieldShowStrip, fieldNameColor, fieldFooterBg].forEach(el => {
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
