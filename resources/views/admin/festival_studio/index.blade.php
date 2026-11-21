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

/* Filter Pills */
.fs-pill-filter {
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: var(--tblr-card-bg, #ffffff);
    color: var(--tblr-body-color, #475569);
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.fs-pill-filter:hover {
    border-color: #1db8ab;
    color: #1db8ab;
}
.fs-pill-filter.active {
    background: #1db8ab;
    color: #ffffff !important;
    border-color: #1db8ab;
}
[data-bs-theme="dark"] .fs-pill-filter {
    border-color: rgba(255, 255, 255, 0.1);
}
[data-bs-theme="dark"] .fs-pill-filter.active {
    background: #1db8ab;
    color: #ffffff !important;
    border-color: #1db8ab;
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
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $festival->id, 'party' => $partyFilter]) }}" 
                       class="fs-date-pill {{ $isSelected ? 'active' : '' }}">
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

    {{-- Festival & Gallery Control Bar --}}
    @if($selectedFestival)
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold m-0 text-dark">{{ $selectedFestival->title }}</h3>
                <span class="badge bg-primary-lt rounded-pill">{{ $selectedFestival->festival_date->format('d M Y') }}</span>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Quick Copy Caption Button --}}
                @if(!empty($selectedFestival->ready_caption))
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" id="btn-copy-caption">
                        <i class="ti ti-copy me-1"></i><span id="copy-btn-text">Copy Caption</span>
                    </button>
                    {{-- Hidden text storage for copy --}}
                    <div id="festival-caption-text" class="d-none">{{ $selectedFestival->ready_caption }}{{ !empty($selectedFestival->hashtags) ? "\n\n" . $selectedFestival->hashtags : '' }}</div>
                @endif

                {{-- Filter Pills --}}
                <div class="d-flex gap-1 align-items-center ms-md-2">
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival->id, 'party' => 'all']) }}" 
                       class="fs-pill-filter {{ $partyFilter === 'all' ? 'active' : '' }}">
                        All
                    </a>
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival->id, 'party' => 'bjp']) }}" 
                       class="fs-pill-filter {{ $partyFilter === 'bjp' ? 'active' : '' }}">
                        BJP
                    </a>
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival->id, 'party' => 'inc']) }}" 
                       class="fs-pill-filter {{ $partyFilter === 'inc' ? 'active' : '' }}">
                        Congress
                    </a>
                    <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival->id, 'party' => 'aap']) }}" 
                       class="fs-pill-filter {{ $partyFilter === 'aap' ? 'active' : '' }}">
                        AAP
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Templates Grid with Live User Branding --}}
    @if($templates->isNotEmpty())
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4">
            @foreach($templates as $template)
                @php
                    $scheduleUrl = route('admin.posts.create', [
                        'date' => $selectedFestival?->festival_date?->format('Y-m-d\T08:00'),
                        'caption' => ($selectedFestival?->ready_caption ?? '') . ($selectedFestival?->hashtags ? "\n\n" . $selectedFestival->hashtags : ''),
                    ]);
                @endphp
                <div class="col">
                    <div class="fs-template-card h-100 d-flex flex-column">
                        
                        {{-- 1:1 Aspect Ratio Poster Container --}}
                        <div class="fs-template-preview-wrapper">
                            <img src="{{ $template->image_url }}" alt="{{ $template->title }}" class="fs-template-preview-img" loading="lazy">
                            
                            {{-- Top Party Badge --}}
                            <div class="position-absolute top-0 start-0 m-2.5 d-flex gap-1 z-1">
                                @if($template->party)
                                    <span class="badge bg-dark text-white rounded-pill shadow-xs" style="font-size: 10px; padding: 3px 8px;">
                                        {{ $template->party->short_code }}
                                    </span>
                                @endif
                            </div>

                            {{-- Hover Actions Overlay --}}
                            <div class="fs-template-overlay">
                                <a href="{{ $scheduleUrl }}" class="btn btn-primary rounded-pill px-3 py-2 w-100 shadow-sm text-decoration-none">
                                    <i class="ti ti-wand me-1"></i>Use This Poster
                                </a>
                                <a href="{{ $scheduleUrl }}" class="btn btn-outline-light rounded-pill px-3 py-1.5 w-100 text-decoration-none small">
                                    <i class="ti ti-calendar-time me-1"></i>Schedule for Festival
                                </a>
                            </div>
                        </div>

                        {{-- Minimal Card Footer --}}
                        <div class="p-2.5 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-dark text-truncate me-2" title="{{ $template->title }}">
                                {{ $template->title }}
                            </span>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <a href="{{ $scheduleUrl }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5" title="Use Poster">
                                    Use
                                </a>
                                <a href="{{ $scheduleUrl }}" class="btn btn-xs btn-ghost-secondary rounded-pill px-2" title="Schedule">
                                    <i class="ti ti-calendar"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="border border-dashed rounded-4 p-5 text-center my-4 bg-light-subtle">
            <div class="avatar avatar-md bg-secondary-lt rounded-circle mb-2">
                <i class="ti ti-photo-off text-secondary fs-3"></i>
            </div>
            <div class="fw-semibold text-dark mb-1">No templates found for this filter</div>
            <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'all']) }}" class="btn btn-xs btn-outline-secondary rounded-pill mt-2">
                Show All Designs
            </a>
        </div>
    @endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var copyBtn = document.getElementById('btn-copy-caption');
    var copyText = document.getElementById('copy-btn-text');
    var captionEl = document.getElementById('festival-caption-text');

    if (copyBtn && captionEl) {
        copyBtn.addEventListener('click', function () {
            var fullText = captionEl.innerText;
            navigator.clipboard.writeText(fullText).then(function () {
                copyText.innerText = 'Copied!';
                copyBtn.classList.replace('btn-outline-secondary', 'btn-success');
                setTimeout(function () {
                    copyText.innerText = 'Copy Caption';
                    copyBtn.classList.replace('btn-success', 'btn-outline-secondary');
                }, 2000);
            }).catch(function () {
                var ta = document.createElement('textarea');
                ta.value = fullText;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                copyText.innerText = 'Copied!';
                setTimeout(function () {
                    copyText.innerText = 'Copy Caption';
                }, 2000);
            });
        });
    }
});
</script>
@endpush
