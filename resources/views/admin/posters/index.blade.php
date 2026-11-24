@extends('admin.layouts.app')

@section('title', 'Readymade Posters')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Readymade Posters</div>
            <h2 class="page-title">All Readymade Posters</h2>
        </div>
        <div class="col-auto ms-auto d-print-none d-flex gap-2">
            <a href="{{ route('admin.poster-categories.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-category me-1"></i> Categories
            </a>
            <a href="{{ route('admin.posters.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Poster
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

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
            <form action="{{ route('admin.posters.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search poster name or caption..." value="{{ request('search') }}" style="width: 220px;">
                </div>

                <select name="category_id" class="form-select" style="width: 190px;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="party_id" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">All Parties & Universal</option>
                    <option value="universal" {{ request('party_id') === 'universal' ? 'selected' : '' }}>Universal Only</option>
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

                @if(request('search') || request('category_id') || request('party_id') || request('status') !== null)
                    <a href="{{ route('admin.posters.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>

            <div class="text-muted small">
                Showing {{ $posters->total() }} posters
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 80px;">Artwork</th>
                    <th>Poster Title</th>
                    <th>Category</th>
                    <th>Target Party</th>
                    <th>Trending / Uses</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posters as $poster)
                    <tr>
                        <td>
                            <div class="position-relative rounded overflow-hidden border shadow-xs" style="width: 64px; height: 64px; background: #0f172a;">
                                <img src="{{ $poster->image_url }}" alt="{{ $poster->name }}" class="w-100 h-100 object-fit-cover">
                                @if($poster->show_on_top)
                                    <span class="position-absolute top-0 end-0 badge bg-danger p-1 rounded-0" title="Pinned to top">
                                        <i class="ti ti-pin" style="font-size: 10px;"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-weight-medium fs-3 text-dark">{{ $poster->name }}</div>
                        </td>
                        <td>
                            <span class="badge border border-primary text-primary bg-transparent fw-bold">
                                {{ $poster->category?->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td>
                            @if($poster->party)
                                <span class="badge border border-azure text-azure bg-transparent fw-bold">
                                    {{ $poster->party->short_code }}
                                </span>
                                <span class="text-secondary small ms-1">{{ $poster->party->name }}</span>
                            @else
                                <span class="badge border border-teal text-teal bg-transparent fw-bold">
                                    <i class="ti ti-world me-1"></i> Universal
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge border border-secondary text-secondary bg-transparent font-monospace">
                                <i class="ti ti-trending-up me-1 text-success"></i>{{ $poster->usage_count }} uses
                            </span>
                            @if($poster->auto_show_date)
                                <div class="small text-muted mt-0.5" style="font-size: 11px;">
                                    <i class="ti ti-calendar me-1"></i>{{ $poster->auto_show_date->format('d M') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-secondary font-monospace">
                            {{ $poster->sort_order }}
                        </td>
                        <td>
                            @if($poster->is_active)
                                <span class="badge border border-success text-success bg-transparent fw-bold">Active</span>
                            @else
                                <span class="badge border border-secondary text-secondary bg-transparent">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list flex-nowrap justify-content-end">
                                <a href="{{ route('admin.posters.edit', $poster) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.posters.destroy', $poster) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this poster artwork?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="ti ti-photo-off fs-1 d-block mb-2 text-secondary"></i>
                            No readymade posters found. <a href="{{ route('admin.posters.create') }}">Add first readymade poster</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posters->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between">
            {{ $posters->links() }}
        </div>
    @endif
</div>
@endsection
