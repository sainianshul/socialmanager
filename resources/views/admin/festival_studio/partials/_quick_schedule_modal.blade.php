{{-- Modern Minimalist Festival Poster & Scheduler Modal --}}
<div class="modal fade" id="quickScheduleModal" tabindex="-1" aria-labelledby="quickScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 960px; width: 95%;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold text-dark mb-0" id="quickScheduleModalLabel">
                                Festival Poster Studio
                            </h5>
                            <span class="badge bg-teal-lt text-teal border border-teal-subtle px-2.5 py-0.5 rounded-pill fw-medium small" id="qs-modal-subheading"></span>
                        </div>
                    </div>
                </div>
                
                {{-- Sleek Minimal Stepper & Close --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-flex align-items-center gap-1 p-1 bg-light rounded-pill border">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fw-semibold" id="qs-step-indicator-1">
                            <i class="ti ti-palette me-1"></i> 1. Poster Style
                        </span>
                        <span class="badge bg-transparent text-secondary rounded-pill px-3 py-1.5 fw-medium" id="qs-step-indicator-2">
                            <i class="ti ti-send me-1"></i> 2. Schedule Details
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                
                {{-- ======================================================== --}}
                {{-- STEP 1: Minimalist Poster Preview & Clean Styling Panel   --}}
                {{-- ======================================================== --}}
                <div id="qs-step-pane-1">
                    <div class="row g-4 align-items-start">
                        
                        {{-- Left Column: Big HD Poster Preview + Single-Row Toolbar --}}
                        <div class="col-lg-6">
                            <div class="position-relative rounded-4 overflow-hidden border shadow-sm" style="aspect-ratio: 1 / 1; background: #0f172a;">
                                <a href="#" id="qs-modal-preview-link" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100 disabled text-decoration-none" title="Click to view full 1080p HD poster in new tab" style="cursor: zoom-in;">
                                    <img id="qs-modal-img" src="" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">
                                </a>

                                {{-- HD Badge --}}
                                <div class="position-absolute top-0 end-0 m-2.5 pointer-events-none">
                                    <span class="badge bg-dark bg-opacity-75 text-white border border-white-20 rounded-pill px-2.5 py-1" style="font-size: 10px; backdrop-filter: blur(6px); letter-spacing: 0.03em;">
                                        <i class="ti ti-sparkles text-warning me-1"></i>1080p HD
                                    </span>
                                </div>

                                {{-- Live Composition Loading Spinner Overlay --}}
                                <div id="qs-modal-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white" style="backdrop-filter: blur(6px); z-index: 10;">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                                    <div class="small fw-semibold">Personalizing Poster...</div>
                                    <div class="text-white-50 small" style="font-size: 11px;">Rendering your branding in 1080p</div>
                                </div>
                            </div>

                            {{-- Clean, Compact Actions Toolbar (Fits on One Row) --}}
                            <div class="d-flex align-items-center justify-content-between gap-1.5 mt-3 pt-1">
                                <a href="#" id="qs-modal-btn-preview" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 flex-fill disabled fw-medium text-center" title="Open Full-Screen 1080p HD in New Tab">
                                    <i class="ti ti-maximize me-1"></i>Preview
                                </a>
                                <a href="#" id="qs-modal-btn-download" target="_blank" download="festival-poster.jpg" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 flex-fill disabled fw-semibold text-center" title="Download High-Res 1080p Poster">
                                    <i class="ti ti-download me-1"></i>Download
                                </a>
                                <button type="button" id="qs-modal-btn-share-wa" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1.5 flex-fill fw-medium text-center" title="Share on WhatsApp">
                                    <i class="ti ti-brand-whatsapp me-1 text-success"></i>WhatsApp
                                </button>
                                <button type="button" id="qs-modal-btn-share-native" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 flex-fill fw-medium text-center" title="Share Link">
                                    <i class="ti ti-share me-1"></i>Share
                                </button>
                            </div>
                        </div>

                        {{-- Right Column: Minimalist Settings Panel --}}
                        <div class="col-lg-6">
                            <div class="d-flex flex-column gap-3">
                                
                                {{-- 1. Branding Footer Ribbon Style --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label for="qs-modal-footer-style" class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                            <i class="ti ti-layout-bottombar text-primary"></i> Branding Footer Style
                                        </label>
                                        <span id="qs-modal-footer-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            <i class="ti ti-lock me-0.5"></i> Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select form-select-md rounded-3 border-light-subtle shadow-none" id="qs-modal-footer-style">
                                        <option value="dual_ribbon_badge">Layer 1: Dual Ribbon Badge (Bhai Dooj Style)</option>
                                        <option value="solid_white_banner">Layer 2: Solid White Banner (Milkha Singh Style)</option>
                                        <option value="floating_tricolor_badge">Layer 3: Floating Tricolor Badge (Diwali Style)</option>
                                        <option value="royal_gold_dock">Layer 4: Royal Gold Banner (Midnight Slate Style)</option>
                                        <option value="candidate_left_ribbon">Layer 5: Candidate Left Ribbon (Mirrored Style)</option>
                                        <option value="modern_capsule_dock">Layer 6: Modern Capsule Dock (Floating Pill Card)</option>
                                        <option value="circular_medallion_badge">Layer 7: Circular Medallion Badge (Framed Portrait)</option>
                                        <option value="saffron_wave_banner">Layer 8: Saffron Wave Banner (Dynamic Wave)</option>
                                        <option value="tricolor_angled_blade">Layer 9: Tricolor Angled Blade (Patriotic Blade)</option>
                                        <option value="royal_maroon_heritage">Layer 10: Royal Maroon Heritage (Sacred Gold & Maroon)</option>
                                    </select>
                                    <div id="qs-modal-footer-locked-msg" class="text-danger small mt-1 d-none" style="font-size: 11px;">
                                        <i class="ti ti-lock me-1"></i> Footer layout is locked for this artwork.
                                    </div>
                                </div>

                                {{-- 2. Top Leadership Strip Header Layout --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <label for="qs-modal-header-layout" class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                            <i class="ti ti-layout-navbar text-primary"></i> Top Leadership Header
                                        </label>
                                        <span id="qs-modal-header-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            <i class="ti ti-lock me-0.5"></i> Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select form-select-md rounded-3 border-light-subtle shadow-none" id="qs-modal-header-layout">
                                        <option value="full_width_strip">Header 1: Full-Width Strip (6-7 Leaders)</option>
                                        <option value="top_left_cluster">Header 2: Top-Left Cluster (3-4 Leaders + Logo)</option>
                                        <option value="top_right_cluster">Header 3: Top-Right Cluster (3-4 Leaders + Logo)</option>
                                        <option value="split_wings">Header 4: Split Wings (3 Left + 3 Right)</option>
                                        <option value="vertical_capsule_strip">Header 5: Vertical Capsule Cards</option>
                                        <option value="party_group_cutout">Header 6: Joint Group Cutout</option>
                                        <option value="frosted_glass_dock">Header 7: Frosted Glass Dock</option>
                                        <option value="tricolor_ribbon_canopy">Header 8: Tricolor Canopy</option>
                                        <option value="none">None: Hide Leadership Strip</option>
                                    </select>
                                    <div id="qs-modal-header-locked-msg" class="text-danger small mt-1 d-none" style="font-size: 11px;">
                                        <i class="ti ti-lock me-1"></i> Leadership header is locked for this artwork.
                                    </div>
                                </div>

                                {{-- 3. Leadership Strip Selector --}}
                                @if(isset($allStrips) && $allStrips->isNotEmpty())
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label for="qs-modal-strip-id" class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                                <i class="ti ti-users text-primary"></i> Leadership Strip
                                            </label>
                                            <a href="{{ route('admin.leader-strips.index') }}" target="_blank" class="text-primary small text-decoration-none" style="font-size: 11px;">
                                                Manage Strips <i class="ti ti-arrow-up-right"></i>
                                            </a>
                                        </div>
                                        <select class="form-select form-select-md rounded-3 border-light-subtle shadow-none" id="qs-modal-strip-id">
                                            @foreach($allStrips as $s)
                                                @php
                                                    $isUser = !empty($s->user_id);
                                                    $partyCode = $s->party?->short_code ?? 'Universal';
                                                    $label = $isUser 
                                                        ? '⭐ ' . $s->name . ' (My Custom Strip)' 
                                                        : '🏛️ ' . $s->name . ' (' . $partyCode . ' Default)';
                                                @endphp
                                                <option value="{{ $s->id }}" {{ ($activeStrip?->id == $s->id) ? 'selected' : '' }} data-party-id="{{ $s->party_id }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                {{-- 4. Advanced Avatar Shape & Ring Accent (Collapsible Minimal Drawer) --}}
                                <div class="border rounded-3 p-2.5 bg-light-subtle">
                                    <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark" data-bs-toggle="collapse" href="#qs-modal-advanced-leader" role="button" aria-expanded="false">
                                        <span class="small fw-semibold d-flex align-items-center gap-1.5">
                                            <i class="ti ti-adjustments-alt text-secondary"></i> Leader Avatar Shape & Ring Color
                                        </span>
                                        <span class="text-secondary small d-flex align-items-center gap-0.5" style="font-size: 11px;">
                                            Customize <i class="ti ti-chevron-down"></i>
                                        </span>
                                    </a>
                                    <div class="collapse mt-2.5 pt-2.5 border-top" id="qs-modal-advanced-leader">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-sm-6">
                                                <label class="form-label small text-secondary mb-1" style="font-size: 11px;">Avatar Shape</label>
                                                <select class="form-select form-select-sm rounded-2" id="qs-modal-leader-shape">
                                                    <option value="circle">Circle (गोल)</option>
                                                    <option value="rounded_square">Rounded (घुमावदार)</option>
                                                    <option value="square">Square (चौकोर)</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small text-secondary mb-1" style="font-size: 11px;">Ring Color</label>
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <input type="color" id="qs-modal-leader-border" class="form-control form-control-color form-control-sm p-0 rounded-2" value="#FF9933" title="Ring Color" style="width: 32px; height: 31px;">
                                                    <div class="btn-group btn-group-sm flex-fill" role="group">
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#FF9933" style="font-size: 10px;" title="Saffron">Saffron</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#FFFFFF" style="font-size: 10px;" title="White">White</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#138808" style="font-size: 10px;" title="Green">Green</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#D4AF37" style="font-size: 10px;" title="Gold">Gold</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. User Branding Profile Summary Bar --}}
                                @php
                                    $userBranding = auth()->user()->defaultBranding ?? auth()->user()->brandings()->first();
                                    $userName = $userBranding?->effective_name ?? auth()->user()->name;
                                    $userParty = $userBranding?->party?->short_code ?? 'Universal';
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-2 min-width-0">
                                        <div class="avatar avatar-xs rounded-circle bg-primary-subtle text-primary border border-primary-subtle flex-shrink-0" style="font-size: 11px;">
                                            <i class="ti ti-user-check"></i>
                                        </div>
                                        <div class="min-width-0">
                                            <div class="small fw-semibold text-dark text-truncate" style="max-width: 180px;">{{ $userName }}</div>
                                            <div class="text-secondary small" style="font-size: 10px;">
                                                Party: <span class="fw-semibold text-dark">{{ $userParty }}</span> • Auto-branded
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-primary small text-decoration-none fw-medium" style="font-size: 11px;">
                                        Edit Profile <i class="ti ti-arrow-up-right"></i>
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
                        <input type="hidden" name="strip_id" id="qs-input-strip-id" value="{{ $activeStrip?->id }}">

                        {{-- Alert Box for Dynamic Errors --}}
                        <div id="qs-alert" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3" role="alert"></div>

                        <div class="row g-3">
                            
                            {{-- Section 1: Publish To (Facebook / Instagram Connected Accounts) --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark d-flex align-items-center gap-1.5 mb-2">
                                    <i class="ti ti-share text-primary"></i> Publish To Connected Social Channels
                                </label>

                                @php
                                    $metaAccounts = isset($accounts) 
                                        ? $accounts->filter(fn($a) => in_array($a->account_type, [1, 2])) 
                                        : collect();
                                @endphp

                                @if($metaAccounts->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($metaAccounts as $acc)
                                            <label class="d-flex align-items-center gap-2 p-2 px-3 rounded-3 border bg-light-subtle cursor-pointer qs-account-row active position-relative">
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
                                                                <i class="ti ti-brand-facebook me-0.5"></i> Facebook
                                                            </span>
                                                        @else
                                                            <span class="badge text-white" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); font-size: 9px; padding: 1px 5px;">
                                                                <i class="ti ti-brand-instagram me-0.5"></i> Instagram
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 rounded-3 border border-dashed bg-light text-center">
                                        <div class="small text-secondary mb-1.5">No active Facebook or Instagram accounts connected yet.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="ti ti-plug me-1"></i>Connect Facebook & Instagram Accounts
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Section 2: Post Caption --}}
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label for="qs-input-caption" class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="ti ti-pencil text-primary"></i> Post Caption & Festive Wishes
                                    </label>
                                    <span class="text-secondary small" style="font-size: 11px;">Auto-formatted for Facebook & Instagram</span>
                                </div>
                                <textarea name="content" id="qs-input-caption" class="form-control rounded-3" rows="4" placeholder="Write a celebratory caption or message for your followers..." style="font-size: 0.95rem; line-height: 1.5;"></textarea>
                            </div>

                            {{-- Section 3: Fixed Date & Editable Schedule Time --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <i class="ti ti-calendar text-primary"></i> Festival Date (Fixed)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="ti ti-lock text-danger"></i></span>
                                    <input type="text" id="qs-display-date" class="form-control bg-light border-start-0 fw-semibold text-dark" readonly disabled>
                                </div>
                                <div class="text-secondary small mt-1" style="font-size: 11px;">
                                    Locked to festival calendar date.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="qs-input-time" class="form-label small fw-bold text-dark mb-1 required d-flex align-items-center gap-1.5">
                                    <i class="ti ti-clock text-primary"></i> Schedule Time
                                </label>
                                <input type="time" name="schedule_time" id="qs-input-time" class="form-control fw-bold rounded-3" value="08:00" required>
                                <div class="text-secondary small mt-1" style="font-size: 11px;">
                                    Recommended: <strong>07:00 AM - 09:00 AM</strong> for maximum festive engagement.
                                </div>
                            </div>

                            {{-- Success Guarantee Callout --}}
                            <div class="col-12">
                                <div class="p-2.5 bg-light-subtle rounded-3 border d-flex align-items-center gap-2">
                                    <div class="avatar avatar-xs rounded-circle bg-success text-white flex-shrink-0" style="font-size: 10px;">
                                        <i class="ti ti-check"></i>
                                    </div>
                                    <div class="small text-secondary" style="font-size: 11px; line-height: 1.4;">
                                        Your post will be generated in 1080×1080 HD quality with your branding and published automatically on the scheduled date and time.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                <div class="text-secondary small d-flex align-items-center gap-1.5">
                    <span id="qs-footer-info-text">Step 1 of 2: Choose poster design styles</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Step 1 Buttons --}}
                    <div id="qs-actions-step-1" class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold" id="qs-btn-next-step">
                            Next: Schedule Details <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>

                    {{-- Step 2 Buttons --}}
                    <div id="qs-actions-step-2" class="d-none d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-medium" id="qs-btn-back-step">
                            <i class="ti ti-arrow-left me-1"></i> Back to Styles
                        </button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold" id="btn-submit-quick-schedule">
                            <i class="ti ti-calendar-time me-1"></i> <span id="btn-submit-text">Schedule Post</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
