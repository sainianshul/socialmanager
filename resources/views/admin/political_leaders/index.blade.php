@extends('admin.layouts.app')

@section('title', 'Political Leaders')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Political Leaders</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-leaders.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Leader
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
            <form action="{{ route('admin.political-leaders.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search leader or post..." value="{{ request('search') }}" style="width: 220px;">
                </div>
                <select name="party_id" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">All Parties</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                            {{ $party->short_code }} - {{ $party->name }}
                        </option>
                    @endforeach
                </select>
                @if($states->count() > 0)
                    <select name="state" class="form-select" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">All States</option>
                        @foreach($states as $st)
                            <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="status" class="form-select" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('party_id') || request('state') || request('status') !== null)
                    <a href="{{ route('admin.political-leaders.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <div class="text-muted small">
                Showing {{ $leaders->total() }} leaders
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Leader Name</th>
                    <th>Party</th>
                    <th>Designation</th>
                    <th>State</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaders as $leader)
                    <tr>
                        <td>
                            <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" class="avatar avatar-md rounded-circle border shadow-sm" style="object-fit: cover;">
                        </td>
                        <td>
                            <div class="font-weight-medium fs-3">{{ $leader->name }}</div>
                        </td>
                        <td>
                            @if($leader->party)
                                <span class="badge badge-outline text-primary fw-bold">
                                    {{ $leader->party->short_code }}
                                </span>
                                <span class="text-body small ms-1">{{ $leader->party->name }}</span>
                            @else
                                <span class="badge badge-outline text-secondary">None</span>
                            @endif
                        </td>
                        <td class="text-body">
                            {{ $leader->designation ?: '-' }}
                        </td>
                        <td>
                            <span class="badge badge-outline text-azure">{{ $leader->state ?: 'National' }}</span>
                        </td>
                        <td>
                            @if($leader->is_active)
                                <span class="badge badge-outline text-success">
                                    <i class="ti ti-check me-1"></i>Active
                                </span>
                            @else
                                <span class="badge badge-outline text-secondary">
                                    <i class="ti ti-x me-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.political-leaders.edit', $leader) }}" class="btn btn-icon btn-sm btn-ghost-primary" title="Edit Leader" data-bs-toggle="tooltip">
                                    <i class="ti ti-edit fs-2"></i>
                                </a>
                                <form action="{{ route('admin.political-leaders.destroy', $leader) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leader?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger" title="Delete Leader" data-bs-toggle="tooltip">
                                        <i class="ti ti-trash fs-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="ti ti-user-x fs-1 d-block mb-2"></i>
                            No political leaders found. Click "Add Leader" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leaders->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $leaders->links() }}
        </div>
    @endif
</div>
@endsection
