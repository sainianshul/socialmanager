@extends('admin.layouts.app')

@section('title', 'Political Parties')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Political Management</div>
            <h2 class="page-title">Political Parties</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.political-parties.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Party
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
            <form action="{{ route('admin.political-parties.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search party or code..." value="{{ request('search') }}" style="width: 250px;">
                </div>
                <select name="status" class="form-select" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('status') !== null)
                    <a href="{{ route('admin.political-parties.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <div class="text-muted small">
                Showing {{ $parties->total() }} political parties
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 80px;">Symbol</th>
                    <th>Party Name</th>
                    <th>Hindi Name</th>
                    <th>Short Code</th>
                    <th>Brand Colors</th>
                    <th>Leaders</th>
                    <th>Templates</th>
                    <th>Header Cutout</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parties as $party)
                    <tr>
                        <td>
                            @if($party->symbol_logo_url)
                                <img src="{{ $party->symbol_logo_url }}" alt="{{ $party->name }}" class="rounded shadow-sm" style="width: 44px; height: 44px; object-fit: contain; background: #fff; padding: 2px;">
                            @else
                                <div class="avatar rounded text-white fw-bold" style="background-color: {{ $party->primary_color }}; width: 44px; height: 44px;">
                                    {{ $party->short_code }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $party->name }}</div>
                        </td>
                        <td class="text-body fw-bold">
                            {{ $party->hindi_name ?? '-' }}
                        </td>
                        <td>
                            <span class="badge bg-blue-lt fw-bold">{{ $party->short_code }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge rounded-circle p-2 border" style="background-color: {{ $party->primary_color }}; width: 22px; height: 22px;" title="Primary: {{ $party->primary_color }}" data-bs-toggle="tooltip"></span>
                                <span class="badge rounded-circle p-2 border" style="background-color: {{ $party->secondary_color ?? '#ffffff' }}; width: 22px; height: 22px;" title="Secondary: {{ $party->secondary_color }}" data-bs-toggle="tooltip"></span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.political-leaders.index', ['party_id' => $party->id]) }}" class="badge bg-azure-lt text-decoration-none">
                                <i class="ti ti-users me-1"></i> {{ $party->leaders_count }} Leaders
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.festival-templates.index', ['party_id' => $party->id]) }}" class="badge bg-teal-lt text-decoration-none">
                                <i class="ti ti-sparkles me-1"></i> {{ $party->templates_count }} Templates
                            </a>
                        </td>
                        <td>
                            @if($party->group_cutout_url)
                                <img src="{{ $party->group_cutout_url }}" alt="Group Cutout" class="rounded border" style="height: 36px; max-width: 140px; object-fit: contain; background: #f8fafc;" title="Top Leader Cutout Available" data-bs-toggle="tooltip">
                            @else
                                <span class="text-muted small">None (Individual chips)</span>
                            @endif
                        </td>
                        <td>
                            @if($party->is_active)
                                <span class="badge bg-success-lt">Active</span>
                            @else
                                <span class="badge bg-secondary-lt">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.political-parties.edit', $party) }}" class="btn btn-icon btn-sm btn-ghost-primary" title="Edit Party" data-bs-toggle="tooltip">
                                    <i class="ti ti-edit fs-2"></i>
                                </a>
                                <form action="{{ route('admin.political-parties.destroy', $party) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this party?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger" title="Delete Party" data-bs-toggle="tooltip">
                                        <i class="ti ti-trash fs-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="ti ti-flag-off fs-1 d-block mb-2"></i>
                            No political parties found. Click "Add Party" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($parties->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $parties->links() }}
        </div>
    @endif
</div>
@endsection
