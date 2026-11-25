{{-- Figma & Excalidraw Inspired Pure-White Festival Poster Studio Panel --}}
@php
    $firstTemplate = $templates->first();
    $userBranding = auth()->user()->defaultBranding ?? auth()->user()->brandings()->first();
    $userName = $userBranding?->effective_name ?? auth()->user()->name;
    $userParty = $userBranding?->party?->short_code ?? 'Universal';
    $activeCutout = $userCutout ?? ($userBranding?->defaultImage?->cutout_url);
    $captionText = ($selectedFestival->ready_caption ?? '') . ($selectedFestival->hashtags ? "\n\n" . $selectedFestival->hashtags : '');
    $isToday = $selectedFestival->festival_date->isToday();
    $targetTime = $isToday ? now()->addMinutes(10)->format('H:i') : '08:00';
@endphp

<div class="offcanvas offcanvas-end border-0 studio-slide-panel" tabindex="-1" id="studioSlidePanel" aria-labelledby="studioSlidePanelLabel" data-bs-backdrop="true">
    {{-- Crisp Pure-White Top Editor Bar --}}
    <div class="offcanvas-header border-bottom py-2.5 px-3 px-lg-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="avatar avatar-xs rounded-3 d-inline-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%); width: 28px; height: 28px;">
                <i class="ti ti-sparkles" style="font-size: 14px;"></i>
            </span>
            <h5 class="offcanvas-title fw-bold text-body mb-0" id="studioSlidePanelLabel" style="font-size: 1.02rem; letter-spacing: -0.02em;">
                Festival Poster Studio
            </h5>
            <span class="badge px-2.5 py-1 rounded-pill fw-medium studio-subheading-badge d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                <i class="ti ti-calendar-event text-teal" style="font-size: 12px;"></i>
                <span id="panel-subheading">{{ $selectedFestival->title }} ({{ $selectedFestival->festival_date->format('d M Y') }})</span>
            </span>
        </div>

        {{-- Crisp Outlined Stepper & Close --}}
        <div class="d-flex align-items-center gap-2">
            <div class="d-none d-sm-flex align-items-center p-1 rounded-pill studio-stepper-wrap">
                <button type="button" class="btn btn-sm border-0 rounded-pill px-3 py-1 fw-semibold panel-step-badge d-inline-flex align-items-center gap-1 active" id="panel-step-indicator-1" style="font-size: 11px;">
                    <i class="ti ti-adjustments-horizontal" style="font-size: 13px;"></i>
                    <span>1. Customize</span>
                </button>
                <button type="button" class="btn btn-sm border-0 rounded-pill px-3 py-1 fw-medium panel-step-badge d-inline-flex align-items-center gap-1" id="panel-step-indicator-2" style="font-size: 11px;">
                    <i class="ti ti-calendar-time" style="font-size: 13px;"></i>
                    <span>2. Schedule</span>
                </button>
            </div>
            <button type="button" class="btn-close ms-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>

    {{-- Panel Body (Pure White Surface) --}}
    <div class="offcanvas-body p-3 p-lg-4">

        {{-- ================================================================= --}}
        {{-- STEP 1: DOT-GRID ARTBOARD STAGE + ICON-LED INSPECTOR TABS         --}}
        {{-- ================================================================= --}}
        <div id="panel-step-pane-1">
            <div class="row g-3 g-lg-4 align-items-start">

                {{-- LEFT COLUMN: Sticky 1:1 Artboard Stage + Action Dock --}}
                <div class="col-lg-6 studio-sticky-stage">
                    <div class="mx-auto" style="max-width: 425px;">

                        {{-- Crisp Excalidraw-Style Dot Canvas Wrapper --}}
                        <div class="studio-canvas-stage">
                            {{-- Design Variants Strip --}}
                            @if($templates->count() > 1)
                                <div class="mb-2.5">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5 px-0.5">
                                        <span class="text-secondary fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                            <i class="ti ti-layout-grid text-teal" style="font-size: 13px;"></i>
                                            Design Variants
                                        </span>
                                        <span class="badge studio-white-pill fw-medium font-monospace rounded-pill px-2" style="font-size: 10px;">
                                            {{ $templates->count() }} Designs
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1" id="panel-template-switcher">
                                        @foreach($templates as $tpl)
                                            @php
                                                $tplFooterStyle = $tpl->layout_config['footer']['style'] ?? ($tpl->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
                                                $tplHeaderLayout = $tpl->layout_config['header']['layout'] ?? ($tpl->layout_config['header']['mode'] ?? 'full_width_strip');
                                                $tplFooterFixed = !empty($tpl->layout_config['footer']['is_fixed']) ? '1' : '0';
                                                $tplHeaderFixed = !empty($tpl->layout_config['header']['is_fixed']) ? '1' : '0';
                                                $tplLeaderShape = $tpl->layout_config['header']['chip_shape'] ?? 'circle';
                                                $tplLeaderBorder = $tpl->layout_config['header']['chip_border_color'] ?? '#FF9933';
                                            @endphp
                                            <div class="panel-template-chip position-relative rounded-3 overflow-hidden cursor-pointer flex-shrink-0 {{ $loop->first ? 'active' : '' }}"
                                                 id="panel-tpl-chip-{{ $tpl->id }}"
                                                 data-template-id="{{ $tpl->id }}"
                                                 data-template-title="{{ $tpl->title }}"
                                                 data-template-image="{{ $tpl->image_url }}"
                                                 data-footer-style="{{ $tplFooterStyle }}"
                                                 data-footer-fixed="{{ $tplFooterFixed }}"
                                                 data-header-layout="{{ $tplHeaderLayout }}"
                                                 data-header-fixed="{{ $tplHeaderFixed }}"
                                                 data-leader-shape="{{ $tplLeaderShape }}"
                                                 data-leader-border="{{ $tplLeaderBorder }}"
                                                 data-party-id="{{ $tpl->party_id ?? '' }}"
                                                 data-strip-id="{{ $tpl->layout_config['header']['strip_id'] ?? '' }}"
                                                 style="width: 52px; height: 52px;">
                                                <img src="{{ $tpl->image_url }}" alt="{{ $tpl->title }}" class="w-100 h-100 object-fit-cover">
                                                <span class="panel-chip-check badge bg-teal rounded-circle p-1 position-absolute top-0 end-0 m-0.5 {{ $loop->first ? '' : 'd-none' }}" style="font-size: 7px;">
                                                    <i class="ti ti-check text-white"></i>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- 1:1 Poster Canvas Frame --}}
                            <div class="studio-canvas-frame position-relative overflow-hidden rounded-3" style="aspect-ratio: 1 / 1; background: #0f172a;">
                                <a href="#" id="panel-preview-link" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100 text-decoration-none" title="Click to inspect full 1080p image" style="cursor: zoom-in;">
                                    <img id="panel-canvas-img" src="{{ $firstTemplate?->image_url }}" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">
                                </a>

                                {{-- Crisp Quality Badge --}}
                                <div class="position-absolute top-0 end-0 m-2 pointer-events-none">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: rgba(15, 23, 42, 0.72); color: #ffffff; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.16); font-size: 10px; letter-spacing: 0.04em;">
                                        1080p HD
                                    </span>
                                </div>

                                {{-- Smooth Frosted Loading State --}}
                                <div id="panel-canvas-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white d-none" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 10;">
                                    <div class="spinner-border text-teal mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                                    <div class="small fw-semibold">Updating Poster...</div>
                                </div>
                            </div>
                        </div>

                        {{-- Crisp Floating White Action Dock + Primary Schedule CTA --}}
                        <div class="d-flex align-items-center gap-2 mt-2.5">
                            <div class="d-flex align-items-center gap-1.5">
                                <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-preview" target="_blank" rel="noopener noreferrer" class="btn rounded-circle d-flex align-items-center justify-content-center p-0 panel-icon-btn" data-bs-toggle="tooltip" title="Full-Screen Preview">
                                    <i class="ti ti-maximize" style="font-size: 15px;"></i>
                                </a>
                                <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-download" target="_blank" download="{{ Str::slug($selectedFestival->title) }}-poster.jpg" class="btn rounded-circle d-flex align-items-center justify-content-center p-0 panel-icon-btn" data-bs-toggle="tooltip" title="Download 1080p Image">
                                    <i class="ti ti-download" style="font-size: 15px;"></i>
                                </a>
                                <button type="button" id="panel-btn-whatsapp" class="btn rounded-circle d-flex align-items-center justify-content-center p-0 panel-icon-btn panel-icon-whatsapp" data-bs-toggle="tooltip" title="Share on WhatsApp">
                                    <i class="ti ti-brand-whatsapp" style="font-size: 16px;"></i>
                                </button>
                                <button type="button" id="panel-btn-share" class="btn rounded-circle d-flex align-items-center justify-content-center p-0 panel-icon-btn" data-bs-toggle="tooltip" title="Copy Share Link">
                                    <i class="ti ti-link" style="font-size: 15px;"></i>
                                </button>
                            </div>

                            <button type="button" class="btn btn-primary rounded-pill flex-grow-1 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 studio-primary-cta" id="panel-btn-go-schedule" style="font-size: 14px; letter-spacing: -0.01em;">
                                <i class="ti ti-calendar-plus" style="font-size: 17px;"></i>
                                <span>Schedule Post</span>
                                <i class="ti ti-arrow-right" style="font-size: 15px;"></i>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- RIGHT COLUMN: Pure-White Figma/Excalidraw Icon Inspector --}}
                <div class="col-lg-6">

                    {{-- Crisp Outlined White Tab Switcher with Icons --}}
                    <div class="studio-segmented-bar p-1 rounded-3 mb-3 d-flex gap-1">
                        <button type="button" class="btn btn-sm flex-fill rounded-2 py-2 fw-semibold studio-seg-tab d-flex align-items-center justify-content-center gap-1.5 active" id="panel-inspector-tab-branding" data-inspector-target="branding">
                            <i class="ti ti-user-circle" style="font-size: 16px;"></i>
                            <span>My Photo &amp; Ribbon</span>
                        </button>
                        <button type="button" class="btn btn-sm flex-fill rounded-2 py-2 fw-medium studio-seg-tab d-flex align-items-center justify-content-center gap-1.5" id="panel-inspector-tab-header" data-inspector-target="header">
                            <i class="ti ti-users-group" style="font-size: 16px;"></i>
                            <span>Top Leaders</span>
                        </button>
                    </div>

                    {{-- ===================================================== --}}
                    {{-- INSPECTOR TAB 1: MY PHOTO, POSITION & BRANDING STRIP   --}}
                    {{-- ===================================================== --}}
                    <div id="panel-inspector-pane-branding">
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-layout-bottombar text-teal" style="font-size: 15px;"></i>
                                    Branding Bottom Strip
                                </span>
                                <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-teal small text-decoration-none fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                    <span>Profile Settings</span>
                                    <i class="ti ti-external-link" style="font-size: 12px;"></i>
                                </a>
                            </div>

                            <div class="d-flex flex-column" style="gap: 14px;">

                                {{-- 1. Photo Cutout Selector --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-photo text-teal" style="font-size: 14px;"></i>
                                            <span>Choose Your Photo</span>
                                        </label>
                                        <label for="panel-upload-photo-input" class="btn btn-sm studio-white-pill rounded-pill cursor-pointer mb-0 px-2.5 py-1 fw-semibold text-teal d-inline-flex align-items-center gap-1" style="font-size: 11px; border-color: #99F6E4 !important;">
                                            <i class="ti ti-cloud-upload" style="font-size: 13px;"></i>
                                            <span>Upload Photo</span>
                                        </label>
                                    </div>
                                    <input type="file" id="panel-upload-photo-input" class="d-none" accept="image/jpeg,image/png,image/webp">
                                    <input type="file" id="qs-upload-photo-input" class="d-none">
                                    <input type="hidden" id="panel-cutout-url" value="{{ $activeCutout }}">

                                    <div class="d-flex align-items-center gap-2 overflow-x-auto py-1 px-0.5" id="panel-photo-chips-wrapper">
                                        @if($activeCutout)
                                            <div class="panel-photo-chip position-relative rounded-circle cursor-pointer active flex-shrink-0"
                                                 data-url="{{ $activeCutout }}">
                                                <img src="{{ $activeCutout }}" alt="Active Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                            </div>
                                        @endif
                                        @foreach($userImages as $uImg)
                                            @if($uImg->cutout_url !== $activeCutout)
                                                <div class="panel-photo-chip position-relative rounded-circle cursor-pointer flex-shrink-0"
                                                     data-url="{{ $uImg->cutout_url }}">
                                                    <img src="{{ $uImg->cutout_url }}" alt="Branding Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 2. Crisp White Photo Size & Adjust Position Controls --}}
                                <div class="p-3 rounded-3 studio-panel-card-inner">
                                    {{-- 2a. Photo Size with Visual Icons --}}
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                                <i class="ti ti-aspect-ratio text-teal" style="font-size: 14px;"></i>
                                                <span>Photo Size</span>
                                            </label>
                                            <span class="badge studio-white-pill font-monospace rounded-pill px-2" id="panel-candidate-scale-label" style="font-size: 10px;">100%</span>
                                        </div>
                                        <input type="hidden" id="panel-candidate-scale" value="100">
                                        <div class="studio-segmented-bar p-1 rounded-3 d-flex gap-1" id="panel-candidate-scale-tiles">
                                            <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-scale="85">
                                                <i class="ti ti-minimize" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Small</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1 active" data-scale="100">
                                                <i class="ti ti-aspect-ratio" style="font-size: 13px;"></i>
                                                <span class="small fw-semibold" style="font-size: 11.5px;">Normal</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-scale="115">
                                                <i class="ti ti-maximize" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Large</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- 2b. Crisp Arrow Position Nudge Controls --}}
                                    <div class="pt-2.5 border-top">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                                <i class="ti ti-arrows-move text-teal" style="font-size: 14px;"></i>
                                                <span>Adjust Position</span>
                                                <i class="ti ti-info-circle text-muted cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Tap arrows to move your photo slightly Left, Right, Up or Down"></i>
                                            </label>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge studio-white-pill fw-medium rounded-pill px-2" id="panel-candidate-pos-status" style="font-size: 10px;">Centered</span>
                                                <button type="button" id="panel-candidate-pos-reset" class="btn btn-sm studio-white-pill rounded-pill px-2 py-0 text-teal fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 10.5px; height: 22px;" title="Reset photo position">
                                                    <i class="ti ti-rotate-clockwise" style="font-size: 11px;"></i>
                                                    <span>Reset</span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" id="panel-candidate-offset-x" value="0">
                                        <input type="hidden" id="panel-candidate-offset-y" value="0">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="studio-segmented-bar p-1 rounded-3 d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm border-0 panel-pos-nudge flex-fill py-1.5 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-axis="x" data-delta="-25" title="Move Left">
                                                        <i class="ti ti-arrow-left" style="font-size: 14px;"></i>
                                                        <span style="font-size: 11.5px;">Left</span>
                                                    </button>
                                                    <span class="border-end" style="height: 16px;"></span>
                                                    <button type="button" class="btn btn-sm border-0 panel-pos-nudge flex-fill py-1.5 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-axis="x" data-delta="25" title="Move Right">
                                                        <span style="font-size: 11.5px;">Right</span>
                                                        <i class="ti ti-arrow-right" style="font-size: 14px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="studio-segmented-bar p-1 rounded-3 d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm border-0 panel-pos-nudge flex-fill py-1.5 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-axis="y" data-delta="-20" title="Move Up">
                                                        <i class="ti ti-arrow-up" style="font-size: 14px;"></i>
                                                        <span style="font-size: 11.5px;">Up</span>
                                                    </button>
                                                    <span class="border-end" style="height: 16px;"></span>
                                                    <button type="button" class="btn btn-sm border-0 panel-pos-nudge flex-fill py-1.5 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-axis="y" data-delta="20" title="Move Down">
                                                        <span style="font-size: 11.5px;">Down</span>
                                                        <i class="ti ti-arrow-down" style="font-size: 14px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. Footer Ribbon Style Selection --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label for="panel-footer-style" class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-layout-bottombar text-teal" style="font-size: 14px;"></i>
                                            <span>Footer Ribbon Style</span>
                                        </label>
                                        <span id="panel-footer-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select qs-premium-select" id="panel-footer-style">
                                        <option value="dual_ribbon_badge">Dual Ribbon Badge</option>
                                        <option value="solid_white_banner">Solid White Banner</option>
                                        <option value="floating_tricolor_badge">Floating Tricolor</option>
                                        <option value="royal_gold_dock">Royal Gold Banner</option>
                                        <option value="candidate_left_ribbon">Candidate Left Ribbon</option>
                                        <option value="modern_capsule_dock">Modern Capsule Dock</option>
                                        <option value="circular_medallion_badge">Circular Medallion</option>
                                        <option value="saffron_wave_banner">Saffron Wave Banner</option>
                                        <option value="tricolor_angled_blade">Tricolor Angled Blade</option>
                                        <option value="royal_maroon_heritage">Royal Maroon Heritage</option>
                                    </select>
                                </div>

                                {{-- 4. Name & Post Override (Crisp Pure-White Accordion) --}}
                                <div>
                                    <a class="d-flex align-items-center justify-content-between text-decoration-none cursor-pointer py-2.5 px-3 rounded-3 studio-panel-card-inner" data-bs-toggle="collapse" href="#panel-candidate-details-collapse" role="button" aria-expanded="false" aria-controls="panel-candidate-details-collapse">
                                        <span class="small fw-semibold text-body d-flex align-items-center gap-1.5" style="font-size: 12px;">
                                            <i class="ti ti-user-edit text-teal" style="font-size: 15px;"></i>
                                            <span>Edit Name &amp; Post</span>
                                        </span>
                                        <span class="small text-secondary d-flex align-items-center gap-1.5" style="font-size: 11px;">
                                            <span class="text-truncate" style="max-width: 115px;">{{ $userName }}</span>
                                            <i class="ti ti-chevron-down" style="font-size: 14px;"></i>
                                        </span>
                                    </a>
                                    <div class="collapse mt-2" id="panel-candidate-details-collapse">
                                        <div class="p-3 rounded-3 studio-panel-card-inner">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label small mb-1 d-inline-flex align-items-center gap-1" style="font-size: 10.5px;">
                                                        <i class="ti ti-id-badge-2 text-teal" style="font-size: 12px;"></i>
                                                        <span>Name</span>
                                                    </label>
                                                    <input type="text" id="panel-candidate-name" name="candidate_name" class="form-control form-control-sm rounded-2" value="{{ $userName }}" placeholder="e.g. राहुल शर्मा" style="font-size: 12px; height: 36px;">
                                                    <input type="hidden" id="qs-modal-candidate-name" value="{{ $userName }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small mb-1 d-inline-flex align-items-center gap-1" style="font-size: 10.5px;">
                                                        <i class="ti ti-pencil text-teal" style="font-size: 12px;"></i>
                                                        <span>Post / Designation</span>
                                                    </label>
                                                    <input type="text" id="panel-candidate-designation" name="candidate_designation" class="form-control form-control-sm rounded-2" value="{{ $userBranding?->designation }}" placeholder="e.g. जिला महामंत्री" style="font-size: 12px; height: 36px;">
                                                    <input type="hidden" id="qs-modal-candidate-designation" value="{{ $userBranding?->designation }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. Social Handles Switch --}}
                                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top">
                                    <span class="small fw-medium text-body d-inline-flex align-items-center gap-1.5" style="font-size: 12px;">
                                        <i class="ti ti-share text-teal" style="font-size: 14px;"></i>
                                        <span>Show Social Media Handles</span>
                                    </span>
                                    <div class="form-check form-switch m-0 ps-0">
                                        <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="panel-show-social" checked style="width: 36px; height: 19px;">
                                        <input type="checkbox" id="qs-modal-show-social" class="d-none" checked>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ===================================================== --}}
                    {{-- INSPECTOR TAB 2: TOP LEADERSHIP HEADER                --}}
                    {{-- ===================================================== --}}
                    <div id="panel-inspector-pane-header" class="d-none">
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-crown text-teal" style="font-size: 15px;"></i>
                                    Top Leadership Header
                                </span>
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-teal small text-decoration-none fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                        <span>Manage Strips</span>
                                        <i class="ti ti-external-link" style="font-size: 12px;"></i>
                                    </a>
                                @endif
                            </div>

                            <div class="d-flex flex-column" style="gap: 14px;">
                                {{-- 1. Leadership Strip Selection --}}
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <div>
                                        <label for="panel-strip-id" class="form-label small mb-1.5 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-users-group text-teal" style="font-size: 14px;"></i>
                                            <span>Leadership Strip</span>
                                        </label>
                                        <select class="form-select qs-premium-select" id="panel-strip-id">
                                            @php
                                                $sysList = (isset($systemStrips) && $systemStrips->isNotEmpty()) ? $systemStrips : $allStrips->where('is_system', true);
                                                $usrList = (isset($userStrips) && $userStrips->isNotEmpty()) ? $userStrips : $allStrips->where('is_system', false);
                                            @endphp
                                            @if($sysList->isNotEmpty())
                                                <optgroup label="Official {{ $userParty }} Leadership Strips">
                                                    @foreach($sysList as $s)
                                                        <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                                            {{ $s->name }}{{ $s->is_default ? ' (Party Default)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                            @if($usrList->isNotEmpty())
                                                <optgroup label="My Custom Strips">
                                                    @foreach($usrList as $s)
                                                        <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                                            {{ $s->name }} (Custom){{ $s->is_default ? ' (My Default)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        </select>
                                    </div>
                                @endif

                                {{-- 2. Header Layout Design --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label for="panel-header-layout" class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-layout-navbar text-teal" style="font-size: 14px;"></i>
                                            <span>Header Layout Style</span>
                                        </label>
                                        <span id="panel-header-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select qs-premium-select" id="panel-header-layout">
                                        <option value="full_width_strip">Full-Width Strip</option>
                                        <option value="top_left_cluster">Top-Left Cluster</option>
                                        <option value="top_right_cluster">Top-Right Cluster</option>
                                        <option value="split_wings">Split Wings</option>
                                        <option value="vertical_capsule_strip">Vertical Capsules</option>
                                        <option value="party_group_cutout">Group Cutout</option>
                                        <option value="frosted_glass_dock">Frosted Glass Dock</option>
                                        <option value="tricolor_ribbon_canopy">Tricolor Canopy</option>
                                        <option value="none">None (Hide Header)</option>
                                    </select>
                                </div>

                                {{-- 3. Leader Frame Shape, Ring Color & Size (Pure White Grouped Block) --}}
                                <div class="p-3 rounded-3 studio-panel-card-inner">
                                    <div class="mb-3">
                                        <label class="form-label small mb-1.5 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-square-rounded text-teal" style="font-size: 14px;"></i>
                                            <span>Frame Shape</span>
                                        </label>
                                        <input type="hidden" id="panel-leader-shape" value="circle">
                                        <div class="studio-segmented-bar p-1 rounded-3 d-flex gap-1" id="panel-shape-tiles">
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1 active" data-shape="circle">
                                                <i class="ti ti-circle" style="font-size: 13px;"></i>
                                                <span class="small fw-semibold" style="font-size: 11.5px;">Circle</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-shape="rounded_square">
                                                <i class="ti ti-square-rounded" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Rounded</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-shape="square">
                                                <i class="ti ti-square" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Square</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3 pt-2.5 border-top">
                                        <label class="form-label small mb-1.5 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-palette text-teal" style="font-size: 14px;"></i>
                                            <span>Ring Border Color</span>
                                        </label>
                                        <div class="d-flex align-items-center gap-2 flex-wrap" id="panel-color-swatches">
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle active" data-color="#FF9933" style="width: 26px; height: 26px; background: #FF9933;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle" data-color="#FFFFFF" style="width: 26px; height: 26px; background: #FFFFFF;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle" data-color="#138808" style="width: 26px; height: 26px; background: #138808;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle" data-color="#D4AF37" style="width: 26px; height: 26px; background: #D4AF37;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle" data-color="#DC2626" style="width: 26px; height: 26px; background: #DC2626;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-circle" data-color="#1E3A8A" style="width: 26px; height: 26px; background: #1E3A8A;"></button>
                                        </div>
                                        <input type="hidden" id="panel-leader-border" value="#FF9933">
                                    </div>

                                    <div class="pt-2.5 border-top">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small mb-0 d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                                <i class="ti ti-aspect-ratio text-teal" style="font-size: 14px;"></i>
                                                <span>Leader Photo Size</span>
                                            </label>
                                            <span class="badge studio-white-pill font-monospace rounded-pill px-2" id="panel-leader-size-label" style="font-size: 10px;">Medium</span>
                                        </div>
                                        <input type="hidden" id="panel-leader-size" value="104">
                                        <div class="studio-segmented-bar p-1 rounded-3 d-flex gap-1" id="panel-leader-size-tiles">
                                            <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-size="88">
                                                <i class="ti ti-minimize" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Small</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1 active" data-size="104">
                                                <i class="ti ti-aspect-ratio" style="font-size: 13px;"></i>
                                                <span class="small fw-semibold" style="font-size: 11.5px;">Medium</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1.5 px-2 rounded-2 d-inline-flex align-items-center justify-content-center gap-1" data-size="120">
                                                <i class="ti ti-maximize" style="font-size: 13px;"></i>
                                                <span class="small fw-medium" style="font-size: 11.5px;">Large</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- STEP 2: SCHEDULE POST DETAILS                                     --}}
        {{-- ================================================================= --}}
        <div id="panel-step-pane-2" class="d-none">

            {{-- Navigation Bar --}}
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="panel-btn-back-step">
                    <i class="ti ti-arrow-left me-1"></i> Back to Design
                </button>
                <span class="badge studio-white-pill text-teal rounded-pill px-3 py-1 font-monospace" id="panel-schedule-festival-date" style="border-color: #99F6E4 !important;">
                    {{ $selectedFestival->festival_date->format('d M Y') }}
                </span>
            </div>

            <div class="row g-4 align-items-start">

                {{-- Left Side: Poster Thumbnail Preview --}}
                <div class="col-md-5 col-lg-4 text-center">
                    <div class="studio-canvas-stage">
                        <div class="studio-canvas-frame rounded-3 overflow-hidden mx-auto" style="max-width: 260px; aspect-ratio: 1 / 1; background: #0f172a;">
                            <img id="panel-schedule-thumb" src="{{ $firstTemplate?->image_url }}" alt="Scheduled Poster" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <div class="fw-bold text-body mt-2.5 text-truncate" id="panel-schedule-template-title" style="font-size: 13px;">
                        {{ $firstTemplate?->title }}
                    </div>
                    <div class="text-muted small" style="font-size: 11px;">1080p Ultra HD Personalized Poster</div>
                </div>

                {{-- Right Side: Schedule Controls --}}
                <div class="col-md-7 col-lg-8">
                    <div class="card studio-panel-card p-3.5 mb-0">
                        {{-- 1. Connected Social Media Accounts --}}
                        <div class="mb-3">
                            <label class="form-label required mb-2 d-inline-flex align-items-center gap-1.5">
                                <i class="ti ti-share text-teal" style="font-size: 14px;"></i>
                                <span>Publish to Social Accounts</span>
                            </label>
                            <div class="d-flex flex-column gap-2" id="panel-accounts-container">
                                @forelse($accounts as $acc)
                                    <label class="form-check panel-account-item active m-0 cursor-pointer p-2.5 rounded-3 d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input panel-acc-cb m-0" value="{{ $acc->id }}" checked>
                                        <span class="form-check-label">
                                            <span class="fw-semibold text-body">{{ $acc->name }}</span>
                                            <span class="text-muted small ms-1">({{ $acc->account_type == 1 ? 'Facebook Page' : 'Instagram Professional' }})</span>
                                        </span>
                                    </label>
                                @empty
                                    <div class="text-muted small">
                                        No active social media accounts connected. <a href="{{ route('admin.accounts.index') }}">Connect Account</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 2. Date & Time Inputs --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label required d-inline-flex align-items-center gap-1.5" for="panel-schedule-date">
                                    <i class="ti ti-calendar-event text-teal" style="font-size: 14px;"></i>
                                    <span>Publish Date</span>
                                </label>
                                <input type="date" id="panel-schedule-date" class="form-control rounded-3" value="{{ $selectedFestival->festival_date->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label required d-inline-flex align-items-center gap-1.5" for="panel-schedule-time">
                                    <i class="ti ti-clock text-teal" style="font-size: 14px;"></i>
                                    <span>Publish Time</span>
                                </label>
                                <input type="time" id="panel-schedule-time" class="form-control rounded-3" value="{{ $targetTime }}">
                            </div>
                        </div>

                        {{-- 3. Caption & Hashtags --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label mb-0 d-inline-flex align-items-center gap-1.5" for="panel-caption-text">
                                    <i class="ti ti-pencil text-teal" style="font-size: 14px;"></i>
                                    <span>Caption &amp; Hashtags</span>
                                </label>
                                <button type="button" class="btn btn-sm studio-white-pill rounded-pill px-2.5 py-0.5 text-teal text-decoration-none d-inline-flex align-items-center gap-1" id="panel-btn-copy-caption" style="font-size: 11px;">
                                    <i class="ti ti-copy" style="font-size: 12px;"></i>
                                    <span>Copy</span>
                                </button>
                            </div>
                            <textarea id="panel-caption-text" class="form-control rounded-3" rows="4" placeholder="Enter post caption...">{{ $captionText }}</textarea>
                        </div>

                        {{-- Alert Slot --}}
                        <div id="panel-schedule-alert" class="d-none alert alert-danger mb-3 rounded-3"></div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top mt-3">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="panel-btn-cancel-step">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary rounded-pill px-4 studio-primary-cta d-inline-flex align-items-center gap-1.5" id="panel-btn-confirm-schedule">
                                <i class="ti ti-calendar-check"></i>
                                <span>Confirm &amp; Schedule Post</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Hidden Metadata Inputs --}}
    <input type="hidden" id="panel-input-template-id" value="{{ $firstTemplate?->id }}">
    <input type="hidden" id="panel-input-festival-id" value="{{ $selectedFestival?->id }}">
    <input type="hidden" id="panel-composed-path" value="">
</div>
