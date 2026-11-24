@extends('admin.layouts.app')

@section('title', 'Poster Categories')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Readymade Posters</div>
            <h2 class="page-title">Poster Categories</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.poster-categories.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Category
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
            <form action="{{ route('admin.poster-categories.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search category name..." value="{{ request('search') }}" style="width: 250px;">
                </div>
                <select name="collection" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="1" {{ request('collection') === '1' ? 'selected' : '' }}>Top Collection</option>
                    <option value="0" {{ request('collection') === '0' ? 'selected' : '' }}>Standard</option>
                </select>
                <select name="status" class="form-select" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('status') !== null || request('collection') !== null)
                    <a href="{{ route('admin.poster-categories.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <div class="text-muted small">
                Showing {{ $categories->total() }} categories
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped table-hover">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Top Collection</th>
                    <th>Total Posters</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="font-weight-medium fs-3 text-dark">{{ $category->name }}</div>
                        </td>
                        <td>
                            @if($category->show_as_collection)
                                <span class="badge border border-purple text-purple bg-transparent fw-bold">
                                    <i class="ti ti-star me-1"></i> Top Collection
                                </span>
                            @else
                                <span class="badge border border-secondary text-secondary bg-transparent">Normal</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.posters.index', ['category_id' => $category->id]) }}" class="badge border border-teal text-teal bg-transparent text-decoration-none fw-bold px-2 py-1">
                                {{ $category->posters_count }} Posters
                            </a>
                        </td>
                        <td class="text-secondary font-monospace">
                            {{ $category->sort_order }}
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge border border-success text-success bg-transparent fw-bold">Active</span>
                            @else
                                <span class="badge border border-secondary text-secondary bg-transparent">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list flex-nowrap justify-content-end">
                                <a href="{{ route('admin.poster-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.poster-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category and all its posters?');">
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
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="ti ti-folder-off fs-1 d-block mb-2 text-secondary"></i>
                            No poster categories found. <a href="{{ route('admin.poster-categories.create') }}">Create first category</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
