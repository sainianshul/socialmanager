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
    <div class="offcanvas-header border-bottom py-3 px-4 px-lg-5 bg-white d-flex align-items-center justify-content-between" style="border-color: #F1F5F9 !important;">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h5 class="offcanvas-title fw-bold text-dark mb-0" id="studioSlidePanelLabel" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                Festival Poster Studio
            </h5>
            <span class="badge px-3 py-1 rounded-pill fw-medium small" id="panel-subheading" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; font-size: 11.5px;">
                {{ $selectedFestival->title }} ({{ $selectedFestival->festival_date->format('d M Y') }})
            </span>
        </div>

        {{-- Stepper Indicator & Close Button --}}
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-sm-flex align-items-center gap-1 p-1 rounded-pill" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
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
    <div class="offcanvas-body p-4 p-lg-5 bg-white">
        
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
                                         style="width: 68px; height: 68px; border: 2px solid {{ $loop->first ? '#0D9488' : '#E2E8F0' }}; transition: all 0.2s ease;">
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
                        <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-preview" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn" style="width: 36px; height: 36px; border-color: #E2E8F0; color: #475569; background: #ffffff;" title="Full-Screen Preview (1080p)">
                            <i class="ti ti-maximize" style="font-size: 16px;"></i>
                        </a>
                        <a href="{{ $firstTemplate?->image_url }}" id="panel-btn-download" target="_blank" download="{{ Str::slug($selectedFestival->title) }}-poster.jpg" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center border-0 shadow-xs p-0 panel-icon-btn" style="width: 36px; height: 36px; background: #0D9488;" title="Download High-Res 1080p">
                            <i class="ti ti-download text-white" style="font-size: 16px;"></i>
                        </a>
                        <button type="button" id="panel-btn-whatsapp" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn" style="width: 36px; height: 36px; background: #ECFDF5; border-color: #A7F3D0; color: #059669;" title="Share on WhatsApp">
                            <i class="ti ti-brand-whatsapp" style="font-size: 17px;"></i>
                        </button>
                        <button type="button" id="panel-btn-share" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-none p-0 panel-icon-btn" style="width: 36px; height: 36px; border-color: #E2E8F0; color: #475569; background: #ffffff;" title="Share Poster Link">
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
                    <div class="d-flex flex-column" style="gap: 18px;">
                        
                        {{-- 1. Leadership Strip --}}
                        @if(isset($allStrips) && $allStrips->isNotEmpty())
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label for="panel-strip-id" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.05em;">
                                        Leadership Strip
                                    </label>
                                    <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 11.5px;">
                                        Manage Strips &rarr;
                                    </a>
                                </div>
                                <select class="form-select qs-premium-select" id="panel-strip-id">
                                    @foreach($allStrips as $s)
                                        @php
                                            $isUser = !empty($s->user_id);
                                            $partyCode = $s->party?->short_code ?? 'Universal';
                                            $label = $isUser ? $s->name . ' (Custom)' : $s->name . ' (' . $partyCode . ' Default)';
                                        @endphp
                                        <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- 2. Top Leadership Header Layout --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label for="panel-header-layout" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.05em;">
                                    Top Leadership Header
                                </label>
                                <span id="panel-header-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                    Fixed by Admin
                                </span>
                            </div>
                            <select class="form-select qs-premium-select" id="panel-header-layout">
                                <option value="full_width_strip">Header 1: Full-Width Strip (6-7 Leaders)</option>
                                <option value="top_left_cluster">Header 2: Top-Left Cluster (3-4 Leaders + Logo)</option>
                                <option value="top_right_cluster">Header 3: Top-Right Cluster (3-4 Leaders + Logo)</option>
                                <option value="split_wings">Header 4: Split Wings (3 Left + 3 Right)</option>
                                <option value="vertical_capsule_strip">Header 5: Vertical Capsule Cards</option>
                                <option value="party_group_cutout">Header 6: Joint Group Cutout</option>
                                <option value="frosted_glass_dock">Header 7: Frosted Glass Dock</option>
                                <option value="tricolor_ribbon_canopy">Header 8: Tricolor Canopy</option>
                                <option value="none">None (Hide Leadership Strip)</option>
                            </select>
                        </div>

                        {{-- 3. Branding Footer Ribbon Style --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label for="panel-footer-style" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.05em;">
                                    Branding Footer Style
                                </label>
                                <span id="panel-footer-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                    Fixed by Admin
                                </span>
                            </div>
                            <select class="form-select qs-premium-select" id="panel-footer-style">
                                <option value="dual_ribbon_badge">Layer 1: Dual Ribbon Badge (Classic Political)</option>
                                <option value="solid_white_banner">Layer 2: Solid White Banner (Minimal Card)</option>
                                <option value="floating_tricolor_badge">Layer 3: Floating Tricolor Badge (Festive Glow)</option>
                                <option value="royal_gold_dock">Layer 4: Royal Gold Banner (Midnight Slate)</option>
                                <option value="candidate_left_ribbon">Layer 5: Candidate Left Ribbon (Mirrored Style)</option>
                                <option value="modern_capsule_dock">Layer 6: Modern Capsule Dock (Floating Pill)</option>
                                <option value="circular_medallion_badge">Layer 7: Circular Medallion Badge (Framed Portrait)</option>
                                <option value="saffron_wave_banner">Layer 8: Saffron Wave Banner (Dynamic Wave)</option>
                                <option value="tricolor_angled_blade">Layer 9: Tricolor Angled Blade (Patriotic Blade)</option>
                                <option value="royal_maroon_heritage">Layer 10: Royal Maroon Heritage (Sacred Gold & Maroon)</option>
                            </select>
                        </div>

                        {{-- 4. Ring Color & Shape Toolbar Row (Clean Pure White Card) --}}
                        <div class="p-3.5 rounded-4 bg-white border" style="background: #ffffff !important; border: 1px solid #E2E8F0 !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fw-semibold small text-uppercase" style="font-size: 10.5px; letter-spacing: 0.05em;">
                                    Leader Ring & Avatar Style
                                </span>
                            </div>

                            <div class="row g-2 align-items-center">
                                <div class="col-sm-5">
                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Avatar Shape</label>
                                    <input type="hidden" id="panel-leader-shape" value="circle">
                                    <div class="d-flex gap-1" id="panel-shape-tiles">
                                        <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2 active" data-shape="circle" style="border: 1.5px solid #0D9488; background: #F0FDFA;">
                                            <span class="small fw-semibold text-teal" style="font-size: 11px;">Circle</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="rounded_square" style="border: 1px solid #E2E8F0; background: #ffffff;">
                                            <span class="small fw-medium text-secondary" style="font-size: 11px;">Round</span>
                                        </button>
                                        <button type="button" class="btn btn-sm panel-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="square" style="border: 1px solid #E2E8F0; background: #ffffff;">
                                            <span class="small fw-medium text-secondary" style="font-size: 11px;">Square</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Ring Color</label>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap" id="panel-color-swatches">
                                        <button type="button" class="btn p-0 panel-swatch rounded-2 active" data-color="#FF9933" style="width: 24px; height: 24px; background: #FF9933; border: 1.5px solid #0D9488;"></button>
                                        <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#FFFFFF" style="width: 24px; height: 24px; background: #FFFFFF; border: 1px solid #E2E8F0;"></button>
                                        <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#138808" style="width: 24px; height: 24px; background: #138808; border: 1.5px solid transparent;"></button>
                                        <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#D4AF37" style="width: 24px; height: 24px; background: #D4AF37; border: 1.5px solid transparent;"></button>
                                        <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#DC2626" style="width: 24px; height: 24px; background: #DC2626; border: 1.5px solid transparent;"></button>
                                        <button type="button" class="btn p-0 panel-swatch rounded-2" data-color="#1E3A8A" style="width: 24px; height: 24px; background: #1E3A8A; border: 1.5px solid transparent;"></button>
                                    </div>
                                    <input type="hidden" id="panel-leader-border" value="#FF9933">
                                    <input type="hidden" id="panel-leader-size" value="106">
                                </div>
                            </div>
                        </div>

                        {{-- 5. Candidate Personalization (Clean Pure White Card) --}}
                        <div class="p-3.5 rounded-4 bg-white border" style="background: #ffffff !important; border: 1px solid #E2E8F0 !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fw-semibold small text-uppercase" style="font-size: 10.5px; letter-spacing: 0.05em;">
                                    Candidate Details
                                </span>
                                <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 11px;">
                                    Branding Profile &rarr;
                                </a>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Name</label>
                                    <input type="text" id="panel-candidate-name" name="candidate_name" class="form-control form-control-sm rounded-2" value="{{ $userName }}" placeholder="e.g. राहुल शर्मा" style="font-size: 12.5px; height: 35px;">
                                    <input type="hidden" id="qs-modal-candidate-name" value="{{ $userName }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1" style="font-size: 10.5px;">Post / Designation</label>
                                    <input type="text" id="panel-candidate-designation" name="candidate_designation" class="form-control form-control-sm rounded-2" value="{{ $userBranding?->designation }}" placeholder="e.g. जिला महामंत्री" style="font-size: 12.5px; height: 35px;">
                                    <input type="hidden" id="qs-modal-candidate-designation" value="{{ $userBranding?->designation }}">
                                </div>
                            </div>

                            {{-- Photo Cutout Selector --}}
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
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
                                             data-url="{{ $activeCutout }}"
                                             style="width: 36px; height: 36px; border: 2px solid #0D9488; outline: 2px solid rgba(13, 148, 136, 0.3); outline-offset: 1px;">
                                            <img src="{{ $activeCutout }}" alt="Active Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                        </div>
                                    @endif
                                    @foreach($userImages as $uImg)
                                        @if($uImg->cutout_url !== $activeCutout)
                                            <div class="panel-photo-chip position-relative rounded-circle cursor-pointer flex-shrink-0"
                                                 data-url="{{ $uImg->cutout_url }}"
                                                 style="width: 36px; height: 36px; border: 2px solid #E2E8F0;">
                                                <img src="{{ $uImg->cutout_url }}" alt="Branding Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Show Social Handles Toggle --}}
                            <div class="mt-2.5 pt-2 border-top d-flex align-items-center justify-content-between">
                                <span class="small text-secondary" style="font-size: 11px;">Social Media Handles</span>
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

        {{-- ================================================================= --}}
        {{-- STEP 2: SCHEDULE POST DETAILS                                     --}}
        {{-- ================================================================= --}}
        <div id="panel-step-pane-2" class="d-none">
            
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none p-0 d-flex align-items-center gap-1.5" id="panel-btn-back-step">
                    <i class="ti ti-arrow-left"></i>
                    <span class="fw-semibold">Back to Poster Style</span>
                </button>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 font-monospace small" id="panel-schedule-festival-date">
                    {{ $selectedFestival->festival_date->format('d M Y') }}
                </span>
            </div>

            <div class="row g-4 align-items-start">
                
                {{-- Left Side: Compact Poster Thumbnail preview --}}
                <div class="col-md-4 text-center">
                    <div class="position-relative rounded-4 overflow-hidden shadow-sm mx-auto" style="max-width: 240px; aspect-ratio: 1 / 1; background: #0f172a; border: 1px solid rgba(0,0,0,0.08);">
                        <img id="panel-schedule-thumb" src="{{ $firstTemplate?->image_url }}" alt="Scheduled Poster" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="small fw-bold text-dark mt-2.5 text-truncate" id="panel-schedule-template-title">
                        {{ $firstTemplate?->title }}
                    </div>
                    <div class="text-secondary small" style="font-size: 11px;">1080p Personalized Artwork</div>
                </div>

                {{-- Right Side: Accounts, Date/Time & Caption --}}
                <div class="col-md-8">
                    <div class="d-flex flex-column gap-3">
                        
                        {{-- Social Media Accounts --}}
                        <div>
                            <label class="form-label text-muted fw-bold small text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">
                                Publish to Social Accounts
                            </label>
                            <div class="d-flex flex-column gap-2" id="panel-accounts-container">
                                @forelse($accounts as $acc)
                                    <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between panel-account-item active cursor-pointer bg-white" data-id="{{ $acc->id }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge rounded-circle p-1 {{ $acc->account_type == 1 ? 'bg-primary' : 'bg-danger' }}">
                                                <i class="ti {{ $acc->account_type == 1 ? 'ti-brand-facebook' : 'ti-brand-instagram' }} text-white"></i>
                                            </span>
                                            <div>
                                                <div class="fw-semibold text-dark small" style="font-size: 12px;">{{ $acc->name }}</div>
                                                <div class="text-secondary" style="font-size: 10px;">{{ $acc->account_type == 1 ? 'Facebook Page' : 'Instagram Professional' }}</div>
                                            </div>
                                        </div>
                                        <input type="checkbox" class="form-check-input panel-acc-cb ms-0" value="{{ $acc->id }}" checked>
                                    </div>
                                @empty
                                    <div class="p-3 text-center bg-white rounded-3 border" style="background: #ffffff !important; border: 1px dashed #CBD5E1 !important;">
                                        <div class="text-secondary small mb-1">No active social media accounts connected.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">Connect Account</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Date & Time --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1" style="font-size: 11px;">Publish Date</label>
                                <input type="date" id="panel-schedule-date" class="form-control form-control-sm rounded-2" value="{{ $selectedFestival->festival_date->format('Y-m-d') }}" style="font-size: 12.5px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1" style="font-size: 11px;">Publish Time</label>
                                <input type="time" id="panel-schedule-time" class="form-control form-control-sm rounded-2" value="{{ $targetTime }}" style="font-size: 12.5px;">
                            </div>
                        </div>

                        {{-- Caption & Hashtags --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-muted small mb-0" style="font-size: 11px;">Caption & Hashtags</label>
                                <button type="button" class="btn btn-link text-primary p-0 small text-decoration-none" id="panel-btn-copy-caption" style="font-size: 11px;">
                                    <i class="ti ti-copy me-1"></i>Copy
                                </button>
                            </div>
                            <textarea id="panel-caption-text" class="form-control rounded-2" rows="4" style="font-size: 12px;">{{ $captionText }}</textarea>
                        </div>

                        {{-- Alert Slot --}}
                        <div id="panel-schedule-alert" class="d-none alert alert-danger small p-2 rounded-2 mb-0"></div>

                        {{-- Final Confirm & Schedule Button --}}
                        <button type="button" class="btn btn-success rounded-pill w-100 py-3 fw-bold shadow-sm" id="panel-btn-confirm-schedule">
                            <i class="ti ti-calendar-check me-1.5"></i>Confirm & Schedule Post
                        </button>
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
