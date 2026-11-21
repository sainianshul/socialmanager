@extends('admin.layouts.app')

@section('title', 'Festival Studio')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => url('/admin'), 'label' => 'Dashboard'],
        ['label' => 'Festival Studio'],
    ]" />
@endsection

@push('styles')
<style>
/* Apple-Style Minimalist Festival Studio */
.fs-date-scroller {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 8px;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}
.fs-date-scroller::-webkit-scrollbar {
    height: 4px;
}
.fs-date-scroller::-webkit-scrollbar-track {
    background: transparent;
}
.fs-date-scroller::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.12);
    border-radius: 4px;
}
[data-bs-theme="dark"] .fs-date-scroller::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
}

.fs-date-pill {
    flex-shrink: 0;
    min-width: 175px;
    padding: 12px 18px;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: var(--tblr-card-bg, #ffffff);
    text-decoration: none !important;
    color: var(--tblr-body-color, #1e293b);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
[data-bs-theme="dark"] .fs-date-pill {
    border-color: rgba(255, 255, 255, 0.08);
}
.fs-date-pill:hover {
    border-color: #1db8ab;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(29, 184, 171, 0.15);
}
.fs-date-pill.active {
    background: linear-gradient(135deg, #1db8ab 0%, #0d9488 100%) !important;
    border-color: #0d9488 !important;
    color: #ffffff !important;
    box-shadow: 0 6px 20px rgba(29, 184, 171, 0.35);
}
.fs-date-pill.active .text-secondary {
    color: rgba(255, 255, 255, 0.85) !important;
}
.fs-date-pill.active .badge.bg-primary-lt {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}
.fs-date-pill.active .badge.bg-warning {
    background: #ffffff !important;
    color: #0d9488 !important;
}

/* Scheduled Date Pill Distinction (Warm Amber / Light Yellow) */
.fs-date-pill.is-scheduled {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
}
.fs-date-pill.is-scheduled:hover {
    border-color: #d97706 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
}
.fs-date-pill.is-scheduled .text-secondary {
    color: #b45309 !important;
}
.fs-date-pill.is-scheduled .badge.bg-primary-lt {
    background: rgba(245, 158, 11, 0.25) !important;
    color: #92400e !important;
}
.fs-date-pill.is-scheduled.active {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
    border-color: #f59e0b !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px #f59e0b, 0 6px 20px rgba(13, 148, 136, 0.35) !important;
}
.fs-date-pill.is-scheduled.active .text-secondary {
    color: rgba(255, 255, 255, 0.9) !important;
}
[data-bs-theme="dark"] .fs-date-pill.is-scheduled {
    background: rgba(245, 158, 11, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.45) !important;
    color: #fde68a !important;
}
[data-bs-theme="dark"] .fs-date-pill.is-scheduled .text-secondary {
    color: rgba(253, 230, 138, 0.8) !important;
}

/* Template Card */
.fs-template-card {
    border: 1px solid rgba(0, 0, 0, 0.07);
    border-radius: 16px;
    background: var(--tblr-card-bg, #ffffff);
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}
[data-bs-theme="dark"] .fs-template-card {
    border-color: rgba(255, 255, 255, 0.08);
}
.fs-template-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.09);
    border-color: rgba(0, 0, 0, 0.15);
}
.fs-template-preview-wrapper {
    position: relative;
    width: 100%;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #0f172a;
    overflow: hidden;
}
.fs-template-preview-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.fs-template-card:hover .fs-template-preview-img {
    transform: scale(1.03);
}

/* Hover Overlay */
.fs-template-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.68);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    padding: 18px;
    z-index: 4;
}
.fs-template-card:hover .fs-template-overlay {
    opacity: 1;
    pointer-events: auto;
}

/* Dynamic Content Area Transition */
#festival-content-area {
    min-height: 250px;
    transition: opacity 0.18s ease-in-out;
}
#festival-content-area.is-loading {
    opacity: 0.35;
    pointer-events: none;
    user-select: none;
}

.cursor-pointer {
    cursor: pointer;
}
.qs-account-row {
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
}
.qs-account-row:hover {
    border-color: #1db8ab !important;
    background: rgba(29, 184, 171, 0.04) !important;
}
.qs-account-row.active {
    border-color: #1db8ab !important;
    background: rgba(29, 184, 171, 0.08) !important;
}
.qs-account-row .qs-check-icon {
    opacity: 0;
    transition: opacity 0.15s ease;
}
.qs-account-row.active .qs-check-icon {
    opacity: 1;
}

/* Floating Clean Notification Toast */
.fs-floating-toast {
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(-20px);
    background: #0f172a;
    color: #ffffff;
    padding: 10px 22px;
    border-radius: 9999px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    font-size: 0.88rem;
    font-weight: 500;
    z-index: 1090;
    opacity: 0;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: none;
}
.fs-floating-toast.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
}

/* Live Poster Header Strip Overlay */
.fs-poster-header-strip {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 6px 8px;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0) 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 2;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.fs-strip-chip {
    display: inline-flex;
    align-items: center;
}
.fs-strip-chip img {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: 1.5px solid #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
    object-fit: cover;
    background: #ffffff;
}
.fs-strip-party-logo {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    object-fit: contain;
    background: #ffffff;
    padding: 1.5px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

/* Live Poster Bottom Branding Overlay */
.fs-poster-bottom-branding {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 6px 8px;
    background: linear-gradient(0deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    z-index: 2;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.fs-branding-badge {
    color: #ffffff;
    line-height: 1.2;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    max-width: 70%;
}
.fs-branding-name {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}
.fs-branding-desig {
    font-size: 0.65rem;
    opacity: 0.9;
}
.fs-branding-cutout {
    width: 44px;
    height: 52px;
    object-fit: contain;
    object-position: bottom;
    filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.6));
}

/* Modal Specific Larger Overlays */
.qs-modal-header-strip {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 10px 14px;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 3;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.qs-modal-header-strip .fs-strip-chip img {
    width: 32px;
    height: 32px;
    border-width: 2px;
}
.qs-modal-bottom-branding {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px 14px;
    background: linear-gradient(0deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0) 100%);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    z-index: 3;
    pointer-events: none;
}
.qs-modal-bottom-branding .fs-branding-name {
    font-size: 0.95rem;
}
.qs-modal-bottom-branding .fs-branding-desig {
    font-size: 0.75rem;
}
.qs-modal-bottom-branding .fs-branding-cutout {
    width: 60px;
    height: 72px;
}
</style>
@endpush

@section('content')

    {{-- Clean Minimal Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                    Festival Studio
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-pill">
                    <i class="ti ti-plus me-1"></i>New Post Composer
                </a>
            </div>
        </div>
    </div>

    {{-- Minimal Horizontal Date Scroller --}}
    @if($upcomingFestivals->isNotEmpty())
        <div class="mb-4">
            <div class="fs-date-scroller">
                @foreach($upcomingFestivals as $festival)
                    @php
                        $isSelected = $selectedFestival && $selectedFestival->id === $festival->id;
                        $isToday = $festival->festival_date->isToday();
                        $daysAway = now()->startOfDay()->diffInDays($festival->festival_date->startOfDay(), false);
                        $isScheduled = in_array($festival->id, $scheduledFestivalIds ?? []);
                    @endphp
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $festival->id]) }}" 
                       class="fs-date-pill {{ $isSelected ? 'active' : '' }} {{ $isScheduled ? 'is-scheduled' : '' }}"
                       id="date-pill-{{ $festival->id }}"
                       data-festival-id="{{ $festival->id }}">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.04em;">
                                {{ $festival->festival_date->format('D') }}
                            </span>
                            <div class="d-flex align-items-center gap-1" id="pill-badge-slot-{{ $festival->id }}">
                                @if($isScheduled)
                                    <span class="badge bg-warning text-dark rounded-pill fw-semibold" style="font-size: 9px; padding: 2px 7px;">
                                        <i class="ti ti-check me-0.5"></i>Set
                                    </span>
                                @endif
                                @if($isToday)
                                    <span class="badge bg-danger text-white rounded-pill" style="font-size: 9px; padding: 2px 7px;">Today</span>
                                @elseif($daysAway === 1)
                                    <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9px; padding: 2px 7px;">Tomorrow</span>
                                @elseif($daysAway > 1 && $daysAway <= 7 && !$isScheduled)
                                    <span class="badge bg-primary-lt rounded-pill" style="font-size: 9px; padding: 2px 7px;">in {{ $daysAway }}d</span>
                                @endif
                            </div>
                        </div>
                        <div class="fw-bold fs-3 mb-1 lh-1">
                            {{ $festival->festival_date->format('d M') }}
                        </div>
                        <div class="text-truncate small fw-medium" style="max-width: 145px;" title="{{ $festival->title }}">
                            {{ $festival->title }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Dynamic Festival Content Area (Smooth AJAX without page refresh) --}}
    <div id="festival-content-area" class="position-relative">
        @include('admin.festival_studio.partials._content')
    </div>

    {{-- Minimal Quick Schedule Modal --}}
    @include('admin.festival_studio.partials._quick_schedule_modal')

    {{-- Clean Floating Feedback Toast --}}
    <div id="fs-floating-toast" class="fs-floating-toast d-flex align-items-center gap-2">
        <i class="ti ti-check text-success fs-3"></i>
        <span id="fs-toast-message">Poster scheduled successfully.</span>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contentArea = document.getElementById('festival-content-area');
    var activeAbortController = null;

    // Toast Notification Helper
    var toastEl = document.getElementById('fs-floating-toast');
    var toastMsg = document.getElementById('fs-toast-message');
    var toastTimeout = null;

    function showStudioToast(message) {
        if (!toastEl) return;
        if (toastTimeout) clearTimeout(toastTimeout);
        toastMsg.innerText = message || 'Poster scheduled successfully.';
        toastEl.classList.add('show');
        toastTimeout = setTimeout(function () {
            toastEl.classList.remove('show');
        }, 3500);
    }

    // Delegate Date Pill Click to switch festival dynamically
    document.addEventListener('click', function (e) {
        var pill = e.target.closest('.fs-date-pill');
        if (!pill) return;

        e.preventDefault();

        // If already selected and not currently loading, skip
        if (pill.classList.contains('active') && !contentArea.classList.contains('is-loading')) {
            return;
        }

        var festivalId = pill.getAttribute('data-festival-id');
        var targetUrl = pill.getAttribute('href');

        switchFestival(targetUrl, festivalId, pill, true);
    });

    function switchFestival(url, festivalId, activePill, pushState) {
        // 1. Immediately update UI active pill state
        document.querySelectorAll('.fs-date-pill').forEach(function (el) {
            el.classList.remove('active');
        });
        if (activePill) {
            activePill.classList.add('active');
            activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        // 2. Abort previous in-flight AJAX request to prevent race conditions
        if (activeAbortController) {
            activeAbortController.abort();
        }
        activeAbortController = new AbortController();

        // 3. Mark content area as loading
        if (contentArea) {
            contentArea.classList.add('is-loading');
        }

        // 4. Fetch festival content via AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            signal: activeAbortController.signal
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            if (data && data.html) {
                contentArea.innerHTML = data.html;

                // Sync URL in browser address bar without page reload
                if (pushState && window.history && window.history.pushState) {
                    window.history.pushState({ festival_id: festivalId, url: url }, '', url);
                }
            }
        })
        .catch(function (err) {
            if (err.name === 'AbortError') {
                return;
            }
            console.error('Festival Studio AJAX error:', err);
            // Fallback graceful degradation
            window.location.href = url;
        })
        .finally(function () {
            if (contentArea) {
                contentArea.classList.remove('is-loading');
            }
        });
    }

    // Handle Browser History Back / Forward navigation (popstate)
    window.addEventListener('popstate', function (event) {
        var currentUrl = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var festivalId = urlParams.get('festival_id');

        var targetPill = festivalId 
            ? document.querySelector('.fs-date-pill[data-festival-id="' + festivalId + '"]')
            : document.querySelector('.fs-date-pill');

        if (targetPill) {
            switchFestival(currentUrl, festivalId, targetPill, false);
        } else {
            window.location.reload();
        }
    });

    // Delegated Quick Schedule Modal Opener
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.btn-trigger-quick-schedule');
        if (!trigger) return;

        var templateId = trigger.getAttribute('data-template-id');
        var templateTitle = trigger.getAttribute('data-template-title') || '';
        var templateImage = trigger.getAttribute('data-template-image') || '';
        var festivalId = trigger.getAttribute('data-festival-id') || '';
        var festivalTitle = trigger.getAttribute('data-festival-title') || '';
        var festivalDate = trigger.getAttribute('data-festival-date') || '';
        var festivalDateFormatted = trigger.getAttribute('data-festival-date-formatted') || '';
        var caption = trigger.getAttribute('data-caption') || '';
        var composerUrl = trigger.getAttribute('data-composer-url') || '#';

        var imgEl = document.getElementById('qs-modal-img');
        if (imgEl) imgEl.src = templateImage;

        var titleEl = document.getElementById('qs-modal-poster-title');
        if (titleEl) titleEl.innerText = templateTitle;

        var badgeEl = document.getElementById('qs-modal-festival-badge');
        if (badgeEl) badgeEl.innerText = festivalDateFormatted;

        var subEl = document.getElementById('qs-modal-subheading');
        if (subEl) subEl.innerText = festivalTitle;

        var inputTpl = document.getElementById('qs-input-template-id');
        if (inputTpl) inputTpl.value = templateId;

        var inputFest = document.getElementById('qs-input-festival-id');
        if (inputFest) inputFest.value = festivalId;

        var inputDate = document.getElementById('qs-input-date');
        if (inputDate && festivalDate) inputDate.value = festivalDate;

        // Dynamic Today vs Future time calculation
        var isToday = trigger.getAttribute('data-is-today') === '1';
        var targetTime = trigger.getAttribute('data-target-time') || (isToday ? '{{ now()->addMinutes(10)->format('H:i') }}' : '08:00');
        var targetTimeFormatted = trigger.getAttribute('data-target-time-formatted') || (isToday ? '{{ now()->addMinutes(10)->format('h:i A') }}' : '08:00 AM');

        var inputTime = document.getElementById('qs-input-time');
        if (inputTime) inputTime.value = targetTime;

        var timeBadge = document.getElementById('qs-modal-time-badge');
        if (timeBadge) {
            timeBadge.innerHTML = '<i class="ti ti-clock me-1 text-primary"></i>' + targetTimeFormatted;
        }

        var footerNote = document.getElementById('qs-modal-footer-note');
        if (footerNote) {
            footerNote.innerText = isToday 
                ? 'Will publish today in 10 minutes (' + targetTimeFormatted + ')' 
                : 'Will publish on festival day at 08:00 AM';
        }

        var inputCaption = document.getElementById('qs-input-caption');
        if (inputCaption) inputCaption.value = caption;

        var composerLink = document.getElementById('qs-link-full-composer');
        if (composerLink) composerLink.href = composerUrl;

        var alertBox = document.getElementById('qs-alert');
        if (alertBox) {
            alertBox.classList.add('d-none');
            alertBox.innerText = '';
        }

        // Reset account checkboxes to checked & active
        document.querySelectorAll('.qs-account-cb').forEach(function (cb) {
            cb.checked = true;
            var row = cb.closest('.qs-account-row');
            if (row) row.classList.add('active');
        });

        var modalEl = document.getElementById('quickScheduleModal');
        if (modalEl) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });

    // Account row toggle on checkbox change or row click
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('qs-account-cb')) {
            var row = e.target.closest('.qs-account-row');
            if (row) {
                if (e.target.checked) {
                    row.classList.add('active');
                } else {
                    row.classList.remove('active');
                }
            }
        }
    });

    // Quick Schedule AJAX Submit
    var submitBtn = document.getElementById('btn-submit-quick-schedule');
    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var btnText = document.getElementById('btn-submit-text');
            var alertBox = document.getElementById('qs-alert');

            // Gather selected account IDs
            var selectedAccountIds = [];
            document.querySelectorAll('.qs-account-cb:checked').forEach(function (cb) {
                selectedAccountIds.push(parseInt(cb.value));
            });

            if (selectedAccountIds.length === 0) {
                if (alertBox) {
                    alertBox.classList.remove('d-none');
                    alertBox.innerText = 'Please select at least one social media channel to schedule.';
                }
                return;
            }

            var templateId = document.getElementById('qs-input-template-id').value;
            var festivalId = document.getElementById('qs-input-festival-id').value;
            var scheduleDate = document.getElementById('qs-input-date').value;
            var scheduleTime = document.getElementById('qs-input-time').value;
            var content = document.getElementById('qs-input-caption').value;

            if (!scheduleDate || !scheduleTime) {
                if (alertBox) {
                    alertBox.classList.remove('d-none');
                    alertBox.innerText = 'Please provide both a schedule date and time.';
                }
                return;
            }

            // Button loading state
            submitBtn.disabled = true;
            if (btnText) btnText.innerText = 'Scheduling...';
            if (alertBox) alertBox.classList.add('d-none');

            fetch('{{ route('admin.festival-studio.quick-schedule') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    template_id: templateId,
                    festival_id: festivalId,
                    account_ids: selectedAccountIds,
                    schedule_date: scheduleDate,
                    schedule_time: scheduleTime,
                    content: content
                })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { status: res.status, ok: res.ok, data: data };
                });
            })
            .then(function (result) {
                submitBtn.disabled = false;
                if (btnText) btnText.innerText = 'Schedule Post';

                if (!result.ok) {
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = result.data.message || 'Scheduling failed. Please try again.';
                    }
                    return;
                }

                // Close modal
                var modalEl = document.getElementById('quickScheduleModal');
                if (modalEl) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                // Update card action area on page to show Scheduled badge
                var actionEl = document.getElementById('card-action-' + templateId);
                if (actionEl) {
                    actionEl.innerHTML = '<span class="badge bg-success text-white rounded-pill px-2.5 py-1 small fw-semibold"><i class="ti ti-check me-1"></i>Scheduled</span>';
                }

                // Immediately update the festival date pill in the top horizontal bar
                var targetFestId = result.data.festival_id || festivalId;
                if (targetFestId) {
                    var pillEl = document.getElementById('date-pill-' + targetFestId);
                    if (pillEl) {
                        pillEl.classList.add('is-scheduled');
                        var badgeSlot = document.getElementById('pill-badge-slot-' + targetFestId);
                        if (badgeSlot && !badgeSlot.querySelector('.badge.bg-warning')) {
                            var setBadge = document.createElement('span');
                            setBadge.className = 'badge bg-warning text-dark rounded-pill fw-semibold';
                            setBadge.style.fontSize = '9px';
                            setBadge.style.padding = '2px 7px';
                            setBadge.innerHTML = '<i class="ti ti-check me-0.5"></i>Set';
                            badgeSlot.prepend(setBadge);
                        }
                    }
                }

                // Show floating notification toast
                showStudioToast(result.data.message || 'Poster scheduled successfully.');
            })
            .catch(function (err) {
                submitBtn.disabled = false;
                if (btnText) btnText.innerText = 'Schedule Post';
                if (alertBox) {
                    alertBox.classList.remove('d-none');
                    alertBox.innerText = 'Network error. Please try again.';
                }
                console.error('Quick schedule error:', err);
            });
        });
    }

    // Delegated Copy Caption Button Handler
    document.addEventListener('click', function (e) {
        var copyBtn = e.target.closest('#btn-copy-caption');
        if (!copyBtn) return;

        var captionEl = document.getElementById('festival-caption-text');
        var copyText = document.getElementById('copy-btn-text') || copyBtn;
        if (!captionEl) return;

        var fullText = captionEl.innerText;

        function showSuccess() {
            var origText = copyText.innerText;
            copyText.innerText = 'Copied!';
            copyBtn.classList.replace('btn-outline-secondary', 'btn-success');
            setTimeout(function () {
                copyText.innerText = origText || 'Copy Caption';
                copyBtn.classList.replace('btn-success', 'btn-outline-secondary');
            }, 2000);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(fullText).then(showSuccess).catch(function () {
                fallbackCopy(fullText, showSuccess);
            });
        } else {
            fallbackCopy(fullText, showSuccess);
        }
    });

    function fallbackCopy(text, callback) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Fallback copy failed', e);
        }
        document.body.removeChild(ta);
    }

    // ======================================================================
    // STEP 4: HEADER STRIP POSTER CONTROLS & DYNAMIC SWITCHING
    // ======================================================================
    // 1. Header Strip Visibility Toggle (Main toolbar)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'fs-toggle-header-strip') {
            var isChecked = e.target.checked;
            document.querySelectorAll('.fs-strip-overlay').forEach(function (el) {
                if (isChecked) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });

            var modalToggle = document.getElementById('qs-modal-toggle-strip');
            if (modalToggle) {
                modalToggle.checked = isChecked;
            }

            try {
                localStorage.setItem('socialmanager_header_strip_enabled', isChecked ? '1' : '0');
            } catch (err) {}
        }

        // 2. Header Strip Toggle inside Quick Schedule Modal
        if (e.target && e.target.id === 'qs-modal-toggle-strip') {
            var isModalChecked = e.target.checked;
            var modalStrip = document.getElementById('qs-modal-header-strip');
            if (modalStrip) {
                if (isModalChecked) {
                    modalStrip.classList.remove('d-none');
                } else {
                    modalStrip.classList.add('d-none');
                }
            }
        }
    });

    // 3. Quick Strip Switcher in Festival Studio
    document.addEventListener('click', async function (e) {
        var switchItem = e.target.closest('.fs-strip-switch-item');
        if (!switchItem) return;

        e.preventDefault();
        var stripId = switchItem.getAttribute('data-id');
        var stripName = switchItem.getAttribute('data-name');
        if (!stripId) return;

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            var response = await fetch('/admin/leader-strips/' + stripId + '/default', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var result = await response.json();
            if (result.success) {
                showStudioToast('Leadership strip changed to: ' + stripName);
                setTimeout(function () {
                    window.location.reload();
                }, 400);
            }
        } catch (err) {
            console.error('Error switching strip in Festival Studio:', err);
        }
    });
});
</script>
@endpush
