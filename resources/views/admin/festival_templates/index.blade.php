@extends('admin.layouts.app')

@section('title', 'Festival Templates')

@push('styles')
<style>
    /* Premium SaaS Template Gallery Styles */
    .tpl-gallery-card {
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        background: #ffffff;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .tpl-gallery-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.12);
        border-color: #CBD5E1;
    }
    .tpl-img-wrapper {
        position: relative;
        padding-top: 100%;
        background: #0f172a;
        overflow: hidden;
    }
    .tpl-img-wrapper img {
        transition: transform 0.35s ease;
    }
    .tpl-gallery-card:hover .tpl-img-wrapper img {
        transform: scale(1.04);
    }
    .tpl-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 2;
    }
    .tpl-gallery-card:hover .tpl-hover-overlay {
        opacity: 1;
    }
    .tpl-filter-card {
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        background: #ffffff;
        box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.04);
    }
    .tpl-filter-select {
        height: 42px;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        background-color: #FAFAFA;
        font-size: 0.88rem;
        color: #1E293B;
        transition: all 0.2s ease;
    }
    .tpl-filter-select:focus {
        border-color: #0D9488;
        background-color: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        outline: none;
    }
</style>
@endpush

@section('content')

{{-- Hero Header Area --}}
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="text-muted fw-semibold small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.06em; color: #0D9488 !important;">
                Creative Asset Management
            </div>
            <h1 class="page-title fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Festival Templates Gallery
            </h1>
            <div class="text-secondary small">
                High-resolution 1080&times;1080 graphic templates, political branding overlays, and layout presets.
            </div>
        </div>
        <div class="col-auto ms-auto d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 rounded-pill fw-medium d-none d-md-inline-block" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; font-size: 12px;">
                {{ $templates->total() }} Templates Available
            </span>
            <a href="{{ route('admin.festival-templates.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm border-0" style="background: #0D9488; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);">
                <i class="ti ti-plus me-1.5"></i> Add Template
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible rounded-4 mb-4 border-0 shadow-sm" role="alert" style="background: #ECFDF5; color: #065F46;">
        <div class="d-flex align-items-center">
            <i class="ti ti-check fs-2 me-2 text-success"></i>
            <div class="fw-medium">{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

{{-- Spacious Unified Filter Toolbar --}}
<div class="card tpl-filter-card mb-4">
    <div class="card-body p-3 p-lg-4">
        <form action="{{ route('admin.festival-templates.index') }}" method="GET" class="row g-2.5 align-items-center">
            
            {{-- Search Bar --}}
            <div class="col-12 col-md-4 col-xl-3">
                <div class="input-icon">
                    <span class="input-icon-addon text-muted"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control tpl-filter-select" placeholder="Search templates or festivals..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Quick Date Range Filter --}}
            <div class="col-6 col-md-2 col-xl-2">
                <select name="date_filter" class="form-select tpl-filter-select" onchange="this.form.submit()">
                    <option value="">All Dates</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="tomorrow" {{ request('date_filter') === 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                    <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>

            {{-- Festival Dropdown --}}
            <div class="col-6 col-md-3 col-xl-2">
                <select name="festival_id" class="form-select tpl-filter-select" onchange="this.form.submit()">
                    <option value="">All Festivals</option>
                    @foreach($festivals as $fest)
                        <option value="{{ $fest->id }}" {{ request('festival_id') == $fest->id ? 'selected' : '' }}>
                            {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M') : '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Political Party --}}
            <div class="col-6 col-md-3 col-xl-2">
                <select name="party_id" class="form-select tpl-filter-select" onchange="this.form.submit()">
                    <option value="">All Parties</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                            {{ $party->short_code }} - {{ $party->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="col-6 col-md-2 col-xl-1">
                <select name="status" class="form-select tpl-filter-select" onchange="this.form.submit()">
                    <option value="">Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Per Page Dropdown --}}
            <div class="col-6 col-md-2 col-xl-1">
                <select name="per_page" class="form-select tpl-filter-select" onchange="this.form.submit()" title="Templates per page">
                    <option value="12" {{ request('per_page') == '12' ? 'selected' : '' }}>12 / page</option>
                    <option value="24" {{ request('per_page', 24) == 24 ? 'selected' : '' }}>24 / page</option>
                    <option value="48" {{ request('per_page') == '48' ? 'selected' : '' }}>48 / page</option>
                    <option value="96" {{ request('per_page') == '96' ? 'selected' : '' }}>96 / page</option>
                </select>
            </div>

            {{-- Clear Filters Button --}}
            @if(request('search') || request('date_filter') || request('month') || request('festival_id') || request('party_id') || request('status') !== null || request('per_page'))
                <div class="col-auto">
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-medium shadow-none" style="font-size: 13px; border-color: #E2E8F0; color: #475569;">
                        <i class="ti ti-x me-1"></i> Clear
                    </a>
                </div>
            @endif

        </form>
    </div>
</div>

{{-- Modern Card Grid --}}
<div class="row g-4">
    @forelse($templates as $tpl)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card h-100 tpl-gallery-card">
                
                {{-- 1:1 Aspect Ratio Artwork Container --}}
                <div class="tpl-img-wrapper">
                    <img src="{{ $tpl->image_url }}" alt="{{ $tpl->title }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                    
                    {{-- Hover Preview Button Overlay --}}
                    <div class="tpl-hover-overlay">
                        <a href="{{ $tpl->image_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-semibold shadow" style="font-size: 12px; color: #0F172A;">
                            <i class="ti ti-maximize me-1 text-primary"></i> Preview (HD)
                        </a>
                    </div>

                    {{-- Top Left Party Emblem Pill --}}
                    <div class="position-absolute top-0 start-0 m-2.5 z-1">
                        @if($tpl->party)
                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-white shadow-sm" style="background-color: {{ $tpl->party->primary_color }}; font-size: 10px; letter-spacing: 0.04em;">
                                {{ $tpl->party->short_code }}
                            </span>
                        @else
                            <span class="badge rounded-pill px-2.5 py-1 fw-medium shadow-sm" style="background: rgba(15, 23, 42, 0.7); color: #fff; backdrop-filter: blur(6px); font-size: 10px;">
                                Universal
                            </span>
                        @endif
                    </div>

                    {{-- Top Right Status Pill --}}
                    <div class="position-absolute top-0 end-0 m-2.5 z-1">
                        @if($tpl->is_active)
                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 shadow-sm" style="font-size: 10px;">
                                Active
                            </span>
                        @else
                            <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 shadow-sm" style="font-size: 10px;">
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Bottom Left Festive Date Badge --}}
                    @if($tpl->festival?->festival_date)
                        <div class="position-absolute bottom-0 start-0 m-2.5 z-1">
                            <span class="badge rounded-pill px-2.5 py-1 fw-medium" style="background: rgba(15, 23, 42, 0.75); color: #ffffff; backdrop-filter: blur(8px); font-size: 10.5px;">
                                <i class="ti ti-calendar me-1 text-warning"></i>{{ $tpl->festival->festival_date->format('d M') }}
                            </span>
                        </div>
                    @endif

                </div>

                {{-- Card Content --}}
                <div class="card-body p-3.5 d-flex flex-column">
                    <div class="text-uppercase fw-semibold mb-1 text-truncate" style="color: #0D9488; font-size: 11px; letter-spacing: 0.05em;">
                        {{ $tpl->festival?->title ?? 'General Festive' }}
                    </div>
                    <h4 class="card-title fw-bold text-dark mb-2 text-truncate" style="font-size: 14.5px; line-height: 1.35;" title="{{ $tpl->title }}">
                        {{ $tpl->title }}
                    </h4>

                    @php
                        $styleKey = $tpl->layout_config['footer']['style'] ?? ($tpl->preset_key ?? 'dual_ribbon_badge');
                        $cleanStyle = ucwords(str_replace(['_badge', '_banner', '_dock', '_blade', '_heritage'], '', str_replace('_', ' ', $styleKey)));
                        $hasCustomStrip = !empty($tpl->layout_config['header']['strip_id']);
                        $isFixed = !empty($tpl->layout_config['footer']['is_fixed']);
                    @endphp

                    <div class="d-flex align-items-center flex-wrap gap-1 mb-3">
                        <span class="badge rounded-pill fw-medium" style="background: #F1F5F9; color: #475569; font-size: 10.5px;" title="Footer Ribbon Style">
                            {{ $cleanStyle }}
                        </span>
                        @if($hasCustomStrip)
                            <span class="badge rounded-pill fw-medium" style="background: #FEF3C7; color: #92400E; font-size: 10.5px;" title="Assigned Custom Leadership Strip">
                                Strip Assigned
                            </span>
                        @endif
                        @if($isFixed)
                            <span class="badge rounded-pill fw-medium" style="background: #FEE2E2; color: #991B1B; font-size: 10.5px;" title="Footer Layout Locked by Admin">
                                <i class="ti ti-lock me-0.5"></i>Locked
                            </span>
                        @endif
                    </div>

                    {{-- Card Footer Action Bar --}}
                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2.5 border-top" style="border-color: #F1F5F9 !important;">
                        <a href="{{ $tpl->image_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-medium shadow-none" style="font-size: 12px; border-color: #E2E8F0; color: #475569;">
                            <i class="ti ti-eye me-1"></i> Preview
                        </a>
                        <div class="d-flex align-items-center gap-1.5">
                            <a href="{{ route('admin.festival-templates.edit', $tpl) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 fw-medium shadow-none" style="font-size: 12px; color: #334155; border: 1px solid #E2E8F0;">
                                <i class="ti ti-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('admin.festival-templates.destroy', $tpl) }}" method="POST" onsubmit="return confirm('Delete this template?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger rounded-circle" title="Delete Template" data-bs-toggle="tooltip">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card py-5 text-center text-muted rounded-4 border" style="background: #FAFAFA; border-color: #E2E8F0 !important;">
                <i class="ti ti-photo-off fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h3 class="fw-bold text-dark mb-1">No festival templates found</h3>
                <p class="text-secondary small mb-3">Add high-resolution 1080&times;1080 graphic templates for upcoming festivals and campaigns.</p>
                <div>
                    <a href="{{ route('admin.festival-templates.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm border-0" style="background: #0D9488;">
                        <i class="ti ti-plus me-1.5"></i> Add First Template
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination & Count Footer --}}
<div class="card mt-4 rounded-4 border-0 shadow-sm" style="background: #ffffff; border: 1px solid #E2E8F0 !important;">
    <div class="card-footer bg-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 px-4 rounded-4">
        <div class="text-secondary small">
            Showing <strong>{{ $templates->firstItem() ?? 0 }}</strong> to <strong>{{ $templates->lastItem() ?? 0 }}</strong> of <strong>{{ $templates->total() }}</strong> templates
        </div>
        @if($templates->hasPages())
            <div>
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
