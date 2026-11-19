@extends('admin.layouts.app')

@section('title', 'Content Calendar & Schedule Posts')

@push('styles')
<style>
    /* FullCalendar Tabler Integration Styling */
    .fc {
        font-family: inherit;
    }
    .fc-theme-standard th {
        background-color: #f8fafc;
        border-color: rgba(4, 32, 69, 0.08);
        padding: 0.65rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .fc-theme-standard td, .fc-theme-standard .fc-scrollgrid {
        border-color: rgba(4, 32, 69, 0.08);
    }
    .fc-daygrid-day-number {
        font-size: 0.825rem;
        font-weight: 600;
        color: #334155;
        padding: 6px 8px !important;
    }
    .fc-day-today {
        background: rgba(29, 184, 171, 0.06) !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        color: #1db8ab;
        font-weight: 700;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 6px !important;
        padding: 2px 5px !important;
        margin-top: 2px !important;
        margin-bottom: 3px !important;
        font-size: 0.76rem !important;
        font-weight: 500 !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
        border-width: 1px !important;
        border-style: solid !important;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.08) !important;
        filter: brightness(1.02);
    }

    /* Status-based Calendar Event Styling */
    .fc-event-published {
        background-color: #ecfdf5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
    }
    .fc-event-published:hover {
        background-color: #d1fae5 !important;
        border-color: #6ee7b7 !important;
    }

    .fc-event-scheduled {
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #1e40af !important;
    }
    .fc-event-scheduled:hover {
        background-color: #dbeafe !important;
        border-color: #93c5fd !important;
    }

    .fc-event-failed {
        background-color: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #991b1b !important;
    }
    .fc-event-failed:hover {
        background-color: #fee2e2 !important;
        border-color: #f87171 !important;
    }

    .fc-event-publishing {
        background-color: #fffbeb !important;
        border-color: #fde68a !important;
        color: #92400e !important;
    }

    .fc-event-draft {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
        border-style: dashed !important;
        color: #475569 !important;
    }
    .fc-toolbar {
        margin-bottom: 1.25rem !important;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #0f172a;
    }
    .fc-button-primary {
        background-color: #ffffff !important;
        border-color: rgba(4, 32, 69, 0.12) !important;
        color: #334155 !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        padding: 0.35rem 0.75rem !important;
        border-radius: 6px !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .fc-button-primary:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .fc-button-primary:focus, .fc-button-primary:active, .fc-button-primary.fc-button-active {
        background-color: #1db8ab !important;
        border-color: #1db8ab !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }
    .post-type-pill {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.12);
        padding: 0.5rem 0.85rem;
        border-radius: 6px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 500;
        background: #ffffff;
    }
    .post-type-pill.active {
        background: #1db8ab;
        color: #ffffff;
        border-color: #1db8ab;
    }
    .composer-platform-btn {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.12);
        border-radius: 8px;
        padding: 0.65rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        background: #ffffff;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex: 1;
        transition: all 0.15s ease;
    }
    .composer-platform-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .composer-platform-btn.active.platform-fb {
        background: #1877f2;
        color: #ffffff;
        border-color: #1877f2;
        box-shadow: 0 2px 4px rgba(24, 119, 242, 0.2);
    }
    .composer-platform-btn.active.platform-ig {
        background: #e1306c;
        color: #ffffff;
        border-color: #e1306c;
        box-shadow: 0 2px 4px rgba(225, 48, 108, 0.2);
    }
    .composer-platform-btn.active.platform-yt {
        background: #e02424;
        color: #ffffff;
        border-color: #e02424;
        box-shadow: 0 2px 4px rgba(224, 36, 36, 0.2);
    }

    /* FullCalendar Dark Theme Overrides */
    [data-bs-theme="dark"] .fc-theme-standard th {
        background-color: #0e1626 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #8896ab !important;
    }
    [data-bs-theme="dark"] .fc-theme-standard td,
    [data-bs-theme="dark"] .fc-theme-standard .fc-scrollgrid {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .fc-daygrid-day-number {
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .fc-day-today {
        background: rgba(29, 184, 171, 0.12) !important;
    }
    [data-bs-theme="dark"] .fc-toolbar-title {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .fc-button-primary {
        background-color: #111a2d !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .fc-button-primary:hover {
        background-color: #162035 !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .fc-button-primary:focus,
    [data-bs-theme="dark"] .fc-button-primary:active,
    [data-bs-theme="dark"] .fc-button-primary.fc-button-active {
        background-color: #1db8ab !important;
        border-color: #1db8ab !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .post-type-pill {
        background: #111a2d !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .post-type-pill:hover {
        background: #162035 !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .composer-platform-btn {
        background: #111a2d !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .composer-platform-btn:hover {
        background: #162035 !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
    }
    [data-bs-theme="dark"] .fc-popover {
        background: #111a2d !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .fc-popover-header {
        background: #0e1626 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
</style>
@endpush

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Content Calendar</h2>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                {{-- Modern Segmented View Switcher (Bigger & Higher-Contrast SaaS Design) --}}
                <div class="btn-group p-1 bg-white border rounded shadow-none align-items-center" role="group" aria-label="View switch" style="height: 40px;">
                    <a href="{{ route('admin.posts.schedule', ['view' => 'calendar', 'account_id' => $selectedAccountId, 'platform' => $selectedPlatform]) }}"
                       class="btn {{ $viewMode === 'calendar' ? 'btn-primary fw-semibold shadow-sm' : 'btn-ghost-secondary text-secondary' }}"
                       style="border-radius: 6px; padding: 0.35rem 0.9rem; font-size: 0.875rem;">
                        <i class="ti ti-calendar me-1.5" style="font-size: 1.05rem;"></i>Calendar View
                    </a>
                    <a href="{{ route('admin.posts.schedule', ['view' => 'list', 'account_id' => $selectedAccountId, 'platform' => $selectedPlatform]) }}"
                       class="btn {{ $viewMode === 'list' ? 'btn-primary fw-semibold shadow-sm' : 'btn-ghost-secondary text-secondary' }}"
                       style="border-radius: 6px; padding: 0.35rem 0.9rem; font-size: 0.875rem;">
                        <i class="ti ti-list-details me-1.5" style="font-size: 1.05rem;"></i>Upcoming List
                    </a>
                </div>

                {{-- + Add Post (Dedicated Page) --}}
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary d-inline-flex align-items-center px-3" style="height: 40px; font-weight: 600;">
                    <i class="ti ti-plus me-1.5"></i>Add Post
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-check fs-2 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle fs-2 me-2"></i>
                <div>{{ session('warning') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    {{-- Stats Cards Row --}}
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded bg-azure-lt">
                                <i class="ti ti-clock fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-secondary">Scheduled Posts</div>
                            <div class="h2 mb-0">{{ (int) ($stats->scheduled_count ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded bg-green-lt">
                                <i class="ti ti-check fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-secondary">Published</div>
                            <div class="h2 mb-0">{{ (int) ($stats->published_count ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded bg-danger-lt">
                                <i class="ti ti-alert-circle fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-secondary">Failed / Attention</div>
                            <div class="h2 mb-0">{{ (int) ($stats->failed_count ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded bg-purple-lt">
                                <i class="ti ti-calendar-event fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-secondary">Total Scheduled</div>
                            <div class="h2 mb-0">{{ (int) ($stats->total ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container: Calendar View OR List View --}}
    @if($viewMode === 'calendar')
        {{-- 1. Full Monthly Calendar View --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h3 class="card-title m-0">Monthly Publishing Schedule</h3>
                    <div class="d-flex align-items-center gap-2 small">
                        <span class="badge badge-outline text-success py-0.5 px-2"><i class="ti ti-check me-1"></i>Published</span>
                        <span class="badge badge-outline text-azure py-0.5 px-2"><i class="ti ti-clock me-1"></i>Scheduled</span>
                        <span class="badge badge-outline text-danger py-0.5 px-2"><i class="ti ti-alert-triangle me-1"></i>Failed</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <form method="GET" action="{{ route('admin.posts.schedule') }}" class="d-flex align-items-center gap-2 m-0">
                        <input type="hidden" name="view" value="calendar">

                        {{-- Platform Select Filter --}}
                        <select name="platform" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                            <option value="">All Platforms</option>
                            <option value="1" {{ $selectedPlatform === 1 ? 'selected' : '' }}>Facebook</option>
                            <option value="2" {{ $selectedPlatform === 2 ? 'selected' : '' }}>Instagram</option>
                            <option value="3" {{ $selectedPlatform === 3 ? 'selected' : '' }}>YouTube</option>
                        </select>

                        {{-- Channel Filter --}}
                        <select name="account_id" class="form-select" style="min-width: 190px;" onchange="this.form.submit()">
                            <option value="">All Channels ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ $acc->account_type_name }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body p-3">
                <div id="content-calendar" style="min-height: 650px;"></div>
            </div>
        </div>
    @else
        {{-- 2. Upcoming Schedules List / Table View --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h3 class="card-title m-0">Upcoming Scheduled Queue</h3>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <form method="GET" action="{{ route('admin.posts.schedule') }}" class="d-flex align-items-center gap-2 m-0">
                        <input type="hidden" name="view" value="list">

                        {{-- Platform Select Filter --}}
                        <select name="platform" class="form-select" style="width: 170px;" onchange="this.form.submit()">
                            <option value="">All Platforms</option>
                            <option value="1" {{ $selectedPlatform === 1 ? 'selected' : '' }}>Facebook</option>
                            <option value="2" {{ $selectedPlatform === 2 ? 'selected' : '' }}>Instagram</option>
                            <option value="3" {{ $selectedPlatform === 3 ? 'selected' : '' }}>YouTube</option>
                        </select>

                        {{-- Channel Filter --}}
                        <select name="account_id" class="form-select" style="min-width: 190px;" onchange="this.form.submit()">
                            <option value="">All Channels ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ $acc->account_type_name }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if($upcomingPosts->isEmpty())
                <div class="card-body">
                    @include('admin.layouts.partials._table-empty', [
                        'id' => 'no-upcoming-posts',
                        'title' => 'No Upcoming Scheduled Posts'
                    ])
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover schedule-table">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Scheduled At</th>
                                <th style="width: 120px;">Platform</th>
                                <th style="width: 180px;">Channel</th>
                                <th style="width: 110px;">Type</th>
                                <th>Content & Media Preview</th>
                                <th style="width: 110px;">Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingPosts as $post)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $post->scheduled_at->format('d M Y') }}</div>
                                        <small class="text-secondary">{{ $post->scheduled_at->format('h:i A') }}</small>
                                    </td>
                                    <td>{!! $post->platform_badge !!}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-xs rounded-circle me-2 bg-{{ $post->account->platform_color }}-lt">
                                                <i class="{{ $post->account->platform_icon }}"></i>
                                            </span>
                                            <div class="text-truncate" style="max-width: 140px;" title="{{ $post->account->name }}">
                                                {{ $post->account->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{!! $post->post_type_badge !!}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $thumbSrc = $post->thumbnail_path ? \Illuminate\Support\Facades\Storage::url($post->thumbnail_path) : $post->first_media_url;
                                            @endphp
                                            @if($thumbSrc)
                                                <img src="{{ $thumbSrc }}" class="rounded me-2 border flex-shrink-0" style="width: 44px; height: 32px; object-fit: cover;" alt="preview">
                                            @endif
                                            <div class="text-truncate" style="max-width: 320px;">
                                                @if(!empty($post->title))
                                                    <div class="fw-semibold text-dark text-truncate">{{ $post->title }}</div>
                                                @endif
                                                <span class="text-secondary small">{{ \Illuminate\Support\Str::limit($post->description ?: $post->content, 60) ?: '—' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{!! $post->status_badge !!}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            {{-- Edit: Only if post is not published --}}
                                            @if($post->isEditable())
                                                <a href="{{ route('admin.posts.schedule.edit', $post->id) }}" class="btn btn-icon btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Scheduled Post">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endif

                                            {{-- Publish Now: Only for future/scheduled/pending posts that are not yet published --}}
                                            @if($post->canBePublishedNow())
                                                <form method="POST" action="{{ route('admin.posts.schedule.publish-now', $post->id) }}" class="d-inline m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Publish Immediately Now">
                                                         <i class="ti ti-send"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Cancel / Delete: Only if not already published --}}
                                            @if($post->status !== \App\Models\ScheduledPost::STATUS_PUBLISHED)
                                                <form method="POST" action="{{ route('admin.posts.schedule.destroy', $post->id) }}" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to cancel this scheduled post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Cancel Scheduled Post">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small align-self-center fst-italic">Completed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($upcomingPosts->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <p class="m-0 text-secondary small">
                            Showing {{ $upcomingPosts->firstItem() }} to {{ $upcomingPosts->lastItem() }} of {{ $upcomingPosts->total() }} entries
                        </p>
                        {{ $upcomingPosts->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- MODAL 1: Create & Schedule Post (Unified Multi-Platform Composer) --}}
    <div class="modal modal-blur fade" id="modal-create-post" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.posts.schedule.store') }}" enctype="multipart/form-data" id="form-create-post">
                    @csrf
                    <input type="hidden" name="platform" id="selected-platform" value="1">
                    <input type="hidden" name="post_type" id="selected-post-type" value="photo">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-calendar-plus me-1 text-primary"></i>Create & Schedule Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        {{-- 1. Target Platform Selector (Buffer-Style Segments) --}}
                        <div class="mb-3">
                            <label class="form-label required fw-bold text-dark mb-2">Select Target Platform</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="composer-platform-btn platform-fb active" data-platform="1" data-name="Facebook">
                                    <i class="ti ti-brand-facebook fs-2"></i> Facebook
                                </button>
                                <button type="button" class="composer-platform-btn platform-ig" data-platform="2" data-name="Instagram">
                                    <i class="ti ti-brand-instagram fs-2"></i> Instagram
                                </button>
                                <button type="button" class="composer-platform-btn platform-yt" data-platform="3" data-name="YouTube">
                                    <i class="ti ti-brand-youtube fs-2"></i> YouTube
                                </button>
                            </div>
                        </div>

                        {{-- 2. Target Channel / Account Selector (Dynamically Filtered) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required m-0" id="composer-account-label">Select Facebook Page</label>
                                <a href="{{ route('admin.accounts.index') }}" target="_blank" class="text-secondary small">
                                    <i class="ti ti-plus me-1"></i>Connect New Channel
                                </a>
                            </div>
                            <select name="account_id" class="form-select" required id="composer-account-select">
                                <option value="">-- Choose Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" data-type="{{ $acc->account_type }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                        [{{ $acc->account_type_name }}] {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="no-account-warning" class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none small">
                                <i class="ti ti-alert-circle me-1"></i>No connected accounts found for this platform.
                                <a href="{{ route('admin.accounts.index') }}" class="alert-link ms-1">Connect one in Accounts Hub</a>.
                            </div>
                        </div>

                        {{-- 3. Dynamic Platform Sections --}}

                        {{-- 3A: Meta (Facebook & Instagram) Post Types --}}
                        <div id="meta-post-types-section" class="mb-3">
                            <label class="form-label required">Post Type</label>
                            <div class="d-flex flex-wrap gap-2" id="post-type-pills-container">
                                <span class="post-type-pill" data-type="text" id="pill-type-text"><i class="ti ti-typography"></i> Text Post</span>
                                <span class="post-type-pill active" data-type="photo"><i class="ti ti-photo"></i> Single Photo</span>
                                <span class="post-type-pill" data-type="carousel"><i class="ti ti-photo-circle"></i> Carousel / Multi-Photo</span>
                                <span class="post-type-pill" data-type="video"><i class="ti ti-video"></i> Video</span>
                                <span class="post-type-pill" data-type="reel" id="pill-type-reel"><i class="ti ti-player-play"></i> Reel</span>
                            </div>
                        </div>

                        {{-- 3B: Meta Message / Caption Section --}}
                        <div id="meta-content-section" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label m-0" id="composer-content-label">Post Caption / Message</label>
                                <span class="text-secondary small" id="caption-counter">0 / 5000</span>
                            </div>
                            <textarea name="content" id="composer-content" class="form-control" rows="4" placeholder="What would you like to share with constituents and followers?"></textarea>
                        </div>

                        {{-- 3C: Meta Media Upload Area --}}
                        <div id="meta-media-section" class="mb-3">
                            <label class="form-label" id="media-label">Media Attachment (Photo / Video)</label>
                            <input type="file" name="media_files[]" class="form-control" id="composer-media-files" accept="image/*,video/*" multiple>
                            <small class="form-hint" id="media-hint">Supports JPEG, PNG, WebP, MP4, MOV (Up to 50MB).</small>
                        </div>

                        {{-- 3D: YouTube Dedicated Fields (Title, Description, Video, Thumbnail, Tags, Privacy) --}}
                        <div id="youtube-fields-section" class="d-none">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label required m-0">Video Title</label>
                                    <span class="text-secondary small" id="yt-title-counter">0 / 100</span>
                                </div>
                                <input type="text" name="title" id="youtube-title" class="form-control" placeholder="Enter engaging YouTube video title" maxlength="100">
                                <small class="form-hint">A clear, concise title helps viewers discover your video.</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label m-0">Video Description</label>
                                    <span class="text-secondary small" id="yt-desc-counter">0 / 5000</span>
                                </div>
                                <textarea name="description" id="youtube-description" class="form-control" rows="4" placeholder="Provide an overview of your video, chapters, links, and relevant contact info..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Video File</label>
                                    <input type="file" name="video_file" id="youtube-video-file" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                                    <small class="form-hint">MP4, MOV, or WebM format (Up to 50MB).</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Custom Video Thumbnail</label>
                                    <input type="file" name="thumbnail" id="youtube-thumbnail" class="form-control" accept="image/jpeg,image/png">
                                    <small class="form-hint">1280x720 (16:9 widescreen, JPEG or PNG).</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tags / Keywords</label>
                                    <input type="text" name="tags" id="youtube-tags" class="form-control" placeholder="policy, community, news, updates">
                                    <small class="form-hint">Comma-separated tags for SEO discoverability.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Privacy Status</label>
                                    <select name="privacy_status" class="form-select" id="youtube-privacy-status">
                                        <option value="public" selected>Public (Visible to everyone)</option>
                                        <option value="unlisted">Unlisted (Anyone with the link can view)</option>
                                        <option value="private">Private (Only you can view)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Shared Scheduling Options --}}
                        <div class="card p-3 bg-light-subtle mb-0 border">
                            <label class="form-label required mb-2">Publishing Mode</label>
                            <div class="d-flex align-items-center gap-4 mb-3 flex-wrap">
                                <label class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="publish_mode" value="schedule" checked id="radio-mode-schedule">
                                    <span class="form-check-label fw-medium"><i class="ti ti-clock me-1 text-primary"></i>Schedule for Later</span>
                                </label>
                                <label class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="publish_mode" value="now" id="radio-mode-now">
                                    <span class="form-check-label fw-medium"><i class="ti ti-send me-1 text-success"></i>Publish Immediately Live</span>
                                </label>
                                <label class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="publish_mode" value="draft" id="radio-mode-draft">
                                    <span class="form-check-label fw-medium"><i class="ti ti-file-pencil me-1 text-secondary"></i>Save as Draft</span>
                                </label>
                            </div>

                            <div id="schedule-datetime-container">
                                <label class="form-label required">Select Date & Time</label>
                                <input type="datetime-local" name="scheduled_at" id="composer-scheduled-at" class="form-control"
                                       value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-post">
                            <i class="ti ti-calendar-check me-1"></i>Confirm & Schedule Facebook Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: Event Details Drawer (When clicking any post on Calendar) --}}
    <div class="modal modal-blur fade" id="modal-event-details" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title m-0" id="event-modal-title">Scheduled Post Details</h5>
                        <span id="event-platform-badge"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Channel & Scheduled Time --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm rounded-circle me-2 bg-blue-lt" id="event-channel-icon-wrapper">
                                <i id="event-channel-icon" class="ti ti-brand-facebook"></i>
                            </span>
                            <div>
                                <div class="fw-semibold text-dark" id="event-channel-name">Channel Name</div>
                                <small class="text-secondary" id="event-channel-platform">Facebook Page</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span id="event-status-badge"></span>
                            <div class="text-secondary small mt-1" id="event-scheduled-time">31 Oct, 10:00 AM</div>
                        </div>
                    </div>

                    {{-- YouTube Specific Info Section --}}
                    <div id="event-youtube-section" class="d-none">
                        <h4 class="fw-bold text-dark mb-2" id="event-yt-title">Video Title</h4>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge badge-outline text-purple" id="event-yt-privacy"><i class="ti ti-lock me-1"></i>Public</span>
                            <span class="badge badge-outline text-red"><i class="ti ti-brand-youtube me-1"></i>YouTube Video</span>
                        </div>
                        <div class="mb-3 d-none" id="event-yt-tags-wrapper">
                            <div class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Tags / Keywords</div>
                            <div class="d-flex flex-wrap gap-1" id="event-yt-tags-container"></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Video Description</div>
                            <div class="text-dark fs-3" id="event-yt-description" style="white-space: pre-wrap; line-height: 1.6;"></div>
                        </div>
                    </div>

                    {{-- Meta (Facebook & Instagram) Specific Info Section --}}
                    <div id="event-meta-section">
                        <div class="mb-2">
                            <span class="badge badge-outline text-secondary text-uppercase" id="event-type-badge">Photo</span>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Post Caption</div>
                            <div class="text-dark fs-3" id="event-caption-text" style="white-space: pre-wrap; line-height: 1.6;">Caption content</div>
                        </div>
                    </div>

                    {{-- Media preview --}}
                    <div id="event-media-container" class="mb-3 d-none text-center">
                        <img id="event-media-img" src="" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;" alt="media preview">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    {{-- Cancel Post --}}
                    <form id="event-delete-form" method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel this scheduled post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="ti ti-trash me-1"></i>Cancel Post
                        </button>
                    </form>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-ghost-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- Edit Post --}}
                        <a id="event-edit-btn" href="#" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-edit me-1"></i>Edit Post
                        </a>
                        {{-- Publish Now --}}
                        <form id="event-publish-form" method="POST" action="">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="ti ti-send me-1"></i>Publish Live Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- FullCalendar 6 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Initialize FullCalendar if in calendar view
        let calendarEl = document.getElementById('content-calendar');
        if (calendarEl) {
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                eventDisplay: 'block',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    list: 'List'
                },
                events: {
                    url: '{{ route('admin.posts.schedule.events') }}',
                    extraParams: {
                        account_id: '{{ $selectedAccountId }}',
                        platform: '{{ $selectedPlatform }}'
                    }
                },
                eventClassNames: function(arg) {
                    let status = parseInt(arg.event.extendedProps.status);
                    if (status === 3) return ['fc-event-published'];
                    if (status === 1) return ['fc-event-scheduled'];
                    if (status === 4) return ['fc-event-failed'];
                    if (status === 2) return ['fc-event-publishing'];
                    return ['fc-event-draft'];
                },
                dateClick: function(info) {
                    window.location.href = "{{ route('admin.posts.create') }}?date=" + info.dateStr + "T10:00";
                },
                eventContent: function(arg) {
                    let props = arg.event.extendedProps;
                    let platform = parseInt(props.platform) || 1;
                    let status = parseInt(props.status);
                    let title = arg.event.title;
                    let time = arg.timeText;

                    // Platform Brand Icons & Specific Colors
                    let platformIcon = 'ti ti-brand-facebook';
                    let platformColor = '#1877f2'; // Facebook Blue
                    if (platform === 2) {
                        platformIcon = 'ti ti-brand-instagram';
                        platformColor = '#d62976'; // Instagram Pink
                    } else if (platform === 3) {
                        platformIcon = 'ti ti-brand-youtube';
                        platformColor = '#e02424'; // YouTube Red
                    }

                    // Status Indicator
                    let statusIcon = 'ti ti-clock';
                    let statusColor = '#0284c7';
                    let statusTitle = 'Scheduled';
                    if (status === 3) {
                        statusIcon = 'ti ti-check';
                        statusColor = '#16a34a';
                        statusTitle = 'Uploaded / Published';
                    } else if (status === 4) {
                        statusIcon = 'ti ti-alert-triangle';
                        statusColor = '#dc2626';
                        statusTitle = 'Failed';
                    } else if (status === 2) {
                        statusIcon = 'ti ti-loader';
                        statusColor = '#d97706';
                        statusTitle = 'Publishing...';
                    } else if (status === 0) {
                        statusIcon = 'ti ti-file-pencil';
                        statusColor = '#64748b';
                        statusTitle = 'Draft';
                    }

                    let container = document.createElement('div');
                    container.className = 'fc-event-custom d-flex align-items-center w-100 text-truncate px-1 py-0.5';
                    container.style.cursor = 'pointer';
                    container.style.fontSize = '0.76rem';
                    container.style.fontWeight = '500';

                    // 1. Platform Icon in its official brand color
                    let icon = document.createElement('i');
                    icon.className = platformIcon + ' me-1 flex-shrink-0';
                    icon.style.color = platformColor;
                    icon.style.fontSize = '0.9rem';
                    container.appendChild(icon);

                    // 2. Scheduled Time
                    if (time) {
                        let timeEl = document.createElement('span');
                        timeEl.className = 'fw-bold me-1 flex-shrink-0 font-monospace';
                        timeEl.style.fontSize = '0.72rem';
                        timeEl.style.opacity = '0.85';
                        timeEl.innerText = time;
                        container.appendChild(timeEl);
                    }

                    // 3. Title text
                    let textEl = document.createElement('span');
                    textEl.className = 'text-truncate flex-grow-1';
                    textEl.innerText = title;
                    container.appendChild(textEl);

                    // 4. Status Indicator icon
                    let sIcon = document.createElement('i');
                    sIcon.className = statusIcon + ' ms-1 flex-shrink-0';
                    sIcon.style.color = statusColor;
                    sIcon.style.fontSize = '0.8rem';
                    sIcon.title = statusTitle;
                    container.appendChild(sIcon);

                    return { domNodes: [container] };
                },
                eventClick: function(info) {
                    let props = info.event.extendedProps;
                    let platform = parseInt(props.platform) || 1;

                    $('#event-modal-title').text('Scheduled Post #' + info.event.id);
                    $('#event-platform-badge').html(props.platform_badge || '');
                    $('#event-channel-name').text(props.account_name || 'Channel');
                    $('#event-channel-platform').text(props.account_type || '');
                    $('#event-channel-icon').attr('class', props.platform_icon || 'ti ti-brand-facebook');
                    $('#event-status-badge').html(props.status_badge || '');
                    $('#event-scheduled-time').text(props.scheduled_at || '');

                    if (platform === 3) { // YouTube
                        $('#event-meta-section').addClass('d-none');
                        $('#event-youtube-section').removeClass('d-none');
                        $('#event-yt-title').text(props.title || 'Untitled Video');
                        $('#event-yt-description').text(props.description || props.content || '— No description provided —');
                        let privacy = props.privacy_status ? props.privacy_status.charAt(0).toUpperCase() + props.privacy_status.slice(1) : 'Public';
                        $('#event-yt-privacy').html('<i class="ti ti-lock me-1"></i>' + privacy);

                        let tagsHtml = '';
                        if (props.tags && Array.isArray(props.tags) && props.tags.length > 0) {
                            props.tags.forEach(function(tag) {
                                tagsHtml += '<span class="badge badge-outline text-secondary me-1 mb-1">#' + tag + '</span>';
                            });
                            $('#event-yt-tags-wrapper').removeClass('d-none');
                            $('#event-yt-tags-container').html(tagsHtml);
                        } else {
                            $('#event-yt-tags-wrapper').addClass('d-none');
                            $('#event-yt-tags-container').empty();
                        }
                    } else { // Meta (Facebook / Instagram)
                        $('#event-youtube-section').addClass('d-none');
                        $('#event-meta-section').removeClass('d-none');
                        $('#event-type-badge').text(props.post_type || 'Text');
                        $('#event-caption-text').text(props.content || '— No caption —');
                    }

                    if (props.thumbnail) {
                        $('#event-media-img').attr('src', props.thumbnail);
                        $('#event-media-container').removeClass('d-none');
                    } else {
                        $('#event-media-container').addClass('d-none');
                    }

                    // Only show "Publish Live Now" for future / scheduled / pending posts that are not yet published
                    if (props.can_publish_now) {
                        $('#event-publish-form').removeClass('d-none').attr('action', props.publish_url);
                    } else {
                        $('#event-publish-form').addClass('d-none');
                    }

                    // Show Edit button only if post is editable (not published/publishing)
                    if (props.is_editable) {
                        $('#event-edit-btn').removeClass('d-none').attr('href', props.edit_url);
                    } else {
                        $('#event-edit-btn').addClass('d-none');
                    }

                    // Hide Cancel button if post has already completed publishing
                    if (props.is_published) {
                        $('#event-delete-form').addClass('d-none');
                    } else {
                        $('#event-delete-form').removeClass('d-none').attr('action', props.delete_url);
                    }

                    let detailsModal = new bootstrap.Modal(document.getElementById('modal-event-details'));
                    detailsModal.show();
                }
            });
            calendar.render();
        }

        // 2. Platform Switcher (Buffer / Hootsuite Style)
        function switchPlatform(platformId) {
            platformId = parseInt(platformId);
            $('#selected-platform').val(platformId);

            // Update button styles
            $('.composer-platform-btn').removeClass('active');
            $('.composer-platform-btn[data-platform="' + platformId + '"]').addClass('active');

            let platformName = platformId === 1 ? 'Facebook' : (platformId === 2 ? 'Instagram' : 'YouTube');
            let entityName = platformId === 3 ? 'Channel' : (platformId === 1 ? 'Page' : 'Business Account');
            $('#composer-account-label').text('Select ' + platformName + ' ' + entityName);

            // Filter account select options
            let visibleCount = 0;
            let currentSelected = $('#composer-account-select').val();
            let isCurrentStillValid = false;
            let firstValidVal = '';

            $('#composer-account-select option').each(function () {
                let optType = $(this).data('type');
                if (!optType) {
                    $(this).show(); // Placeholder option
                } else if (parseInt(optType) === platformId) {
                    $(this).show();
                    visibleCount++;
                    if (!firstValidVal) firstValidVal = $(this).val();
                    if ($(this).val() === currentSelected) isCurrentStillValid = true;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount > 0) {
                $('#no-account-warning').addClass('d-none');
                if (!isCurrentStillValid) {
                    $('#composer-account-select').val(firstValidVal);
                }
            } else {
                $('#no-account-warning').removeClass('d-none');
                $('#composer-account-select').val('');
            }

            // Toggle dynamic form sections
            if (platformId === 3) { // YouTube
                $('#meta-post-types-section').addClass('d-none');
                $('#meta-content-section').addClass('d-none');
                $('#meta-media-section').addClass('d-none');
                $('#youtube-fields-section').removeClass('d-none');
                $('#selected-post-type').val('video');
                $('#youtube-title').prop('required', true);
                $('#youtube-video-file').prop('required', true);
            } else if (platformId === 2) { // Instagram
                $('#youtube-fields-section').addClass('d-none');
                $('#meta-post-types-section').removeClass('d-none');
                $('#meta-content-section').removeClass('d-none');
                $('#meta-media-section').removeClass('d-none');
                $('#pill-type-text').addClass('d-none'); // IG doesn't support text-only
                $('#pill-type-reel').removeClass('d-none');
                $('#youtube-title').prop('required', false);
                $('#youtube-video-file').prop('required', false);

                if ($('#selected-post-type').val() === 'text') {
                    $('.post-type-pill[data-type="photo"]').click();
                }
            } else { // Facebook
                $('#youtube-fields-section').addClass('d-none');
                $('#meta-post-types-section').removeClass('d-none');
                $('#meta-content-section').removeClass('d-none');
                $('#meta-media-section').removeClass('d-none');
                $('#pill-type-text').removeClass('d-none');
                $('#pill-type-reel').removeClass('d-none');
                $('#youtube-title').prop('required', false);
                $('#youtube-video-file').prop('required', false);
            }

            updateSubmitButtonText();
        }

        $('.composer-platform-btn').on('click', function () {
            let pid = $(this).data('platform');
            switchPlatform(pid);
        });

        // Initialize with default platform (Facebook = 1)
        switchPlatform(1);

        function updateSubmitButtonText() {
            let platformId = parseInt($('#selected-platform').val()) || 1;
            let platformName = platformId === 1 ? 'Facebook Post' : (platformId === 2 ? 'Instagram Post' : 'YouTube Video');
            let mode = $('input[name="publish_mode"]:checked').val();
            let submitBtn = $('#btn-submit-post');

            if (mode === 'schedule') {
                submitBtn.html('<i class="ti ti-calendar-check me-1"></i>Confirm & Schedule ' + platformName);
            } else if (mode === 'now') {
                submitBtn.html('<i class="ti ti-send me-1"></i>Publish Immediately to ' + (platformId === 3 ? 'YouTube' : (platformId === 2 ? 'Instagram' : 'Facebook')));
            } else {
                submitBtn.html('<i class="ti ti-file-pencil me-1"></i>Save ' + platformName + ' as Draft');
            }
        }

        // 3. Post Type Pill Selection for Meta
        $('.post-type-pill').on('click', function () {
            $('.post-type-pill').removeClass('active');
            $(this).addClass('active');

            let type = $(this).data('type');
            $('#selected-post-type').val(type);

            if (type === 'text') {
                $('#meta-media-section').addClass('d-none');
            } else {
                $('#meta-media-section').removeClass('d-none');
                if (type === 'video' || type === 'reel') {
                    $('#media-label').text('Upload Video File (MP4, MOV)');
                    $('#media-hint').text('Video for Facebook/Reels up to 50MB.');
                    $('#composer-media-files').attr('accept', 'video/*');
                } else {
                    $('#media-label').text('Upload Photo(s)');
                    $('#media-hint').text('JPEG, PNG, WebP image files.');
                    $('#composer-media-files').attr('accept', 'image/*');
                }
            }
        });

        // 4. Character Counters
        $('#composer-content').on('input', function () {
            let len = $(this).val().length;
            $('#caption-counter').text(len + ' / 5000');
        });

        $('#youtube-title').on('input', function () {
            let len = $(this).val().length;
            $('#yt-title-counter').text(len + ' / 100');
        });

        $('#youtube-description').on('input', function () {
            let len = $(this).val().length;
            $('#yt-desc-counter').text(len + ' / 5000');
        });

        // 5. Radio Mode (Schedule vs Publish Now vs Draft) Toggle
        $('input[name="publish_mode"]').on('change', function () {
            let mode = $(this).val();

            if (mode === 'schedule') {
                $('#schedule-datetime-container').removeClass('d-none');
            } else {
                $('#schedule-datetime-container').addClass('d-none');
            }

            updateSubmitButtonText();
        });

        // Form Submit Loading Feedback
        $('#form-create-post').on('submit', function () {
            let btn = $('#btn-submit-post');
            btn.prop('disabled', true);
            btn.html('<i class="ti ti-loader animate-spin me-1"></i>Processing...');
        });
    });
</script>
@endpush
