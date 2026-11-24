{{-- Apple-Grade Minimalist Festival Poster Slide-Over Panel --}}
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
    {{-- Panel Header --}}
    <div class="offcanvas-header border-bottom py-3 px-4 px-lg-5 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h5 class="offcanvas-title fw-bold text-body mb-0" id="studioSlidePanelLabel" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                Festival Poster Studio
            </h5>
            <span class="badge px-3 py-1 rounded-pill fw-medium small studio-subheading-badge" id="panel-subheading" style="font-size: 11.5px;">
                {{ $selectedFestival->title }} ({{ $selectedFestival->festival_date->format('d M Y') }})
            </span>
        </div>

        {{-- Stepper Indicator & Close Button --}}
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-sm-flex align-items-center gap-1 p-1 rounded-pill studio-stepper-wrap">
                <span class="badge text-white rounded-pill px-3 py-1.5 fw-semibold cursor-pointer panel-step-badge active" id="panel-step-indicator-1" style="font-size: 11px; background: #0D9488;">
                    1. Poster Style
                </span>
                <span class="badge bg-transparent text-secondary rounded-pill px-3 py-1.5 fw-medium cursor-pointer panel-step-badge" id="panel-step-indicator-2" style="font-size: 11px;">
                    2. Schedule Details
                </span>
            </div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>

    {{-- Panel Body --}}
    <div class="offcanvas-body p-4 p-lg-5">
        
        {{-- ================================================================= --}}
        {{-- STEP 1: MINIMALIST POSTER PREVIEW & PRO TOOLBAR                  --}}
        {{-- ================================================================= --}}
        <div id="panel-step-pane-1">
            <div class="row g-4 g-lg-5 align-items-start">
                
                {{-- Left Column: 1080p Canvas + Action Icon Bar + Big Schedule Button --}}
                <div class="col-lg-6">
                    
                    {{-- Multi-Template Mini Switcher (if more than 1 template) --}}
                    @if($templates->count() > 1)
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase text-secondary fw-semibold small" style="font-size: 10.5px; letter-spacing: 0.05em;">
                                    Select Template (डिज़ाइन चुनें)
                                </span>
                                <span class="text-secondary small font-monospace" style="font-size: 11px;">
                                    {{ $templates->count() }} Designs
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 overflow-x-auto py-1" id="panel-template-switcher">
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
                                         style="width: 68px; height: 68px; transition: all 0.2s ease;">
                                        <img src="{{ $tpl->image_url }}" alt="{{ $tpl->title }}" class="w-100 h-100 object-fit-cover">
                                        <span class="panel-chip-check badge bg-success rounded-circle p-1 position-absolute top-0 end-0 m-1 {{ $loop->first ? '' : 'd-none' }}" style="font-size: 7px;">
                                            <i class="ti ti-check"></i>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 1:1 Live Preview Canvas --}}
                    <div class="position-relative overflow-hidden shadow-sm rounded-4" style="aspect-ratio: 1 / 1; background: #0f172a; border: 1px solid rgba(0, 0, 0, 0.08);">
                        <a href="#" id="panel-preview-link" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100 text-decoration-none" title="Click to view 1080p full resolution in new tab" style="cursor: zoom-in;">
                            <img id="panel-canvas-img" src="{{ $firstTemplate?->image_url }}" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">
                        </a>

                        {{-- 1080p HD Badge --}}
                        <div class="position-absolute top-0 end-0 m-3 pointer-events-none">
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background: rgba(15, 23, 42, 0.78); color: #ffffff; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15); font-size: 11px; letter-spacing: 0.04em;">
                                1080p Ultra HD
                            </span>
                        </div>

                        {{-- Loading Spinner Overlay --}}
                        <div id="panel-canvas-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white d-none" style="backdrop-filter: blur(8px); z-index: 10;">
                            <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                            <div class="small fw-semibold">Personalizing Poster...</div>
                            <div class="text-white-50 small mt-0.5" style="font-size: 11px;">Rendering your branding in 1080p</div>
                        </div>
                    </div>

                    {{-- Action Icons Bar: Compact, Sleek, Clean Circular Icons --}}
                    <div class="d-flex align-items-center justify-content-center gap-2.5 mt-3">
                        <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-preview" target="_blank" rel="noopener noreferrer" class="btn rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn" title="Full-Screen Preview (1080p)">
                            <i class="ti ti-maximize" style="font-size: 16px;"></i>
                        </a>
                        <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-download" target="_blank" download="{{ Str::slug($selectedFestival->title) }}-poster.jpg" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center border-0 shadow-xs p-0 panel-icon-btn" style="background: #0D9488;" title="Download High-Res 1080p">
                            <i class="ti ti-download text-white" style="font-size: 16px;"></i>
                        </a>
                        <button type="button" id="panel-btn-whatsapp" class="btn rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn panel-icon-whatsapp" title="Share on WhatsApp">
                            <i class="ti ti-brand-whatsapp" style="font-size: 17px;"></i>
                        </button>
                        <button type="button" id="panel-btn-share" class="btn rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn" title="Share Poster Link">
                            <i class="ti ti-share" style="font-size: 16px;"></i>
                        </button>
                    </div>

                    {{-- Big Schedule Button with Generous Top Margin --}}
                    <div class="mt-4 pt-1">
                        <button type="button" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="panel-btn-go-schedule" style="background: #0D9488; border-color: #0D9488; font-size: 15px; letter-spacing: -0.01em;">
                            <i class="ti ti-calendar-time fs-2"></i>
                            <span>Schedule Post</span>
                            <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>

                </div>

                {{-- Right Column: Apple-Clean Toolbar --}}
                <div class="col-lg-6">
                    <div class="d-flex flex-column" style="gap: 20px;">
                        
                        {{-- ========================================================= --}}
                        {{-- SECTION 1: TOP LEADERSHIP HEADER (शीर्ष नेतृत्व हेडर)       --}}
                        {{-- ========================================================= --}}
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-crown text-primary" style="font-size: 14px;"></i>
                                    Top Leadership Header
                                </span>
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 11px;">
                                        Manage Strips &rarr;
                                    </a>
                                @endif
                            </div>

                            <div class="d-flex flex-column" style="gap: 14px;">
                                {{-- 1.1 Leadership Strip Selection --}}
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <div>
                                        <label for="panel-strip-id" class="form-label text-muted fw-semibold small text-uppercase mb-1" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            Select Leadership Strip
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

                                {{-- 1.2 Header Layout Design --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label for="panel-header-layout" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            Header Layout Design
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

                                {{-- 1.3 Leader Ring Shape & Color --}}
                                <div class="row g-2 align-items-center pt-1">
                                    <div class="col-sm-5">
                                        <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Ring Shape</label>
                                        <input type="hidden" id="panel-leader-shape" value="circle">
                                        <div class="d-flex gap-1" id="panel-shape-tiles">
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2 active" data-shape="circle">
                                                <span class="small fw-semibold" style="font-size: 11px;">Circle</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="rounded_square">
                                                <span class="small fw-medium" style="font-size: 11px;">Round</span>
                                            </button>
                                            <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="square">
                                                <span class="small fw-medium" style="font-size: 11px;">Square</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Ring Color</label>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap" id="panel-color-swatches">
                                            <button type="button" class="btn p-0 panel-swatch rounded-2 active" data-color="#FF9933" style="width: 24px; height: 24px; background: #FF9933;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#FFFFFF" style="width: 24px; height: 24px; background: #FFFFFF;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#138808" style="width: 24px; height: 24px; background: #138808;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#D4AF37" style="width: 24px; height: 24px; background: #D4AF37;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#DC2626" style="width: 24px; height: 24px; background: #DC2626;"></button>
                                            <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#1E3A8A" style="width: 24px; height: 24px; background: #1E3A8A;"></button>
                                        </div>
                                        <input type="hidden" id="panel-leader-border" value="#FF9933">
                                    </div>
                                </div>

                                {{-- 1.4 Leader Avatar Size --}}
                                <div class="pt-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label text-muted small mb-0" style="font-size: 10.5px;">Leader Avatar Size</label>
                                        <span class="text-secondary small font-monospace" id="panel-leader-size-label" style="font-size: 10px;">Medium</span>
                                    </div>
                                    <input type="hidden" id="panel-leader-size" value="104">
                                    <div class="d-flex gap-1" id="panel-leader-size-tiles">
                                        <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1 px-1 rounded-2" data-size="88">
                                            <span class="small fw-medium" style="font-size: 11px;">Small</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1 px-1 rounded-2 active" data-size="104">
                                            <span class="small fw-semibold" style="font-size: 11px;">Medium</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-leader-size-pill flex-fill py-1 px-1 rounded-2" data-size="120">
                                            <span class="small fw-medium" style="font-size: 11px;">Large</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================= --}}
                        {{-- SECTION 2: BRANDING BOTTOM STRIP (ब्रांडिंग बॉटम स्ट्रिप)    --}}
                        {{-- ========================================================= --}}
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-id-badge-2 text-primary" style="font-size: 14px;"></i>
                                    Branding Bottom Strip
                                </span>
                                <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 11px;">
                                    Branding Profile &rarr;
                                </a>
                            </div>

                            <div class="d-flex flex-column" style="gap: 14px;">
                                {{-- 2.1 Footer Ribbon Style Selection --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label for="panel-footer-style" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            Footer Ribbon Style
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

                                {{-- 2.2 Candidate Photo Size (बड़ा / छोटा करने के लिए) --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label text-muted small mb-0" style="font-size: 10.5px;">Candidate Photo Scale</label>
                                        <span class="text-secondary small font-monospace" id="panel-candidate-scale-label" style="font-size: 10px;">100%</span>
                                    </div>
                                    <input type="hidden" id="panel-candidate-scale" value="100">
                                    <div class="d-flex gap-1" id="panel-candidate-scale-tiles">
                                        <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1 px-1 rounded-2" data-scale="85">
                                            <span class="small fw-medium" style="font-size: 11px;">Small (85%)</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1 px-1 rounded-2 active" data-scale="100">
                                            <span class="small fw-semibold" style="font-size: 11px;">Normal (100%)</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-candidate-scale-pill flex-fill py-1 px-1 rounded-2" data-scale="115">
                                            <span class="small fw-medium" style="font-size: 11px;">Large (115%)</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- 2.3 Photo Cutout Selector --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label text-muted small mb-0" style="font-size: 10.5px;">Photo Cutout</label>
                                        <label for="panel-upload-photo-input" class="text-primary small fw-semibold cursor-pointer mb-0" style="font-size: 11px;">
                                            + Upload Photo
                                        </label>
                                    </div>
                                    <input type="file" id="panel-upload-photo-input" class="d-none" accept="image/jpeg,image/png,image/webp">
                                    <input type="file" id="qs-upload-photo-input" class="d-none">
                                    <input type="hidden" id="panel-cutout-url" value="{{ $activeCutout }}">

                                    <div class="d-flex align-items-center gap-2 overflow-x-auto py-1" id="panel-photo-chips-wrapper">
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

                                {{-- 2.4 Candidate Details (DEFAULT HIDDEN / COLLAPSED) --}}
                                <div class="pt-2 border-top">
                                    <a class="d-flex align-items-center justify-content-between text-decoration-none cursor-pointer py-2 px-3 rounded-2 studio-panel-card-inner" data-bs-toggle="collapse" href="#panel-candidate-details-collapse" role="button" aria-expanded="false" aria-controls="panel-candidate-details-collapse">
                                        <span class="small fw-bold text-body d-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="ti ti-user-edit text-primary"></i>
                                            Candidate Details (Name & Post)
                                        </span>
                                        <span class="small text-secondary d-flex align-items-center gap-1" style="font-size: 11px;">
                                            <span class="fw-semibold">Edit</span>
                                            <i class="ti ti-chevron-down" style="font-size: 12px;"></i>
                                        </span>
                                    </a>
                                    <div class="collapse mt-2" id="panel-candidate-details-collapse">
                                        <div class="p-3 rounded-3 studio-panel-card-inner">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Name</label>
                                                    <input type="text" id="panel-candidate-name" name="candidate_name" class="form-control form-control-sm rounded-2" value="{{ $userName }}" placeholder="e.g. राहुल शर्मा" style="font-size: 12px; height: 35px;">
                                                    <input type="hidden" id="qs-modal-candidate-name" value="{{ $userName }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Post / Designation</label>
                                                    <input type="text" id="panel-candidate-designation" name="candidate_designation" class="form-control form-control-sm rounded-2" value="{{ $userBranding?->designation }}" placeholder="e.g. जिला महामंत्री" style="font-size: 12px; height: 35px;">
                                                    <input type="hidden" id="qs-modal-candidate-designation" value="{{ $userBranding?->designation }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2.5 Show Social Handles Toggle --}}
                                <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                                    <span class="small text-body-secondary" style="font-size: 11px;">Social Media Handles</span>
                                    <div class="form-check form-switch m-0 ps-0">
                                        <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="panel-show-social" checked style="width: 34px; height: 18px;">
                                        <input type="checkbox" id="qs-modal-show-social" class="d-none" checked>
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
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <button type="button" class="btn btn-sm btn-white border shadow-xs rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 studio-back-btn" id="panel-btn-back-step" style="font-size: 12px; font-weight: 600;">
                    <i class="ti ti-arrow-left"></i>
                    <span>Back to Poster Style</span>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-teal-lt text-teal rounded-pill px-3 py-1.5 fw-bold" id="panel-schedule-festival-date" style="font-size: 11.5px;">
                        <i class="ti ti-calendar-event me-1"></i>{{ $selectedFestival->title }} ({{ $selectedFestival->festival_date->format('d M Y') }})
                    </span>
                </div>
            </div>

            <div class="row g-4 g-lg-5 align-items-start">
                
                {{-- Left Side: Compact Poster Thumbnail preview Card --}}
                <div class="col-md-5 col-lg-4">
                    <div class="card studio-panel-card p-3 text-center mb-3">
                        <div class="position-relative rounded-3 overflow-hidden shadow-sm mx-auto w-100" style="aspect-ratio: 1 / 1; background: #0f172a; border: 1px solid rgba(0,0,0,0.08);">
                            <img id="panel-schedule-thumb" src="{{ $firstTemplate?->image_url }}" alt="Scheduled Poster" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="small fw-bold text-body mt-2.5 text-truncate" id="panel-schedule-template-title" style="font-size: 13px;">
                            {{ $firstTemplate?->title }}
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-1.5 mt-1">
                            <span class="badge bg-teal-lt text-teal rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 10.5px;">
                                1080p Ultra HD
                            </span>
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 10.5px;">
                                Personalized
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Structured Cards with Generous Padding --}}
                <div class="col-md-7 col-lg-8">
                    <div class="d-flex flex-column gap-3.5">
                        
                        {{-- CARD 1: Connected Social Media Accounts --}}
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-share text-primary" style="font-size: 14px;"></i>
                                    Publish to Social Accounts
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 10.5px;">
                                    Auto-Selected
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-2.5" id="panel-accounts-container">
                                @forelse($accounts as $acc)
                                    <div class="p-3 rounded-3 d-flex align-items-center justify-content-between panel-account-item active cursor-pointer" data-id="{{ $acc->id }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 38px; height: 38px; background: {{ $acc->account_type == 1 ? '#1877F2' : 'linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)' }};">
                                                <i class="ti {{ $acc->account_type == 1 ? 'ti-brand-facebook' : 'ti-brand-instagram' }} text-white" style="font-size: 19px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-body" style="font-size: 13.5px; line-height: 1.2;">{{ $acc->name }}</div>
                                                <div class="text-secondary small mt-0.5" style="font-size: 11px;">{{ $acc->account_type == 1 ? 'Facebook Page' : 'Instagram Professional' }}</div>
                                            </div>
                                        </div>
                                        <input type="checkbox" class="form-check-input panel-acc-cb ms-0" value="{{ $acc->id }}" checked>
                                    </div>
                                @empty
                                    <div class="p-3 text-center studio-panel-card-inner rounded-3 border" style="border-style: dashed !important;">
                                        <div class="text-secondary small mb-1">No active social media accounts connected.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Connect Account</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- CARD 2: Date, Time & Caption --}}
                        <div class="card studio-panel-card p-3.5 mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-uppercase fw-bold studio-section-title d-flex align-items-center gap-1.5">
                                    <i class="ti ti-calendar-time text-primary" style="font-size: 14px;"></i>
                                    Schedule Timing & Caption
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                {{-- Date & Time Inputs with crisp spacing & labels --}}
                                <div class="row g-2.5">
                                    <div class="col-6">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            <i class="ti ti-calendar me-1 text-primary"></i> Publish Date
                                        </label>
                                        <input type="date" id="panel-schedule-date" class="form-control rounded-2" value="{{ $selectedFestival->festival_date->format('Y-m-d') }}" style="height: 42px; font-size: 12.5px; font-weight: 500;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            <i class="ti ti-clock me-1 text-primary"></i> Publish Time
                                        </label>
                                        <input type="time" id="panel-schedule-time" class="form-control rounded-2" value="{{ $targetTime }}" style="height: 42px; font-size: 12.5px; font-weight: 500;">
                                    </div>
                                </div>

                                {{-- Caption & Hashtags with comfortable padding --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-0" style="font-size: 10.5px; letter-spacing: 0.04em;">
                                            <i class="ti ti-message-2 me-1 text-primary"></i> Caption & Hashtags
                                        </label>
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-primary text-decoration-none d-flex align-items-center gap-1 shadow-none" id="panel-btn-copy-caption" style="font-size: 11px;">
                                            <i class="ti ti-copy"></i>
                                            <span>Copy</span>
                                        </button>
                                    </div>
                                    <textarea id="panel-caption-text" class="form-control rounded-2 p-2.5" rows="4" style="font-size: 12px; line-height: 1.6; resize: vertical;">{{ $captionText }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Alert Slot --}}
                        <div id="panel-schedule-alert" class="d-none alert alert-danger small p-2.5 rounded-3 mb-0"></div>

                        {{-- Final Confirm & Schedule Button with generous breathing room --}}
                        <div class="pt-2">
                            <button type="button" class="btn btn-success w-100 py-3 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2" id="panel-btn-confirm-schedule" style="height: 52px; font-size: 15px; letter-spacing: 0.02em; background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important; border: none; box-shadow: 0 10px 25px -4px rgba(16, 185, 129, 0.4) !important;">
                                <i class="ti ti-calendar-check" style="font-size: 20px;"></i>
                                <span>Confirm & Schedule Post</span>
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
