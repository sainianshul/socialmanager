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
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
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

        /* Minimalist Modal UI Enhancements */
        #quickScheduleModal .modal-content {
            border-radius: 24px !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            box-shadow: 0 30px 80px -20px rgba(15, 23, 42, 0.2) !important;
        }
        .qs-premium-select {
            height: 48px !important;
            border-radius: 12px !important;
            border: 1.5px solid #E2E8F0 !important;
            background-color: #FAFAFA !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #1E293B !important;
            padding: 0 16px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748B' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 14px 10px !important;
        }
        .qs-premium-select:hover {
            border-color: #CBD5E1 !important;
            background-color: #FFFFFF !important;
        }
        .qs-premium-select:focus {
            border-color: #0D9488 !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1) !important;
            outline: none !important;
        }
        #quickScheduleModal .form-control {
            border-color: #e2e8f0;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        #quickScheduleModal .form-control:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }
        .qs-account-row {
            transition: all 0.18s ease;
            border: 1.5px solid #e2e8f0 !important;
            background: #ffffff !important;
        }
        .qs-account-row:hover {
            border-color: #0d9488 !important;
            transform: translateY(-1px);
        }
        .qs-account-row.active {
            border-color: #0d9488 !important;
            background: #f0fdfa !important;
        }

        /* Studio Editor Minimalist Apple Styling */
        .studio-filmstrip-card {
            cursor: pointer;
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .studio-filmstrip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        .studio-filmstrip-card.active {
            border-color: #0d9488 !important;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.25) !important;
        }
        .studio-photo-chip {
            transition: all 0.15s ease;
        }
        .studio-photo-chip:hover {
            transform: scale(1.08);
        }
        .studio-account-item {
            transition: all 0.15s ease;
        }
        .studio-account-item:hover {
            border-color: #0d9488 !important;
            background-color: #f0fdfa !important;
        }
        .studio-account-item.active {
            border-color: #0d9488 !important;
            background-color: #f0fdfa !important;
        }
        .inspector-tab-btn.active {
            background-color: #0d9488 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(13, 148, 136, 0.25);
        }
        .fs-view-btn.active {
            background-color: #ffffff !important;
            color: #0d9488 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
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
                <a href="{{ route('admin.posts.create') }}"
                    class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-pill">
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
                        id="date-pill-{{ $festival->id }}" data-festival-id="{{ $festival->id }}">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-secondary small fw-bold text-uppercase"
                                style="font-size: 11px; letter-spacing: 0.04em;">
                                {{ $festival->festival_date->format('D') }}
                            </span>
                            <div class="d-flex align-items-center gap-1" id="pill-badge-slot-{{ $festival->id }}">
                                @if($isScheduled)
                                    <span class="badge bg-warning text-dark rounded-pill fw-semibold"
                                        style="font-size: 9px; padding: 2px 7px;">
                                        <i class="ti ti-check me-0.5"></i>Set
                                    </span>
                                @endif
                                @if($isToday)
                                    <span class="badge bg-danger text-white rounded-pill"
                                        style="font-size: 9px; padding: 2px 7px;">Today</span>
                                @elseif($daysAway === 1)
                                    <span class="badge bg-warning text-dark rounded-pill"
                                        style="font-size: 9px; padding: 2px 7px;">Tomorrow</span>
                                @elseif($daysAway > 1 && $daysAway <= 7 && !$isScheduled)
                                    <span class="badge bg-primary-lt rounded-pill" style="font-size: 9px; padding: 2px 7px;">in
                                        {{ $daysAway }}d</span>
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

                            // Re-initialize Studio Editor with newly loaded festival templates
                            if (typeof initStudioEditor === 'function') {
                                initStudioEditor();
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

            // Delegated Quick Schedule Modal Opener (Step 1 -> Step 2 Multi-Step Wizard)
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
                var footerStyle = trigger.getAttribute('data-footer-style') || 'dual_ribbon_badge';
                var footerFixed = trigger.getAttribute('data-footer-fixed') === '1';
                var headerLayout = trigger.getAttribute('data-header-layout') || 'full_width_strip';
                var headerFixed = trigger.getAttribute('data-header-fixed') === '1';
                var leaderShape = trigger.getAttribute('data-leader-shape') || 'circle';
                var leaderBorder = trigger.getAttribute('data-leader-border') || '#FF9933';

                // 1. Reset Wizard to Step 1
                showWizardStep(1);

                var imgEl = document.getElementById('qs-modal-img');
                if (imgEl) imgEl.src = templateImage;

                var previewLink = document.getElementById('qs-modal-preview-link');
                var btnPreview = document.getElementById('qs-modal-btn-preview');
                if (previewLink) {
                    previewLink.href = templateImage;
                    previewLink.classList.remove('disabled');
                }
                if (btnPreview) {
                    btnPreview.href = templateImage;
                    btnPreview.classList.remove('disabled');
                }

                var subEl = document.getElementById('qs-modal-subheading');
                if (subEl) subEl.innerText = festivalTitle + (festivalDateFormatted ? ' (' + festivalDateFormatted + ')' : '');

                var inputTpl = document.getElementById('qs-input-template-id');
                if (inputTpl) inputTpl.value = templateId;

                var inputFest = document.getElementById('qs-input-festival-id');
                if (inputFest) inputFest.value = festivalId;

                var inputDate = document.getElementById('qs-input-date');
                if (inputDate && festivalDate) inputDate.value = festivalDate;

                var displayDate = document.getElementById('qs-display-date');
                if (displayDate) displayDate.value = (festivalDateFormatted || festivalDate) + ' - ' + festivalTitle;

                // Dynamic target time
                var isToday = trigger.getAttribute('data-is-today') === '1';
                var targetTime = trigger.getAttribute('data-target-time') || (isToday ? '{{ now()->addMinutes(10)->format('H:i') }}' : '08:00');

                var inputTime = document.getElementById('qs-input-time');
                if (inputTime) inputTime.value = targetTime;

                var inputCaption = document.getElementById('qs-input-caption');
                if (inputCaption) inputCaption.value = caption;

                // Set style dropdowns to template defaults and apply Lock/Fixed state
                var selectFooter = document.getElementById('qs-modal-footer-style');
                var footerLockedBadge = document.getElementById('qs-modal-footer-locked-badge');
                var footerLockedMsg = document.getElementById('qs-modal-footer-locked-msg');
                if (selectFooter) {
                    selectFooter.value = footerStyle;
                    selectFooter.disabled = footerFixed;
                    if (footerLockedBadge) footerLockedBadge.classList.toggle('d-none', !footerFixed);
                    if (footerLockedMsg) footerLockedMsg.classList.toggle('d-none', !footerFixed);
                }

                var selectHeader = document.getElementById('qs-modal-header-layout');
                var headerLockedBadge = document.getElementById('qs-modal-header-locked-badge');
                var headerLockedMsg = document.getElementById('qs-modal-header-locked-msg');
                if (selectHeader) {
                    selectHeader.value = headerLayout;
                    selectHeader.disabled = headerFixed;
                    if (headerLockedBadge) headerLockedBadge.classList.toggle('d-none', !headerFixed);
                    if (headerLockedMsg) headerLockedMsg.classList.toggle('d-none', !headerFixed);
                }

                // Set Advanced Leader Customization inputs
                var selectLeaderShape = document.getElementById('qs-modal-leader-shape');
                if (selectLeaderShape) selectLeaderShape.value = leaderShape;

                var inputLeaderBorder = document.getElementById('qs-modal-leader-border');
                if (inputLeaderBorder) inputLeaderBorder.value = leaderBorder;

                var inputHiddenShape = document.getElementById('qs-input-leader-shape');
                if (inputHiddenShape) inputHiddenShape.value = leaderShape;

                var inputHiddenBorder = document.getElementById('qs-input-leader-border');
                if (inputHiddenBorder) inputHiddenBorder.value = leaderBorder;

                var leaderSize = trigger.getAttribute('data-leader-size') || '106';
                var inputModalSize = document.getElementById('qs-modal-leader-size');
                if (inputModalSize) inputModalSize.value = leaderSize;
                var inputHiddenSize = document.getElementById('qs-input-leader-size');
                if (inputHiddenSize) inputHiddenSize.value = leaderSize;

                // Sync UI Shape Tiles, Color Swatches & Size Buttons
                if (typeof updateShapeTilesUI === 'function') updateShapeTilesUI(leaderShape);
                if (typeof updateColorSwatchesUI === 'function') updateColorSwatchesUI(leaderBorder);
                if (typeof updateSizeButtonsUI === 'function') updateSizeButtonsUI(leaderSize);

                // Auto-select Leadership Strip (Template Assigned, User Custom First, or Party Official Default)
                var tplPartyId = trigger.getAttribute('data-party-id') || '';
                var tplStripId = trigger.getAttribute('data-strip-id') || '';
                var selectStrip = document.getElementById('qs-modal-strip-id');
                var inputStrip = document.getElementById('qs-input-strip-id');
                if (selectStrip) {
                    if (tplStripId) {
                        selectStrip.value = tplStripId;
                    } else {
                        var currentOption = selectStrip.options[selectStrip.selectedIndex];
                        var isCustom = currentOption && currentOption.text.indexOf('⭐') !== -1;
                        // If user has not selected their own custom strip, align with template party if available
                        if (!isCustom && tplPartyId) {
                            for (var i = 0; i < selectStrip.options.length; i++) {
                                if (selectStrip.options[i].getAttribute('data-party-id') === tplPartyId) {
                                    selectStrip.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    }
                    if (inputStrip) inputStrip.value = selectStrip.value;
                }

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

                // Reset Social Media Handles Toggle to Checked by Default
                var toggleSocial = document.getElementById('qs-modal-show-social');
                if (toggleSocial) toggleSocial.checked = true;

                // Trigger Live Server-Side Poster Composition for Step 1
                composeModalPoster(templateId, footerStyle, headerLayout, leaderShape, leaderBorder, selectStrip ? selectStrip.value : null, leaderSize, true);
            });

            // Wizard Step Navigation
            function showWizardStep(step) {
                var pane1 = document.getElementById('qs-step-pane-1');
                var pane2 = document.getElementById('qs-step-pane-2');
                var actions1 = document.getElementById('qs-actions-step-1');
                var actions2 = document.getElementById('qs-actions-step-2');
                var ind1 = document.getElementById('qs-step-indicator-1');
                var ind2 = document.getElementById('qs-step-indicator-2');
                var footerText = document.getElementById('qs-footer-info-text');

                if (step === 1) {
                    if (pane1) pane1.classList.remove('d-none');
                    if (pane2) pane2.classList.add('d-none');
                    if (actions1) actions1.classList.remove('d-none');
                    if (actions2) actions2.classList.add('d-none');
                    if (ind1) {
                        ind1.className = 'badge bg-primary text-white px-3 py-1.5 rounded-pill fw-semibold';
                        ind1.innerText = '1. Poster Style';
                    }
                    if (ind2) {
                        ind2.className = 'badge bg-transparent text-secondary px-3 py-1.5 rounded-pill fw-medium';
                        ind2.innerText = '2. Schedule Details';
                    }
                    if (footerText) footerText.innerText = 'Step 1 of 2: Choose poster design styles';
                } else if (step === 2) {
                    if (pane1) pane1.classList.add('d-none');
                    if (pane2) pane2.classList.remove('d-none');
                    if (actions1) actions1.classList.add('d-none');
                    if (actions2) actions2.classList.remove('d-none');
                    if (ind1) {
                        ind1.className = 'badge bg-success text-white px-3 py-1.5 rounded-pill fw-semibold';
                        ind1.innerText = '1. Poster Ready';
                    }
                    if (ind2) {
                        ind2.className = 'badge bg-primary text-white px-3 py-1.5 rounded-pill fw-semibold';
                        ind2.innerText = '2. Schedule Details';
                    }
                    if (footerText) footerText.innerText = 'Step 2 of 2: Review schedule time, accounts & caption';

                    // Sync hidden inputs for final submission
                    var selectFooter = document.getElementById('qs-modal-footer-style');
                    var selectHeader = document.getElementById('qs-modal-header-layout');
                    var inputFooter = document.getElementById('qs-input-footer-style');
                    var inputHeader = document.getElementById('qs-input-header-layout');
                    if (inputFooter && selectFooter) inputFooter.value = selectFooter.value;
                    if (inputHeader && selectHeader) inputHeader.value = selectHeader.value;

                    var selectLeaderShape = document.getElementById('qs-modal-leader-shape');
                    var inputLeaderBorder = document.getElementById('qs-modal-leader-border');
                    var inputModalSize = document.getElementById('qs-modal-leader-size');
                    var inputHiddenShape = document.getElementById('qs-input-leader-shape');
                    var inputHiddenBorder = document.getElementById('qs-input-leader-border');
                    var inputHiddenSize = document.getElementById('qs-input-leader-size');
                    if (inputHiddenShape && selectLeaderShape) inputHiddenShape.value = selectLeaderShape.value;
                    if (inputHiddenBorder && inputLeaderBorder) inputHiddenBorder.value = inputLeaderBorder.value;
                    if (inputHiddenSize && inputModalSize) inputHiddenSize.value = inputModalSize.value;

                    var selectStrip = document.getElementById('qs-modal-strip-id');
                    var inputStrip = document.getElementById('qs-input-strip-id');
                    if (inputStrip && selectStrip) inputStrip.value = selectStrip.value;

                    var toggleSocial = document.getElementById('qs-modal-show-social');
                    var inputSocial = document.getElementById('qs-input-show-social');
                    if (inputSocial && toggleSocial) inputSocial.value = toggleSocial.checked ? '1' : '0';

                    var inputCandName = document.getElementById('qs-modal-candidate-name');
                    var inputCandDesig = document.getElementById('qs-modal-candidate-designation');
                    var inputModalCutout = document.getElementById('qs-modal-cutout-url');
                    var inputHiddenName = document.getElementById('qs-input-candidate-name');
                    var inputHiddenDesig = document.getElementById('qs-input-candidate-designation');
                    var inputHiddenCutout = document.getElementById('qs-input-cutout-url');

                    if (inputHiddenName && inputCandName) inputHiddenName.value = inputCandName.value;
                    if (inputHiddenDesig && inputCandDesig) inputHiddenDesig.value = inputCandDesig.value;
                    if (inputHiddenCutout && inputModalCutout) inputHiddenCutout.value = inputModalCutout.value;
                }
            }

            var btnNextStep = document.getElementById('qs-btn-next-step');
            if (btnNextStep) {
                btnNextStep.addEventListener('click', function () {
                    showWizardStep(2);
                });
            }

            var btnBackStep = document.getElementById('qs-btn-back-step');
            if (btnBackStep) {
                btnBackStep.addEventListener('click', function () {
                    showWizardStep(1);
                });
            }

            // Live Composition on Style Dropdown & Advanced Leader Customization Change (Step 1)
            var selectFooterStyle = document.getElementById('qs-modal-footer-style');
            var selectHeaderLayout = document.getElementById('qs-modal-header-layout');
            var selectLeaderShape = document.getElementById('qs-modal-leader-shape');
            var inputLeaderBorder = document.getElementById('qs-modal-leader-border');
            var selectStrip = document.getElementById('qs-modal-strip-id');

            function updateShapeTilesUI(selectedShape) {
                var targetShape = selectedShape || 'circle';
                document.querySelectorAll('.qs-shape-btn').forEach(function (btn) {
                    var shape = btn.getAttribute('data-shape');
                    var icon = btn.querySelector('.qs-shape-icon');
                    var text = btn.querySelector('.qs-shape-text');
                    if (shape === targetShape) {
                        btn.style.borderColor = '#2563EB';
                        btn.style.backgroundColor = '#EFF6FF';
                        if (icon) icon.style.borderColor = '#2563EB';
                        if (text) {
                            text.style.color = '#1E40AF';
                            text.style.fontWeight = '600';
                        }
                    } else {
                        btn.style.borderColor = '#E2E8F0';
                        btn.style.backgroundColor = '#FFFFFF';
                        if (icon) icon.style.borderColor = '#64748B';
                        if (text) {
                            text.style.color = '#64748B';
                            text.style.fontWeight = '500';
                        }
                    }
                });
            }

            function updateColorSwatchesUI(selectedColor) {
                var normColor = (selectedColor || '#FF9933').toLowerCase().trim();
                var matched = false;
                document.querySelectorAll('.qs-color-swatch').forEach(function (btn) {
                    var c = (btn.getAttribute('data-color') || '').toLowerCase().trim();
                    var check = btn.querySelector('.qs-color-check');
                    if (c === normColor) {
                        matched = true;
                        btn.style.outline = '2px solid #2563EB';
                        btn.style.outlineOffset = '2px';
                        if (check) check.classList.remove('d-none');
                    } else {
                        btn.style.outline = 'none';
                        btn.style.outlineOffset = '0px';
                        if (check) check.classList.add('d-none');
                    }
                });
                var customBox = document.getElementById('qs-custom-color-box');
                if (customBox) {
                    if (!matched) {
                        customBox.style.outline = '2px solid #2563EB';
                        customBox.style.outlineOffset = '2px';
                    } else {
                        customBox.style.outline = 'none';
                        customBox.style.outlineOffset = '0px';
                    }
                }
            }

            function setLeaderSize(size) {
                var s = Math.max(70, Math.min(145, parseInt(size) || 106));
                var inputModalSize = document.getElementById('qs-modal-leader-size');
                var inputHiddenSize = document.getElementById('qs-input-leader-size');
                if (inputModalSize) inputModalSize.value = s;
                if (inputHiddenSize) inputHiddenSize.value = s;
                updateSizeButtonsUI(s);
                onStyleChange();
            }

            function updateSizeButtonsUI(size) {
                var s = parseInt(size) || 106;
                document.querySelectorAll('.qs-size-btn').forEach(function (btn) {
                    var bSize = parseInt(btn.getAttribute('data-size'));
                    if (Math.abs(bSize - s) <= 6) {
                        btn.className = 'btn btn-primary px-2.5 py-1 qs-size-btn active';
                    } else {
                        btn.className = 'btn btn-outline-secondary px-2.5 py-1 qs-size-btn';
                    }
                });
            }

            function onStyleChange() {
                var tplId = document.getElementById('qs-input-template-id')?.value;
                if (!tplId) return;
                var fStyle = selectFooterStyle ? selectFooterStyle.value : 'dual_ribbon_badge';
                var hLayout = selectHeaderLayout ? selectHeaderLayout.value : 'full_width_strip';
                var lShape = selectLeaderShape ? selectLeaderShape.value : 'circle';
                var lBorder = inputLeaderBorder ? inputLeaderBorder.value : '#FF9933';
                var lSize = document.getElementById('qs-modal-leader-size')?.value || '106';
                var stripId = selectStrip ? selectStrip.value : document.getElementById('qs-input-strip-id')?.value;
                var toggleSocial = document.getElementById('qs-modal-show-social');
                var isSocial = toggleSocial ? toggleSocial.checked : true;
                composeModalPoster(tplId, fStyle, hLayout, lShape, lBorder, stripId, lSize, isSocial);
            }

            if (selectFooterStyle) selectFooterStyle.addEventListener('change', onStyleChange);
            if (selectHeaderLayout) selectHeaderLayout.addEventListener('change', onStyleChange);
            if (selectLeaderShape) selectLeaderShape.addEventListener('change', onStyleChange);
            var toggleSocial = document.getElementById('qs-modal-show-social');
            if (toggleSocial) toggleSocial.addEventListener('change', onStyleChange);
            if (inputLeaderBorder) {
                inputLeaderBorder.addEventListener('input', function () {
                    updateColorSwatchesUI(this.value);
                    onStyleChange();
                });
                inputLeaderBorder.addEventListener('change', function () {
                    updateColorSwatchesUI(this.value);
                    onStyleChange();
                });
            }
            if (selectStrip) selectStrip.addEventListener('change', onStyleChange);

            // Click handlers for shape buttons
            document.querySelectorAll('.qs-shape-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var shape = this.getAttribute('data-shape') || 'circle';
                    if (selectLeaderShape) selectLeaderShape.value = shape;
                    var inputHiddenShape = document.getElementById('qs-input-leader-shape');
                    if (inputHiddenShape) inputHiddenShape.value = shape;
                    updateShapeTilesUI(shape);
                    onStyleChange();
                });
            });

            // Click handlers for Excalidraw color swatches
            document.querySelectorAll('.qs-color-swatch').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var color = this.getAttribute('data-color') || '#FF9933';
                    if (inputLeaderBorder) inputLeaderBorder.value = color;
                    var inputHiddenBorder = document.getElementById('qs-input-leader-border');
                    if (inputHiddenBorder) inputHiddenBorder.value = color;
                    updateColorSwatchesUI(color);
                    onStyleChange();
                });
            });

            // Click handlers for size presets
            document.querySelectorAll('.qs-size-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var size = parseInt(this.getAttribute('data-size')) || 106;
                    setLeaderSize(size);
                });
            });

            // Click handler for Decrease Size (-)
            var btnSizeDec = document.getElementById('qs-btn-size-dec');
            if (btnSizeDec) {
                btnSizeDec.addEventListener('click', function () {
                    var inputModalSize = document.getElementById('qs-modal-leader-size');
                    var curr = parseInt(inputModalSize ? inputModalSize.value : '106') || 106;
                    setLeaderSize(curr - 8);
                });
            }

            // Click handler for Increase Size (+)
            var btnSizeInc = document.getElementById('qs-btn-size-inc');
            if (btnSizeInc) {
                btnSizeInc.addEventListener('click', function () {
                    var inputModalSize = document.getElementById('qs-modal-leader-size');
                    var curr = parseInt(inputModalSize ? inputModalSize.value : '106') || 106;
                    setLeaderSize(curr + 8);
                });
            }

            // Helper: Compose High-Res Branded Poster via API
            function composeModalPoster(templateId, footerStyle, headerLayout, leaderShape, leaderBorder, stripId, leaderSize, showSocial) {
                var spinner = document.getElementById('qs-modal-spinner');
                var imgEl = document.getElementById('qs-modal-img');
                var previewLink = document.getElementById('qs-modal-preview-link');
                var btnPreview = document.getElementById('qs-modal-btn-preview');
                var btnDownload = document.getElementById('qs-modal-btn-download');
                var inputComposed = document.getElementById('qs-input-composed-image');

                if (spinner) spinner.classList.remove('d-none');
                if (btnDownload) {
                    btnDownload.classList.add('disabled');
                    btnDownload.href = '#';
                }
                if (btnPreview) {
                    btnPreview.classList.add('disabled');
                    btnPreview.href = '#';
                }
                if (previewLink) {
                    previewLink.classList.add('disabled');
                    previewLink.href = '#';
                }

                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var currentStrip = stripId || (selectStrip ? selectStrip.value : document.getElementById('qs-input-strip-id')?.value);
                var currentSize = leaderSize || document.getElementById('qs-modal-leader-size')?.value || '106';
                var toggleSocial = document.getElementById('qs-modal-show-social');
                var isSocial = (typeof showSocial !== 'undefined' && showSocial !== null)
                    ? Boolean(showSocial)
                    : (toggleSocial ? toggleSocial.checked : true);

                var candNameInput = document.getElementById('qs-modal-candidate-name');
                var candDesigInput = document.getElementById('qs-modal-candidate-designation');
                var candCutoutInput = document.getElementById('qs-modal-cutout-url');

                var payload = {
                    template_id: parseInt(templateId),
                    footer_style: footerStyle || selectFooterStyle?.value,
                    header_layout: headerLayout || selectHeaderLayout?.value,
                    leader_shape: leaderShape || selectLeaderShape?.value || 'circle',
                    leader_border_color: leaderBorder || inputLeaderBorder?.value || '#FF9933',
                    leader_size: parseInt(currentSize),
                    include_strip: (headerLayout !== 'none'),
                    show_social_handles: isSocial,
                    strip_id: currentStrip ? parseInt(currentStrip) : null,
                    candidate_name: candNameInput ? candNameInput.value.trim() : null,
                    candidate_designation: candDesigInput ? candDesigInput.value.trim() : null,
                    cutout_url: candCutoutInput ? candCutoutInput.value : null
                };

                fetch('{{ route('admin.festival-studio.compose') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success && data.image_url) {
                            if (imgEl) imgEl.src = data.image_url;
                            if (previewLink) {
                                previewLink.href = data.image_url;
                                previewLink.classList.remove('disabled');
                            }
                            if (btnPreview) {
                                btnPreview.href = data.image_url;
                                btnPreview.classList.remove('disabled');
                            }
                            if (btnDownload) {
                                btnDownload.href = data.image_url;
                                btnDownload.classList.remove('disabled');
                            }
                            if (inputComposed) {
                                inputComposed.value = data.relative_path;
                            }
                        }
                    })
                    .catch(function (err) {
                        console.error('Poster composition failed:', err);
                    })
                    .finally(function () {
                        if (spinner) spinner.classList.add('d-none');
                    });
            }

            // Candidate Name & Designation live typing listener (debounced 400ms)
            var candDebounceTimer = null;
            function onCandidateTextChange() {
                clearTimeout(candDebounceTimer);
                candDebounceTimer = setTimeout(function () {
                    onStyleChange();
                }, 400);
            }

            var inputCandName = document.getElementById('qs-modal-candidate-name');
            var inputCandDesig = document.getElementById('qs-modal-candidate-designation');
            if (inputCandName) {
                inputCandName.addEventListener('input', onCandidateTextChange);
                inputCandName.addEventListener('change', onStyleChange);
            }
            if (inputCandDesig) {
                inputCandDesig.addEventListener('input', onCandidateTextChange);
                inputCandDesig.addEventListener('change', onStyleChange);
            }

            // Candidate Photo Selection Chips click handler
            document.addEventListener('click', function (e) {
                var chip = e.target.closest('.qs-photo-chip');
                if (!chip) return;
                var cutout = chip.getAttribute('data-cutout');
                if (!cutout) return;

                // Update visual active state
                document.querySelectorAll('.qs-photo-chip').forEach(function (c) {
                    c.classList.remove('active');
                    c.style.outline = '1px solid #E2E8F0';
                    var check = c.querySelector('.qs-photo-check');
                    if (check) check.classList.add('d-none');
                });
                chip.classList.add('active');
                chip.style.outline = '2.5px solid #0D9488';
                chip.style.outlineOffset = '2px';
                var activeCheck = chip.querySelector('.qs-photo-check');
                if (activeCheck) activeCheck.classList.remove('d-none');

                // Update hidden cutout input & re-compose
                var inputCutout = document.getElementById('qs-modal-cutout-url');
                if (inputCutout) inputCutout.value = cutout;

                onStyleChange();
            });

            // Quick Photo Upload via AJAX
            var uploadPhotoInput = document.getElementById('qs-upload-photo-input');
            if (uploadPhotoInput) {
                uploadPhotoInput.addEventListener('change', function () {
                    var file = this.files[0];
                    if (!file) return;

                    var uploadSpinner = document.getElementById('qs-upload-spinner');
                    if (uploadSpinner) uploadSpinner.classList.remove('d-none');

                    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    var formData = new FormData();
                    formData.append('photo', file);

                    fetch('{{ route('admin.festival-studio.upload-photo') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success && data.url) {
                            var wrapper = document.getElementById('qs-photo-chips-wrapper');
                            if (wrapper) {
                                // Deactivate existing chips
                                document.querySelectorAll('.qs-photo-chip').forEach(function (c) {
                                    c.classList.remove('active');
                                    c.style.outline = '1px solid #E2E8F0';
                                    var check = c.querySelector('.qs-photo-check');
                                    if (check) check.classList.add('d-none');
                                });

                                // Create new active chip for the uploaded photo
                                var newChip = document.createElement('button');
                                newChip.type = 'button';
                                newChip.className = 'btn p-0 rounded-circle position-relative qs-photo-chip active flex-shrink-0';
                                newChip.setAttribute('data-cutout', data.path);
                                newChip.style.cssText = 'width: 44px; height: 44px; outline: 2.5px solid #0D9488; outline-offset: 2px;';
                                newChip.innerHTML = '<img src="' + data.url + '" class="rounded-circle w-100 h-100" style="object-fit: cover;">' +
                                    '<span class="qs-photo-check position-absolute bottom-0 end-0 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 14px; height: 14px; font-size: 9px;">&#10003;</span>';

                                wrapper.insertBefore(newChip, wrapper.firstChild);
                            }

                            var inputCutout = document.getElementById('qs-modal-cutout-url');
                            if (inputCutout) inputCutout.value = data.path;

                            onStyleChange();
                        } else {
                            alert(data.message || 'Failed to upload photo.');
                        }
                    })
                    .catch(function (err) {
                        console.error('Photo upload error:', err);
                        alert('Could not upload photo. Please check image format (JPG, PNG, WebP) and size (under 10MB).');
                    })
                    .finally(function () {
                        if (uploadSpinner) uploadSpinner.classList.add('d-none');
                        uploadPhotoInput.value = '';
                    });
                });
            }

            // Share Handlers in Modal (WhatsApp & Native Share)
            var btnShareWa = document.getElementById('qs-modal-btn-share-wa');
            if (btnShareWa) {
                btnShareWa.addEventListener('click', function () {
                    var imgEl = document.getElementById('qs-modal-img');
                    var caption = document.getElementById('qs-input-caption')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;
                    var text = caption + '\n\n' + shareUrl;
                    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
                });
            }

            var btnShareNative = document.getElementById('qs-modal-btn-share-native');
            if (btnShareNative) {
                btnShareNative.addEventListener('click', function () {
                    var imgEl = document.getElementById('qs-modal-img');
                    var caption = document.getElementById('qs-input-caption')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;

                    if (navigator.share) {
                        navigator.share({
                            title: 'Festival Greeting Poster',
                            text: caption,
                            url: shareUrl
                        }).catch(function () { });
                    } else {
                        navigator.clipboard.writeText(shareUrl).then(function () {
                            showStudioToast('Poster link copied to clipboard!');
                        }).catch(function () {
                            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(caption + '\n' + shareUrl), '_blank');
                        });
                    }
                });
            }

            // Delegated Share Button Handler on Grid Cards
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-trigger-share');
                if (!btn) return;

                var imgUrl = btn.getAttribute('data-template-image') || window.location.href;
                var caption = btn.getAttribute('data-caption') || '';
                var title = btn.getAttribute('data-festival-title') || 'Festival Greeting Poster';

                if (navigator.share) {
                    navigator.share({
                        title: title,
                        text: caption,
                        url: imgUrl
                    }).catch(function () { });
                } else {
                    var textToShare = (caption ? caption + '\n\n' : '') + imgUrl;
                    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(textToShare), '_blank');
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

            // Quick Schedule AJAX Submit (Step 2)
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
                    var footerStyle = document.getElementById('qs-input-footer-style')?.value || selectFooterStyle?.value;
                    var headerLayout = document.getElementById('qs-input-header-layout')?.value || selectHeaderLayout?.value;
                    var leaderShape = document.getElementById('qs-input-leader-shape')?.value || selectLeaderShape?.value || 'circle';
                    var leaderBorder = document.getElementById('qs-input-leader-border')?.value || inputLeaderBorder?.value || '#FF9933';
                    var stripId = document.getElementById('qs-input-strip-id')?.value || (selectStrip ? selectStrip.value : null);
                    var composedImage = document.getElementById('qs-input-composed-image')?.value || '';

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
                            content: content,
                            composed_image: composedImage,
                            footer_style: footerStyle,
                            header_layout: headerLayout,
                            leader_shape: leaderShape,
                            leader_border_color: leaderBorder,
                            strip_id: stripId ? parseInt(stripId) : null,
                            include_strip: (headerLayout !== 'none')
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
                                actionEl.innerHTML = '<span class="badge bg-success text-white rounded-pill px-3 py-1.5 small fw-semibold"><i class="ti ti-check me-1"></i>Scheduled</span>';
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

                    try {
                        localStorage.setItem('socialmanager_header_strip_enabled', isChecked ? '1' : '0');
                    } catch (err) { }
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

            // ======================================================================
            // APPLE-GRADE STUDIO EDITOR: CANVAS, INSPECTOR & FILMSTRIP LOGIC
            // ======================================================================
            var currentStudioTemplateId = null;
            var studioDebounceTimer = null;

            function initStudioEditor() {
                var activeFilmstrip = document.querySelector('.studio-filmstrip-card.active');
                if (!activeFilmstrip) {
                    activeFilmstrip = document.querySelector('.studio-filmstrip-card');
                }
                if (activeFilmstrip) {
                    currentStudioTemplateId = activeFilmstrip.getAttribute('data-template-id');
                    composeStudioPoster();
                }
            }

            function composeStudioPoster() {
                if (!currentStudioTemplateId) return;

                var spinner = document.getElementById('studio-canvas-spinner');
                var imgEl = document.getElementById('studio-canvas-img');
                var linkEl = document.getElementById('studio-canvas-link');
                var btnPreview = document.getElementById('studio-btn-preview');
                var btnDownload = document.getElementById('studio-btn-download');

                if (spinner) spinner.classList.remove('d-none');
                if (btnDownload) btnDownload.classList.add('disabled');
                if (btnPreview) btnPreview.classList.add('disabled');

                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var fStyle = document.getElementById('studio-footer-style')?.value || 'dual_ribbon_badge';
                var hLayout = document.getElementById('studio-header-layout')?.value || 'full_width_strip';
                var stripId = document.getElementById('studio-strip-id')?.value;
                var lShape = document.getElementById('studio-leader-shape')?.value || 'circle';
                var lBorder = document.getElementById('studio-leader-border')?.value || '#FF9933';
                var lSize = document.getElementById('studio-leader-size')?.value || '106';
                var isSocial = document.getElementById('studio-show-social')?.checked ?? true;
                var candName = document.getElementById('studio-candidate-name')?.value?.trim();
                var candDesig = document.getElementById('studio-candidate-designation')?.value?.trim();
                var candCutout = document.getElementById('studio-cutout-url')?.value;

                var payload = {
                    template_id: parseInt(currentStudioTemplateId),
                    footer_style: fStyle,
                    header_layout: hLayout,
                    leader_shape: lShape,
                    leader_border_color: lBorder,
                    leader_size: parseInt(lSize),
                    include_strip: (hLayout !== 'none'),
                    show_social_handles: isSocial,
                    strip_id: stripId ? parseInt(stripId) : null,
                    candidate_name: candName || null,
                    candidate_designation: candDesig || null,
                    cutout_url: candCutout || null
                };

                fetch('{{ route('admin.festival-studio.compose') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success && data.image_url) {
                        if (imgEl) imgEl.src = data.image_url;
                        if (linkEl) linkEl.href = data.image_url;
                        if (btnPreview) {
                            btnPreview.href = data.image_url;
                            btnPreview.classList.remove('disabled');
                        }
                        if (btnDownload) {
                            btnDownload.href = data.image_url;
                            btnDownload.classList.remove('disabled');
                        }
                        var wrapper = document.getElementById('studio-canvas-wrapper');
                        if (wrapper) wrapper.setAttribute('data-composed-path', data.relative_path || '');
                    }
                })
                .catch(function(err) {
                    console.error('Studio compose error:', err);
                })
                .finally(function() {
                    if (spinner) spinner.classList.add('d-none');
                });
            }

            // Initial trigger on page load
            initStudioEditor();

            // 1. View Mode Switcher (Studio Editor vs All Designs Grid)
            document.addEventListener('click', function(e) {
                var viewBtn = e.target.closest('.fs-view-btn');
                if (!viewBtn) return;
                document.querySelectorAll('.fs-view-btn').forEach(function(b) {
                    b.classList.remove('active', 'fw-semibold');
                    b.classList.add('text-secondary');
                });
                viewBtn.classList.add('active', 'fw-semibold');
                viewBtn.classList.remove('text-secondary');

                var viewMode = viewBtn.getAttribute('data-view');
                var studioWorkspace = document.getElementById('fs-studio-workspace');
                var gridWorkspace = document.getElementById('fs-grid-workspace');

                if (viewMode === 'grid') {
                    if (studioWorkspace) studioWorkspace.classList.add('d-none');
                    if (gridWorkspace) gridWorkspace.classList.remove('d-none');
                } else {
                    if (studioWorkspace) studioWorkspace.classList.remove('d-none');
                    if (gridWorkspace) gridWorkspace.classList.add('d-none');
                }
            });

            // 2. Filmstrip Thumbnail Click or Grid Card Click to Activate Template
            document.addEventListener('click', function(e) {
                var filmstripCard = e.target.closest('.studio-filmstrip-card');
                var gridCardBtn = e.target.closest('.btn-activate-studio-template');

                var targetEl = filmstripCard || gridCardBtn;
                if (!targetEl) return;

                var tplId = targetEl.getAttribute('data-template-id');
                var tplTitle = targetEl.getAttribute('data-template-title');
                var fStyle = targetEl.getAttribute('data-footer-style');
                var hLayout = targetEl.getAttribute('data-header-layout');
                var stripId = targetEl.getAttribute('data-strip-id');

                currentStudioTemplateId = tplId;

                // Ensure Studio workspace is visible if clicked from grid
                var studioWorkspace = document.getElementById('fs-studio-workspace');
                var gridWorkspace = document.getElementById('fs-grid-workspace');
                if (gridWorkspace && !gridWorkspace.classList.contains('d-none')) {
                    gridWorkspace.classList.add('d-none');
                    if (studioWorkspace) studioWorkspace.classList.remove('d-none');
                    document.querySelectorAll('.fs-view-btn').forEach(function(b) {
                        if (b.getAttribute('data-view') === 'studio') {
                            b.classList.add('active', 'fw-semibold');
                            b.classList.remove('text-secondary');
                        } else {
                            b.classList.remove('active', 'fw-semibold');
                            b.classList.add('text-secondary');
                        }
                    });
                }

                // Update active filmstrip card
                document.querySelectorAll('.studio-filmstrip-card').forEach(function(c) {
                    c.classList.remove('active');
                    c.style.borderColor = '#e2e8f0';
                    var check = c.querySelector('.filmstrip-check');
                    if (check) check.classList.add('d-none');
                });

                var activeCard = document.getElementById('filmstrip-card-' + tplId);
                if (activeCard) {
                    activeCard.classList.add('active');
                    activeCard.style.borderColor = '#0d9488';
                    var check = activeCard.querySelector('.filmstrip-check');
                    if (check) check.classList.remove('d-none');
                    activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }

                // Update Stage Header
                var titleEl = document.getElementById('studio-active-template-title');
                if (titleEl && tplTitle) titleEl.textContent = tplTitle;
                var idBadge = document.getElementById('studio-active-id-badge');
                if (idBadge) idBadge.textContent = '#' + tplId;

                // Update Dropdown Values
                var selFooter = document.getElementById('studio-footer-style');
                if (selFooter && fStyle) selFooter.value = fStyle;
                var selHeader = document.getElementById('studio-header-layout');
                if (selHeader && hLayout) selHeader.value = hLayout;
                var selStrip = document.getElementById('studio-strip-id');
                if (selStrip && stripId) selStrip.value = stripId;

                // Compose poster on canvas
                composeStudioPoster();
            });

            // 3. Debounced Candidate Inputs
            function onStudioDebouncedInput() {
                clearTimeout(studioDebounceTimer);
                studioDebounceTimer = setTimeout(function() {
                    composeStudioPoster();
                }, 400);
            }

            document.addEventListener('input', function(e) {
                if (e.target && (e.target.id === 'studio-candidate-name' || e.target.id === 'studio-candidate-designation')) {
                    onStudioDebouncedInput();
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target && (
                    e.target.id === 'studio-footer-style' ||
                    e.target.id === 'studio-header-layout' ||
                    e.target.id === 'studio-strip-id' ||
                    e.target.id === 'studio-show-social'
                )) {
                    composeStudioPoster();
                }
            });

            // 4. Photo Cutout Chips in Studio
            document.addEventListener('click', function(e) {
                var chip = e.target.closest('.studio-photo-chip');
                if (!chip) return;
                var url = chip.getAttribute('data-url');
                if (!url) return;

                document.querySelectorAll('.studio-photo-chip').forEach(function(c) {
                    c.classList.remove('active');
                    c.style.border = '2px solid #e2e8f0';
                    c.style.outline = 'none';
                });
                chip.classList.add('active');
                chip.style.border = '2px solid #0d9488';
                chip.style.outline = '2px solid rgba(13, 148, 136, 0.3)';
                chip.style.outlineOffset = '1px';

                var hiddenCutout = document.getElementById('studio-cutout-url');
                if (hiddenCutout) hiddenCutout.value = url;

                composeStudioPoster();
            });

            // 5. Studio Photo Upload
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'studio-upload-photo-input') {
                    var file = e.target.files[0];
                    if (!file) return;

                    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    var formData = new FormData();
                    formData.append('photo', file);

                    var spinner = document.getElementById('studio-canvas-spinner');
                    if (spinner) spinner.classList.remove('d-none');

                    fetch('{{ route('admin.festival-studio.upload-photo') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.success && data.url) {
                            var wrapper = document.getElementById('studio-photo-chips-wrapper');
                            if (wrapper) {
                                document.querySelectorAll('.studio-photo-chip').forEach(function(c) {
                                    c.classList.remove('active');
                                    c.style.border = '2px solid #e2e8f0';
                                    c.style.outline = 'none';
                                });

                                var newChip = document.createElement('div');
                                newChip.className = 'studio-photo-chip position-relative rounded-circle cursor-pointer active flex-shrink-0';
                                newChip.setAttribute('data-url', data.path);
                                newChip.style.cssText = 'width: 38px; height: 38px; border: 2px solid #0d9488; outline: 2px solid rgba(13, 148, 136, 0.3); outline-offset: 1px;';
                                newChip.innerHTML = '<img src="' + data.url + '" class="w-100 h-100 rounded-circle object-fit-cover bg-white">';
                                wrapper.insertBefore(newChip, wrapper.firstChild);
                            }

                            var hiddenCutout = document.getElementById('studio-cutout-url');
                            if (hiddenCutout) hiddenCutout.value = data.path;

                            composeStudioPoster();
                        } else {
                            alert(data.message || 'Failed to upload photo.');
                        }
                    })
                    .catch(function(err) {
                        console.error('Photo upload error:', err);
                        alert('Could not upload photo.');
                    })
                    .finally(function() {
                        if (spinner) spinner.classList.add('d-none');
                        e.target.value = '';
                    });
                }
            });

            // 6. Shape Tiles & Ring Color Swatches
            document.addEventListener('click', function(e) {
                var shapeTile = e.target.closest('.studio-shape-tile');
                if (shapeTile) {
                    var shape = shapeTile.getAttribute('data-shape');
                    document.querySelectorAll('.studio-shape-tile').forEach(function(t) {
                        t.classList.remove('active');
                        t.style.border = '1.5px solid #e2e8f0';
                        t.style.background = '#ffffff';
                    });
                    shapeTile.classList.add('active');
                    shapeTile.style.border = '1.5px solid #0d9488';
                    shapeTile.style.background = '#f0fdfa';

                    var hiddenShape = document.getElementById('studio-leader-shape');
                    if (hiddenShape) hiddenShape.value = shape;
                    composeStudioPoster();
                    return;
                }

                var swatch = e.target.closest('.studio-swatch');
                if (swatch) {
                    var color = swatch.getAttribute('data-color');
                    document.querySelectorAll('.studio-swatch').forEach(function(s) {
                        s.classList.remove('active');
                        s.style.border = '1.5px solid transparent';
                    });
                    swatch.classList.add('active');
                    swatch.style.border = '1.5px solid #0d9488';

                    var hiddenBorder = document.getElementById('studio-leader-border');
                    if (hiddenBorder) hiddenBorder.value = color;
                    composeStudioPoster();
                }
            });

            // 7. Inspector Tab Switching & Stepper
            document.addEventListener('click', function(e) {
                var tabBtn = e.target.closest('.inspector-tab-btn');
                var nextBtn = e.target.closest('#studio-btn-next-step');
                var backBtn = e.target.closest('#studio-btn-back-step');

                if (tabBtn) {
                    var paneId = tabBtn.getAttribute('data-pane');
                    document.querySelectorAll('.inspector-tab-btn').forEach(function(b) {
                        b.classList.remove('active');
                        b.classList.add('text-secondary');
                    });
                    tabBtn.classList.add('active');
                    tabBtn.classList.remove('text-secondary');

                    if (paneId === 'pane-design') {
                        document.getElementById('inspector-pane-design')?.classList.remove('d-none');
                        document.getElementById('inspector-pane-schedule')?.classList.add('d-none');
                    } else {
                        document.getElementById('inspector-pane-design')?.classList.add('d-none');
                        document.getElementById('inspector-pane-schedule')?.classList.remove('d-none');
                    }
                    return;
                }

                if (nextBtn) {
                    document.getElementById('inspector-pane-design')?.classList.add('d-none');
                    document.getElementById('inspector-pane-schedule')?.classList.remove('d-none');
                    document.getElementById('tab-btn-design')?.classList.remove('active');
                    document.getElementById('tab-btn-design')?.classList.add('text-secondary');
                    document.getElementById('tab-btn-schedule')?.classList.add('active');
                    document.getElementById('tab-btn-schedule')?.classList.remove('text-secondary');
                    return;
                }

                if (backBtn) {
                    document.getElementById('inspector-pane-design')?.classList.remove('d-none');
                    document.getElementById('inspector-pane-schedule')?.classList.add('d-none');
                    document.getElementById('tab-btn-design')?.classList.add('active');
                    document.getElementById('tab-btn-design')?.classList.remove('text-secondary');
                    document.getElementById('tab-btn-schedule')?.classList.remove('active');
                    document.getElementById('tab-btn-schedule')?.classList.add('text-secondary');
                    return;
                }
            });

            // 8. Studio Social Account Item Toggle
            document.addEventListener('click', function(e) {
                var item = e.target.closest('.studio-account-item');
                if (!item) return;
                if (e.target.tagName.toLowerCase() === 'input') return;

                var cb = item.querySelector('.studio-acc-cb');
                if (cb) {
                    cb.checked = !cb.checked;
                    item.classList.toggle('active', cb.checked);
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('studio-acc-cb')) {
                    var item = e.target.closest('.studio-account-item');
                    if (item) item.classList.toggle('active', e.target.checked);
                }
            });

            // 9. Submit Schedule from Studio
            document.addEventListener('click', function(e) {
                var submitBtn = e.target.closest('#studio-btn-submit-schedule');
                if (!submitBtn) return;

                var selectedAccountIds = [];
                document.querySelectorAll('.studio-acc-cb:checked').forEach(function(cb) {
                    selectedAccountIds.push(parseInt(cb.value));
                });

                var alertBox = document.getElementById('studio-schedule-alert');
                if (selectedAccountIds.length === 0) {
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Please select at least one social media account.';
                    }
                    return;
                }

                var schedDate = document.getElementById('studio-schedule-date')?.value;
                var schedTime = document.getElementById('studio-schedule-time')?.value;
                var caption = document.getElementById('studio-caption-text')?.value;
                var activePill = document.querySelector('.fs-date-pill.active');
                var festivalId = activePill ? activePill.getAttribute('data-festival-id') : null;

                if (!schedDate || !schedTime) {
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Please provide date and time.';
                    }
                    return;
                }

                var origText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status"></span>Scheduling...';
                if (alertBox) alertBox.classList.add('d-none');

                var fStyle = document.getElementById('studio-footer-style')?.value || 'dual_ribbon_badge';
                var hLayout = document.getElementById('studio-header-layout')?.value || 'full_width_strip';
                var stripId = document.getElementById('studio-strip-id')?.value;
                var lShape = document.getElementById('studio-leader-shape')?.value || 'circle';
                var lBorder = document.getElementById('studio-leader-border')?.value || '#FF9933';
                var lSize = document.getElementById('studio-leader-size')?.value || '106';
                var isSocial = document.getElementById('studio-show-social')?.checked ?? true;
                var candName = document.getElementById('studio-candidate-name')?.value?.trim();
                var candDesig = document.getElementById('studio-candidate-designation')?.value?.trim();
                var candCutout = document.getElementById('studio-cutout-url')?.value;
                var composedPath = document.getElementById('studio-canvas-wrapper')?.getAttribute('data-composed-path') || '';

                var payload = {
                    template_id: parseInt(currentStudioTemplateId),
                    festival_id: festivalId ? parseInt(festivalId) : null,
                    account_ids: selectedAccountIds,
                    schedule_date: schedDate,
                    schedule_time: schedTime,
                    content: caption,
                    composed_image: composedPath,
                    footer_style: fStyle,
                    header_layout: hLayout,
                    leader_shape: lShape,
                    leader_border_color: lBorder,
                    leader_size: parseInt(lSize),
                    include_strip: (hLayout !== 'none'),
                    show_social_handles: isSocial,
                    strip_id: stripId ? parseInt(stripId) : null,
                    candidate_name: candName || null,
                    candidate_designation: candDesig || null,
                    cutout_url: candCutout || null
                };

                fetch('{{ route('admin.festival-studio.quick-schedule') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { status: res.status, ok: res.ok, data: data };
                    });
                })
                .then(function(result) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;

                    if (!result.ok) {
                        if (alertBox) {
                            alertBox.classList.remove('d-none');
                            alertBox.innerText = result.data.message || 'Scheduling failed. Please try again.';
                        }
                        return;
                    }

                    showStudioToast(result.data.message || 'Poster scheduled successfully!');

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
                })
                .catch(function(err) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Network error while scheduling.';
                    }
                    console.error('Schedule error:', err);
                });
            });

            // 10. WhatsApp & Share from Studio
            document.addEventListener('click', function(e) {
                if (e.target.closest('#studio-btn-whatsapp')) {
                    var imgEl = document.getElementById('studio-canvas-img');
                    var caption = document.getElementById('studio-caption-text')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;
                    var text = caption + '\n\n' + shareUrl;
                    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
                    return;
                }

                if (e.target.closest('#studio-btn-share')) {
                    var imgEl = document.getElementById('studio-canvas-img');
                    var caption = document.getElementById('studio-caption-text')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;

                    if (navigator.share) {
                        navigator.share({
                            title: 'Festival Greeting Poster',
                            text: caption,
                            url: shareUrl
                        }).catch(function() {});
                    } else {
                        navigator.clipboard.writeText(shareUrl).then(function() {
                            showStudioToast('Poster link copied to clipboard!');
                        }).catch(function() {
                            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(caption + '\n' + shareUrl), '_blank');
                        });
                    }
                }
            });
        });
    </script>
@endpush