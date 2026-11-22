@extends('admin.layouts.app')

@section('title', 'Festivals & Special Days')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Template Management</div>
            <h2 class="page-title">Festivals & Special Days</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.festivals.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Festival
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
            <form action="{{ route('admin.festivals.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search festival or caption..." value="{{ request('search') }}" style="width: 220px;">
                </div>
                <select name="category" class="form-select" style="width: 180px;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <select name="time" class="form-select" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">All Dates</option>
                    <option value="upcoming" {{ request('time') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="past" {{ request('time') === 'past' ? 'selected' : '' }}>Past</option>
                </select>
                <select name="status" class="form-select" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request('search') || request('category') || request('time') || request('status') !== null)
                    <a href="{{ route('admin.festivals.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <div class="text-muted small">
                Showing {{ $festivals->total() }} festivals
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped table-hover">
            <thead>
                <tr>
                    <th>Festival / Special Day</th>
                    <th style="width: 160px;">Date</th>
                    <th style="width: 170px;">Category</th>
                    <th style="width: 130px;">Templates</th>
                    <th style="width: 110px;">Status</th>
                    <th class="w-1 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($festivals as $festival)
                    @php
                        $festivalDate = \Carbon\Carbon::parse($festival->festival_date);
                        $isToday = $festivalDate->isToday();
                        $isFuture = $festivalDate->isFuture();
                        $daysDiff = (int) now()->startOfDay()->diffInDays($festivalDate->startOfDay(), false);
                    @endphp
                    <tr>
                        <td>
                            <div class="font-weight-medium fs-3 text-dark mb-0.5">{{ $festival->title }}</div>
                            @if($festival->ready_caption)
                                <div class="text-secondary small text-truncate" style="max-width: 380px;" title="{{ $festival->ready_caption }}">
                                    {{ Str::limit($festival->ready_caption, 80) }}
                                </div>
                            @endif
                            @if($festival->hashtags)
                                <div class="text-muted small mt-0.5" style="font-size: 11px;">
                                    {{ Str::limit($festival->hashtags, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $festivalDate->format('d M, Y') }}</div>
                            <div class="small text-muted d-flex align-items-center gap-1">
                                <span>{{ $festivalDate->format('l') }}</span>
                                @if($isToday)
                                    <span class="badge bg-success-lt text-success" style="font-size: 10px;">Today</span>
                                @elseif($isFuture)
                                    <span class="badge bg-blue-lt text-blue" style="font-size: 10px;">In {{ $daysDiff }}d</span>
                                @else
                                    <span class="badge bg-secondary-lt text-secondary" style="font-size: 10px;">Past</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-outline {{ $festival->category_badge_color }} fw-semibold px-2 py-0.5" style="font-size: 11px;">
                                {{ $festival->category_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.festival-templates.index', ['festival_id' => $festival->id]) }}" class="badge badge-outline text-primary text-decoration-none px-2 py-1" title="View Templates for this festival">
                                <i class="ti ti-photo me-1"></i>{{ $festival->templates_count }} {{ Str::plural('Template', $festival->templates_count) }}
                            </a>
                        </td>
                        <td>
                            @if($festival->is_active)
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
                                <a href="{{ route('admin.festivals.edit', $festival) }}" class="btn btn-icon btn-sm btn-ghost-primary" title="Edit Festival" data-bs-toggle="tooltip">
                                    <i class="ti ti-edit fs-2"></i>
                                </a>
                                <form action="{{ route('admin.festivals.destroy', $festival) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this festival? All attached templates will also be removed.');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-sm btn-ghost-danger" title="Delete Festival" data-bs-toggle="tooltip">
                                        <i class="ti ti-trash fs-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                            <h3>No festivals found</h3>
                            <p class="text-secondary small">Click "Add Festival" to schedule a new festival or observance day.</p>
                            <a href="{{ route('admin.festivals.create') }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Festival
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($festivals->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $festivals->links() }}
        </div>
    @endif
</div>
@endsection
