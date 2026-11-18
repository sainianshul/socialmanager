@extends('admin.layouts.app')

@section('title', 'Posts')

@section('content')

    {{-- Clean Page Header (Exact Tabler Pattern like Users & Accounts) --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Posts</h2>
            </div>
            <div class="col-auto">
                <form id="sync-posts-form" method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                    <button type="submit" class="btn btn-primary" id="btn-sync-posts">
                        <i class="ti ti-refresh me-1" id="sync-icon"></i>Sync Posts
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
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

    {{-- Main Tabler Card Container --}}
    <div class="card">
        
        {{-- Card Header Toolbar (Standard Tabler Format like Users Index) --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                
                {{-- Left: Search Input --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <form method="GET" action="{{ route('admin.posts.index') }}" class="m-0" id="search-form">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if($selectedAccountId)
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                        @endif
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="width: 250px;" placeholder="Search posts..." />
                        </div>
                    </form>

                    @if(request('q'))
                        <a href="{{ route('admin.posts.index', ['view' => $viewMode, 'account_id' => $selectedAccountId]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Clear Search">
                            <i class="ti ti-x me-1"></i>Clear
                        </a>
                    @endif
                </div>

                {{-- Right: Channel Filter Dropdown, Total Count & View Mode Toggle --}}
                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                    {{-- Channel Filter Dropdown --}}
                    <form id="filter-form" method="GET" action="{{ route('admin.posts.index') }}" class="m-0">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        <select name="account_id" id="account-filter-select" class="form-select" style="width: 220px;" onchange="this.form.submit()">
                            <option value="">All Accounts ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ $acc->account_type_name }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if($selectedAccountId)
                        <a href="{{ route('admin.posts.index', ['view' => $viewMode, 'q' => request('q')]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Reset Account Filter">
                            <i class="ti ti-x me-1"></i>Reset
                        </a>
                    @endif

                    <span class="text-secondary small me-1 d-none d-sm-inline">{{ $posts->total() }} Posts</span>

                    {{-- View Switcher Buttons (Normal Standard Size) --}}
                    <div class="btn-group" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" 
                           class="btn btn-icon {{ $viewMode === 'table' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                           data-bs-toggle="tooltip" title="Table View">
                            <i class="ti ti-list"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'gallery']) }}" 
                           class="btn btn-icon {{ $viewMode === 'gallery' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                           data-bs-toggle="tooltip" title="Grid View">
                            <i class="ti ti-layout-grid"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Content Area --}}
        @if($posts->isEmpty())
            <div class="card-body text-center py-5">
                <div class="avatar avatar-xl rounded-circle bg-primary-lt mx-auto mb-3">
                    <i class="ti ti-article-off fs-1 text-primary"></i>
                </div>
                <h3 class="fw-bold mb-4">No Posts Found</h3>
                <div>
                    <form method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-refresh me-1"></i>Sync Posts from Meta
                        </button>
                    </form>
                </div>
            </div>
        @else

            {{-- 1. TABLE VIEW (Default Standard Tabler Table) --}}
            @if($viewMode === 'table')
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">Media</th>
                                <th>Message</th>
                                <th>Channel</th>
                                <th>Engagement</th>
                                <th>Published</th>
                                <th class="w-1 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    {{-- Media Thumbnail --}}
                                    <td>
                                        @if($post->media_url)
                                            <a href="{{ $post->permalink_url ?: '#' }}" target="_blank">
                                                <span class="avatar avatar-md rounded border" style="background-image: url('{{ $post->media_url }}')"></span>
                                            </a>
                                        @else
                                            <span class="avatar avatar-md rounded bg-secondary-lt text-secondary">
                                                <i class="ti ti-file-text fs-2"></i>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Message Caption --}}
                                    <td>
                                        <div class="fw-medium text-reset" style="max-width: 440px;">
                                            @if($post->message)
                                                <span class="d-inline-block text-truncate" style="max-width: 420px;">
                                                    {{ $post->message }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic">[Media attachment without caption]</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Channel --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $post->account->platform_icon }} text-{{ $post->account->platform_color }} fs-2"></i>
                                            <div>
                                                <div class="fw-medium">{{ $post->account->name }}</div>
                                                <small class="text-secondary">{{ $post->account->account_type_name }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Engagement Counters --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Likes">
                                                <i class="ti ti-thumb-up text-primary me-1"></i>{{ number_format($post->likes_count) }}
                                            </span>
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Comments">
                                                <i class="ti ti-message-circle text-green me-1"></i>{{ number_format($post->comments_count) }}
                                            </span>
                                            @if($post->shares_count > 0)
                                                <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Shares">
                                                    <i class="ti ti-share me-1"></i>{{ number_format($post->shares_count) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Published Date --}}
                                    <td>
                                        <div class="text-secondary small">
                                            {{ $post->published_at ? $post->published_at->format('d M Y, h:i A') : 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $post->published_at ? $post->published_at->diffForHumans() : '' }}
                                        </small>
                                    </td>

                                    {{-- External Link Action --}}
                                    <td class="text-end">
                                        @if($post->permalink_url)
                                            <a href="{{ $post->permalink_url }}" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Open on Meta">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            {{-- 2. GRID / GALLERY VIEW (Exact Tabler Cards Style from Screenshot 2) --}}
            @else
                <div class="card-body p-3">
                    <div class="row row-cards">
                        @foreach($posts as $post)
                            <div class="col-sm-6 col-lg-4">
                                <div class="card card-sm h-100 overflow-hidden">
                                    {{-- Image Header (Edge to edge like Tabler Screenshot 2) --}}
                                    @if($post->media_url)
                                        <a href="{{ $post->permalink_url ?: '#' }}" target="_blank" class="d-block overflow-hidden bg-light">
                                            <img src="{{ $post->media_url }}" class="card-img-top" style="height: 220px; width: 100%; object-fit: cover;" alt="Post image" loading="lazy">
                                        </a>
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-secondary border-bottom" style="height: 180px;">
                                            <i class="ti ti-article fs-1"></i>
                                        </div>
                                    @endif

                                    {{-- Card Body --}}
                                    <div class="card-body d-flex flex-column justify-content-between p-3">
                                        @if($post->message)
                                            <div class="text-secondary small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45; min-height: 2.9em;">
                                                {{ $post->message }}
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic mb-3" style="min-height: 2.9em;">
                                                [Media attachment without text caption]
                                            </div>
                                        @endif

                                        {{-- Author & Engagement Row (Exact Screenshot 2 pattern) --}}
                                        <div class="d-flex align-items-center pt-2 border-top">
                                            @if($post->account->profile_pic_url)
                                                <span class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" style="background-image: url('{{ $post->account->profile_pic_url }}')"></span>
                                            @else
                                                <span class="avatar avatar-sm rounded-circle bg-primary-lt text-primary fw-bold me-2 flex-shrink-0">
                                                    {{ strtoupper(substr($post->account->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div class="lh-sm text-truncate me-2" style="max-width: 140px;">
                                                <div class="fw-semibold text-dark text-truncate small">{{ $post->account->name }}</div>
                                                <div class="text-secondary fs-7">{{ $post->published_at ? $post->published_at->diffForHumans() : 'Recent' }}</div>
                                            </div>
                                            <div class="ms-auto d-flex align-items-center gap-3 text-secondary small flex-shrink-0">
                                                <span title="Comments" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-message-circle me-1 text-muted"></i> {{ number_format($post->comments_count) }}
                                                </span>
                                                <span title="Likes" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-heart text-danger me-1"></i> {{ number_format($post->likes_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @endif

        {{-- Pagination in Card Footer --}}
        @if($posts->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between">
                <span class="text-secondary small">
                    Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts
                </span>
                <div>
                    {{ $posts->links() }}
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
<script>
    $(function () {
        // Tooltip init
        $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });

        // Show spinner on Sync button click
        $('#sync-posts-form').on('submit', function () {
            let btn = $('#btn-sync-posts');
            btn.addClass('disabled').prop('disabled', true);
            $('#sync-icon').addClass('ti-spin');
            btn.contents().filter(function() {
                return this.nodeType === 3;
            }).replaceWith(' Syncing...');
        });
    });
</script>
@endpush
