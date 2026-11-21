@extends('admin.layouts.app')

@section('title', 'Festival Studio - Auto Poster & Scheduler')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => url('/admin'), 'label' => 'Dashboard'],
        ['label' => 'Festival Studio'],
    ]" />
@endsection

@push('styles')
<style>
/* Apple-inspired Minimalist Festival Studio Styles */
.fs-date-scroller {
    display: flex;
    gap: 10px;
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
    min-width: 140px;
    padding: 10px 16px;
    border-radius: 14px;
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
    border-color: var(--tblr-primary, #0054a6);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
.fs-date-pill.active {
    background: #0f172a !important;
    border-color: #0f172a !important;
    color: #ffffff !important;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.25);
}
[data-bs-theme="dark"] .fs-date-pill.active {
    background: #f8fafc !important;
    border-color: #f8fafc !important;
    color: #0f172a !important;
}
.fs-date-pill.active .text-secondary {
    color: rgba(255, 255, 255, 0.7) !important;
}
[data-bs-theme="dark"] .fs-date-pill.active .text-secondary {
    color: rgba(15, 23, 42, 0.7) !important;
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
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    border-color: rgba(0, 0, 0, 0.12);
}
.fs-template-preview-wrapper {
    position: relative;
    width: 100%;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #f1f5f9;
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
    padding: 16px;
}
.fs-template-card:hover .fs-template-overlay {
    opacity: 1;
    pointer-events: auto;
}

.fs-hero-card {
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 18px;
    background: var(--tblr-card-bg, #ffffff);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
}
[data-bs-theme="dark"] .fs-hero-card {
    border-color: rgba(255, 255, 255, 0.06);
}

.fs-caption-box {
    background: rgba(248, 250, 252, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 14px;
    padding: 16px;
    font-size: 0.95rem;
    line-height: 1.6;
}
[data-bs-theme="dark"] .fs-caption-box {
    background: rgba(30, 41, 59, 0.5);
    border-color: rgba(255, 255, 255, 0.06);
}

.fs-pill-filter {
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: var(--tblr-card-bg, #ffffff);
    color: var(--tblr-body-color, #475569);
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.fs-pill-filter:hover {
    border-color: #0f172a;
    color: #0f172a;
}
.fs-pill-filter.active {
    background: #0f172a;
    color: #ffffff !important;
    border-color: #0f172a;
}
[data-bs-theme="dark"] .fs-pill-filter {
    border-color: rgba(255, 255, 255, 0.1);
}
[data-bs-theme="dark"] .fs-pill-filter.active {
    background: #f8fafc;
    color: #0f172a !important;
    border-color: #f8fafc;
}
</style>
@endpush

@section('content')

    {{-- Minimal Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-uppercase tracking-wider text-secondary fw-semibold small mb-1" style="font-size: 11px; letter-spacing: 0.08em;">
                    Auto-Branded Festival Campaigns
                </div>
                <h1 class="page-title fw-bold text-dark d-flex align-items-center gap-2 mb-1" style="font-size: 1.75rem; letter-spacing: -0.02em;">
                    Festival Studio
                </h1>
                <p class="text-secondary mb-0 small" style="max-width: 650px;">
                    Pick an upcoming festival, choose from ready-made authentic designs, and publish or schedule with your branding in 1 click.
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
                    <i class="ti ti-plus me-1.5"></i>Manual Post Composer
                </a>
            </div>
        </div>
    </div>

    {{-- Apple-Style Date Scroller --}}
    @if($upcomingFestivals->isNotEmpty())
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <span class="text-secondary fw-semibold small text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">
                    Upcoming Festivals & Special Days
                </span>
                <span class="text-secondary small">{{ $upcomingFestivals->count() }} special days in calendar</span>
            </div>

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
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">
                                {{ $festival->festival_date->format('D') }}
                            </span>
                            @if($isToday)
                                <span class="badge bg-danger text-white rounded-pill" style="font-size: 9px; padding: 2px 6px;">Today</span>
                            @elseif($daysAway === 1)
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9px; padding: 2px 6px;">Tomorrow</span>
                            @elseif($daysAway > 1 && $daysAway <= 7)
                                <span class="badge bg-primary-lt rounded-pill" style="font-size: 9px; padding: 2px 6px;">in {{ $daysAway }}d</span>
                            @endif
                        </div>
                        <div class="fw-bold fs-3 mb-1 lh-1">
                            {{ $festival->festival_date->format('d M') }}
                        </div>
                        <div class="text-truncate small fw-medium" style="max-width: 120px;" title="{{ $festival->title }}">
                            {{ $festival->title }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Active Festival Hero & Ready Caption Section --}}
    @if($selectedFestival)
        <div class="fs-hero-card p-4 mb-4">
            <div class="row g-4 align-items-start">
                
                {{-- Left: Festival Greetings & Details --}}
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-lt rounded-pill px-2.5 py-1">
                            {{ $selectedFestival->festival_date->format('l, d F Y') }}
                        </span>
                        <span class="badge bg-secondary-lt rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 10px;">
                            {{ str_replace('_', ' ', $selectedFestival->category) }}
                        </span>
                    </div>

                    <h2 class="fw-bold mb-3" style="letter-spacing: -0.01em;">
                        {{ $selectedFestival->title }}
                    </h2>

                    @if(!empty($selectedFestival->ready_caption))
                        <div class="fs-caption-box position-relative">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-secondary fw-semibold small text-uppercase" style="font-size: 11px;">
                                    <i class="ti ti-message-2 me-1"></i>Pre-written Greeting / Shubhkaamna
                                </span>
                                <button type="button" class="btn btn-sm btn-ghost-secondary py-1 px-2.5 rounded-pill" id="btn-copy-caption" title="Copy to clipboard">
                                    <i class="ti ti-copy me-1"></i><span id="copy-btn-text">Copy</span>
                                </button>
                            </div>
                            <div id="festival-caption-text" class="text-body mb-2" style="white-space: pre-line;">{{ $selectedFestival->ready_caption }}</div>
                            @if(!empty($selectedFestival->hashtags))
                                <div class="text-primary small fw-semibold" id="festival-hashtags-text">{{ $selectedFestival->hashtags }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Right: User Branding Preview & Status --}}
                <div class="col-lg-4">
                    <div class="border rounded-3 p-3 bg-light-subtle h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary fw-semibold small text-uppercase" style="font-size: 11px;">
                                <i class="ti ti-id me-1"></i>Active Branding
                            </span>
                            <span class="badge bg-success-lt rounded-pill" style="font-size: 10px;">Auto-applied</span>
                        </div>

                        <div class="d-flex align-items-center gap-2.5 mb-2">
                            <span class="avatar avatar-md rounded-circle border" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Leader') }}&background=0f172a&color=fff&bold=true')"></span>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-secondary small text-truncate">
                                    {{ $userBranding?->designation ?? 'Politician / Leader' }}
                                </div>
                            </div>
                        </div>

                        <div class="small text-secondary mb-3">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Party:</span>
                                <span class="fw-semibold text-dark">{{ $userBranding?->party?->name ?? 'BJP (Bharatiya Janata Party)' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Auto Slots:</span>
                                <span class="fw-semibold text-success">Photo Cutout + Party Symbol</span>
                            </div>
                        </div>

                        @php
                            $composerUrl = route('admin.posts.create', [
                                'date' => $selectedFestival->festival_date->format('Y-m-d\T08:00'),
                                'caption' => $selectedFestival->ready_caption . ($selectedFestival->hashtags ? "\n\n" . $selectedFestival->hashtags : ''),
                            ]);
                        @endphp
                        <a href="{{ $composerUrl }}" class="btn btn-primary w-100 rounded-3 py-2 text-decoration-none">
                            <i class="ti ti-send me-1.5"></i>Use Greeting in Post Composer
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Template Gallery Section --}}
    <div class="mb-4">
        
        {{-- Header & Party Filter Tabs --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h3 class="fw-bold m-0" style="letter-spacing: -0.01em;">Poster Templates</h3>
                <span class="text-secondary small">{{ $templates->count() }} design variants ready for this festival</span>
            </div>

            {{-- Filter Pills --}}
            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'all']) }}" 
                   class="fs-pill-filter {{ $partyFilter === 'all' ? 'active' : '' }}">
                    All Designs
                </a>
                <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'bjp']) }}" 
                   class="fs-pill-filter {{ $partyFilter === 'bjp' ? 'active' : '' }}">
                    BJP Theme
                </a>
                <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'inc']) }}" 
                   class="fs-pill-filter {{ $partyFilter === 'inc' ? 'active' : '' }}">
                    Congress Theme
                </a>
                <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'aap']) }}" 
                   class="fs-pill-filter {{ $partyFilter === 'aap' ? 'active' : '' }}">
                    AAP Theme
                </a>
            </div>
        </div>

        {{-- Templates Grid --}}
        @if($templates->isNotEmpty())
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                @foreach($templates as $template)
                    @php
                        $scheduleUrl = route('admin.posts.create', [
                            'date' => $selectedFestival?->festival_date?->format('Y-m-d\T08:00'),
                            'caption' => ($selectedFestival?->ready_caption ?? '') . ($selectedFestival?->hashtags ? "\n\n" . $selectedFestival->hashtags : ''),
                        ]);
                    @endphp
                    <div class="col">
                        <div class="fs-template-card h-100 d-flex flex-column">
                            
                            {{-- Square Thumbnail Container --}}
                            <div class="fs-template-preview-wrapper">
                                <img src="{{ $template->image_url }}" alt="{{ $template->title }}" class="fs-template-preview-img" loading="lazy">
                                
                                {{-- Badges --}}
                                <div class="position-absolute top-0 start-0 m-2.5 d-flex gap-1">
                                    @if($template->party)
                                        <span class="badge bg-dark text-white rounded-pill shadow-xs" style="font-size: 10px; padding: 4px 8px;">
                                            {{ $template->party->short_code }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark rounded-pill shadow-xs" style="font-size: 10px; padding: 4px 8px;">
                                            Neutral
                                        </span>
                                    @endif
                                </div>

                                <div class="position-absolute top-0 end-0 m-2.5">
                                    <span class="badge bg-black-lt text-white rounded-pill" style="font-size: 10px; padding: 3px 6px;">
                                        1080 &times; 1080
                                    </span>
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

                            {{-- Card Footer --}}
                            <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <div class="fw-semibold text-dark text-truncate mb-1" title="{{ $template->title }}">
                                        {{ $template->title }}
                                    </div>
                                    <div class="text-secondary small d-flex align-items-center justify-content-between">
                                        <span>Slot: Bottom-Right</span>
                                        <span class="text-success"><i class="ti ti-check me-0.5"></i>HD Ready</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                    <a href="{{ $scheduleUrl }}" class="btn btn-sm btn-outline-primary flex-fill rounded-pill">
                                        <i class="ti ti-pencil me-1"></i>Use
                                    </a>
                                    <a href="{{ $scheduleUrl }}" class="btn btn-sm btn-ghost-secondary flex-fill rounded-pill" title="Schedule">
                                        <i class="ti ti-calendar me-1"></i>Schedule
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="border border-dashed rounded-4 p-5 text-center my-3 bg-light-subtle">
                <div class="avatar avatar-lg bg-secondary-lt rounded-circle mb-3">
                    <i class="ti ti-photo-off text-secondary fs-2"></i>
                </div>
                <h4 class="fw-bold mb-1">No templates found for this filter</h4>
                <p class="text-secondary small mb-3">Try selecting "All Designs" to see all available templates for this festival.</p>
                <a href="{{ route('admin.festival-studio.index', ['festival_id' => $selectedFestival?->id, 'party' => 'all']) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    Show All Templates
                </a>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var copyBtn = document.getElementById('btn-copy-caption');
    var copyText = document.getElementById('copy-btn-text');
    var captionEl = document.getElementById('festival-caption-text');
    var hashtagsEl = document.getElementById('festival-hashtags-text');

    if (copyBtn && captionEl) {
        copyBtn.addEventListener('click', function () {
            var fullText = captionEl.innerText;
            if (hashtagsEl) {
                fullText += "\n\n" + hashtagsEl.innerText;
            }

            navigator.clipboard.writeText(fullText).then(function () {
                copyText.innerText = 'Copied!';
                copyBtn.classList.replace('btn-ghost-secondary', 'btn-success');
                setTimeout(function () {
                    copyText.innerText = 'Copy';
                    copyBtn.classList.replace('btn-success', 'btn-ghost-secondary');
                }, 2000);
            }).catch(function () {
                // Fallback
                var ta = document.createElement('textarea');
                ta.value = fullText;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                copyText.innerText = 'Copied!';
                setTimeout(function () {
                    copyText.innerText = 'Copy';
                }, 2000);
            });
        });
    }
});
</script>
@endpush
