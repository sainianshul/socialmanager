@extends('admin.layouts.app')

@section('title', 'YouTube Videos')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">YouTube Videos</h2>
            </div>
            <div class="col-auto">
                <form id="sync-posts-form" method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                    <button type="submit" class="btn btn-primary" id="btn-sync-posts">
                        <i class="ti ti-refresh me-1" id="sync-icon"></i>Sync YouTube Videos
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
                            <span class="avatar avatar-md rounded-2 bg-red-lt text-red">
                                <i class="ti ti-brand-youtube fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">VIDEOS SYNCED</div>
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
                            <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                                <i class="ti ti-eye fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL VIEWS</div>
                            <div class="h2 mb-0 fw-bold text-dark">
                                {{ $totalViews >= 1000000 ? round($totalViews / 1000000, 1) . 'M' : ($totalViews >= 1000 ? round($totalViews / 1000, 1) . 'K' : number_format($totalViews)) }}
                            </div>
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
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">LIKES & UPVOTES</div>
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
                                <i class="ti ti-messages fs-2"></i>
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
    </div>

    {{-- Main Container Card --}}
    <div class="card">
        
        {{-- Toolbar: Search (Left), Channel Filter & View Switcher (Right) --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                
                {{-- Left: Search Input --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <form method="GET" action="{{ route('admin.posts.youtube') }}" class="m-0">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if($selectedAccountId)
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                        @endif
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="width: 250px;" placeholder="Search YouTube video titles..." />
                        </div>
                    </form>

                    @if(request('q'))
                        <a href="{{ route('admin.posts.youtube', ['view' => $viewMode, 'account_id' => $selectedAccountId]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Clear Search">
                            <i class="ti ti-x me-1"></i>Clear
                        </a>
                    @endif
                </div>

                {{-- Right: Channel Select Filter, Total Count & View Mode Toggle --}}
                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                    {{-- Channel Filter --}}
                    <form method="GET" action="{{ route('admin.posts.youtube') }}" class="m-0">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        <select name="account_id" class="form-select" style="width: 220px;" onchange="this.form.submit()">
                            <option value="">All YouTube Channels ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }} ({{ number_format($acc->subscriber_count) }} subs)
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if($selectedAccountId)
                        <a href="{{ route('admin.posts.youtube', ['view' => $viewMode, 'q' => request('q')]) }}" class="btn btn-ghost-secondary btn-sm" data-bs-toggle="tooltip" title="Reset Channel Filter">
                            <i class="ti ti-x me-1"></i>Reset
                        </a>
                    @endif

                    <span class="text-secondary small me-1 d-none d-sm-inline">{{ $posts->total() }} Videos</span>

                    <div class="btn-group" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" 
                           class="btn btn-icon {{ $viewMode === 'table' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                           data-bs-toggle="tooltip" title="Table View">
                            <i class="ti ti-list"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'gallery']) }}" 
                           class="btn btn-icon {{ $viewMode === 'gallery' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                           data-bs-toggle="tooltip" title="Video Grid">
                            <i class="ti ti-layout-grid"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Content View --}}
        @if($posts->isEmpty())
            <div class="card-body text-center py-5">
                <div class="avatar avatar-xl rounded-circle bg-red-lt mx-auto mb-3">
                    <i class="ti ti-brand-youtube fs-1 text-danger"></i>
                </div>
                <h3 class="fw-bold mb-4">No YouTube Videos Synced</h3>
                <div>
                    @if($accounts->isEmpty())
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary px-4">
                            <i class="ti ti-plug me-1"></i>Connect YouTube Channel
                        </a>
                    @else
                        <form method="POST" action="{{ route('admin.posts.sync') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-refresh me-1"></i>Sync YouTube Videos
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
                                <th class="w-1">Thumbnail</th>
                                <th>Video Title</th>
                                <th>Channel</th>
                                <th>Duration</th>
                                <th>Visibility</th>
                                <th>Metrics</th>
                                <th>Published</th>
                                <th class="w-1 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>
                                        <div class="position-relative" style="width: 80px;">
                                            @php
                                                $thumb = $post->thumbnail_url ?: $post->media_url;
                                                $videoLink = 'https://youtube.com/watch?v=' . ($post->platform_post_id ?: $post->meta_post_id);
                                            @endphp
                                            @if($thumb)
                                                <a href="{{ $videoLink }}" target="_blank">
                                                    <img src="{{ $thumb }}" class="rounded border" style="width: 80px; height: 45px; object-fit: cover;" alt="Thumbnail">
                                                </a>
                                            @else
                                                <span class="avatar avatar-md rounded bg-secondary-lt text-secondary">
                                                    <i class="ti ti-video fs-2"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-reset" style="max-width: 380px;">
                                            <span class="d-inline-block text-truncate" style="max-width: 360px;">
                                                {{ $post->display_title }}
                                            </span>
                                        </div>
                                        <small class="text-secondary font-monospace">ID: {{ $post->platform_post_id ?: $post->meta_post_id }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($post->account->profile_pic_url)
                                                <span class="avatar avatar-xs rounded-circle" style="background-image: url('{{ $post->account->profile_pic_url }}')"></span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $post->account->name }}</div>
                                                <small class="text-secondary">{{ number_format($post->account->subscriber_count) }} subscribers</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-lt font-monospace">{{ $post->formatted_duration }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $post->privacy_status === 'public' ? 'success' : 'secondary' }}-lt text-capitalize">
                                            {{ $post->privacy_status ?: 'Public' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Views">
                                                <i class="ti ti-eye text-azure me-1"></i>{{ $post->formatted_views }}
                                            </span>
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Likes">
                                                <i class="ti ti-thumb-up text-primary me-1"></i>{{ number_format($post->likes_count) }}
                                            </span>
                                            <span class="badge badge-outline text-secondary fw-normal px-2 py-1" data-bs-toggle="tooltip" title="Comments">
                                                <i class="ti ti-messages text-teal me-1"></i>{{ number_format($post->comments_count) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-secondary small">
                                            {{ $post->published_at ? $post->published_at->format('d M Y') : 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $post->published_at ? $post->published_at->diffForHumans() : '' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ $videoLink }}" target="_blank" class="btn btn-sm btn-icon btn-outline-danger" data-bs-toggle="tooltip" title="Watch on YouTube">
                                            <i class="ti ti-player-play"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            {{-- 2. GRID / GALLERY VIEW (YouTube 16:9 Widescreen Video Cards) --}}
            @else
                <div class="card-body p-3">
                    <div class="row row-cards">
                        @foreach($posts as $post)
                            @php
                                $thumb = $post->thumbnail_url ?: $post->media_url;
                                $videoLink = 'https://youtube.com/watch?v=' . ($post->platform_post_id ?: $post->meta_post_id);
                            @endphp
                            <div class="col-sm-6 col-lg-4">
                                <div class="card card-sm h-100 overflow-hidden">
                                    {{-- 16:9 Video Thumbnail with Overlays --}}
                                    <div class="position-relative bg-dark" style="aspect-ratio: 16/9; overflow: hidden;">
                                        @if($thumb)
                                            <a href="{{ $videoLink }}" target="_blank" class="d-block w-100 h-100">
                                                <img src="{{ $thumb }}" class="w-100 h-100" style="object-fit: cover;" alt="YouTube Thumbnail" loading="lazy">
                                            </a>
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                                                <i class="ti ti-brand-youtube fs-1 text-danger"></i>
                                            </div>
                                        @endif

                                        {{-- Duration Badge (Bottom-Right) --}}
                                        <span class="badge bg-dark text-white position-absolute bottom-0 end-0 m-2 font-monospace px-2 py-1 opacity-90" style="font-size: 0.75rem;">
                                            {{ $post->formatted_duration }}
                                        </span>

                                        {{-- Privacy Status (Top-Left) --}}
                                        <span class="badge bg-{{ $post->privacy_status === 'public' ? 'success' : 'secondary' }} text-white position-absolute top-0 start-0 m-2 text-capitalize opacity-90" style="font-size: 0.7rem;">
                                            {{ $post->privacy_status ?: 'Public' }}
                                        </span>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="card-body d-flex flex-column justify-content-between p-3">
                                        <div class="mb-3">
                                            <a href="{{ $videoLink }}" target="_blank" class="text-dark text-decoration-none fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; min-height: 2.8em;">
                                                {{ $post->display_title }}
                                            </a>
                                        </div>

                                        {{-- Channel Row & Metrics Footer --}}
                                        <div class="d-flex align-items-center pt-2 border-top">
                                            @if($post->account->profile_pic_url)
                                                <span class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" style="background-image: url('{{ $post->account->profile_pic_url }}')"></span>
                                            @else
                                                <span class="avatar avatar-sm rounded-circle bg-red-lt text-red fw-bold me-2 flex-shrink-0">
                                                    {{ strtoupper(substr($post->account->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div class="lh-sm text-truncate me-2" style="max-width: 130px;">
                                                <div class="fw-semibold text-dark text-truncate small">{{ $post->account->name }}</div>
                                                <div class="text-secondary fs-7">{{ $post->published_at ? $post->published_at->diffForHumans() : 'Recent' }}</div>
                                            </div>
                                            <div class="ms-auto d-flex align-items-center gap-2 text-secondary small flex-shrink-0">
                                                <span title="Views" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-eye text-azure me-1"></i> {{ $post->formatted_views }}
                                                </span>
                                                <span title="Likes" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-thumb-up text-primary me-1"></i> {{ number_format($post->likes_count) }}
                                                </span>
                                                <span title="Comments" class="d-inline-flex align-items-center">
                                                    <i class="ti ti-messages text-teal me-1"></i> {{ number_format($post->comments_count) }}
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

            @if($posts->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $posts->links() }}
                </div>
            @endif

        @endif

    </div>

@endsection

