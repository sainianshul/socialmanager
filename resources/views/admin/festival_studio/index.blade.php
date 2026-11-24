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

        /* Scheduled Date Pill Distinction */
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

        /* Template Grid Cards */
        .fs-template-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 20px;
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
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.09);
            border-color: rgba(13, 148, 136, 0.35);
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

        /* Card Hover Overlay */
        .fs-template-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
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
            padding: 24px;
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

        /* High-Contrast Backdrop Overlay for Slide-Over Studio Panel (Eliminates Background Clutter) */
        .offcanvas-backdrop {
            background-color: #0b1329 !important;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.3s ease !important;
        }

        .offcanvas-backdrop.show {
            opacity: 0.72 !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
        }

        [data-bs-theme="dark"] .offcanvas-backdrop.show {
            opacity: 0.88 !important;
            background-color: #020617 !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
        }

        /* Apple-Grade Right Slide-Over Panel (Theme Adaptive) */
        .studio-slide-panel {
            width: 960px !important;
            max-width: 95vw !important;
            border-top-left-radius: 24px;
            border-bottom-left-radius: 24px;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: -20px 0 60px rgba(15, 23, 42, 0.28) !important;
            background-color: var(--tblr-bg-surface, #ffffff) !important;
            color: var(--tblr-body-color, #1e293b) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
            z-index: 1055 !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel {
            background-color: #0d1527 !important;
            color: #f8fafc !important;
            border-left: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: -25px 0 70px rgba(0, 0, 0, 0.75) !important;
        }

        .studio-slide-panel .offcanvas-header {
            background-color: var(--tblr-bg-surface, #ffffff);
            border-bottom-color: var(--tblr-border-color, #f1f5f9) !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel .offcanvas-header {
            background-color: #0d1527 !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        .studio-slide-panel .offcanvas-body {
            background-color: var(--tblr-bg-surface, #ffffff);
            color: var(--tblr-body-color, #1e293b);
        }

        [data-bs-theme="dark"] .studio-slide-panel .offcanvas-body {
            background-color: #0d1527 !important;
            color: #f8fafc !important;
        }

        /* Stepper badge wrapper in Header */
        .studio-stepper-wrap {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
        }

        [data-bs-theme="dark"] .studio-stepper-wrap {
            background: #162238 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Subheading badge in Header */
        .studio-subheading-badge {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        [data-bs-theme="dark"] .studio-subheading-badge {
            background: #162238 !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Toolbar Section Cards (Section 1 & 2) */
        .studio-panel-card {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
            border-radius: 18px !important;
        }

        [data-bs-theme="dark"] .studio-panel-card {
            background-color: #131d33 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        }

        .studio-panel-card-inner {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .studio-panel-card-inner {
            background-color: #0d1527 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
        }

        .studio-section-title {
            color: #0f172a !important;
            font-size: 11.5px;
            letter-spacing: 0.06em;
            font-weight: 700;
        }

        [data-bs-theme="dark"] .studio-section-title {
            color: #f8fafc !important;
        }

        .studio-slide-panel .form-label {
            color: #334155 !important;
            font-weight: 600 !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel .form-label {
            color: #cbd5e1 !important;
        }

        @media (max-width: 768px) {
            .studio-slide-panel {
                width: 100% !important;
                max-width: 100vw !important;
                border-radius: 0 !important;
            }
        }

        /* Premium Select Styling (Theme Adaptive) */
        .qs-premium-select {
            height: 46px !important;
            border-radius: 12px !important;
            border: 1px solid #E2E8F0 !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            color: #1E293B !important;
            padding: 0 16px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748B' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 14px 10px !important;
        }

        [data-bs-theme="dark"] .qs-premium-select {
            background-color: #0d1527 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
            box-shadow: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394A3B8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        }

        .qs-premium-select:hover {
            border-color: #0D9488 !important;
        }

        .qs-premium-select:focus {
            border-color: #0D9488 !important;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15) !important;
            outline: none !important;
        }

        [data-bs-theme="dark"] .qs-premium-select option {
            background-color: #0d1527 !important;
            color: #f8fafc !important;
        }

        /* Form Controls in Slide Panel */
        .studio-slide-panel .form-control {
            background-color: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            color: #1E293B !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel .form-control {
            background-color: #0d1527 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
            box-shadow: none !important;
        }

        .studio-slide-panel .form-control:focus {
            border-color: #0D9488 !important;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15) !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel input[type="date"],
        [data-bs-theme="dark"] .studio-slide-panel input[type="time"] {
            color-scheme: dark !important;
        }

        /* Pill Buttons (Shape, Leader Size, Candidate Scale) */
        .panel-pill-btn,
        .panel-shape-tile,
        .panel-leader-size-pill,
        .panel-candidate-scale-pill {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            color: #475569;
            transition: all 0.15s ease;
        }

        .panel-pill-btn:hover,
        .panel-shape-tile:hover,
        .panel-leader-size-pill:hover,
        .panel-candidate-scale-pill:hover {
            border-color: #0D9488;
            color: #0D9488;
        }

        .panel-pill-btn.active,
        .panel-shape-tile.active,
        .panel-leader-size-pill.active,
        .panel-candidate-scale-pill.active {
            border: 1.5px solid #0D9488 !important;
            background: #F0FDFA !important;
            color: #0D9488 !important;
        }

        [data-bs-theme="dark"] .panel-pill-btn,
        [data-bs-theme="dark"] .panel-shape-tile,
        [data-bs-theme="dark"] .panel-leader-size-pill,
        [data-bs-theme="dark"] .panel-candidate-scale-pill {
            background: #0d1527 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .panel-pill-btn:hover,
        [data-bs-theme="dark"] .panel-shape-tile:hover,
        [data-bs-theme="dark"] .panel-leader-size-pill:hover,
        [data-bs-theme="dark"] .panel-candidate-scale-pill:hover {
            border-color: #14b8a6 !important;
            color: #2dd4bf !important;
        }

        [data-bs-theme="dark"] .panel-pill-btn.active,
        [data-bs-theme="dark"] .panel-shape-tile.active,
        [data-bs-theme="dark"] .panel-leader-size-pill.active,
        [data-bs-theme="dark"] .panel-candidate-scale-pill.active {
            border: 1.5px solid #14b8a6 !important;
            background: rgba(13, 148, 136, 0.22) !important;
            color: #2dd4bf !important;
        }

        /* Action Icon Buttons */
        .panel-icon-btn {
            width: 36px !important;
            height: 36px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
            border: 1px solid #E2E8F0;
            color: #475569;
        }

        [data-bs-theme="dark"] .panel-icon-btn {
            background: #141f36 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }

        .panel-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .panel-icon-whatsapp {
            background: #ECFDF5;
            border-color: #A7F3D0;
            color: #059669;
        }

        [data-bs-theme="dark"] .panel-icon-whatsapp {
            background: rgba(5, 150, 105, 0.15) !important;
            border-color: rgba(5, 150, 105, 0.3) !important;
            color: #34d399 !important;
        }

        /* Template Switcher Chips in Panel */
        .panel-template-chip {
            cursor: pointer;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            transition: all 0.2s ease;
        }

        [data-bs-theme="dark"] .panel-template-chip {
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .panel-template-chip:hover {
            transform: translateY(-2px);
        }

        .panel-template-chip.active {
            border-color: #0D9488 !important;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.35) !important;
        }

        /* Photo Chips */
        .panel-photo-chip {
            cursor: pointer;
            width: 38px;
            height: 38px;
            border: 2px solid #E2E8F0;
            transition: all 0.15s ease;
        }

        [data-bs-theme="dark"] .panel-photo-chip {
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .panel-photo-chip:hover {
            transform: scale(1.08);
        }

        .panel-photo-chip.active {
            border-color: #0D9488 !important;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.3) !important;
        }

        /* Swatches */
        .panel-swatch {
            transition: all 0.15s ease;
            border: 1.5px solid transparent;
        }

        .panel-swatch[data-color="#FFFFFF"] {
            border: 1.5px solid #E2E8F0;
        }

        [data-bs-theme="dark"] .panel-swatch[data-color="#FFFFFF"] {
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
        }

        .panel-swatch:hover {
            transform: scale(1.1);
        }

        .panel-swatch.active {
            border-color: #0D9488 !important;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.35) !important;
            transform: scale(1.08);
        }

        /* Borders & Text in Dark Theme */
        [data-bs-theme="dark"] .studio-slide-panel .border,
        [data-bs-theme="dark"] .studio-slide-panel .border-top,
        [data-bs-theme="dark"] .studio-slide-panel .border-bottom {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .studio-slide-panel .text-muted,
        [data-bs-theme="dark"] .studio-slide-panel .form-label {
            color: #94a3b8 !important;
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
    </style>
@endpush

@section('content')

    {{-- Clean Minimal Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title fw-bold text-body mb-0" style="font-size: 1.6rem; letter-spacing: -0.02em;">
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

            // ======================================================================
            // 1. DYNAMIC FESTIVAL DATE SWITCHING VIA AJAX
            // ======================================================================
            document.addEventListener('click', function (e) {
                var pill = e.target.closest('.fs-date-pill');
                if (!pill) return;

                e.preventDefault();

                if (pill.classList.contains('active') && !contentArea.classList.contains('is-loading')) {
                    return;
                }

                var festivalId = pill.getAttribute('data-festival-id');
                var targetUrl = pill.getAttribute('href');

                switchFestival(targetUrl, festivalId, pill, true);
            });

            function switchFestival(url, festivalId, activePill, pushState) {
                document.querySelectorAll('.fs-date-pill').forEach(function (el) {
                    el.classList.remove('active');
                });
                if (activePill) {
                    activePill.classList.add('active');
                    activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }

                if (activeAbortController) {
                    activeAbortController.abort();
                }
                activeAbortController = new AbortController();

                if (contentArea) {
                    contentArea.classList.add('is-loading');
                }

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

                            if (pushState && window.history && window.history.pushState) {
                                window.history.pushState({ festival_id: festivalId, url: url }, '', url);
                            }
                        }
                    })
                    .catch(function (err) {
                        if (err.name === 'AbortError') return;
                        console.error('Festival Studio AJAX error:', err);
                        window.location.href = url;
                    })
                    .finally(function () {
                        if (contentArea) {
                            contentArea.classList.remove('is-loading');
                        }
                    });
            }

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

            // ======================================================================
            // 2. RIGHT SLIDE-OVER PANEL: OPEN & POPULATE TEMPLATE DATA
            // ======================================================================
            var panelDebounceTimer = null;
            var currentPanelTemplateId = null;

            document.addEventListener('click', function (e) {
                var trigger = e.target.closest('.btn-open-slide-panel');
                if (!trigger) return;

                var tplId = trigger.getAttribute('data-template-id');
                var tplTitle = trigger.getAttribute('data-template-title') || '';
                var tplImage = trigger.getAttribute('data-template-image') || '';
                var festId = trigger.getAttribute('data-festival-id') || '';
                var festTitle = trigger.getAttribute('data-festival-title') || '';
                var festDateFormatted = trigger.getAttribute('data-festival-date-formatted') || '';
                var festDate = trigger.getAttribute('data-festival-date') || '';
                var caption = trigger.getAttribute('data-caption') || '';
                var fStyle = trigger.getAttribute('data-footer-style') || 'dual_ribbon_badge';
                var hLayout = trigger.getAttribute('data-header-layout') || 'full_width_strip';
                var stripId = trigger.getAttribute('data-strip-id') || '';
                var lShape = trigger.getAttribute('data-leader-shape') || 'circle';
                var lBorder = trigger.getAttribute('data-leader-border') || '#FF9933';

                currentPanelTemplateId = tplId;

                // 1. Reset Stepper to Step 1
                switchPanelStep(1);

                // 2. Update Hidden Metadata
                var hidTpl = document.getElementById('panel-input-template-id');
                if (hidTpl) hidTpl.value = tplId;
                var hidFest = document.getElementById('panel-input-festival-id');
                if (hidFest) hidFest.value = festId;

                // 3. Update Header Info
                var subEl = document.getElementById('panel-subheading');
                if (subEl && festTitle) {
                    subEl.innerText = festTitle + (festDateFormatted ? ' (' + festDateFormatted + ')' : '');
                }

                // 4. Update Images & Links
                var imgEl = document.getElementById('panel-canvas-img');
                if (imgEl && tplImage) imgEl.src = tplImage;
                var prevLink = document.getElementById('panel-preview-link');
                if (prevLink && tplImage) prevLink.href = tplImage;
                var btnPrev = document.getElementById('panel-btn-preview');
                if (btnPrev && tplImage) btnPrev.href = tplImage;
                var btnDl = document.getElementById('panel-btn-download');
                if (btnDl && tplImage) btnDl.href = tplImage;

                // Step 2 Thumbnail & Title
                var schedThumb = document.getElementById('panel-schedule-thumb');
                if (schedThumb && tplImage) schedThumb.src = tplImage;
                var schedTitle = document.getElementById('panel-schedule-template-title');
                if (schedTitle) schedTitle.innerText = tplTitle;
                var schedDateBadge = document.getElementById('panel-schedule-festival-date');
                if (schedDateBadge && festDateFormatted) schedDateBadge.innerText = festDateFormatted;
                var schedDateInput = document.getElementById('panel-schedule-date');
                if (schedDateInput && festDate) schedDateInput.value = festDate;
                var schedCaption = document.getElementById('panel-caption-text');
                if (schedCaption && caption) schedCaption.value = caption;

                // 5. Update Toolbar Selects & Controls
                var selFooter = document.getElementById('panel-footer-style');
                if (selFooter && fStyle) selFooter.value = fStyle;
                var selHeader = document.getElementById('panel-header-layout');
                if (selHeader && hLayout) selHeader.value = hLayout;
                var selStrip = document.getElementById('panel-strip-id');
                if (selStrip && stripId) selStrip.value = stripId;

                // Avatar Shape & Border
                var hidShape = document.getElementById('panel-leader-shape');
                if (hidShape && lShape) hidShape.value = lShape;
                document.querySelectorAll('.panel-shape-tile').forEach(function (tile) {
                    var s = tile.getAttribute('data-shape');
                    tile.classList.toggle('active', s === lShape);
                });

                var hidBorder = document.getElementById('panel-leader-border');
                if (hidBorder && lBorder) hidBorder.value = lBorder;
                document.querySelectorAll('.panel-swatch').forEach(function (sw) {
                    var c = sw.getAttribute('data-color');
                    sw.classList.toggle('active', c.toLowerCase() === lBorder.toLowerCase());
                });

                // Update Template Switcher Chips active state
                document.querySelectorAll('.panel-template-chip').forEach(function (chip) {
                    var cid = chip.getAttribute('data-template-id');
                    var isCActive = (cid === tplId);
                    chip.classList.toggle('active', isCActive);
                    var check = chip.querySelector('.panel-chip-check');
                    if (check) check.classList.toggle('d-none', !isCActive);
                });

                // 6. Open Slide-Over Panel via Bootstrap Offcanvas
                var panelEl = document.getElementById('studioSlidePanel');
                if (panelEl) {
                    var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(panelEl);
                    bsOffcanvas.show();
                }

                // 7. Compose live poster
                composePanelPoster();
            });

            // ======================================================================
            // 3. COMPOSE POSTER IN PANEL VIA AJAX
            // ======================================================================
            function composePanelPoster() {
                var tplId = currentPanelTemplateId || document.getElementById('panel-input-template-id')?.value;
                if (!tplId) return;

                var spinner = document.getElementById('panel-canvas-spinner');
                var imgEl = document.getElementById('panel-canvas-img');
                var linkEl = document.getElementById('panel-preview-link');
                var btnPreview = document.getElementById('panel-btn-preview');
                var btnDownload = document.getElementById('panel-btn-download');
                var schedThumb = document.getElementById('panel-schedule-thumb');

                if (spinner) spinner.classList.remove('d-none');
                if (btnDownload) btnDownload.classList.add('disabled');
                if (btnPreview) btnPreview.classList.add('disabled');

                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                var fStyle = document.getElementById('panel-footer-style')?.value || 'dual_ribbon_badge';
                var hLayout = document.getElementById('panel-header-layout')?.value || 'full_width_strip';
                var stripId = document.getElementById('panel-strip-id')?.value;
                var lShape = document.getElementById('panel-leader-shape')?.value || 'circle';
                var lBorder = document.getElementById('panel-leader-border')?.value || '#FF9933';
                var lSize = document.getElementById('panel-leader-size')?.value || '104';
                var candScale = document.getElementById('panel-candidate-scale')?.value || '100';
                var isSocial = document.getElementById('panel-show-social')?.checked ?? true;
                var candName = document.getElementById('panel-candidate-name')?.value?.trim();
                var candDesig = document.getElementById('panel-candidate-designation')?.value?.trim();
                var candCutout = document.getElementById('panel-cutout-url')?.value;

                var payload = {
                    template_id: parseInt(tplId),
                    footer_style: fStyle,
                    header_layout: hLayout,
                    leader_shape: lShape,
                    leader_border_color: lBorder,
                    leader_size: parseInt(lSize),
                    candidate_scale: parseInt(candScale),
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
                .then(function (res) { return res.json(); })
                .then(function (data) {
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
                        if (schedThumb) schedThumb.src = data.image_url;

                        var hiddenComposed = document.getElementById('panel-composed-path');
                        if (hiddenComposed) hiddenComposed.value = data.relative_path || '';
                    }
                })
                .catch(function (err) {
                    console.error('Slide-over panel compose error:', err);
                })
                .finally(function () {
                    if (spinner) spinner.classList.add('d-none');
                });
            }

            // ======================================================================
            // 4. REACTIVE TOOLBAR CONTROLS
            // ======================================================================
            // Template switch inside panel
            document.addEventListener('click', function (e) {
                var chip = e.target.closest('.panel-template-chip');
                if (!chip) return;

                var tplId = chip.getAttribute('data-template-id');
                var tplTitle = chip.getAttribute('data-template-title');
                var fStyle = chip.getAttribute('data-footer-style');
                var hLayout = chip.getAttribute('data-header-layout');
                var stripId = chip.getAttribute('data-strip-id');

                currentPanelTemplateId = tplId;
                var hidTpl = document.getElementById('panel-input-template-id');
                if (hidTpl) hidTpl.value = tplId;

                document.querySelectorAll('.panel-template-chip').forEach(function (c) {
                    c.classList.remove('active');
                    c.style.borderColor = '#E2E8F0';
                    var check = c.querySelector('.panel-chip-check');
                    if (check) check.classList.add('d-none');
                });

                chip.classList.add('active');
                chip.style.borderColor = '#0D9488';
                var check = chip.querySelector('.panel-chip-check');
                if (check) check.classList.remove('d-none');

                var selFooter = document.getElementById('panel-footer-style');
                if (selFooter && fStyle) selFooter.value = fStyle;
                var selHeader = document.getElementById('panel-header-layout');
                if (selHeader && hLayout) selHeader.value = hLayout;
                var selStrip = document.getElementById('panel-strip-id');
                if (selStrip && stripId) selStrip.value = stripId;

                var schedTitle = document.getElementById('panel-schedule-template-title');
                if (schedTitle && tplTitle) schedTitle.innerText = tplTitle;

                composePanelPoster();
            });

            // Debounced Candidate Inputs
            document.addEventListener('input', function (e) {
                if (e.target && (e.target.id === 'panel-candidate-name' || e.target.id === 'panel-candidate-designation')) {
                    clearTimeout(panelDebounceTimer);
                    panelDebounceTimer = setTimeout(function () {
                        composePanelPoster();
                    }, 400);
                }
            });

            // Select Dropdown Changes & Social Toggle
            document.addEventListener('change', function (e) {
                if (e.target && (
                    e.target.id === 'panel-footer-style' ||
                    e.target.id === 'panel-header-layout' ||
                    e.target.id === 'panel-strip-id' ||
                    e.target.id === 'panel-show-social'
                )) {
                    composePanelPoster();
                }
            });

            // Shape Tiles
            document.addEventListener('click', function (e) {
                var tile = e.target.closest('.panel-shape-tile');
                if (!tile) return;

                var shape = tile.getAttribute('data-shape');
                document.querySelectorAll('.panel-shape-tile').forEach(function (t) {
                    t.classList.remove('active');
                });
                tile.classList.add('active');

                var hidShape = document.getElementById('panel-leader-shape');
                if (hidShape) hidShape.value = shape;
                composePanelPoster();
            });

            // Ring Color Swatches
            document.addEventListener('click', function (e) {
                var swatch = e.target.closest('.panel-swatch');
                if (!swatch) return;

                var color = swatch.getAttribute('data-color');
                document.querySelectorAll('.panel-swatch').forEach(function (s) {
                    s.classList.remove('active');
                });
                swatch.classList.add('active');

                var hidBorder = document.getElementById('panel-leader-border');
                if (hidBorder) hidBorder.value = color;
                composePanelPoster();
            });

            // Leader Avatar Size Pills
            document.addEventListener('click', function (e) {
                var pill = e.target.closest('.panel-leader-size-pill');
                if (!pill) return;

                var size = pill.getAttribute('data-size');
                document.querySelectorAll('.panel-leader-size-pill').forEach(function (p) {
                    p.classList.remove('active');
                });
                pill.classList.add('active');

                var hidSize = document.getElementById('panel-leader-size');
                if (hidSize) hidSize.value = size;

                var label = document.getElementById('panel-leader-size-label');
                if (label) {
                    label.innerText = (parseInt(size) <= 90) ? 'Small' : ((parseInt(size) <= 106) ? 'Medium' : 'Large');
                }

                composePanelPoster();
            });

            // Candidate Photo Scale Pills
            document.addEventListener('click', function (e) {
                var pill = e.target.closest('.panel-candidate-scale-pill');
                if (!pill) return;

                var scale = pill.getAttribute('data-scale');
                document.querySelectorAll('.panel-candidate-scale-pill').forEach(function (p) {
                    p.classList.remove('active');
                });
                pill.classList.add('active');

                var hidScale = document.getElementById('panel-candidate-scale');
                if (hidScale) hidScale.value = scale;

                var label = document.getElementById('panel-candidate-scale-label');
                if (label) {
                    label.innerText = scale + '%';
                }

                composePanelPoster();
            });

            // Photo Cutout Chips
            document.addEventListener('click', function (e) {
                var chip = e.target.closest('.panel-photo-chip');
                if (!chip) return;

                var url = chip.getAttribute('data-url');
                if (!url) return;

                document.querySelectorAll('.panel-photo-chip').forEach(function (c) {
                    c.classList.remove('active');
                });
                chip.classList.add('active');

                var hidCutout = document.getElementById('panel-cutout-url');
                if (hidCutout) hidCutout.value = url;
                composePanelPoster();
            });

            // Photo Upload
            document.addEventListener('change', function (e) {
                if (e.target && e.target.id === 'panel-upload-photo-input') {
                    var file = e.target.files[0];
                    if (!file) return;

                    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    var formData = new FormData();
                    formData.append('photo', file);

                    var spinner = document.getElementById('panel-canvas-spinner');
                    if (spinner) spinner.classList.remove('d-none');

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
                            var wrapper = document.getElementById('panel-photo-chips-wrapper');
                            if (wrapper) {
                                document.querySelectorAll('.panel-photo-chip').forEach(function (c) {
                                    c.classList.remove('active');
                                });

                                var newChip = document.createElement('div');
                                newChip.className = 'panel-photo-chip position-relative rounded-circle cursor-pointer active flex-shrink-0';
                                newChip.setAttribute('data-url', data.path);
                                newChip.innerHTML = '<img src="' + data.url + '" class="w-100 h-100 rounded-circle object-fit-cover bg-white">';
                                wrapper.insertBefore(newChip, wrapper.firstChild);
                            }

                            var hidCutout = document.getElementById('panel-cutout-url');
                            if (hidCutout) hidCutout.value = data.path;

                            composePanelPoster();
                        } else {
                            alert(data.message || 'Failed to upload photo.');
                        }
                    })
                    .catch(function (err) {
                        console.error('Photo upload error:', err);
                        alert('Could not upload photo.');
                    })
                    .finally(function () {
                        if (spinner) spinner.classList.add('d-none');
                        e.target.value = '';
                    });
                }
            });

            // ======================================================================
            // 5. STEPPER NAVIGATION (STEP 1 <-> STEP 2)
            // ======================================================================
            function switchPanelStep(step) {
                var pane1 = document.getElementById('panel-step-pane-1');
                var pane2 = document.getElementById('panel-step-pane-2');
                var ind1 = document.getElementById('panel-step-indicator-1');
                var ind2 = document.getElementById('panel-step-indicator-2');

                if (step === 1) {
                    if (pane1) pane1.classList.remove('d-none');
                    if (pane2) pane2.classList.add('d-none');

                    if (ind1) {
                        ind1.classList.add('active', 'text-white');
                        ind1.classList.remove('bg-transparent', 'text-secondary');
                        ind1.style.background = '#0D9488';
                    }
                    if (ind2) {
                        ind2.classList.remove('active', 'text-white');
                        ind2.classList.add('bg-transparent', 'text-secondary');
                        ind2.style.background = 'transparent';
                    }
                } else {
                    if (pane1) pane1.classList.add('d-none');
                    if (pane2) pane2.classList.remove('d-none');

                    // Guarantee Step 2 thumbnail always matches the exact styled image composed in Step 1
                    var liveImg = document.getElementById('panel-canvas-img');
                    var schedThumb = document.getElementById('panel-schedule-thumb');
                    if (schedThumb && liveImg && liveImg.src) {
                        schedThumb.src = liveImg.src;
                    }

                    if (ind1) {
                        ind1.classList.remove('active', 'text-white');
                        ind1.classList.add('bg-transparent', 'text-secondary');
                        ind1.style.background = 'transparent';
                    }
                    if (ind2) {
                        ind2.classList.add('active', 'text-white');
                        ind2.classList.remove('bg-transparent', 'text-secondary');
                        ind2.style.background = '#0D9488';
                    }
                }
            }

            // Big Schedule button under preview
            document.addEventListener('click', function (e) {
                if (e.target.closest('#panel-btn-go-schedule')) {
                    switchPanelStep(2);
                }
                if (e.target.closest('#panel-btn-back-step') || e.target.closest('#panel-btn-cancel-step')) {
                    switchPanelStep(1);
                }
                if (e.target.closest('#panel-step-indicator-1')) {
                    switchPanelStep(1);
                }
                if (e.target.closest('#panel-step-indicator-2')) {
                    switchPanelStep(2);
                }
            });

            // Social Account Item Toggle
            document.addEventListener('click', function (e) {
                var item = e.target.closest('.panel-account-item');
                if (!item) return;
                if (e.target.tagName.toLowerCase() === 'input') return;

                var cb = item.querySelector('.panel-acc-cb');
                if (cb) {
                    cb.checked = !cb.checked;
                    item.classList.toggle('active', cb.checked);
                }
            });

            document.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('panel-acc-cb')) {
                    var item = e.target.closest('.panel-account-item');
                    if (item) item.classList.toggle('active', e.target.checked);
                }
            });

            // ======================================================================
            // 6. CONFIRM & SCHEDULE FESTIVAL POST
            // ======================================================================
            document.addEventListener('click', function (e) {
                var submitBtn = e.target.closest('#panel-btn-confirm-schedule');
                if (!submitBtn) return;

                var selectedAccountIds = [];
                document.querySelectorAll('.panel-acc-cb:checked').forEach(function (cb) {
                    selectedAccountIds.push(parseInt(cb.value));
                });

                var alertBox = document.getElementById('panel-schedule-alert');
                if (selectedAccountIds.length === 0) {
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Please select at least one social media account.';
                    }
                    return;
                }

                var schedDate = document.getElementById('panel-schedule-date')?.value;
                var schedTime = document.getElementById('panel-schedule-time')?.value;
                var caption = document.getElementById('panel-caption-text')?.value;
                var festivalId = document.getElementById('panel-input-festival-id')?.value;
                var templateId = currentPanelTemplateId || document.getElementById('panel-input-template-id')?.value;

                if (!schedDate || !schedTime) {
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Please provide publish date and time.';
                    }
                    return;
                }

                var origText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status"></span>Scheduling...';
                if (alertBox) alertBox.classList.add('d-none');

                var fStyle = document.getElementById('panel-footer-style')?.value || 'dual_ribbon_badge';
                var hLayout = document.getElementById('panel-header-layout')?.value || 'full_width_strip';
                var stripId = document.getElementById('panel-strip-id')?.value;
                var lShape = document.getElementById('panel-leader-shape')?.value || 'circle';
                var lBorder = document.getElementById('panel-leader-border')?.value || '#FF9933';
                var lSize = document.getElementById('panel-leader-size')?.value || '104';
                var candScale = document.getElementById('panel-candidate-scale')?.value || '100';
                var isSocial = document.getElementById('panel-show-social')?.checked ?? true;
                var candName = document.getElementById('panel-candidate-name')?.value?.trim();
                var candDesig = document.getElementById('panel-candidate-designation')?.value?.trim();
                var candCutout = document.getElementById('panel-cutout-url')?.value;
                var composedPath = document.getElementById('panel-composed-path')?.value || '';

                var payload = {
                    template_id: parseInt(templateId),
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
                    candidate_scale: parseInt(candScale),
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
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { status: res.status, ok: res.ok, data: data };
                    });
                })
                .then(function (result) {
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

                    // Update Card Action to Scheduled badge
                    var cardAction = document.getElementById('card-action-' + templateId);
                    if (cardAction) {
                        cardAction.innerHTML = '<span class="badge bg-success text-white rounded-pill px-3 py-1.5 small fw-semibold"><i class="ti ti-check me-1"></i>Scheduled</span>';
                    }

                    // Update Date Pill
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

                    // Close offcanvas after 400ms
                    setTimeout(function () {
                        var panelEl = document.getElementById('studioSlidePanel');
                        if (panelEl) {
                            var bsOffcanvas = bootstrap.Offcanvas.getInstance(panelEl);
                            if (bsOffcanvas) bsOffcanvas.hide();
                        }
                    }, 400);
                })
                .catch(function (err) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                        alertBox.innerText = 'Network error while scheduling.';
                    }
                    console.error('Schedule error:', err);
                });
            });

            // ======================================================================
            // 7. ACTION ICONS: WHATSAPP, SHARE & COPY CAPTION
            // ======================================================================
            document.addEventListener('click', function (e) {
                if (e.target.closest('#panel-btn-whatsapp')) {
                    var imgEl = document.getElementById('panel-canvas-img');
                    var caption = document.getElementById('panel-caption-text')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;
                    var text = caption + '\n\n' + shareUrl;
                    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
                    return;
                }

                if (e.target.closest('#panel-btn-share')) {
                    var imgEl = document.getElementById('panel-canvas-img');
                    var caption = document.getElementById('panel-caption-text')?.value || '';
                    var shareUrl = imgEl ? imgEl.src : window.location.href;

                    if (navigator.share) {
                        navigator.share({
                            title: 'Festival Greeting Poster',
                            text: caption,
                            url: shareUrl
                        }).catch(function () {});
                    } else {
                        navigator.clipboard.writeText(shareUrl).then(function () {
                            showStudioToast('Poster link copied to clipboard!');
                        }).catch(function () {
                            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(caption + '\n' + shareUrl), '_blank');
                        });
                    }
                }
            });

            // Copy Caption Buttons
            document.addEventListener('click', function (e) {
                var copyBtn = e.target.closest('#btn-copy-caption, #panel-btn-copy-caption');
                if (!copyBtn) return;

                var textToCopy = '';
                if (copyBtn.id === 'panel-btn-copy-caption') {
                    textToCopy = document.getElementById('panel-caption-text')?.value || '';
                } else {
                    var captionEl = document.getElementById('festival-caption-text');
                    textToCopy = captionEl ? captionEl.innerText : '';
                }

                if (!textToCopy) return;

                function showCopySuccess() {
                    var origHtml = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="ti ti-check me-1"></i>Copied!';
                    setTimeout(function () {
                        copyBtn.innerHTML = origHtml;
                    }, 2000);
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(showCopySuccess).catch(function () {
                        fallbackCopy(textToCopy, showCopySuccess);
                    });
                } else {
                    fallbackCopy(textToCopy, showCopySuccess);
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
        });
    </script>
@endpush
