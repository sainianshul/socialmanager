{{-- Multi-Step Festival Poster Customizer & Schedule Modal --}}
<div class="modal fade" id="quickScheduleModal" tabindex="-1" aria-labelledby="quickScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 860px; width: 95%;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Modal Header with Clean Step Tabs --}}
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="modal-title fw-bold text-dark mb-0" id="quickScheduleModalLabel">
                        Festival Poster & Schedule
                    </h4>
                    <div class="text-secondary small" id="qs-modal-subheading"></div>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill" id="qs-step-indicator-1">1. Poster Style</span>
                    <span class="badge bg-secondary-lt text-secondary px-3 py-1.5 rounded-pill" id="qs-step-indicator-2">2. Schedule Details</span>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                
                {{-- ======================================================== --}}
                {{-- STEP 1: Big Poster Preview & Strip / Header Selectors    --}}
                {{-- ======================================================== --}}
                <div id="qs-step-pane-1">
                    <div class="row g-4 align-items-center">
                        {{-- Left Column: Big HD Poster Preview --}}
                        <div class="col-md-6">
                            <div class="position-relative rounded-3 overflow-hidden border shadow-sm bg-dark" style="aspect-ratio: 1 / 1;">
                                <img id="qs-modal-img" src="" alt="Poster Preview" class="w-100 h-100 d-block" style="object-fit: contain;">

                                {{-- Live Composition Loading Spinner Overlay --}}
                                <div id="qs-modal-spinner" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white" style="backdrop-filter: blur(4px);">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                                    <div class="small fw-semibold">Composing Custom Poster...</div>
                                </div>
                            </div>

                            {{-- Actions Under Poster: Download & Share --}}
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-3 flex-wrap">
                                <a href="#" id="qs-modal-btn-download" target="_blank" download="festival-poster.jpg" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 disabled" title="Download High-Res 1080p Poster">
                                    <i class="ti ti-download me-1"></i>Download (1080p)
                                </a>
                                <button type="button" id="qs-modal-btn-share-wa" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5" title="Share on WhatsApp">
                                    <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                                </button>
                                <button type="button" id="qs-modal-btn-share-native" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5" title="Share Link or Image">
                                    <i class="ti ti-share me-1"></i>Share
                                </button>
                            </div>
                        </div>

                        {{-- Right Column: Style Controls --}}
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h4 class="fw-bold text-dark mb-3">Customize Poster Design</h4>

                                {{-- Branding Frame Layer (Footer Style) --}}
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fw-bold m-0">Branding Frame Layer (Footer Style)</label>
                                        <span id="qs-modal-footer-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            <i class="ti ti-lock me-0.5"></i> Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select" id="qs-modal-footer-style">
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
                                    <div id="qs-modal-footer-locked-msg" class="text-danger small mt-1 d-none">
                                        <i class="ti ti-lock me-1"></i> This strip is fixed for this template and cannot be changed.
                                    </div>
                                </div>

                                {{-- Top Leadership Header Style --}}
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fw-bold m-0">Top Leadership Header</label>
                                        <span id="qs-modal-header-locked-badge" class="badge bg-danger-lt text-danger d-none" style="font-size: 10px;">
                                            <i class="ti ti-lock me-0.5"></i> Fixed by Admin
                                        </span>
                                    </div>
                                    <select class="form-select" id="qs-modal-header-layout">
                                        <option value="full_width_strip">Header 1: Full-Width Strip (6-7 Leaders Across Top)</option>
                                        <option value="top_left_cluster">Header 2: Top-Left Cluster (3-4 Leaders + Logo)</option>
                                        <option value="top_right_cluster">Header 3: Top-Right Cluster (3-4 Leaders + Logo)</option>
                                        <option value="split_wings">Header 4: Split Wings (3 Left + 3 Right, Center Grand Void)</option>
                                        <option value="vertical_capsule_strip">Header 5: Vertical Capsule Strip (Tall Cards + Badge)</option>
                                        <option value="party_group_cutout">Header 6: Joint Group Cutout (Standing Cutout + Logo)</option>
                                        <option value="frosted_glass_dock">Header 7: Modern Frosted Glass Dock (Translucent Pill)</option>
                                        <option value="tricolor_ribbon_canopy">Header 8: Tricolor Ribbon Canopy (Tiranga Arch & Gold Rings)</option>
                                        <option value="none">None: Hide Leadership Strip (Artwork Only)</option>
                                    </select>
                                    <div id="qs-modal-header-locked-msg" class="text-danger small mt-1 d-none">
                                        <i class="ti ti-lock me-1"></i> This leadership header is fixed for this template and cannot be changed.
                                    </div>
                                </div>

                                {{-- Advanced Leader Badges (Shape & Ring Color) --}}
                                <div class="mb-3 border rounded p-2.5 bg-white">
                                    <a class="text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold" data-bs-toggle="collapse" href="#qs-modal-advanced-leader" role="button" aria-expanded="false">
                                        <span class="small d-flex align-items-center gap-1">
                                            <i class="ti ti-adjustments-alt text-primary"></i> Advanced: Leader Shape & Border Color
                                        </span>
                                        <span class="badge bg-secondary-lt" style="font-size: 10px;">Customize <i class="ti ti-chevron-down ms-0.5"></i></span>
                                    </a>
                                    <div class="collapse mt-2 pt-2 border-top" id="qs-modal-advanced-leader">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold mb-1">Avatar Shape</label>
                                                <select class="form-select form-select-sm" id="qs-modal-leader-shape">
                                                    <option value="circle">Circle (गोल)</option>
                                                    <option value="rounded_square">Rounded (घुमावदार)</option>
                                                    <option value="square">Square (चौकोर)</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold mb-1">Ring Color</label>
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <input type="color" id="qs-modal-leader-border" class="form-control form-control-color form-control-sm p-0" value="#FF9933" title="Ring Color">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#FF9933" style="font-size: 10px;">Saffron</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#FFFFFF" style="font-size: 10px;">White</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#138808" style="font-size: 10px;">Green</button>
                                                        <button type="button" class="btn btn-outline-secondary px-1.5 py-0.5 qs-color-preset" data-color="#D4AF37" style="font-size: 10px;">Gold</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2.5 bg-white rounded border small text-secondary">
                                    <i class="ti ti-check text-success me-1"></i>Select any footer or header style to preview your personalized poster with your photo, name, and party emblem.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- STEP 2: Caption, Fixed Date, Time & Social Accounts      --}}
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

                        {{-- Alert Box for Dynamic Errors --}}
                        <div id="qs-alert" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3" role="alert"></div>

                        <div class="row g-3">
                            {{-- Date (Fixed) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="ti ti-calendar me-1"></i> Festival Date (Fixed)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="ti ti-lock"></i></span>
                                    <input type="text" id="qs-display-date" class="form-control bg-light" readonly disabled>
                                </div>
                            </div>

                            {{-- Time (Editable) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold required">
                                    <i class="ti ti-clock me-1"></i> Schedule Time
                                </label>
                                <input type="time" name="schedule_time" id="qs-input-time" class="form-control" value="08:00" required>
                            </div>

                            {{-- Target Social Channels (Pre-checked by default!) --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-1.5">
                                    <i class="ti ti-share me-1"></i> Publish To Connected Accounts
                                </label>

                                @php
                                    $metaAccounts = isset($accounts) 
                                        ? $accounts->filter(fn($a) => in_array($a->account_type, [1, 2])) 
                                        : collect();
                                @endphp

                                @if($metaAccounts->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($metaAccounts as $acc)
                                            <label class="d-flex align-items-center gap-2 p-2 px-3 rounded-3 border bg-light-subtle cursor-pointer qs-account-row active">
                                                <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}" checked class="form-check-input m-0 qs-account-cb">
                                                <div class="avatar avatar-xs rounded-circle bg-white border">
                                                    @if($acc->profile_pic_url)
                                                        <img src="{{ $acc->profile_pic_url }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                                    @else
                                                        <i class="ti ti-user text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div class="small fw-semibold text-dark">{{ $acc->name }}</div>
                                                <span class="badge {{ $acc->account_type == 1 ? 'bg-blue-lt' : 'bg-pink-lt' }} small">
                                                    {{ $acc->account_type == 1 ? 'Facebook' : 'Instagram' }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-2.5 rounded-3 border border-dashed bg-light-subtle text-center">
                                        <div class="small text-secondary mb-1">No active Facebook or Instagram accounts connected.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">
                                            <i class="ti ti-link me-1"></i>Connect Accounts
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Caption / Greeting Message --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-1">Greeting Caption & Hashtags</label>
                                <textarea name="content" id="qs-input-caption" class="form-control" rows="5" placeholder="Enter celebration message..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-3 px-4 bg-light-subtle d-flex align-items-center justify-content-between">
                <div class="text-secondary small d-flex align-items-center gap-1.5">
                    <span id="qs-footer-info-text">Step 1 of 2: Choose poster styles</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Step 1 Buttons --}}
                    <div id="qs-actions-step-1" class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-semibold" id="qs-btn-next-step">
                            Next: Schedule Details <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>

                    {{-- Step 2 Buttons --}}
                    <div id="qs-actions-step-2" class="d-none d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" id="qs-btn-back-step">
                            <i class="ti ti-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-1.5 fw-semibold" id="btn-submit-quick-schedule">
                            <i class="ti ti-calendar-time me-1"></i> <span id="btn-submit-text">Schedule Post</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
