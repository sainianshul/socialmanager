{{-- Modern Minimalist Festival Poster & Scheduler Modal --}}
<div class="modal fade" id="quickScheduleModal" tabindex="-1" aria-labelledby="quickScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1040px; width: 95%;">
        <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden" style="border-radius: 24px; box-shadow: 0 25px 70px -15px rgba(15, 23, 42, 0.22);">
            
            {{-- Modal Header: Clean & Spacious --}}
            <div class="modal-header border-bottom py-3 px-4 px-lg-5 bg-white d-flex align-items-center justify-content-between" style="border-color: #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="quickScheduleModalLabel" style="font-size: 1.25rem; letter-spacing: -0.01em;">
                        Festival Poster Studio
                    </h5>
                    <span class="badge px-3 py-1.5 rounded-pill fw-medium small" id="qs-modal-subheading" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; font-size: 12px;"></span>
                </div>
                
                {{-- Sleek Stepper Pills & Close Button --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-flex align-items-center gap-1 p-1 rounded-pill" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fw-semibold" id="qs-step-indicator-1" style="font-size: 12px;">
                            1. Poster Style
                        </span>
                        <span class="badge bg-transparent text-secondary rounded-pill px-3 py-1.5 fw-medium" id="qs-step-indicator-2" style="font-size: 12px;">
                            2. Schedule Details
                        </span>
                    </div>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body: Generous Padding & Whitespace --}}
            <div class="modal-body p-4 p-lg-5 bg-white">
                
                {{-- ======================================================== --}}
                {{-- STEP 1: Minimalist Poster Preview & Clean Styling Panel   --}}
                {{-- ======================================================== --}}
                <div id="qs-step-pane-1">
                    <div class="row g-4 g-lg-5 align-items-start">
                        
                        {{-- Left Column: High-Res Poster Showcase + Unified Actions Toolbar --}}
                        <div class="col-lg-6">
                            <div class="position-relative overflow-hidden shadow-sm" style="aspect-ratio: 1 / 1; background: #0f172a; border-radius: 20px; border: 1px solid rgba(0, 0, 0, 0.08);">
                                <a href="#" id="qs-modal-preview-link" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100 disabled text-decoration-none" title="Click to view full 1080p HD poster in new tab" style="cursor: zoom-in;">
                                    <img id="qs-modal-img" src="" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">
                                </a>

                                {{-- HD Badge --}}
                                <div class="position-absolute top-0 end-0 m-3 pointer-events-none">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background: rgba(15, 23, 42, 0.75); color: #ffffff; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15); font-size: 11px; letter-spacing: 0.04em;">
                                        1080p HD
                                    </span>
                                </div>

                                {{-- Live Composition Loading Spinner Overlay --}}
                                <div id="qs-modal-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white" style="backdrop-filter: blur(8px); z-index: 10;">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                                    <div class="small fw-semibold">Personalizing Poster...</div>
                                    <div class="text-white-50 small mt-0.5" style="font-size: 11px;">Rendering your branding in 1080p</div>
                                </div>
                            </div>

                            {{-- Clean Action Toolbar: Balanced Spacing & Generous Margins --}}
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-4">
                                <a href="#" id="qs-modal-btn-preview" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-pill px-3 py-2 flex-fill disabled fw-medium text-center shadow-none" style="font-size: 13px; border-color: #E2E8F0; color: #475569;" title="Open Full-Screen 1080p in New Tab">
                                    Preview
                                </a>
                                <a href="#" id="qs-modal-btn-download" target="_blank" download="festival-poster.jpg" class="btn btn-primary rounded-pill px-3 py-2 flex-fill disabled fw-semibold text-center border-0 shadow-sm" style="font-size: 13px; background: #0D9488; box-shadow: 0 2px 8px rgba(13, 148, 136, 0.25);" title="Download High-Res 1080p Poster">
                                    Download
                                </a>
                                <button type="button" id="qs-modal-btn-share-wa" class="btn btn-outline-success rounded-pill px-3 py-2 flex-fill fw-medium text-center shadow-none" style="font-size: 13px; background: #ECFDF5; border-color: #A7F3D0; color: #059669;" title="Share on WhatsApp">
                                    WhatsApp
                                </button>
                                <button type="button" id="qs-modal-btn-share-native" class="btn btn-outline-secondary rounded-pill px-3 py-2 flex-fill fw-medium text-center shadow-none" style="font-size: 13px; border-color: #E2E8F0; color: #475569;" title="Share Link">
                                    Share
                                </button>
                            </div>
                        </div>

                        {{-- Right Column: Spacious Minimalist Customization Panel --}}
                        <div class="col-lg-6">
                            <div class="d-flex flex-column" style="gap: 20px;">
                                
                                {{-- 1. Branding Footer Ribbon Style --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="qs-modal-footer-style" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.06em;">
                                            Branding Footer Style
                                        </label>
                                        <span id="qs-modal-footer-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select qs-premium-select" id="qs-modal-footer-style">
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
                                    <div id="qs-modal-footer-locked-msg" class="text-danger small mt-1.5 d-none" style="font-size: 11px;">
                                        Footer layout is locked for this artwork.
                                    </div>
                                </div>

                                {{-- 2. Top Leadership Strip Header Layout --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="qs-modal-header-layout" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.06em;">
                                            Top Leadership Header
                                        </label>
                                        <span id="qs-modal-header-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select qs-premium-select" id="qs-modal-header-layout">
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
                                    <div id="qs-modal-header-locked-msg" class="text-danger small mt-1.5 d-none" style="font-size: 11px;">
                                        Leadership header is locked for this artwork.
                                    </div>
                                </div>

                                {{-- 3. Leadership Strip Selector (Clean & Emoji-Free) --}}
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label for="qs-modal-strip-id" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.06em;">
                                                Leadership Strip
                                            </label>
                                            <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 12px;">
                                                Manage Strips &rarr;
                                            </a>
                                        </div>
                                        <select class="form-select qs-premium-select" id="qs-modal-strip-id">
                                            @foreach($allStrips as $s)
                                                @php
                                                    $isUser = !empty($s->user_id);
                                                    $partyCode = $s->party?->short_code ?? 'Universal';
                                                    $label = $isUser 
                                                        ? $s->name . ' (Custom Strip)' 
                                                        : $s->name . ' (' . $partyCode . ' Default)';
                                                @endphp
                                                <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                {{-- 4. Advanced Avatar Shape & Ring Accent (Clean Modern Collapsible Drawer) --}}
                                <div class="rounded-4 p-3" style="background: #FAFAFA; border: 1px solid #F1F5F9;">
                                    <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark" data-bs-toggle="collapse" href="#qs-modal-advanced-leader" role="button" aria-expanded="false">
                                        <span class="small fw-semibold text-secondary" style="font-size: 13px;">
                                            Leader Avatar Shape & Ring Accent
                                        </span>
                                        <span class="text-primary small fw-medium" style="font-size: 12px;">
                                            Customize &darr;
                                        </span>
                                    </a>
                                    <div class="collapse mt-3 pt-3 border-top" id="qs-modal-advanced-leader" style="border-color: #E2E8F0 !important;">
                                        <div class="row g-3">
                                            {{-- Avatar Shape Selector (Excalidraw / Figma Style Tiles) --}}
                                            <div class="col-sm-5">
                                                <label class="form-label text-muted small mb-2 d-block" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Avatar Shape</label>
                                                <input type="hidden" id="qs-modal-leader-shape" value="circle">
                                                <div class="d-flex align-items-center gap-1.5" id="qs-shape-group">
                                                    <button type="button" class="btn btn-sm qs-shape-btn flex-fill d-flex flex-column align-items-center justify-content-center py-2 px-1 rounded-3 transition-all" data-shape="circle" style="border: 1.5px solid #2563EB; background: #EFF6FF;" title="Circle (गोल)">
                                                        <span class="qs-shape-icon d-inline-block rounded-circle" style="width: 18px; height: 18px; border: 2px solid #2563EB; background: transparent;"></span>
                                                        <span class="qs-shape-text mt-1 text-truncate" style="font-size: 10.5px; font-weight: 600; color: #1E40AF; line-height: 1;">Circle</span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm qs-shape-btn flex-fill d-flex flex-column align-items-center justify-content-center py-2 px-1 rounded-3 transition-all" data-shape="rounded_square" style="border: 1.5px solid #E2E8F0; background: #FFFFFF;" title="Rounded (घुमावदार)">
                                                        <span class="qs-shape-icon d-inline-block rounded-2" style="width: 18px; height: 18px; border: 2px solid #64748B; background: transparent;"></span>
                                                        <span class="qs-shape-text mt-1 text-truncate text-secondary" style="font-size: 10.5px; font-weight: 500; color: #64748B; line-height: 1;">Round</span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm qs-shape-btn flex-fill d-flex flex-column align-items-center justify-content-center py-2 px-1 rounded-3 transition-all" data-shape="square" style="border: 1.5px solid #E2E8F0; background: #FFFFFF;" title="Square (चौकोर)">
                                                        <span class="qs-shape-icon d-inline-block rounded-0" style="width: 18px; height: 18px; border: 2px solid #64748B; background: transparent;"></span>
                                                        <span class="qs-shape-text mt-1 text-truncate text-secondary" style="font-size: 10.5px; font-weight: 500; color: #64748B; line-height: 1;">Square</span>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Ring Color Swatches (Excalidraw Style Color Boxes) --}}
                                            <div class="col-sm-7">
                                                <label class="form-label text-muted small mb-2 d-block" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Ring Accent Color</label>
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap" id="qs-color-swatches-wrapper" style="min-height: 48px;">
                                                    <!-- Saffron -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#FF9933" title="Saffron (केसरिया)" style="width: 28px; height: 28px; background-color: #FF9933; border: 1px solid rgba(0,0,0,0.1); outline: 2px solid #2563EB; outline-offset: 2px;">
                                                        <span class="qs-color-check position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 13px; line-height: 1; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.6));">&#10003;</span>
                                                    </button>
                                                    <!-- White -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#FFFFFF" title="White (सफेद)" style="width: 28px; height: 28px; background-color: #FFFFFF; border: 1.5px solid #CBD5E1;">
                                                        <span class="qs-color-check d-none position-absolute top-50 start-50 translate-middle text-dark fw-bold" style="font-size: 13px; line-height: 1;">&#10003;</span>
                                                    </button>
                                                    <!-- Green -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#138808" title="Green (हरा)" style="width: 28px; height: 28px; background-color: #138808; border: 1px solid rgba(0,0,0,0.1);">
                                                        <span class="qs-color-check d-none position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 13px; line-height: 1; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.6));">&#10003;</span>
                                                    </button>
                                                    <!-- Gold -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#D4AF37" title="Gold (सुनहरा)" style="width: 28px; height: 28px; background-color: #D4AF37; border: 1px solid rgba(0,0,0,0.1);">
                                                        <span class="qs-color-check d-none position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 13px; line-height: 1; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.6));">&#10003;</span>
                                                    </button>
                                                    <!-- Red -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#DC2626" title="Red (लाल)" style="width: 28px; height: 28px; background-color: #DC2626; border: 1px solid rgba(0,0,0,0.1);">
                                                        <span class="qs-color-check d-none position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 13px; line-height: 1; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.6));">&#10003;</span>
                                                    </button>
                                                    <!-- Navy Blue -->
                                                    <button type="button" class="btn p-0 qs-color-swatch position-relative rounded-2 shadow-sm" data-color="#1E3A8A" title="Navy Blue (नीला)" style="width: 28px; height: 28px; background-color: #1E3A8A; border: 1px solid rgba(0,0,0,0.1);">
                                                        <span class="qs-color-check d-none position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 13px; line-height: 1; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.6));">&#10003;</span>
                                                    </button>
                                                    <!-- Custom Color Picker Box -->
                                                    <div id="qs-custom-color-box" class="position-relative rounded-2 overflow-hidden shadow-sm d-flex align-items-center justify-content-center" title="Custom Color (कस्टम रंग)" style="width: 28px; height: 28px; border: 1.5px dashed #94A3B8; background: conic-gradient(from 180deg at 50% 50%, #FF0000 0deg, #FFFF00 60deg, #00FF00 120deg, #00FFFF 180deg, #0000FF 240deg, #FF00FF 300deg, #FF0000 360deg); cursor: pointer;">
                                                        <input type="color" id="qs-modal-leader-border" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" value="#FF9933" style="cursor: pointer;">
                                                        <span class="text-white fw-bold position-relative" style="font-size: 12px; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.8)); pointer-events: none;">+</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Leader Avatar Size (Increase / Decrease Buttons & Presets) --}}
                                            <div class="col-12 pt-2.5 mt-2 border-top" style="border-color: #E2E8F0 !important;">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <label class="form-label text-muted small mb-0 d-block" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Avatar Size (लीडर बॉक्स का साइज़)</label>
                                                        <span class="text-secondary" style="font-size: 11px;">Tap &minus; / &#43; to adjust</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1.5">
                                                        <!-- Minus Button -->
                                                        <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center p-0 rounded-2 shadow-sm" id="qs-btn-size-dec" title="Decrease Size (छोटा करें)" style="width: 30px; height: 30px; border-color: #CBD5E1 !important; background: #FFFFFF;">
                                                            <span class="fw-bold text-dark" style="font-size: 16px; line-height: 1;">&minus;</span>
                                                        </button>
                                                        
                                                        <!-- Size Presets: S, M, L -->
                                                        <div class="btn-group btn-group-sm" role="group" id="qs-size-preset-group">
                                                            <button type="button" class="btn btn-outline-secondary px-2.5 py-1 qs-size-btn" data-size="88" style="font-size: 11px; border-color: #E2E8F0;">Small</button>
                                                            <button type="button" class="btn btn-primary px-2.5 py-1 qs-size-btn active" data-size="106" style="font-size: 11px;">Default</button>
                                                            <button type="button" class="btn btn-outline-secondary px-2.5 py-1 qs-size-btn" data-size="124" style="font-size: 11px; border-color: #E2E8F0;">Large</button>
                                                        </div>

                                                        <!-- Plus Button -->
                                                        <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center p-0 rounded-2 shadow-sm" id="qs-btn-size-inc" title="Increase Size (बड़ा करें)" style="width: 30px; height: 30px; border-color: #CBD5E1 !important; background: #FFFFFF;">
                                                            <span class="fw-bold text-dark" style="font-size: 16px; line-height: 1;">&#43;</span>
                                                        </button>

                                                        <!-- Hidden input for modal leader size -->
                                                        <input type="hidden" id="qs-modal-leader-size" value="106">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. Show / Hide Social Media Handles Switch --}}
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4" style="background: #FAFAFA; border: 1px solid #F1F5F9;">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 32px; height: 32px; background: #EEF2FF; color: #4F46E5;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <label for="qs-modal-show-social" class="form-label fw-semibold text-dark mb-0 cursor-pointer" style="font-size: 13px;">
                                                Social Media Handles
                                            </label>
                                            <div class="text-secondary small" style="font-size: 11px;">
                                                Show / Hide phone & social handles on footer strip
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0 ps-0">
                                        <input class="form-check-input ms-0" type="checkbox" id="qs-modal-show-social" checked style="cursor: pointer; width: 42px; height: 22px;">
                                    </div>
                                </div>

                                {{-- 6. User Branding Profile Summary Bar (Generous White Card) --}}
                                @php
                                    $userBranding = auth()->user()->defaultBranding ?? auth()->user()->brandings()->first();
                                    $userName = $userBranding?->effective_name ?? auth()->user()->name;
                                    $userParty = $userBranding?->party?->short_code ?? 'Universal';
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="d-flex align-items-center gap-2.5 min-width-0">
                                        <div class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="background: #E0F2FE; color: #0284C7; font-size: 12px; width: 34px; height: 34px;">
                                            {{ mb_substr($userName, 0, 1) }}
                                        </div>
                                        <div class="min-width-0">
                                            <div class="fw-semibold text-dark text-truncate" style="font-size: 13.5px; max-width: 200px;">{{ $userName }}</div>
                                            <div class="text-secondary" style="font-size: 11px;">
                                                Party: <span class="fw-semibold text-dark">{{ $userParty }}</span> &bull; Auto-branded in 1080p
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 12px;">
                                        Edit Profile &rarr;
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- STEP 2: Facebook / Meta Style Social Post Composer       --}}
                {{-- ======================================================== --}}
                <div id="qs-step-pane-2" class="d-none">
                    <form id="quick-schedule-form">
                        @csrf
                        <input type="hidden" name="template_id" id="qs-input-template-id" value="">
                        <input type="hidden" name="festival_id" id="qs-input-festival-id" value="">
                        <input type="hidden" name="schedule_date" id="qs-input-date" value="">
                        <input type="hidden" name="composed_image" id="qs-input-composed-image" value="">
                        <input type="hidden" name="footer_style" id="qs-input-footer-style" value="">
                        <input type="hidden" name="header_layout" id="qs-input-header-layout" value="">
                        <input type="hidden" name="leader_shape" id="qs-input-leader-shape" value="circle">
                        <input type="hidden" name="leader_border_color" id="qs-input-leader-border" value="#FF9933">
                        <input type="hidden" name="leader_size" id="qs-input-leader-size" value="106">
                        <input type="hidden" name="strip_id" id="qs-input-strip-id" value="{{ $activeStrip?->id }}">
                        <input type="hidden" name="show_social_handles" id="qs-input-show-social" value="1">

                        {{-- Alert Box for Dynamic Errors --}}
                        <div id="qs-alert" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3" role="alert"></div>

                        <div class="row g-4">
                            
                            {{-- Section 1: Publish To (Facebook / Instagram Connected Accounts) --}}
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold small text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.06em;">
                                    Publish To Connected Social Channels
                                </label>

                                @php
                                    $metaAccounts = isset($accounts) 
                                        ? $accounts->filter(fn($a) => in_array($a->account_type, [1, 2])) 
                                        : collect();
                                @endphp

                                @if($metaAccounts->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2.5">
                                        @foreach($metaAccounts as $acc)
                                            <label class="d-flex align-items-center gap-2.5 p-2.5 px-3 rounded-4 border cursor-pointer qs-account-row active position-relative" style="background: #ffffff; border-color: #E2E8F0;">
                                                <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}" checked class="form-check-input m-0 qs-account-cb">
                                                <div class="avatar avatar-xs rounded-circle bg-white border flex-shrink-0">
                                                    @if($acc->profile_pic_url)
                                                        <img src="{{ $acc->profile_pic_url }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                                    @else
                                                        <i class="ti ti-user text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div class="min-width-0">
                                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 160px;">{{ $acc->name }}</div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        @if($acc->account_type == 1)
                                                            <span class="badge bg-blue text-white" style="font-size: 9px; padding: 1px 5px;">
                                                                Facebook
                                                            </span>
                                                        @else
                                                            <span class="badge text-white" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); font-size: 9px; padding: 1px 5px;">
                                                                Instagram
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 rounded-4 border border-dashed bg-light text-center">
                                        <div class="small text-secondary mb-2">No active Facebook or Instagram accounts connected yet.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-medium">
                                            Connect Social Accounts &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Section 2: Post Caption --}}
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label for="qs-input-caption" class="form-label text-muted fw-semibold small text-uppercase mb-0" style="font-size: 11px; letter-spacing: 0.06em;">
                                        Post Caption & Festive Wishes
                                    </label>
                                    <span class="text-secondary small" style="font-size: 11px;">Auto-formatted for Facebook & Instagram</span>
                                </div>
                                <textarea name="content" id="qs-input-caption" class="form-control rounded-4 shadow-none p-3" rows="4" placeholder="Write a celebratory caption or message for your followers..." style="font-size: 0.95rem; line-height: 1.6; border-color: #E2E8F0;"></textarea>
                            </div>

                            {{-- Section 3: Fixed Date & Editable Schedule Time --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-semibold small text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.06em;">
                                    Festival Date (Fixed)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #E2E8F0; border-radius: 12px 0 0 12px;"><i class="ti ti-lock text-secondary"></i></span>
                                    <input type="text" id="qs-display-date" class="form-control bg-light border-start-0 fw-semibold text-dark shadow-none" style="height: 48px; border-color: #E2E8F0; border-radius: 0 12px 12px 0;" readonly disabled>
                                </div>
                                <div class="text-secondary small mt-1.5" style="font-size: 11px;">
                                    Locked to festival calendar date.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="qs-input-time" class="form-label text-muted fw-semibold small text-uppercase mb-2 required" style="font-size: 11px; letter-spacing: 0.06em;">
                                    Schedule Time
                                </label>
                                <input type="time" name="schedule_time" id="qs-input-time" class="form-control fw-bold rounded-3 shadow-none" value="08:00" required style="height: 48px; border-color: #E2E8F0; border-radius: 12px; font-size: 15px;">
                                <div class="text-secondary small mt-1.5" style="font-size: 11px;">
                                    Recommended: <strong>07:00 AM - 09:00 AM</strong> for maximum reach.
                                </div>
                            </div>

                            {{-- Success Guarantee Callout --}}
                            <div class="col-12">
                                <div class="p-3 rounded-4 border d-flex align-items-center gap-2.5" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                                    <div class="avatar avatar-xs rounded-circle bg-success text-white flex-shrink-0" style="font-size: 10px;">
                                        &check;
                                    </div>
                                    <div class="small text-secondary" style="font-size: 12px; line-height: 1.5;">
                                        Your post will be generated in 1080&times;1080 HD quality with your branding cutout and published automatically on the scheduled date and time.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>

            {{-- Modal Footer: Generous & Clean --}}
            <div class="modal-footer border-top py-3 px-4 px-lg-5 bg-white d-flex align-items-center justify-content-between" style="border-color: #F1F5F9 !important;">
                <div class="text-secondary small d-flex align-items-center gap-1.5">
                    <span id="qs-footer-info-text" style="color: #64748B; font-size: 12px;">Step 1 of 2: Choose poster design styles</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Step 1 Buttons --}}
                    <div id="qs-actions-step-1" class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-medium shadow-none" data-bs-dismiss="modal" style="border-color: #E2E8F0; color: #475569; font-size: 13px;">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm border-0" id="qs-btn-next-step" style="background: #0D9488; font-size: 13px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);">
                            Next: Schedule Details &rarr;
                        </button>
                    </div>

                    {{-- Step 2 Buttons --}}
                    <div id="qs-actions-step-2" class="d-none d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-medium shadow-none" id="qs-btn-back-step" style="border-color: #E2E8F0; color: #475569; font-size: 13px;">
                            &larr; Back to Styles
                        </button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm border-0" id="btn-submit-quick-schedule" style="background: #0D9488; font-size: 13px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);">
                            <span id="btn-submit-text">Schedule Post</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
