@extends('admin.layouts.app')

@section('title', 'Festival Templates')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Templates</div>
            <h2 class="page-title">Festival Templates</h2>
        </div>
        <div class="col-auto ms-auto d-print-none d-flex gap-2">
            <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-photo-edit me-1"></i> Open Studio
            </a>
            <a href="{{ route('admin.festival-templates.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Festival Template
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-check fs-2 me-2"></i>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header py-3">
        <form action="{{ route('admin.festival-templates.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center w-100">
            {{-- Search Input --}}
            <div class="input-icon" style="min-width: 200px; flex: 1 1 200px;">
                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search template..." value="{{ request('search') }}">
            </div>

            {{-- Month Filter --}}
            <select name="month" class="form-select" style="width: 150px;" onchange="this.form.submit()">
                <option value="">All Months</option>
                <option value="01" {{ request('month') === '01' ? 'selected' : '' }}>01 - January</option>
                <option value="02" {{ request('month') === '02' ? 'selected' : '' }}>02 - February</option>
                <option value="03" {{ request('month') === '03' ? 'selected' : '' }}>03 - March</option>
                <option value="04" {{ request('month') === '04' ? 'selected' : '' }}>04 - April</option>
                <option value="05" {{ request('month') === '05' ? 'selected' : '' }}>05 - May</option>
                <option value="06" {{ request('month') === '06' ? 'selected' : '' }}>06 - June</option>
                <option value="07" {{ request('month') === '07' ? 'selected' : '' }}>07 - July</option>
                <option value="08" {{ request('month') === '08' ? 'selected' : '' }}>08 - August</option>
                <option value="09" {{ request('month') === '09' ? 'selected' : '' }}>09 - September</option>
                <option value="10" {{ request('month') === '10' ? 'selected' : '' }}>10 - October</option>
                <option value="11" {{ request('month') === '11' ? 'selected' : '' }}>11 - November</option>
                <option value="12" {{ request('month') === '12' ? 'selected' : '' }}>12 - December</option>
            </select>

            {{-- Festival Dropdown --}}
            <select name="festival_id" class="form-select" style="width: 190px;" onchange="this.form.submit()">
                <option value="">All Festivals</option>
                @foreach($festivals as $fest)
                    <option value="{{ $fest->id }}" {{ request('festival_id') == $fest->id ? 'selected' : '' }}>
                        {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M') : '' }})
                    </option>
                @endforeach
            </select>

            {{-- Party Dropdown --}}
            <select name="party_id" class="form-select" style="width: 160px;" onchange="this.form.submit()">
                <option value="">All Parties</option>
                @foreach($parties as $party)
                    <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                        {{ $party->short_code }} - {{ $party->name }}
                    </option>
                @endforeach
            </select>

            {{-- Status Dropdown --}}
            <select name="status" class="form-select" style="width: 120px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            {{-- Per Page Dropdown --}}
            <select name="per_page" class="form-select" style="width: 120px;" onchange="this.form.submit()" title="Templates per page">
                <option value="12" {{ request('per_page') == '12' ? 'selected' : '' }}>12 / page</option>
                <option value="24" {{ request('per_page', 24) == 24 ? 'selected' : '' }}>24 / page</option>
                <option value="48" {{ request('per_page') == '48' ? 'selected' : '' }}>48 / page</option>
                <option value="96" {{ request('per_page') == '96' ? 'selected' : '' }}>96 / page</option>
            </select>

            @if(request('search') || request('month') || request('festival_id') || request('party_id') || request('status') !== null || request('per_page'))
                <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="row row-cards">
    @forelse($templates as $tpl)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card h-100 shadow-sm border position-relative">
                <div class="card-img-top position-relative" style="padding-top: 100%; background: #0f172a; overflow: hidden; border-radius: 4px 4px 0 0;">
                    <img src="{{ $tpl->image_url }}" alt="{{ $tpl->title }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                        @if($tpl->party)
                            <span class="badge text-white" style="background-color: {{ $tpl->party->primary_color }};">
                                {{ $tpl->party->short_code }}
                            </span>
                        @else
                            <span class="badge bg-dark-lt text-white">All Parties</span>
                        @endif
                        @if($tpl->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="text-muted small mb-1">
                        <i class="ti ti-calendar-event me-1"></i> {{ $tpl->festival?->title ?? 'General Festival' }}
                    </div>
                    <h4 class="card-title mb-2 text-truncate" title="{{ $tpl->title }}">
                        {{ $tpl->title }}
                    </h4>
                    <div class="d-flex align-items-center gap-1 mb-3">
                        <span class="badge bg-blue-lt small text-truncate" style="max-width: 140px;">{{ $tpl->preset_key ?? 'classic_political_right' }}</span>
                        <span class="badge bg-purple-lt small">{{ $tpl->photo_position ?? 'bottom-right' }}</span>
                    </div>
                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-sm btn-outline-secondary" title="Preview / Test">
                            <i class="ti ti-eye me-1"></i> Preview
                        </a>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.festival-templates.edit', $tpl) }}" class="btn btn-icon btn-sm btn-ghost-primary" title="Edit Template" data-bs-toggle="tooltip">
                                <i class="ti ti-edit fs-2"></i>
                            </a>
                            <form action="{{ route('admin.festival-templates.destroy', $tpl) }}" method="POST" onsubmit="return confirm('Delete this template?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger" title="Delete Template" data-bs-toggle="tooltip">
                                    <i class="ti ti-trash fs-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card py-5 text-center text-muted">
                <i class="ti ti-photo-off fs-1 d-block mb-2"></i>
                <h3>No festival templates found</h3>
                <p>Add new 1080x1080 graphic templates for upcoming festivals and campaigns.</p>
                <div class="mt-2">
                    <a href="{{ route('admin.festival-templates.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add Template
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination & Count Footer --}}
<div class="card mt-4">
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
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
