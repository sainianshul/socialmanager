{{-- Minimalist Apple-Style Quick Schedule Modal --}}
<div class="modal fade" id="quickScheduleModal" tabindex="-1" aria-labelledby="quickScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="modal-title fw-bold text-dark mb-0" id="quickScheduleModalLabel" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                        Schedule Festival Greeting
                    </h4>
                    <div class="text-secondary small" id="qs-modal-subheading" style="font-size: 12px;">
                        Ready-to-publish branded festival poster
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                <div class="row g-4">
                    
                    {{-- Left Column: Poster Preview --}}
                    <div class="col-md-5">
                        <div class="position-relative rounded-3 overflow-hidden border shadow-xs bg-dark">
                            <div style="padding-top: 100%; position: relative;">
                                <img id="qs-modal-img" src="" alt="Poster Preview" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <div class="fw-semibold text-dark small text-truncate" id="qs-modal-poster-title"></div>
                            <span class="badge bg-primary-lt rounded-pill mt-1" id="qs-modal-festival-badge"></span>
                        </div>
                    </div>

                    {{-- Right Column: Controls & Inputs --}}
                    <div class="col-md-7">
                        <form id="quick-schedule-form">
                            @csrf
                            <input type="hidden" name="template_id" id="qs-input-template-id" value="">
                            <input type="hidden" name="festival_id" id="qs-input-festival-id" value="">

                            {{-- Alert Box for Dynamic Errors --}}
                            <div id="qs-alert" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3" role="alert"></div>

                            {{-- Schedule Date & Time Row --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-calendar me-1 text-primary"></i>Schedule Date & Time
                                </label>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <input type="date" class="form-control rounded-3" name="schedule_date" id="qs-input-date" required>
                                    </div>
                                    <div class="col-5">
                                        <input type="time" class="form-control rounded-3" name="schedule_time" id="qs-input-time" value="08:00" required>
                                    </div>
                                </div>
                                <div class="text-secondary small mt-1" style="font-size: 11px;">
                                    Morning 07:00 AM - 08:30 AM has highest festival reach.
                                </div>
                            </div>

                            {{-- Target Social Channels --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-share me-1 text-primary"></i>Publish To
                                </label>

                                @if(isset($accounts) && $accounts->isNotEmpty())
                                    <div class="d-flex flex-column gap-2" style="max-height: 140px; overflow-y: auto;">
                                        @foreach($accounts as $acc)
                                            <label class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light-subtle cursor-pointer qs-account-row" style="transition: all 0.15s ease;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}" checked class="form-check-input m-0 qs-account-cb">
                                                    <div class="avatar avatar-xs rounded-circle bg-white border position-relative">
                                                        @if($acc->profile_pic_url)
                                                            <img src="{{ $acc->profile_pic_url }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                                        @else
                                                            <i class="ti ti-user text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="lh-sm text-truncate" style="max-width: 220px;">
                                                        <div class="small fw-semibold text-dark text-truncate">{{ $acc->name }}</div>
                                                        <div class="text-secondary" style="font-size: 10px;">
                                                            @if($acc->account_type == 1)
                                                                <span class="text-primary"><i class="ti ti-brand-facebook me-0.5"></i>Facebook Page</span>
                                                            @elseif($acc->account_type == 2)
                                                                <span class="text-danger"><i class="ti ti-brand-instagram me-0.5"></i>Instagram Profile</span>
                                                            @elseif($acc->account_type == 3)
                                                                <span class="text-danger"><i class="ti ti-brand-youtube me-0.5"></i>YouTube</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="ti ti-check text-primary qs-check-icon fs-4"></i>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-2.5 rounded-3 border border-dashed bg-light-subtle text-center">
                                        <div class="small text-secondary mb-1">No active social channels connected.</div>
                                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-xs btn-outline-primary rounded-pill">
                                            <i class="ti ti-link me-1"></i>Connect Facebook / Instagram
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Caption / Shubhkaamna Message --}}
                            <div class="mb-0">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label small fw-semibold text-dark mb-0">
                                        <i class="ti ti-message-2 me-1 text-primary"></i>Greeting Caption & Hashtags
                                    </label>
                                    <span class="text-secondary small" style="font-size: 11px;">Editable</span>
                                </div>
                                <textarea name="content" id="qs-input-caption" class="form-control rounded-3" rows="4" style="resize: vertical; font-size: 0.88rem; line-height: 1.5;" placeholder="Enter celebration message..."></textarea>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-2.5 px-4 bg-light-subtle d-flex align-items-center justify-content-between">
                <div>
                    <a href="#" id="qs-link-full-composer" class="text-secondary small text-decoration-none">
                        <i class="ti ti-external-link me-1"></i>Advanced Post Composer
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-1.5" id="btn-submit-quick-schedule">
                        <i class="ti ti-calendar-time me-1"></i><span id="btn-submit-text">Schedule Post</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
