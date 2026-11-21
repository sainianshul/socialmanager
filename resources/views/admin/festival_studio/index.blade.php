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
                    @endphp
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $festival->id]) }}" 
                       class="fs-date-pill {{ $isSelected ? 'active' : '' }}"
                       data-festival-id="{{ $festival->id }}">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.04em;">
                                {{ $festival->festival_date->format('D') }}
                            </span>
                            @if($isToday)
                                <span class="badge bg-danger text-white rounded-pill" style="font-size: 9px; padding: 2px 7px;">Today</span>
                            @elseif($daysAway === 1)
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9px; padding: 2px 7px;">Tomorrow</span>
                            @elseif($daysAway > 1 && $daysAway <= 7)
                                <span class="badge bg-primary-lt rounded-pill" style="font-size: 9px; padding: 2px 7px;">in {{ $daysAway }}d</span>
                            @endif
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contentArea = document.getElementById('festival-content-area');
    var activeAbortController = null;

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
});
</script>
@endpush
