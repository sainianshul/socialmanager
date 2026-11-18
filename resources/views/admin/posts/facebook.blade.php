@extends('admin.layouts.app')

@section('title', 'Facebook Posts')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Facebook Posts</h2>
            </div>
            <div class="col-auto">
                <form id="sync-posts-form" method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                    <button type="submit" class="btn btn-primary" id="btn-sync-posts">
                        <i class="ti ti-refresh me-1" id="sync-icon"></i>Sync Facebook Posts
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

    {{-- Metric Stat Cards (Formal & Clean) --}}
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-blue-lt text-blue">
                                <i class="ti ti-brand-facebook fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">PUBLICATIONS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalPosts) }}</div>
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
                            <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                                <i class="ti ti-thumb-up fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">REACTIONS & LIKES</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalLikes) }}</div>
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
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-message-circle fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">COMMENTS</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalComments) }}</div>
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
                            <span class="avatar avatar-md rounded-2 bg-purple-lt text-purple">
                                <i class="ti ti-share fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">SHARES</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalShares) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="card">
        
        {{-- Toolbar: Search (Left), Account Filter & View Switcher (Right) --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                
                {{-- Left: Search Input --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <form method="GET" action="{{ route('admin.posts.facebook') }}" class="m-0">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if($selectedAccountId)
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                        @endif
                        @if(request('date'))
                            <input type="hidden" name="date" value="{{ request('date') }}">
                        @endif
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="width: 240px;" placeholder="Search Facebook posts..." />
                        </div>
                    </form>

                    @if(request('q'))
                        <a href="{{ route('admin.posts.facebook', ['view' => $viewMode, 'account_id' => $selectedAccountId, 'date' => request('date')]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Clear Search">
                            <i class="ti ti-x me-1"></i>Clear
                        </a>
                    @endif
                </div>

                {{-- Right: Account Select Filter, Date Filter, Total Count & View Mode Toggle --}}
                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                    {{-- Facebook Page & Date Filter --}}
                    <form method="GET" action="{{ route('admin.posts.facebook') }}" class="m-0 d-flex align-items-center flex-wrap gap-2">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        <select name="account_id" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                            <option value="">All Facebook Pages ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-icon" style="width: 155px;" data-bs-toggle="tooltip" title="Filter by date">
                            <span class="input-icon-addon"><i class="ti ti-calendar"></i></span>
                            <input type="date" name="date" value="{{ request('date') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                    </form>

                    @if(request('date'))
                        <a href="{{ route('admin.posts.facebook', ['view' => $viewMode, 'q' => request('q'), 'account_id' => $selectedAccountId]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Clear Date Filter">
                            <i class="ti ti-x me-1"></i>{{ request('date') }}
                        </a>
                    @endif

                    @if($selectedAccountId)
                        <a href="{{ route('admin.posts.facebook', ['view' => $viewMode, 'q' => request('q'), 'date' => request('date')]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Reset Account Filter">
                            <i class="ti ti-x me-1"></i>Reset
                        </a>
                    @endif

                    <span class="text-secondary small me-1 d-none d-sm-inline">{{ $posts->total() }} Posts</span>

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

        {{-- Content View --}}
        @if($posts->isEmpty())
            <div class="card-body text-center py-5">
                <div class="avatar avatar-xl rounded-circle bg-blue-lt mx-auto mb-3">
                    <i class="ti ti-brand-facebook fs-1 text-facebook"></i>
                </div>
                <h3 class="fw-bold mb-4">No Facebook Posts Synced</h3>
                <div>
                    @if($accounts->isEmpty())
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary px-4">
                            <i class="ti ti-plug me-1"></i>Connect Facebook Page
                        </a>
                    @else
                        <form method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-refresh me-1"></i>Sync Facebook Posts
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else

            {{-- 1. TABLE VIEW --}}
            @if($viewMode === 'table')
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">Media</th>
                                <th>Message</th>
                                <th>Page</th>
                                <th>Type</th>
                                <th>Engagement</th>
                                <th>Published</th>
                                <th class="w-1 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr>
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
                                    <td>
                                        <div class="fw-medium text-reset" style="max-width: 420px;">
                                            @if($post->message)
                                                <span class="d-inline-block text-truncate" style="max-width: 400px;">
                                                    {{ $post->message }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic">[Media attachment without caption]</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($post->account->profile_pic_url)
                                                <span class="avatar avatar-xs rounded-circle" style="background-image: url('{{ $post->account->profile_pic_url }}')"></span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $post->account->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-lt text-blue text-uppercase">{{ $post->media_type ?: 'Status' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Likes">
                                                <i class="ti ti-thumb-up text-primary me-1"></i>{{ number_format($post->likes_count) }}
                                            </span>
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Comments">
                                                <i class="ti ti-message-circle text-teal me-1"></i>{{ number_format($post->comments_count) }}
                                            </span>
                                            @if($post->shares_count > 0)
                                                <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Shares">
                                                    <i class="ti ti-share text-purple me-1"></i>{{ number_format($post->shares_count) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-secondary small">
                                            {{ $post->published_at ? $post->published_at->format('d M Y, h:i A') : 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $post->published_at ? $post->published_at->diffForHumans() : '' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        @if($post->permalink_url)
                                            <a href="{{ $post->permalink_url }}" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Open on Facebook">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            {{-- 2. GRID / GALLERY VIEW (Facebook Cards Style) --}}
            @else
                <div class="card-body p-3">
                    <div class="row row-cards">
                        @foreach($posts as $post)
                            <div class="col-sm-6 col-lg-4">
                                <div class="card card-sm h-100 overflow-hidden">
                                    {{-- Media Preview or Status Banner --}}
                                    <div class="position-relative">
                                        @if($post->media_url)
                                            <a href="{{ $post->permalink_url ?: '#' }}" target="_blank" class="d-block overflow-hidden bg-light">
                                                <img src="{{ $post->media_url }}" class="card-img-top" style="height: 220px; width: 100%; object-fit: cover;" alt="Facebook Post image" loading="lazy">
                                            </a>
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-secondary border-bottom" style="height: 180px;">
                                                <i class="ti ti-article fs-1 text-muted"></i>
                                            </div>
                                        @endif
                                        @if($post->media_type)
                                            <span class="badge bg-dark text-white position-absolute top-0 end-0 m-2 opacity-75">
                                                {{ ucfirst($post->media_type) }}
                                            </span>
                                        @endif
                                    </div>

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

                                        {{-- Page Info & Engagement Footer --}}
                                        <div class="d-flex align-items-center pt-2 border-top">
                                            @if($post->account->profile_pic_url)
                                                <span class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" style="background-image: url('{{ $post->account->profile_pic_url }}')"></span>
                                            @else
                                                <span class="avatar avatar-sm rounded-circle bg-blue-lt text-blue fw-bold me-2 flex-shrink-0">
                                                    {{ strtoupper(substr($post->account->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div class="lh-sm text-truncate me-2" style="max-width: 140px;">
                                                <div class="fw-semibold text-dark text-truncate small">{{ $post->account->name }}</div>
                                                <div class="text-secondary fs-7">{{ $post->published_at ? $post->published_at->diffForHumans() : 'Recent' }}</div>
                                            </div>
                                            <div class="ms-auto d-flex align-items-center gap-3 text-secondary small flex-shrink-0">
                                                <span title="Likes" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-thumb-up text-primary me-1"></i> {{ number_format($post->likes_count) }}
                                                </span>
                                                <span title="Comments" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-message-circle text-teal me-1"></i> {{ number_format($post->comments_count) }}
                                                </span>
                                                @if($post->shares_count > 0)
                                                    <span title="Shares" class="d-inline-flex align-items-center">
                                                        <i class="ti ti-share text-purple me-1"></i> {{ number_format($post->shares_count) }}
                                                    </span>
                                                @endif
                                                @if($post->permalink_url)
                                                    <a href="{{ $post->permalink_url }}" target="_blank" class="text-secondary" data-bs-toggle="tooltip" title="View on Facebook">
                                                        <i class="ti ti-external-link"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($posts->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $posts->links() }}
                </div>
            @endif

        @endif

    </div>

@endsection

