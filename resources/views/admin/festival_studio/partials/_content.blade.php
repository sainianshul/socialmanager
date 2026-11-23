{{-- Festival & Gallery Control Bar --}}
@if($selectedFestival)
    @php
        $firstTemplate = $templates->first();
        $isToday = $selectedFestival->festival_date->isToday();
        $targetTime = $isToday ? now()->addMinutes(10)->format('H:i') : '08:00';
        $targetTimeFormatted = $isToday ? now()->addMinutes(10)->format('h:i A') : '08:00 AM';
        $captionText = ($selectedFestival->ready_caption ?? '') . ($selectedFestival->hashtags ? "\n\n" . $selectedFestival->hashtags : '');
        $userBranding = auth()->user()->defaultBranding ?? auth()->user()->brandings()->first();
        $userName = $userBranding?->effective_name ?? auth()->user()->name;
        $userParty = $userBranding?->party?->short_code ?? 'Universal';
        $activeCutout = $userCutout ?? ($userBranding?->defaultImage?->cutout_url);
    @endphp

    {{-- Top Bar: Festival Title, Badges & View Mode Switcher --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h2 class="fw-bold m-0 text-dark" style="font-size: 1.45rem; letter-spacing: -0.02em;">
                        {{ $selectedFestival->title }}
                    </h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 font-monospace small">
                        {{ $selectedFestival->festival_date->format('d M Y') }}
                    </span>
                    @if($isToday)
                        <span class="badge bg-danger rounded-pill px-2.5 py-1 text-white small">Today</span>
                    @endif
                </div>
                <div class="text-secondary small mt-0.5">
                    Customize your personalized festival poster in 1080p and publish to social channels
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Quick Copy Caption Button --}}
            @if(!empty($selectedFestival->ready_caption))
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-none" id="btn-copy-caption">
                    <i class="ti ti-copy me-1"></i><span id="copy-btn-text">Copy Caption</span>
                </button>
                <div id="festival-caption-text" class="d-none">{{ $captionText }}</div>
            @endif

            {{-- View Mode Switcher: Apple Segmented Pill --}}
            <div class="btn-group p-0.5 rounded-pill bg-light border shadow-2xs" role="group" id="fs-view-toggle-group">
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold fs-view-btn active" id="btn-view-studio" data-view="studio" style="font-size: 12.5px;">
                    <i class="ti ti-palette me-1"></i>Studio Editor
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-secondary fs-view-btn" id="btn-view-grid" data-view="grid" style="font-size: 12.5px;">
                    <i class="ti ti-layout-grid me-1"></i>All Designs ({{ $templates->count() }})
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- VIEW 1: APPLE-GRADE SPLIT-SCREEN STUDIO EDITOR                            --}}
    {{-- ========================================================================= --}}
    @if($firstTemplate)
        @php
            $defaultFooterStyle = $firstTemplate->layout_config['footer']['style'] ?? ($firstTemplate->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
            $defaultHeaderLayout = $firstTemplate->layout_config['header']['layout'] ?? ($firstTemplate->layout_config['header']['mode'] ?? 'full_width_strip');
            $footerFixed = !empty($firstTemplate->layout_config['footer']['is_fixed']) ? '1' : '0';
            $headerFixed = !empty($firstTemplate->layout_config['header']['is_fixed']) ? '1' : '0';
            $leaderShape = $firstTemplate->layout_config['header']['chip_shape'] ?? 'circle';
            $leaderBorder = $firstTemplate->layout_config['header']['chip_border_color'] ?? '#FF9933';
        @endphp

        <div id="fs-studio-workspace" class="row g-4 align-items-start mb-5">
            
            {{-- ----------------------------------------------------------------- --}}
            {{-- CENTER STAGE: Prominent 1080p Canvas + Action Bar + Filmstrip      --}}
            {{-- ----------------------------------------------------------------- --}}
            <div class="col-12 col-xl-8 col-lg-7">
                <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.06) !important;">
                    
                    {{-- Canvas Stage Action Bar (Apple-Style Glassy Toolbar) --}}
                    <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #FAFAFA;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 small font-monospace" id="studio-active-id-badge">
                                #{{ $firstTemplate->id }}
                            </span>
                            <span class="fw-bold text-dark text-truncate" id="studio-active-template-title" style="max-width: 280px; font-size: 0.95rem;">
                                {{ $firstTemplate->title }}
                            </span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ $firstTemplate->image_url }}" id="studio-btn-preview" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-none" style="font-size: 12.5px;" title="Open 1080p in New Tab">
                                <i class="ti ti-maximize me-1"></i>HD View
                            </a>
                            <a href="{{ $firstTemplate->image_url }}" id="studio-btn-download" download="{{ Str::slug($selectedFestival->title) }}-poster.jpg" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="font-size: 12.5px; background: #0D9488; border-color: #0D9488;" title="Download High-Res 1080p">
                                <i class="ti ti-download me-1"></i>Download
                            </a>
                            <button type="button" id="studio-btn-whatsapp" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 shadow-none" style="font-size: 12.5px; background: #ECFDF5; border-color: #A7F3D0; color: #059669;" title="Share on WhatsApp">
                                <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                            </button>
                            <button type="button" id="studio-btn-share" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-none" style="font-size: 12.5px;" title="Share Poster Link">
                                <i class="ti ti-share me-1"></i>Share
                            </button>
                        </div>
                    </div>

                    {{-- The Canvas Viewport: Centered 1:1 Aspect Ratio with Soft Shadows --}}
                    <div class="p-4 p-md-5 d-flex align-items-center justify-content-center" style="background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%); min-height: 480px;">
                        <div class="position-relative shadow-lg rounded-4 overflow-hidden" id="studio-canvas-wrapper" style="width: 100%; max-width: 540px; aspect-ratio: 1 / 1; background: #0F172A; box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.22) !important;">
                            
                            {{-- Live Image --}}
                            <a href="{{ $firstTemplate->image_url }}" id="studio-canvas-link" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100 text-decoration-none" title="Click to view 1080p full resolution" style="cursor: zoom-in;">
                                <img id="studio-canvas-img" src="{{ $firstTemplate->image_url }}" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">
                            </a>

                            {{-- 1080p Ultra HD Badge --}}
                            <div class="position-absolute top-0 end-0 m-3 pointer-events-none">
                                <span class="badge rounded-pill px-3 py-1.5 fw-semibold shadow-sm" style="background: rgba(15, 23, 42, 0.78); color: #ffffff; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15); font-size: 11px; letter-spacing: 0.04em;">
                                    1080p Ultra HD
                                </span>
                            </div>

                            {{-- Live Loading Spinner Overlay --}}
                            <div id="studio-canvas-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white d-none" style="backdrop-filter: blur(8px); z-index: 10;">
                                <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                                <div class="small fw-semibold">Personalizing Poster...</div>
                                <div class="text-white-50 small mt-0.5" style="font-size: 11px;">Rendering your branding in 1080p</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Template Filmstrip / Drawer --}}
                    <div class="p-3 px-4 border-top bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase text-secondary fw-bold small" style="font-size: 11px; letter-spacing: 0.05em;">
                                <i class="ti ti-layout-grid text-primary me-1"></i>Select Design (डिज़ाइन चुनें)
                            </span>
                            <span class="text-secondary small font-monospace" style="font-size: 11px;">
                                {{ $templates->count() }} Designs Available
                            </span>
                        </div>

                        {{-- Horizontal Scrollable Filmstrip --}}
                        <div class="d-flex align-items-center gap-3 overflow-x-auto py-2" id="studio-template-filmstrip">
                            @foreach($templates as $tpl)
                                @php
                                    $isFirst = $loop->first;
                                    $tplFooterStyle = $tpl->layout_config['footer']['style'] ?? ($tpl->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
                                    $tplHeaderLayout = $tpl->layout_config['header']['layout'] ?? ($tpl->layout_config['header']['mode'] ?? 'full_width_strip');
                                    $tplFooterFixed = !empty($tpl->layout_config['footer']['is_fixed']) ? '1' : '0';
                                    $tplHeaderFixed = !empty($tpl->layout_config['header']['is_fixed']) ? '1' : '0';
                                    $tplLeaderShape = $tpl->layout_config['header']['chip_shape'] ?? 'circle';
                                    $tplLeaderBorder = $tpl->layout_config['header']['chip_border_color'] ?? '#FF9933';
                                @endphp
                                <div class="studio-filmstrip-card position-relative rounded-3 overflow-hidden cursor-pointer flex-shrink-0 {{ $isFirst ? 'active' : '' }}"
                                     id="filmstrip-card-{{ $tpl->id }}"
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
                                     style="width: 88px; height: 88px; border: 2px solid {{ $isFirst ? '#0d9488' : '#e2e8f0' }}; transition: all 0.2s ease;">
                                    <img src="{{ $tpl->image_url }}" alt="{{ $tpl->title }}" class="fs-template-preview-img w-100 h-100" style="object-fit: cover;">
                                    
                                    {{-- Active Checkmark Badge --}}
                                    <span class="filmstrip-check badge bg-success rounded-circle p-1 position-absolute top-0 end-0 m-1 {{ $isFirst ? '' : 'd-none' }}" style="font-size: 8px;">
                                        <i class="ti ti-check"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ----------------------------------------------------------------- --}}
            {{-- RIGHT COLUMN: Apple-Style Inspector Toolbar (Settings & Schedule) --}}
            {{-- ----------------------------------------------------------------- --}}
            <div class="col-12 col-xl-4 col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.06) !important; position: sticky; top: 80px;">
                    
                    {{-- Inspector Segmented Header --}}
                    <div class="p-3 border-bottom bg-light-subtle d-flex align-items-center justify-content-between rounded-top-4">
                        <div class="btn-group w-100 p-0.5 rounded-pill bg-white border" role="group" id="studio-inspector-tabs">
                            <button type="button" class="btn btn-sm rounded-pill fw-semibold inspector-tab-btn active" id="tab-btn-design" data-pane="pane-design" style="font-size: 12.5px;">
                                <i class="ti ti-palette me-1"></i>1. Design & Style
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill fw-medium text-secondary inspector-tab-btn" id="tab-btn-schedule" data-pane="pane-schedule" style="font-size: 12.5px;">
                                <i class="ti ti-calendar me-1"></i>2. Schedule Post
                            </button>
                        </div>
                    </div>

                    {{-- Inspector Scrollable Body --}}
                    <div class="p-4" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                        
                        {{-- ---------------------------------------------------- --}}
                        {{-- PANE 1: DESIGN & CANDIDATE PERSONALIZATION           --}}
                        {{-- ---------------------------------------------------- --}}
                        <div id="inspector-pane-design">
                            <div class="d-flex flex-column gap-3">
                                
                                {{-- 1. Candidate Personalization Card --}}
                                <div class="rounded-3 p-3 bg-light-subtle border">
                                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                                        <span class="small fw-bold text-dark text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">
                                            <i class="ti ti-user text-primary me-1"></i>Candidate Info (उम्मीदवार विवरण)
                                        </span>
                                        <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-primary small text-decoration-none" style="font-size: 11px;">
                                            Branding Profile &rarr;
                                        </a>
                                    </div>

                                    <div class="row g-2 mb-2.5">
                                        <div class="col-6">
                                            <label class="form-label text-muted small mb-1" style="font-size: 11px;">Name (नाम)</label>
                                            <input type="text" id="studio-candidate-name" class="form-control form-control-sm rounded-2" value="{{ $userName }}" placeholder="e.g. राहुल शर्मा" style="font-size: 12.5px; height: 36px;">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted small mb-1" style="font-size: 11px;">Post (पद)</label>
                                            <input type="text" id="studio-candidate-designation" class="form-control form-control-sm rounded-2" value="{{ $userBranding?->designation }}" placeholder="e.g. जिला महामंत्री" style="font-size: 12.5px; height: 36px;">
                                        </div>
                                    </div>

                                    {{-- Photo Cutout Selector Chips --}}
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label text-muted small mb-0" style="font-size: 11px;">Photo Cutout (तस्वीर चुनें)</label>
                                            <label for="studio-upload-photo-input" class="text-primary small fw-semibold cursor-pointer mb-0" style="font-size: 11px;">
                                                + Upload Photo
                                            </label>
                                        </div>
                                        <input type="file" id="studio-upload-photo-input" class="d-none" accept="image/jpeg,image/png,image/webp">
                                        <input type="hidden" id="studio-cutout-url" value="{{ $activeCutout }}">

                                        <div class="d-flex align-items-center gap-2 overflow-x-auto py-1" id="studio-photo-chips-wrapper">
                                            @if($activeCutout)
                                                <div class="studio-photo-chip position-relative rounded-circle cursor-pointer active flex-shrink-0"
                                                     data-url="{{ $activeCutout }}"
                                                     style="width: 38px; height: 38px; border: 2px solid #0d9488; outline: 2px solid rgba(13, 148, 136, 0.3); outline-offset: 1px;">
                                                    <img src="{{ $activeCutout }}" alt="Active Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                                </div>
                                            @endif
                                            @foreach($userImages as $uImg)
                                                @if($uImg->cutout_url !== $activeCutout)
                                                    <div class="studio-photo-chip position-relative rounded-circle cursor-pointer flex-shrink-0"
                                                         data-url="{{ $uImg->cutout_url }}"
                                                         style="width: 38px; height: 38px; border: 2px solid #e2e8f0;">
                                                        <img src="{{ $uImg->cutout_url }}" alt="Branding Photo" class="w-100 h-100 rounded-circle object-fit-cover bg-white">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Social Handles Toggle --}}
                                    <div class="mt-2.5 pt-2.5 border-top d-flex align-items-center justify-content-between">
                                        <span class="small text-secondary" style="font-size: 11.5px;">Show Social & Phone Handles</span>
                                        <div class="form-check form-switch m-0 ps-0">
                                            <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="studio-show-social" checked style="width: 36px; height: 20px;">
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Footer Ribbon Style --}}
                                <div>
                                    <label for="studio-footer-style" class="form-label text-muted fw-bold small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.05em;">
                                        Branding Footer Style
                                    </label>
                                    <select class="form-select qs-premium-select" id="studio-footer-style">
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

                                {{-- 3. Top Leadership Header --}}
                                <div>
                                    <label for="studio-header-layout" class="form-label text-muted fw-bold small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.05em;">
                                        Top Leadership Header
                                    </label>
                                    <select class="form-select qs-premium-select" id="studio-header-layout">
                                        <option value="full_width_strip">Header 1: Full-Width Strip (6-7 Leaders Across)</option>
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

                                {{-- 4. Leadership Strip Picker --}}
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="studio-strip-id" class="form-label text-muted fw-bold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.05em;">
                                                Leadership Strip
                                            </label>
                                            <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none" style="font-size: 11px;">
                                                Manage &rarr;
                                            </a>
                                        </div>
                                        <select class="form-select qs-premium-select" id="studio-strip-id">
                                            @foreach($allStrips as $s)
                                                @php
                                                    $isUser = !empty($s->user_id);
                                                    $partyCode = $s->party?->short_code ?? 'Universal';
                                                    $label = $isUser ? $s->name . ' (Custom Strip)' : $s->name . ' (' . $partyCode . ' Default)';
                                                @endphp
                                                <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                {{-- 5. Avatar Shape & Ring Accent Collapsible --}}
                                <div class="rounded-3 p-3 bg-light-subtle border">
                                    <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark" data-bs-toggle="collapse" href="#studio-avatar-advanced" role="button" aria-expanded="false">
                                        <span class="small fw-semibold text-secondary" style="font-size: 12px;">
                                            Leader Shape & Ring Color
                                        </span>
                                        <span class="text-primary small" style="font-size: 11px;">
                                            Customize &darr;
                                        </span>
                                    </a>
                                    <div class="collapse mt-2.5 pt-2.5 border-top" id="studio-avatar-advanced">
                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1.5" style="font-size: 10.5px; font-weight: 600;">Shape</label>
                                            <input type="hidden" id="studio-leader-shape" value="circle">
                                            <div class="d-flex gap-1.5" id="studio-shape-tiles">
                                                <button type="button" class="btn btn-sm studio-shape-tile flex-fill py-1 px-1 rounded-2 active" data-shape="circle" style="border: 1.5px solid #0d9488; background: #f0fdfa;">
                                                    <span class="small fw-semibold text-teal" style="font-size: 11px;">Circle</span>
                                                </button>
                                                <button type="button" class="btn btn-sm studio-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="rounded_square" style="border: 1.5px solid #e2e8f0; background: #ffffff;">
                                                    <span class="small fw-medium text-secondary" style="font-size: 11px;">Round</span>
                                                </button>
                                                <button type="button" class="btn btn-sm studio-shape-tile flex-fill py-1 px-1 rounded-2" data-shape="square" style="border: 1.5px solid #e2e8f0; background: #ffffff;">
                                                    <span class="small fw-medium text-secondary" style="font-size: 11px;">Square</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label text-muted small mb-1.5" style="font-size: 10.5px; font-weight: 600;">Ring Color</label>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap" id="studio-color-swatches">
                                                <button type="button" class="btn p-0 studio-swatch rounded-2 active" data-color="#FF9933" style="width: 26px; height: 26px; background: #FF9933; border: 1.5px solid #0d9488;"></button>
                                                <button type="button" class="btn p-0 studio-swatch rounded-2" data-color="#FFFFFF" style="width: 26px; height: 26px; background: #FFFFFF; border: 1.5px solid #e2e8f0;"></button>
                                                <button type="button" class="btn p-0 studio-swatch rounded-2" data-color="#138808" style="width: 26px; height: 26px; background: #138808; border: 1.5px solid transparent;"></button>
                                                <button type="button" class="btn p-0 studio-swatch rounded-2" data-color="#D4AF37" style="width: 26px; height: 26px; background: #D4AF37; border: 1.5px solid transparent;"></button>
                                                <button type="button" class="btn p-0 studio-swatch rounded-2" data-color="#DC2626" style="width: 26px; height: 26px; background: #DC2626; border: 1.5px solid transparent;"></button>
                                                <button type="button" class="btn p-0 studio-swatch rounded-2" data-color="#1E3A8A" style="width: 26px; height: 26px; background: #1E3A8A; border: 1.5px solid transparent;"></button>
                                            </div>
                                            <input type="hidden" id="studio-leader-border" value="#FF9933">
                                            <input type="hidden" id="studio-leader-size" value="106">
                                        </div>
                                    </div>
                                </div>

                                {{-- Next Button to Step 2 --}}
                                <button type="button" class="btn btn-primary rounded-pill w-100 py-2.5 fw-semibold shadow-sm mt-2" id="studio-btn-next-step" style="background: #0d9488; border-color: #0d9488;">
                                    <i class="ti ti-calendar-time me-1.5"></i>Next: Schedule Post &rarr;
                                </button>
                            </div>
                        </div>

                        {{-- ---------------------------------------------------- --}}
                        {{-- PANE 2: SCHEDULE & PUBLISH DETAILS                   --}}
                        {{-- ---------------------------------------------------- --}}
                        <div id="inspector-pane-schedule" class="d-none">
                            <div class="d-flex flex-column gap-3">
                                
                                {{-- Target Social Accounts --}}
                                <div>
                                    <label class="form-label text-muted fw-bold small text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.05em;">
                                        Publish to Social Accounts
                                    </label>
                                    <div class="d-flex flex-column gap-2" id="studio-accounts-container">
                                        @forelse($accounts as $acc)
                                            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between studio-account-item active cursor-pointer bg-white" data-id="{{ $acc->id }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge rounded-circle p-1 {{ $acc->account_type == 1 ? 'bg-primary' : 'bg-danger' }}">
                                                        <i class="ti {{ $acc->account_type == 1 ? 'ti-brand-facebook' : 'ti-brand-instagram' }} text-white"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-semibold text-dark small" style="font-size: 12px;">{{ $acc->name }}</div>
                                                        <div class="text-secondary" style="font-size: 10px;">{{ $acc->account_type == 1 ? 'Facebook Page' : 'Instagram Professional' }}</div>
                                                    </div>
                                                </div>
                                                <input type="checkbox" class="form-check-input studio-acc-cb ms-0" value="{{ $acc->id }}" checked>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center bg-light-subtle rounded-3 border">
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
                                        <input type="date" id="studio-schedule-date" class="form-control form-control-sm rounded-2" value="{{ $selectedFestival->festival_date->format('Y-m-d') }}" style="font-size: 12.5px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small mb-1" style="font-size: 11px;">Publish Time</label>
                                        <input type="time" id="studio-schedule-time" class="form-control form-control-sm rounded-2" value="{{ $targetTime }}" style="font-size: 12.5px;">
                                    </div>
                                </div>

                                {{-- Ready Caption & Hashtags --}}
                                <div>
                                    <label class="form-label text-muted small mb-1" style="font-size: 11px;">Caption & Hashtags</label>
                                    <textarea id="studio-caption-text" class="form-control rounded-2" rows="4" style="font-size: 12px;">{{ $captionText }}</textarea>
                                </div>

                                {{-- Alert Message Slot --}}
                                <div id="studio-schedule-alert" class="d-none alert alert-danger small p-2 rounded-2 mb-0"></div>

                                {{-- Schedule Submit Button --}}
                                <button type="button" class="btn btn-success rounded-pill w-100 py-2.5 fw-semibold shadow-sm" id="studio-btn-submit-schedule">
                                    <i class="ti ti-calendar-check me-1.5"></i>Schedule Festival Post
                                </button>

                                <button type="button" class="btn btn-outline-secondary rounded-pill w-100 py-2 small" id="studio-btn-back-step">
                                    &larr; Back to Design
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- VIEW 2: ALL DESIGNS GRID GALLERY (Toggleable via Top Bar)                  --}}
    {{-- ========================================================================= --}}
    <div id="fs-grid-workspace" class="d-none mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold text-dark m-0" style="font-size: 1.1rem;">
                All Poster Templates (सभी डिज़ाइन)
            </h4>
            <span class="text-secondary small">Click any design to customize in Studio</span>
        </div>

        @if($templates->isNotEmpty())
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach($templates as $template)
                    @php
                        $composerUrl = route('admin.posts.create', [
                            'date' => $selectedFestival?->festival_date?->format('Y-m-d\T08:00'),
                            'caption' => $captionText,
                        ]);
                        $isScheduled = in_array($template->id, $scheduledTemplateIds ?? []);
                        $tplFooterStyle = $template->layout_config['footer']['style'] ?? ($template->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
                        $tplHeaderLayout = $template->layout_config['header']['layout'] ?? ($template->layout_config['header']['mode'] ?? 'full_width_strip');
                        $tplFooterFixed = !empty($template->layout_config['footer']['is_fixed']) ? '1' : '0';
                        $tplHeaderFixed = !empty($template->layout_config['header']['is_fixed']) ? '1' : '0';
                        $tplLeaderShape = $template->layout_config['header']['chip_shape'] ?? 'circle';
                        $tplLeaderBorder = $template->layout_config['header']['chip_border_color'] ?? '#FF9933';
                    @endphp
                    <div class="col" id="template-card-col-{{ $template->id }}">
                        <div class="fs-template-card h-100 d-flex flex-column shadow-xs" id="template-card-{{ $template->id }}">
                            
                            {{-- 1:1 Aspect Ratio Large Poster Container --}}
                            <div class="fs-template-preview-wrapper cursor-pointer btn-activate-studio-template"
                                 data-template-id="{{ $template->id }}"
                                 data-template-title="{{ $template->title }}"
                                 data-template-image="{{ $template->image_url }}"
                                 data-festival-id="{{ $selectedFestival?->id }}"
                                 data-festival-title="{{ $selectedFestival?->title }}"
                                 data-festival-date="{{ $selectedFestival?->festival_date?->format('Y-m-d') }}"
                                 data-festival-date-formatted="{{ $selectedFestival?->festival_date?->format('d M Y') }}"
                                 data-is-today="{{ $isToday ? '1' : '0' }}"
                                 data-target-time="{{ $targetTime }}"
                                 data-target-time-formatted="{{ $targetTimeFormatted }}"
                                 data-caption="{{ $captionText }}"
                                 data-footer-style="{{ $tplFooterStyle }}"
                                 data-footer-fixed="{{ $tplFooterFixed }}"
                                 data-header-layout="{{ $tplHeaderLayout }}"
                                 data-header-fixed="{{ $tplHeaderFixed }}"
                                 data-leader-shape="{{ $tplLeaderShape }}"
                                 data-leader-border="{{ $tplLeaderBorder }}"
                                 data-composer-url="{{ $composerUrl }}"
                                 data-party-id="{{ $template->party_id ?? '' }}"
                                 data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                                
                                <img src="{{ $template->image_url }}" alt="{{ $template->title }}" class="fs-template-preview-img" loading="lazy">
                                
                                {{-- Top Party Badge --}}
                                @if($template->party)
                                    <div class="position-absolute top-0 start-0 m-2.5 d-flex gap-1 z-1">
                                        <span class="badge bg-dark text-white rounded-pill shadow-xs" style="font-size: 11px; padding: 4px 10px;">
                                            {{ $template->party->short_code }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Hover Actions Overlay --}}
                                <div class="fs-template-overlay">
                                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 w-100 shadow-sm btn-activate-studio-template"
                                            data-template-id="{{ $template->id }}"
                                            data-template-title="{{ $template->title }}"
                                            data-template-image="{{ $template->image_url }}"
                                            data-footer-style="{{ $tplFooterStyle }}"
                                            data-footer-fixed="{{ $tplFooterFixed }}"
                                            data-header-layout="{{ $tplHeaderLayout }}"
                                            data-header-fixed="{{ $tplHeaderFixed }}"
                                            data-leader-shape="{{ $tplLeaderShape }}"
                                            data-leader-border="{{ $tplLeaderBorder }}"
                                            data-party-id="{{ $template->party_id ?? '' }}"
                                            data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                                        <i class="ti ti-palette me-1"></i>Customize in Studio
                                    </button>
                                </div>
                            </div>

                            {{-- Card Footer with Title, Share & Schedule Actions --}}
                            <div class="p-3 d-flex align-items-center justify-content-between bg-white border-top">
                                <span class="fw-bold text-dark text-truncate me-2" title="{{ $template->title }}" id="card-title-{{ $template->id }}" style="font-size: 0.95rem;">
                                    {{ $template->title }}
                                </span>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0" id="card-action-{{ $template->id }}">
                                    @if($isScheduled)
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1.5 small fw-semibold">
                                            <i class="ti ti-check me-1"></i>Scheduled
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold btn-activate-studio-template"
                                                data-template-id="{{ $template->id }}"
                                                data-template-title="{{ $template->title }}"
                                                data-template-image="{{ $template->image_url }}"
                                                data-footer-style="{{ $tplFooterStyle }}"
                                                data-footer-fixed="{{ $tplFooterFixed }}"
                                                data-header-layout="{{ $tplHeaderLayout }}"
                                                data-header-fixed="{{ $tplHeaderFixed }}"
                                                data-leader-shape="{{ $tplLeaderShape }}"
                                                data-leader-border="{{ $tplLeaderBorder }}"
                                                data-party-id="{{ $template->party_id ?? '' }}"
                                                data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@else
    <div class="border border-dashed rounded-4 p-5 text-center my-4 bg-light-subtle">
        <div class="avatar avatar-md bg-secondary-lt rounded-circle mb-2">
            <i class="ti ti-photo-off text-secondary fs-3"></i>
        </div>
        <div class="fw-semibold text-dark mb-1">No templates found for this date</div>
        <div class="text-secondary small">Select another upcoming date to explore designs.</div>
    </div>
@endif
