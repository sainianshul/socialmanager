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

<div class="card">
    <form action="{{ route('admin.festival-templates.store') }}" method="POST" enctype="multipart/form-data" id="template-form">
        @csrf

        {{-- Hidden technical fields --}}
        <input type="hidden" name="preset_key" id="field-preset" value="{{ old('preset_key', 'classic_political_right') }}">
        <input type="hidden" name="show_top_strip" id="field-show-strip" value="{{ old('show_top_strip', '1') }}">

        <div class="card-body">
            <div class="row g-3">
                @php
                    $currentMonth = old('festival_month', now()->format('m'));
                @endphp

                {{-- Month / Quick Date Filter --}}
                <div class="col-md-3">
                    <label class="form-label">Festival Month</label>
                    <select class="form-select" id="filter-festival-month" name="festival_month">
                        <option value="">All Dates & Months</option>
                        <optgroup label="Quick Dates">
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                        </optgroup>
                        <optgroup label="By Calendar Month">
                            <option value="01" {{ $currentMonth === '01' ? 'selected' : '' }}>01 - January</option>
                            <option value="02" {{ $currentMonth === '02' ? 'selected' : '' }}>02 - February</option>
                            <option value="03" {{ $currentMonth === '03' ? 'selected' : '' }}>03 - March</option>
                            <option value="04" {{ $currentMonth === '04' ? 'selected' : '' }}>04 - April</option>
                            <option value="05" {{ $currentMonth === '05' ? 'selected' : '' }}>05 - May</option>
                            <option value="06" {{ $currentMonth === '06' ? 'selected' : '' }}>06 - June</option>
                            <option value="07" {{ $currentMonth === '07' ? 'selected' : '' }}>07 - July</option>
                            <option value="08" {{ $currentMonth === '08' ? 'selected' : '' }}>08 - August</option>
                            <option value="09" {{ $currentMonth === '09' ? 'selected' : '' }}>09 - September</option>
                            <option value="10" {{ $currentMonth === '10' ? 'selected' : '' }}>10 - October</option>
                            <option value="11" {{ $currentMonth === '11' ? 'selected' : '' }}>11 - November</option>
                            <option value="12" {{ $currentMonth === '12' ? 'selected' : '' }}>12 - December</option>
                        </optgroup>
                    </select>
                </div>

                {{-- Festival Selection with Quick Search --}}
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label required m-0">Festival</label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" id="toggle-new-festival">
                            <i class="ti ti-plus" id="new-fest-icon"></i> <span id="new-fest-text">New Festival</span>
                        </button>
                    </div>

                    <div class="input-group input-group-sm mb-1.5">
                        <span class="input-group-text bg-white text-muted"><i class="ti ti-search"></i></span>
                        <input type="text" id="festival-search-input" class="form-control" placeholder="Search festival (e.g. Diwali, Samvidhan)...">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-clear-festival-search" style="display: none;">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>

                    <select name="festival_id" class="form-select @error('festival_id') is-invalid @enderror" id="field-festival" required>
                        <option value="">Select Festival</option>
                        @foreach($festivals as $fest)
                            <option value="{{ $fest->id }}"
                                    data-month="{{ $fest->festival_date ? $fest->festival_date->format('m') : '' }}"
                                    data-date="{{ $fest->festival_date ? $fest->festival_date->format('Y-m-d') : '' }}"
                                    data-title="{{ strtolower($fest->title) }}"
                                    {{ old('festival_id') == $fest->id ? 'selected' : '' }}>
                                {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M Y') : 'No Date' }})
                            </option>
                        @endforeach
                    </select>
                    @error('festival_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="new-festival-box" class="mt-2 p-2 bg-light rounded border d-none">
                        <label class="form-label small mb-1">New Festival Title</label>
                        <input type="text" name="new_festival_title" id="field-new-festival-title" class="form-control form-control-sm" value="{{ old('new_festival_title') }}" placeholder="Festival Title">
                    </div>
                </div>

                {{-- Political Party --}}
                <div class="col-md-4">
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

                {{-- Template Title --}}
                <div class="col-md-6">
                    <label class="form-label required">Template Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Template Title" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Artwork Image Upload --}}
                <div class="col-md-6">
                    <label class="form-label required">Artwork Image</label>
                    <input type="file" name="template_image" id="field-image" class="form-control @error('template_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                    @error('template_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Branding Frame Layer (Footer Style) --}}
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
                    <div class="mt-1 d-flex align-items-center justify-content-between">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" name="footer_fixed" value="1" id="field-footer-fixed" {{ old('footer_fixed') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="field-footer-fixed">
                                <i class="ti ti-lock me-1 text-danger"></i><strong>Make this Strip Fixed</strong> (Locked on poster, user cannot change)
                            </label>
                        </div>
                        <span class="text-muted small">Default: Changeable by user</span>
                    </div>
                    @error('footer_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Top Leadership Header (Header Style) --}}
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
                    <div class="mt-1 d-flex align-items-center justify-content-between">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" name="header_fixed" value="1" id="field-header-fixed" {{ old('header_fixed') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="field-header-fixed">
                                <i class="ti ti-lock me-1 text-danger"></i><strong>Make this Header Fixed</strong> (Locked on poster, user cannot change)
                            </label>
                        </div>
                        <span class="text-muted small">Default: Changeable by user</span>
                    </div>

                    {{-- Assigned Leadership Strip --}}
                    <div class="mt-2.5 pt-2 border-top">
                        <label class="form-label small fw-semibold text-secondary mb-1 d-flex align-items-center justify-content-between">
                            <span><i class="ti ti-users text-primary me-1"></i>Assigned Leadership Strip (Optional)</span>
                            <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none" style="font-size: 11px;">
                                <i class="ti ti-plus me-0.5"></i>Create Custom Strip
                            </a>
                        </label>
                        <select name="strip_id" class="form-select form-select-sm" id="field-strip-id">
                            <option value="">-- Auto-Resolve (User Custom Strip or Party Default) --</option>
                            @if(isset($leaderStrips))
                                @foreach($leaderStrips as $ls)
                                    @php
                                        $isUser = !empty($ls->user_id);
                                        $partyCode = $ls->party?->short_code ?? 'Universal';
                                        $label = $isUser 
                                            ? '⭐ ' . $ls->name . ' (Custom Strip)' 
                                            : '🏛️ ' . $ls->name . ' (' . $partyCode . ' Official)';
                                    @endphp
                                    <option value="{{ $ls->id }}" {{ old('strip_id') == $ls->id ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="text-muted small" style="font-size: 11px;">Pick a specific strip for this template, or leave empty for auto-party resolution.</span>
                    </div>
                    @error('header_layout')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Candidate Photo Position --}}
                <div class="col-md-6">
                    <label class="form-label required">Candidate Photo Position</label>
                    <select name="photo_position" class="form-select @error('photo_position') is-invalid @enderror" id="field-photo-position" required>
                        <option value="bottom-right" {{ old('photo_position', 'bottom-right') == 'bottom-right' ? 'selected' : '' }}>Right Side (Standard)</option>
                        <option value="bottom-left" {{ old('photo_position') == 'bottom-left' ? 'selected' : '' }}>Left Side (When Artwork Is On Right)</option>
                    </select>
                    @error('photo_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Advanced Options Collapsible --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold" data-bs-toggle="collapse" href="#advanced-leader-settings" role="button" aria-expanded="false" aria-controls="advanced-leader-settings">
                            <span class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-adjustments-alt text-secondary"></i> Advanced: Leader Shape & Border Color
                            </span>
                            <span class="badge bg-secondary-lt small">Click to Expand <i class="ti ti-chevron-down ms-1"></i></span>
                        </a>
                        <div class="collapse mt-3" id="advanced-leader-settings">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Leader Avatar Shape</label>
                                    <select name="leader_shape" class="form-select form-select-sm" id="field-leader-shape">
                                        <option value="circle" {{ old('leader_shape', 'circle') === 'circle' ? 'selected' : '' }}>Circle (गोल - Standard)</option>
                                        <option value="rounded_square" {{ old('leader_shape') === 'rounded_square' ? 'selected' : '' }}>Rounded Square (घुमावदार चौकोर)</option>
                                        <option value="square" {{ old('leader_shape') === 'square' ? 'selected' : '' }}>Square (चौकोर / Boxed)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Leader Ring / Border Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="leader_border_color" id="field-leader-border" class="form-control form-control-color form-control-sm" value="{{ old('leader_border_color', '#FF9933') }}" title="Choose Custom Color">
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

                {{-- Active Status (Clean standard select like political leaders form) --}}
                <div class="col-md-6">
                    <label class="form-label required">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Live Preview Box: Hidden initially, shown only when an artwork image is selected --}}
                <div id="preview-box" class="col-md-12 d-none">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark">
                                <i class="ti ti-eye me-1"></i> Live Preview
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-manual-refresh">
                                    <i class="ti ti-refresh me-1"></i> Refresh
                                </button>
                                <a href="#" id="btn-download-preview" class="btn btn-sm btn-outline-success" download="preview-poster.jpg">
                                    <i class="ti ti-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        <div class="position-relative mx-auto rounded overflow-hidden border bg-dark" style="max-width: 480px; aspect-ratio: 1 / 1;" id="preview-wrapper">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-50 text-white z-3 d-none" id="preview-loading">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <span class="small fw-semibold">Rendering Live Preview...</span>
                            </div>
                            <img src="" alt="Template Live Preview" class="w-100 h-100 d-block" style="object-fit: contain;" id="preview-image">
                        </div>
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
    </form>
</div>

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

    const previewBox = document.getElementById('preview-box');
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
    const filterMonthSelect = document.getElementById('filter-festival-month');
    const festivalSearchInput = document.getElementById('festival-search-input');
    const btnClearSearch = document.getElementById('btn-clear-festival-search');
    const fieldNewFestTitle = document.getElementById('field-new-festival-title');

    // Month & Search Filter on Festival Select (Preserves Ascending Date Order)
    if (fieldFestivalSelect && filterMonthSelect) {
        const originalFestivalOptions = Array.from(fieldFestivalSelect.options).map(opt => ({
            value: opt.value,
            text: opt.textContent,
            month: opt.getAttribute('data-month') || '',
            date: opt.getAttribute('data-date') || '',
            title: (opt.getAttribute('data-title') || opt.textContent).toLowerCase(),
            selected: opt.selected
        }));

        function applyFestivalFilter() {
            const selectedFilter = filterMonthSelect.value;
            const query = festivalSearchInput ? festivalSearchInput.value.trim().toLowerCase() : '';
            const currentVal = fieldFestivalSelect.value;

            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            const tmrw = new Date(now);
            tmrw.setDate(now.getDate() + 1);
            const tomorrowStr = `${tmrw.getFullYear()}-${String(tmrw.getMonth() + 1).padStart(2, '0')}-${String(tmrw.getDate()).padStart(2, '0')}`;

            const dayOfWeek = (now.getDay() + 6) % 7; // Monday = 0
            const startOfWeekDate = new Date(now);
            startOfWeekDate.setDate(now.getDate() - dayOfWeek);
            const startOfWeekStr = `${startOfWeekDate.getFullYear()}-${String(startOfWeekDate.getMonth() + 1).padStart(2, '0')}-${String(startOfWeekDate.getDate()).padStart(2, '0')}`;

            const endOfWeekDate = new Date(startOfWeekDate);
            endOfWeekDate.setDate(startOfWeekDate.getDate() + 6);
            const endOfWeekStr = `${endOfWeekDate.getFullYear()}-${String(endOfWeekDate.getMonth() + 1).padStart(2, '0')}-${String(endOfWeekDate.getDate()).padStart(2, '0')}`;

            fieldFestivalSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select Festival';
            fieldFestivalSelect.appendChild(placeholder);

            let hasCurrent = false;

            originalFestivalOptions.forEach(item => {
                if (!item.value) return;

                let matchDate = true;
                if (selectedFilter === 'today') {
                    matchDate = (item.date === todayStr);
                } else if (selectedFilter === 'tomorrow') {
                    matchDate = (item.date === tomorrowStr);
                } else if (selectedFilter === 'this_week') {
                    matchDate = (item.date >= startOfWeekStr && item.date <= endOfWeekStr);
                } else if (selectedFilter === 'this_month') {
                    matchDate = (item.month === mm);
                } else if (selectedFilter) {
                    matchDate = (item.month === selectedFilter);
                }

                const matchSearch = !query || item.title.includes(query) || item.text.toLowerCase().includes(query);

                if (matchDate && matchSearch) {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    opt.setAttribute('data-month', item.month);
                    opt.setAttribute('data-date', item.date);
                    opt.setAttribute('data-title', item.title);
                    if (item.value === currentVal) {
                        opt.selected = true;
                        hasCurrent = true;
                    }
                    fieldFestivalSelect.appendChild(opt);
                }
            });

            if (!hasCurrent && currentVal !== '') {
                fieldFestivalSelect.value = '';
            }

            if (btnClearSearch) {
                btnClearSearch.style.display = query ? 'inline-block' : 'none';
            }
        }

        filterMonthSelect.addEventListener('change', applyFestivalFilter);

        if (festivalSearchInput) {
            festivalSearchInput.addEventListener('input', applyFestivalFilter);
        }

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', function () {
                festivalSearchInput.value = '';
                applyFestivalFilter();
                festivalSearchInput.focus();
            });
        }

        applyFestivalFilter();
    }

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
        if (!fieldImage || !fieldImage.files || !fieldImage.files[0]) {
            if (previewBox) previewBox.classList.add('d-none');
            return;
        }

        if (previewBox) previewBox.classList.remove('d-none');
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
        formData.append('template_image', fieldImage.files[0]);

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
        if (!fieldImage || !fieldImage.files || !fieldImage.files[0]) return;
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
});
</script>
@endpush
