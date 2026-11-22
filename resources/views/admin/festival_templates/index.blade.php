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
            <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-outline-teal">
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
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
            <form action="{{ route('admin.festival-templates.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search template..." value="{{ request('search') }}" style="width: 220px;">
                </div>
                <select name="festival_id" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">All Festivals</option>
                    @foreach($festivals as $fest)
                        <option value="{{ $fest->id }}" {{ request('festival_id') == $fest->id ? 'selected' : '' }}>
                            {{ $fest->title }} ({{ $fest->festival_date ? $fest->festival_date->format('d M') : '' }})
                        </option>
                    @endforeach
                </select>
                <select name="party_id" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">All Parties</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                            {{ $party->short_code }} - {{ $party->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="form-select" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('festival_id') || request('party_id') || request('status') !== null)
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <div class="text-muted small">
                Showing {{ $templates->total() }} templates
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    @forelse($templates as $tpl)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card h-100 shadow-sm border-0 position-relative">
                <div class="card-img-top position-relative" style="padding-top: 100%; background: #0f172a; overflow: hidden; border-radius: 8px 8px 0 0;">
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
                        <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-sm btn-outline-teal" title="Preview / Test">
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

@if($templates->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $templates->links() }}
    </div>
@endif
@endsection
