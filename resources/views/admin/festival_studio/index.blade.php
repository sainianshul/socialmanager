@extends('admin.layouts.app')

@section('title', 'Festival Studio - Auto Poster & Scheduler')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => url('/admin'), 'label' => 'Dashboard'],
        ['label' => 'Festival Studio'],
    ]" />
@endsection

@section('content')

    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Automated Poster Maker</div>
                <h2 class="page-title d-flex align-items-center gap-2">
                    <i class="ti ti-sparkles text-warning fs-1"></i>
                    Festival Studio
                </h2>
                <p class="text-secondary mb-0 mt-1">
                    Upcoming festivals, national days & jayanti posters auto-branded with your photo, party symbol and designation.
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>New Post Composer
                </a>
            </div>
        </div>
    </div>

    {{-- Upcoming Festivals Date Bar --}}
    @if($upcomingFestivals->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase">
                        <i class="ti ti-calendar-event me-1 text-primary"></i>Upcoming Festivals & Special Days
                    </span>
                    <span class="badge bg-primary-lt">{{ $upcomingFestivals->count() }} Upcoming</span>
                </div>
                
                <div class="d-flex gap-2 overflow-auto py-1" style="scrollbar-width: thin;">
                    @foreach($upcomingFestivals as $festival)
                        @php
                            $isSelected = $selectedFestival && $selectedFestival->id === $festival->id;
                            $isToday = $festival->festival_date->isToday();
                            $daysAway = now()->startOfDay()->diffInDays($festival->festival_date->startOfDay(), false);
                        @endphp
                        <a href="{{ route('admin.festival-studio.index', ['festival_id' => $festival->id]) }}" 
                           class="btn {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }} text-start flex-shrink-0 px-3 py-2 text-decoration-none"
                           style="min-width: 170px;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span class="fw-bold fs-5">{{ $festival->festival_date->format('d M') }}</span>
                                @if($isToday)
                                    <span class="badge bg-danger text-white">Today</span>
                                @elseif($daysAway === 1)
                                    <span class="badge bg-warning text-dark">Tomorrow</span>
                                @elseif($daysAway > 1 && $daysAway <= 7)
                                    <span class="badge bg-info-lt">{{ $daysAway }} days</span>
                                @endif
                            </div>
                            <div class="small text-truncate mt-1" style="max-width: 150px;" title="{{ $festival->title }}">
                                {{ $festival->title }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Main Workspace Card --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">
                @if($selectedFestival)
                    <span>{{ $selectedFestival->title }}</span>
                    <span class="text-secondary fw-normal fs-5 ms-2">({{ $selectedFestival->festival_date->format('l, d F Y') }})</span>
                @else
                    <span>Festival Posters</span>
                @endif
            </h3>
            @if($selectedFestival)
                <span class="badge bg-secondary-lt text-uppercase">{{ str_replace('_', ' ', $selectedFestival->category) }}</span>
            @endif
        </div>

        <div class="card-body">
            @if($selectedFestival && !empty($selectedFestival->ready_caption))
                <div class="alert alert-info bg-info-lt mb-4">
                    <div class="fw-bold mb-1">
                        <i class="ti ti-message-2 me-1"></i>Ready-made Caption / Shubhkaamna Sandesh:
                    </div>
                    <div class="text-body" style="white-space: pre-line;">{{ $selectedFestival->ready_caption }}</div>
                    @if(!empty($selectedFestival->hashtags))
                        <div class="text-primary mt-2 fw-semibold small">{{ $selectedFestival->hashtags }}</div>
                    @endif
                </div>
            @endif

            {{-- Empty Template Placeholder for Now --}}
            <div class="border border-dashed rounded-3 p-5 text-center my-3">
                <div class="avatar avatar-xl bg-primary-lt rounded-circle mb-3">
                    <i class="ti ti-layout-grid text-primary fs-1"></i>
                </div>
                <h3 class="fw-bold">Ready Templates Loading Soon</h3>
                <p class="text-secondary mx-auto mb-4" style="max-width: 520px;">
                    This festival is scheduled for <strong>{{ $selectedFestival?->festival_date->format('d M Y') }}</strong>. 
                    Blank poster frames and auto-branded layouts will appear here for 1-click publishing and automated festival scheduling.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Create Manual Post
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
