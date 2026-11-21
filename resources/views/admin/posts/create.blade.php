@extends('admin.layouts.app')

@section('title', 'Create Post')

@section('breadcrumbs')
    <ol class="breadcrumb m-0" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-home fs-3"></i></a></li>
        <li class="breadcrumb-item">Content</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.posts.schedule') }}">Schedule</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create Post</li>
    </ol>
@endsection

@php
    $fbCount = $accounts->where('account_type', \App\Models\Account::TYPE_FACEBOOK)->count();
    $igCount = $accounts->where('account_type', \App\Models\Account::TYPE_INSTAGRAM)->count();
    $ytCount = $accounts->where('account_type', \App\Models\Account::TYPE_YOUTUBE)->count();

    $accountCounts = [
        1 => $fbCount,
        2 => $igCount,
        3 => $ytCount,
    ];

    $initialPlatform = (int) old('platform', $selectedPlatform);
    if (!request()->has('platform') && !old('platform')) {
        if ($accountCounts[$initialPlatform] === 0) {
            foreach ([1, 2, 3] as $p) {
                if ($accountCounts[$p] > 0) {
                    $initialPlatform = $p;
                    break;
                }
            }
        }
    }
@endphp

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Create Post</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to Schedule
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="ti ti-alert-circle fs-2 me-2"></i>
                <strong>Please resolve the following issues:</strong>
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <div class="card shadow-xs">
        <div class="card-header py-3 px-4">
            <h3 class="card-title fw-bold m-0 text-dark">New Social Post</h3>
        </div>

        <form method="POST" action="{{ route('admin.posts.schedule.store') }}" enctype="multipart/form-data" id="form-create-post">
            @csrf
            <input type="hidden" name="platform" id="selected-platform" value="{{ $initialPlatform }}">
            <input type="hidden" name="post_type" id="selected-post-type" value="{{ old('post_type', 'photo') }}">
            <input type="hidden" name="account_id" id="composer-account-id-fallback" value="{{ old('account_id', $selectedAccountId) }}">

            <div class="card-body p-4">
                {{-- Platform Selection Buttons --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">Target Platform</label>
                    <div class="d-flex gap-2 flex-wrap" id="platform-btn-group">
                        <button type="button" class="btn composer-platform-btn platform-fb {{ $initialPlatform === 1 ? 'active' : '' }}" data-platform="1" data-name="Facebook">
                            <i class="ti ti-brand-facebook fs-2 me-1.5"></i>Facebook
                        </button>
                        <button type="button" class="btn composer-platform-btn platform-ig {{ $initialPlatform === 2 ? 'active' : '' }}" data-platform="2" data-name="Instagram">
                            <i class="ti ti-brand-instagram fs-2 me-1.5"></i>Instagram
                        </button>
                        <button type="button" class="btn composer-platform-btn platform-yt {{ $initialPlatform === 3 ? 'active' : '' }}" data-platform="3" data-name="YouTube">
                            <i class="ti ti-brand-youtube fs-2 me-1.5"></i>YouTube
                        </button>
                    </div>
                </div>

                {{-- Empty State: Displayed when the selected platform has NO connected accounts --}}
                <div id="no-account-state" class="p-4 p-md-5 my-3 text-center rounded-3 border border-dashed bg-light-subtle d-none">
                    <h3 class="fw-bold text-dark mb-2" id="no-account-title">Please Connect Your Facebook Account</h3>
                    <p class="text-secondary mx-auto mb-3 small" style="max-width: 480px;" id="no-account-desc">
                        Please connect your Facebook Page to create or schedule posts.
                    </p>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary px-4" id="no-account-connect-btn">
                            <i class="ti ti-plug me-1.5"></i><span id="no-account-btn-text">Connect Facebook Account</span>
                        </a>
                    </div>
                </div>

                {{-- Form Fields Container: Visible ONLY when the selected platform has connected accounts --}}
                <div id="composer-form-fields">
                    {{-- Target Account Selector (Multi-select for FB/IG, single for YT) --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark m-0" id="composer-account-label">Select Facebook Page(s)</label>
                            <a href="{{ route('admin.accounts.index') }}" target="_blank" class="text-secondary small">
                                <i class="ti ti-plus me-1"></i>Manage Accounts
                            </a>
                        </div>

                        {{-- 1. Facebook Accounts Multi-Select Dropdown --}}
                        <div id="fb-primary-account-container">
                            <div class="dropdown custom-multi-select">
                                <button class="form-select text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="fb-primary-dropdown-btn">
                                    <span class="text-truncate text-dark" id="fb-primary-dropdown-label">-- Select Facebook Page(s) --</span>
                                    <i class="ti ti-chevron-down text-secondary ms-2"></i>
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto;">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-1 border-bottom small">
                                        <span class="text-muted fw-semibold">Facebook Pages</span>
                                        <div>
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 me-2" id="fb-select-all">Select all</button>
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-secondary" id="fb-clear-all">Clear</button>
                                        </div>
                                    </div>
                                    @foreach($accounts->where('account_type', \App\Models\Account::TYPE_FACEBOOK) as $acc)
                                        <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer rounded py-1.5 px-2 mb-0">
                                            <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}"
                                                   class="form-check-input m-0 fb-primary-checkbox"
                                                   data-name="{{ $acc->name }}"
                                                   {{ (old('account_id', $selectedAccountId) == $acc->id || (is_array(old('account_ids')) && in_array($acc->id, old('account_ids')))) ? 'checked' : '' }}>
                                            <span class="avatar avatar-xs rounded bg-blue-lt text-blue"><i class="ti ti-brand-facebook"></i></span>
                                            <span class="small fw-medium text-dark text-truncate">{{ $acc->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- 2. Instagram Accounts Multi-Select Dropdown --}}
                        <div id="ig-primary-account-container" class="d-none">
                            <div class="dropdown custom-multi-select">
                                <button class="form-select text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="ig-primary-dropdown-btn">
                                    <span class="text-truncate text-dark" id="ig-primary-dropdown-label">-- Select Instagram Account(s) --</span>
                                    <i class="ti ti-chevron-down text-secondary ms-2"></i>
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto;">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-1 border-bottom small">
                                        <span class="text-muted fw-semibold">Instagram Accounts</span>
                                        <div>
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 me-2" id="ig-select-all">Select all</button>
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-secondary" id="ig-clear-all">Clear</button>
                                        </div>
                                    </div>
                                    @foreach($accounts->where('account_type', \App\Models\Account::TYPE_INSTAGRAM) as $acc)
                                        <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer rounded py-1.5 px-2 mb-0">
                                            <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}"
                                                   class="form-check-input m-0 ig-primary-checkbox"
                                                   data-name="{{ $acc->name }}"
                                                   {{ (old('account_id', $selectedAccountId) == $acc->id || (is_array(old('account_ids')) && in_array($acc->id, old('account_ids')))) ? 'checked' : '' }} disabled>
                                            <span class="avatar avatar-xs rounded bg-pink-lt text-pink"><i class="ti ti-brand-instagram"></i></span>
                                            <span class="small fw-medium text-dark text-truncate">{{ $acc->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- 3. YouTube Account Dropdown --}}
                        <div id="yt-primary-account-container" class="d-none">
                            <select class="form-select" id="composer-yt-account-select">
                                <option value="">-- Choose YouTube Channel --</option>
                                @foreach($accounts->where('account_type', \App\Models\Account::TYPE_YOUTUBE) as $acc)
                                    <option value="{{ $acc->id }}" {{ old('account_id', $selectedAccountId) == $acc->id ? 'selected' : '' }}>
                                        [{{ $acc->account_type_name }}] {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Meta Post Types (Facebook & Instagram) --}}
                    @php
                        $selectedPostType = old('post_type', 'photo');
                    @endphp
                    <div id="meta-post-types-section" class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Post Type</label>
                        <div class="d-flex flex-wrap gap-2" id="post-type-pills-container">
                            <span class="post-type-pill {{ $selectedPostType === 'text' ? 'active' : '' }}" data-type="text" id="pill-type-text"><i class="ti ti-typography"></i> Text</span>
                            <span class="post-type-pill {{ $selectedPostType === 'photo' ? 'active' : '' }}" data-type="photo"><i class="ti ti-photo"></i> Photo</span>
                            <span class="post-type-pill {{ $selectedPostType === 'carousel' ? 'active' : '' }}" data-type="carousel"><i class="ti ti-photo-circle"></i> Carousel</span>
                            <span class="post-type-pill {{ $selectedPostType === 'video' ? 'active' : '' }}" data-type="video"><i class="ti ti-video"></i> Video</span>
                            <span class="post-type-pill {{ $selectedPostType === 'reel' ? 'active' : '' }}" data-type="reel" id="pill-type-reel"><i class="ti ti-player-play"></i> Reel</span>
                        </div>
                    </div>

                    {{-- Meta Caption / Content --}}
                    <div id="meta-content-section" class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <label class="form-label fw-bold text-dark m-0">Caption</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-2 shadow-xs" id="btn-toggle-ai-caption" data-bs-toggle="modal" data-bs-target="#modal-ai-caption" title="Generate AI Captions & Viral Hashtags">
                                    <i class="ti ti-sparkles text-primary"></i>
                                    <span class="fw-semibold">AI Caption & Hashtags</span>
                                </button>
                                <span class="text-secondary small" id="caption-counter">0 / 5000</span>
                            </div>
                        </div>

                        <textarea name="content" id="composer-content" class="form-control" rows="5" placeholder="Write caption...">{{ old('content') }}</textarea>
                    </div>

                    {{-- Meta Media Upload Area --}}
                    <div id="meta-media-section" class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                <i class="ti ti-photo-plus text-primary"></i> Media Attachment
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-secondary border rounded-pill" id="media-attached-count" style="font-size: 11px;">0 attached</span>
                                <button type="button" class="btn btn-link btn-xs text-danger text-decoration-none p-0 d-none" id="btn-clear-all-media">Clear All</button>
                            </div>
                        </div>

                        {{-- Hidden real file input --}}
                        <input type="file" name="media_files[]" class="d-none" id="composer-media-files" accept="image/*,video/*" multiple>

                        {{-- Two Distinct Action Buttons: Upload From Device & Design on Canva --}}
                        <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                            <button type="button" class="btn btn-outline-secondary rounded-2 px-3.5 py-2 fw-medium d-inline-flex align-items-center gap-2 shadow-xs" id="btn-trigger-file-browser">
                                <i class="ti ti-device-laptop fs-2 text-secondary"></i>
                                <span>Upload From Device</span>
                            </button>
                            <button type="button" class="btn btn-canva-brand rounded-2 px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 ms-1" data-bs-toggle="modal" data-bs-target="#modal-canva-studio">
                                <i class="ti ti-brand-canva fs-2 text-white"></i>
                                <span class="text-white">Design on Canva</span>
                            </button>
                        </div>

                        {{-- Live Attachments Grid --}}
                        <div class="row g-2 d-none mt-1" id="composer-media-preview-grid"></div>

                        {{-- Canva Imported Hidden Inputs Container --}}
                        <div id="canva-imported-media-container"></div>
                    </div>

                    {{-- Cross-Posting Section (Directly under Media Attachment) --}}
                    <div id="cross-posting-section" class="mb-4">
                        {{-- 1. When on Facebook: "Also Post to Instagram" --}}
                        <div id="cross-post-to-ig-container" class="card card-sm border bg-light-subtle p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <label class="form-check form-switch m-0 d-flex align-items-center cursor-pointer">
                                    <input class="form-check-input me-2" type="checkbox" id="check-cross-post-to-ig" {{ $igCount === 0 ? 'disabled' : '' }}>
                                    <span class="form-check-label fw-bold text-dark d-flex align-items-center gap-1.5">
                                        <i class="ti ti-brand-instagram text-pink fs-2"></i> Also Post to Instagram
                                    </span>
                                </label>
                                @if($igCount === 0)
                                    <span class="text-secondary small">No connected Instagram accounts (<a href="{{ route('admin.accounts.index') }}" target="_blank">Connect</a>)</span>
                                @endif
                            </div>

                            {{-- Instagram Accounts Multi-Select (expands when switch is checked) --}}
                            <div id="cross-post-ig-accounts-box" class="mt-3 ps-4 d-none">
                                <label class="form-label small fw-semibold text-secondary mb-1">Select Instagram Account(s)</label>
                                <div class="dropdown custom-multi-select">
                                    <button class="form-select text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="cross-ig-dropdown-btn">
                                        <span class="text-truncate text-dark" id="cross-ig-dropdown-label">-- Select Instagram Account(s) --</span>
                                        <i class="ti ti-chevron-down text-secondary ms-2"></i>
                                    </button>
                                    <div class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 200px; overflow-y: auto;">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-1 border-bottom small">
                                            <span class="text-muted fw-semibold">Instagram Accounts</span>
                                            <div>
                                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 me-2" id="cross-ig-select-all">Select all</button>
                                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-secondary" id="cross-ig-clear-all">Clear</button>
                                            </div>
                                        </div>
                                        @foreach($accounts->where('account_type', \App\Models\Account::TYPE_INSTAGRAM) as $acc)
                                            <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer rounded py-1.5 px-2 mb-0">
                                                <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}"
                                                       class="form-check-input m-0 cross-ig-checkbox"
                                                       data-name="{{ $acc->name }}" disabled>
                                                <span class="avatar avatar-xs rounded bg-pink-lt text-pink"><i class="ti ti-brand-instagram"></i></span>
                                                <span class="small fw-medium text-dark text-truncate">{{ $acc->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-text text-muted small mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Photos or video attachments are required when cross-posting to Instagram.
                                </div>
                            </div>
                        </div>

                        {{-- 2. When on Instagram: "Also Post to Facebook" --}}
                        <div id="cross-post-to-fb-container" class="card card-sm border bg-light-subtle p-3 d-none">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <label class="form-check form-switch m-0 d-flex align-items-center cursor-pointer">
                                    <input class="form-check-input me-2" type="checkbox" id="check-cross-post-to-fb" {{ $fbCount === 0 ? 'disabled' : '' }}>
                                    <span class="form-check-label fw-bold text-dark d-flex align-items-center gap-1.5">
                                        <i class="ti ti-brand-facebook text-blue fs-2"></i> Also Post to Facebook
                                    </span>
                                </label>
                                @if($fbCount === 0)
                                    <span class="text-secondary small">No connected Facebook Pages (<a href="{{ route('admin.accounts.index') }}" target="_blank">Connect</a>)</span>
                                @endif
                            </div>

                            {{-- Facebook Accounts Multi-Select (expands when switch is checked) --}}
                            <div id="cross-post-fb-accounts-box" class="mt-3 ps-4 d-none">
                                <label class="form-label small fw-semibold text-secondary mb-1">Select Facebook Page(s)</label>
                                <div class="dropdown custom-multi-select">
                                    <button class="form-select text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="cross-fb-dropdown-btn">
                                        <span class="text-truncate text-dark" id="cross-fb-dropdown-label">-- Select Facebook Page(s) --</span>
                                        <i class="ti ti-chevron-down text-secondary ms-2"></i>
                                    </button>
                                    <div class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 200px; overflow-y: auto;">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-1 border-bottom small">
                                            <span class="text-muted fw-semibold">Facebook Pages</span>
                                            <div>
                                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 me-2" id="cross-fb-select-all">Select all</button>
                                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-secondary" id="cross-fb-clear-all">Clear</button>
                                            </div>
                                        </div>
                                        @foreach($accounts->where('account_type', \App\Models\Account::TYPE_FACEBOOK) as $acc)
                                            <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer rounded py-1.5 px-2 mb-0">
                                                <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}"
                                                       class="form-check-input m-0 cross-fb-checkbox"
                                                       data-name="{{ $acc->name }}" disabled>
                                                <span class="avatar avatar-xs rounded bg-blue-lt text-blue"><i class="ti ti-brand-facebook"></i></span>
                                                <span class="small fw-medium text-dark text-truncate">{{ $acc->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- YouTube Dedicated Fields --}}
                    <div id="youtube-fields-section" class="d-none">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required fw-bold text-dark m-0">Video Title</label>
                                <span class="text-secondary small" id="yt-title-counter">0 / 100</span>
                            </div>
                            <input type="text" name="title" id="youtube-title" class="form-control" placeholder="Video title" maxlength="100" value="{{ old('title') }}">
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark m-0">Description</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-teal d-inline-flex align-items-center gap-1 py-0.5 px-2.5 rounded-2" id="btn-generate-ai-desc" title="Generate SEO-optimized YouTube description from Title using AI">
                                        <i class="ti ti-sparkles text-teal"></i>
                                        <span id="btn-ai-desc-text">Generate Description From AI</span>
                                    </button>
                                    <span class="text-secondary small" id="yt-desc-counter">0 / 5000</span>
                                </div>
                            </div>
                            <textarea name="description" id="youtube-description" class="form-control" rows="4" placeholder="Video description (optional)">{{ old('description') }}</textarea>
                            <small class="form-hint mt-1">
                                <i class="ti ti-bulb me-1 text-teal"></i>Tip: Enter a Video Title above and click <strong>Generate Description From AI</strong> for an SEO-friendly summary and hashtags.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label required fw-bold text-dark">Video File</label>
                                <input type="file" name="video_file" id="youtube-video-file" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label class="form-label fw-bold text-dark m-0">Thumbnail</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#modal-canva-studio">
                                        <i class="ti ti-brand-canva me-1"></i>Canva Thumbnail (16:9)
                                    </button>
                                </div>
                                <div class="p-2.5 rounded border bg-light-subtle">
                                    <input type="file" name="thumbnail" id="youtube-thumbnail" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span class="text-secondary" style="font-size: 11px;">1280 &times; 720 px (16:9 recommended)</span>
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#modal-canva-studio">
                                            <i class="ti ti-sparkles me-1"></i>Create in Canva
                                        </button>
                                    </div>
                                    <div id="youtube-thumbnail-preview-box" class="mt-2 d-none">
                                        <img id="youtube-thumbnail-preview" src="" class="rounded border shadow-xs" style="max-height: 85px; width: auto; object-fit: cover;" alt="Thumbnail preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-dark">Tags</label>
                                <input type="text" name="tags" id="youtube-tags" class="form-control" placeholder="tag1, tag2, tag3" value="{{ old('tags') }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required fw-bold text-dark">Privacy Status</label>
                                <select name="privacy_status" class="form-select" id="youtube-privacy-status">
                                    <option value="public" {{ old('privacy_status', 'public') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="unlisted" {{ old('privacy_status', 'unlisted') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                                    <option value="private" {{ old('privacy_status', 'private') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Publishing Mode --}}
                    <div class="card p-3 bg-light-subtle mb-0 border">
                        <label class="form-label fw-bold text-dark mb-2">Publishing Mode</label>
                        <div class="d-flex align-items-center gap-4 mb-3 flex-wrap">
                            <label class="form-check m-0">
                                <input class="form-check-input" type="radio" name="publish_mode" value="schedule" {{ old('publish_mode', 'schedule') == 'schedule' ? 'checked' : '' }} id="radio-mode-schedule">
                                <span class="form-check-label fw-medium"><i class="ti ti-clock me-1 text-primary"></i>Schedule for Later</span>
                            </label>
                            <label class="form-check m-0">
                                <input class="form-check-input" type="radio" name="publish_mode" value="now" {{ old('publish_mode') == 'now' ? 'checked' : '' }} id="radio-mode-now">
                                <span class="form-check-label fw-medium"><i class="ti ti-send me-1 text-success"></i>Publish Immediately</span>
                            </label>
                            <label class="form-check m-0">
                                <input class="form-check-input" type="radio" name="publish_mode" value="draft" {{ old('publish_mode') == 'draft' ? 'checked' : '' }} id="radio-mode-draft">
                                <span class="form-check-label fw-medium"><i class="ti ti-file-pencil me-1 text-secondary"></i>Save as Draft</span>
                            </label>
                        </div>

                        <div id="schedule-datetime-container">
                            <label class="form-label fw-bold text-dark">Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="composer-scheduled-at" class="form-control"
                                   value="{{ old('scheduled_at', $prefillDate) }}">
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center py-3 px-4" id="composer-card-footer">
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" id="btn-submit-post">
                    <i class="ti ti-calendar-check me-1.5"></i>Schedule Post
                </button>
            </div>
        </form>
    </div>

    {{-- AI Caption & Hashtag Assistant Modal --}}
    @include('admin.posts.partials._ai_caption_studio')

    {{-- Canva Design Studio Modal --}}
    @include('admin.posts.partials._canva_studio_modal')

@endsection

@push('styles')
<style>
    .composer-platform-btn {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.12);
        border-radius: 8px;
        padding: 0.6rem 1.15rem;
        font-weight: 600;
        font-size: 0.875rem;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .composer-platform-btn:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .composer-platform-btn.platform-fb.active {
        background-color: #1877f2 !important;
        color: #ffffff !important;
        border-color: #1877f2 !important;
        box-shadow: 0 2px 6px rgba(24, 119, 242, 0.25);
    }
    .composer-platform-btn.platform-ig.active {
        background-color: #e1306c !important;
        color: #ffffff !important;
        border-color: #e1306c !important;
        box-shadow: 0 2px 6px rgba(225, 48, 108, 0.25);
    }
    .composer-platform-btn.platform-yt.active {
        background-color: #e02424 !important;
        color: #ffffff !important;
        border-color: #e02424 !important;
        box-shadow: 0 2px 6px rgba(224, 36, 36, 0.25);
    }
    .post-type-pill {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.12);
        padding: 0.45rem 0.85rem;
        border-radius: 6px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 500;
        background: #ffffff;
        color: #334155;
    }
    .post-type-pill:hover {
        background: #f8fafc;
    }
    .post-type-pill.active {
        background: #1db8ab;
        color: #ffffff;
        border-color: #1db8ab;
    }
    .custom-multi-select .dropdown-item:hover {
        background-color: #f1f5f9;
    }
    .custom-multi-select .dropdown-item:active {
        background-color: inherit;
        color: inherit;
    }
    [data-bs-theme="dark"] .composer-platform-btn {
        background: #111a2d !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .composer-platform-btn:hover {
        background: #162035 !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .post-type-pill {
        background: #111a2d !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .post-type-pill:hover {
        background: #162035 !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .custom-multi-select .dropdown-menu {
        background: #111a2d;
        border-color: rgba(255, 255, 255, 0.12);
    }
    [data-bs-theme="dark"] .custom-multi-select .dropdown-item:hover {
        background-color: #1e293b;
    }
    .media-source-btn {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }
    .media-source-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    [data-bs-theme="dark"] .media-source-btn {
        background: #111a2d !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .media-source-btn:hover {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
        color: #f8fafc !important;
    }
    .btn-canva-brand {
        background: linear-gradient(135deg, #00c4cc 0%, #7d2ae8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(125, 42, 232, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-canva-brand:hover {
        background: linear-gradient(135deg, #00b3ba 0%, #6e24cf 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(125, 42, 232, 0.35) !important;
        transform: translateY(-1px);
    }
    .btn-canva-brand:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(125, 42, 232, 0.2) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const accountCounts = {
        1: {{ $accountCounts[1] }},
        2: {{ $accountCounts[2] }},
        3: {{ $accountCounts[3] }}
    };

    const platformConfig = {
        1: {
            name: 'Facebook',
            emptyTitle: 'Please Connect Your Facebook Account',
            emptyDesc: 'Please connect your Facebook Page to create or schedule posts.',
            connectBtnText: 'Connect Facebook Account'
        },
        2: {
            name: 'Instagram',
            emptyTitle: 'Please Connect Your Instagram Account',
            emptyDesc: 'Please connect your Instagram Business account to create or schedule posts.',
            connectBtnText: 'Connect Instagram Account'
        },
        3: {
            name: 'YouTube',
            emptyTitle: 'Please Connect Your YouTube Channel',
            emptyDesc: 'Please connect your YouTube Channel to upload or schedule videos.',
            connectBtnText: 'Connect YouTube Channel'
        }
    };

    let currentPlatform = parseInt($('#selected-platform').val()) || 1;

    // Helper: update dropdown label based on checked checkboxes
    function updateMultiSelectLabel(checkboxSelector, labelSelector, defaultText, singularName) {
        let checked = $(checkboxSelector + ':checked');
        let count = checked.length;
        if (count === 0) {
            $(labelSelector).text(defaultText).removeClass('fw-semibold').addClass('text-secondary');
        } else if (count === 1) {
            $(labelSelector).text(checked.first().data('name')).removeClass('text-secondary').addClass('fw-semibold');
        } else if (count <= 3) {
            let names = [];
            checked.each(function () { names.push($(this).data('name')); });
            $(labelSelector).text(count + ' ' + singularName + 's (' + names.join(', ') + ')').removeClass('text-secondary').addClass('fw-semibold');
        } else {
            $(labelSelector).text(count + ' ' + singularName + 's selected').removeClass('text-secondary').addClass('fw-semibold');
        }
        updateFallbackAndButton();
    }

    function updateFallbackAndButton() {
        // Collect first valid account id for single fallback
        let firstChecked = $('input[name="account_ids[]"]:enabled:checked').first().val();
        if (currentPlatform === 3) {
            firstChecked = $('#composer-yt-account-select').val();
        }
        $('#composer-account-id-fallback').val(firstChecked || '');

        updateSubmitButton();
    }

    // Apply Post Type (Text, Photo, Carousel, Video, Reel)
    function applyPostType(type) {
        type = type || 'photo';
        $('#selected-post-type').val(type);
        $('.post-type-pill').removeClass('active');
        $('.post-type-pill[data-type="' + type + '"]').addClass('active');

        if (currentPlatform === 1) { // Facebook
            if (type === 'text') {
                // If text post on FB: hide media attachment & hide cross-post to Instagram
                $('#meta-media-section').addClass('d-none');
                $('#cross-post-to-ig-container').addClass('d-none');
                if ($('#check-cross-post-to-ig').is(':checked')) {
                    $('#check-cross-post-to-ig').prop('checked', false).trigger('change');
                }
            } else {
                // Photo, Carousel, Video, Reel
                $('#meta-media-section').removeClass('d-none');
                $('#cross-post-to-ig-container').removeClass('d-none');
            }
        } else if (currentPlatform === 2) { // Instagram
            $('#meta-media-section').removeClass('d-none');
        }
    }

    // Apply platform switch (Facebook / Instagram / YouTube)
    function applyPlatform(platform) {
        currentPlatform = parseInt(platform);
        $('#selected-platform').val(currentPlatform);

        // Update button active states
        $('.composer-platform-btn').removeClass('active');
        if (currentPlatform === 1) $('.platform-fb').addClass('active');
        else if (currentPlatform === 2) $('.platform-ig').addClass('active');
        else if (currentPlatform === 3) $('.platform-yt').addClass('active');

        const availableCount = accountCounts[currentPlatform] || 0;
        const config = platformConfig[currentPlatform] || platformConfig[1];

        if (availableCount === 0) {
            $('#composer-form-fields').addClass('d-none');
            $('#composer-card-footer').addClass('d-none');

            $('#no-account-title').text(config.emptyTitle);
            $('#no-account-desc').text(config.emptyDesc);
            $('#no-account-btn-text').text(config.connectBtnText);
            $('#no-account-state').removeClass('d-none');

            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);
            $('#composer-scheduled-at').prop('required', false);
            return;
        }

        $('#no-account-state').addClass('d-none');
        $('#composer-form-fields').removeClass('d-none');
        $('#composer-card-footer').removeClass('d-none');

        // Platform-specific UI layout
        if (currentPlatform === 1) { // Facebook
            $('#composer-account-label').text('Select Facebook Page(s)');
            $('#fb-primary-account-container').removeClass('d-none');
            $('.fb-primary-checkbox').prop('disabled', false);

            $('#ig-primary-account-container').addClass('d-none');
            $('.ig-primary-checkbox').prop('disabled', true);

            $('#yt-primary-account-container').addClass('d-none');

            // Show Meta sections
            $('#meta-post-types-section').removeClass('d-none');
            $('#meta-content-section').removeClass('d-none');
            $('#meta-media-section').removeClass('d-none');
            $('#youtube-fields-section').addClass('d-none');
            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);

            // Cross-posting: Show "Also Post to Instagram", hide "Also Post to Facebook"
            $('#cross-posting-section').removeClass('d-none');
            $('#cross-post-to-ig-container').removeClass('d-none');
            $('#cross-post-to-fb-container').addClass('d-none');

            // Uncheck cross FB
            $('#check-cross-post-to-fb').prop('checked', false);
            $('#cross-post-fb-accounts-box').addClass('d-none');
            $('.cross-fb-checkbox').prop('checked', false).prop('disabled', true);

            // Post type pills
            $('#pill-type-text').show();
            $('#pill-type-reel').hide();
            let currentFbType = $('#selected-post-type').val() || 'photo';
            applyPostType(currentFbType);

            // Auto-check first FB page if none checked
            if ($('.fb-primary-checkbox:checked').length === 0) {
                $('.fb-primary-checkbox').first().prop('checked', true);
            }
            updateMultiSelectLabel('.fb-primary-checkbox', '#fb-primary-dropdown-label', '-- Select Facebook Page(s) --', 'Page');

        } else if (currentPlatform === 2) { // Instagram
            $('#composer-account-label').text('Select Instagram Account(s)');
            $('#ig-primary-account-container').removeClass('d-none');
            $('.ig-primary-checkbox').prop('disabled', false);

            $('#fb-primary-account-container').addClass('d-none');
            $('.fb-primary-checkbox').prop('disabled', true);

            $('#yt-primary-account-container').addClass('d-none');

            // Show Meta sections
            $('#meta-post-types-section').removeClass('d-none');
            $('#meta-content-section').removeClass('d-none');
            $('#meta-media-section').removeClass('d-none');
            $('#youtube-fields-section').addClass('d-none');
            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);

            // Cross-posting: Show "Also Post to Facebook", hide "Also Post to Instagram"
            $('#cross-posting-section').removeClass('d-none');
            $('#cross-post-to-fb-container').removeClass('d-none');
            $('#cross-post-to-ig-container').addClass('d-none');

            // Uncheck cross IG
            $('#check-cross-post-to-ig').prop('checked', false);
            $('#cross-post-ig-accounts-box').addClass('d-none');
            $('.cross-ig-checkbox').prop('checked', false).prop('disabled', true);

            // Post type pills: Instagram requires media (no text-only)
            $('#pill-type-text').hide();
            $('#pill-type-reel').show();
            let currentIgType = $('#selected-post-type').val();
            if (currentIgType === 'text') {
                currentIgType = 'photo';
            }
            applyPostType(currentIgType || 'photo');

            // Auto-check first IG account if none checked
            if ($('.ig-primary-checkbox:checked').length === 0) {
                $('.ig-primary-checkbox').first().prop('checked', true);
            }
            updateMultiSelectLabel('.ig-primary-checkbox', '#ig-primary-dropdown-label', '-- Select Instagram Account(s) --', 'Account');

        } else if (currentPlatform === 3) { // YouTube
            $('#composer-account-label').text('Select YouTube Channel');
            $('#yt-primary-account-container').removeClass('d-none');
            $('#fb-primary-account-container').addClass('d-none');
            $('#ig-primary-account-container').addClass('d-none');
            $('.fb-primary-checkbox').prop('disabled', true);
            $('.ig-primary-checkbox').prop('disabled', true);

            // Hide Meta sections and cross-posting
            $('#meta-post-types-section').addClass('d-none');
            $('#meta-content-section').addClass('d-none');
            $('#meta-media-section').addClass('d-none');
            $('#cross-posting-section').addClass('d-none');

            // Show YouTube sections
            $('#youtube-fields-section').removeClass('d-none');
            $('#youtube-title').prop('required', true);
            $('#youtube-video-file').prop('required', $('#radio-mode-now').is(':checked') || $('#radio-mode-schedule').is(':checked'));
            $('#composer-media-files').prop('required', false);

            if (!$('#composer-yt-account-select').val()) {
                $('#composer-yt-account-select option[value!=""]').first().prop('selected', true);
            }
            updateFallbackAndButton();
        }

        updateSubmitButton();
    }

    // 1. Primary Facebook Checkboxes change
    $(document).on('change', '.fb-primary-checkbox', function () {
        updateMultiSelectLabel('.fb-primary-checkbox', '#fb-primary-dropdown-label', '-- Select Facebook Page(s) --', 'Page');
    });
    $('#fb-select-all').on('click', function () {
        $('.fb-primary-checkbox').prop('checked', true).trigger('change');
    });
    $('#fb-clear-all').on('click', function () {
        $('.fb-primary-checkbox').prop('checked', false).trigger('change');
    });

    // 2. Primary Instagram Checkboxes change
    $(document).on('change', '.ig-primary-checkbox', function () {
        updateMultiSelectLabel('.ig-primary-checkbox', '#ig-primary-dropdown-label', '-- Select Instagram Account(s) --', 'Account');
    });
    $('#ig-select-all').on('click', function () {
        $('.ig-primary-checkbox').prop('checked', true).trigger('change');
    });
    $('#ig-clear-all').on('click', function () {
        $('.ig-primary-checkbox').prop('checked', false).trigger('change');
    });

    // 3. YouTube Select change
    $('#composer-yt-account-select').on('change', function () {
        updateFallbackAndButton();
    });

    // 4. "Also Post to Instagram" switch toggle (on FB tab)
    $('#check-cross-post-to-ig').on('change', function () {
        if ($(this).is(':checked')) {
            $('#cross-post-ig-accounts-box').removeClass('d-none');
            $('.cross-ig-checkbox').prop('disabled', false);
            if ($('.cross-ig-checkbox:checked').length === 0) {
                $('.cross-ig-checkbox').first().prop('checked', true);
            }
            updateMultiSelectLabel('.cross-ig-checkbox', '#cross-ig-dropdown-label', '-- Select Instagram Account(s) --', 'Account');
        } else {
            $('#cross-post-ig-accounts-box').addClass('d-none');
            $('.cross-ig-checkbox').prop('checked', false).prop('disabled', true);
            updateMultiSelectLabel('.cross-ig-checkbox', '#cross-ig-dropdown-label', '-- Select Instagram Account(s) --', 'Account');
        }
    });
    $(document).on('change', '.cross-ig-checkbox', function () {
        updateMultiSelectLabel('.cross-ig-checkbox', '#cross-ig-dropdown-label', '-- Select Instagram Account(s) --', 'Account');
    });
    $('#cross-ig-select-all').on('click', function () {
        $('.cross-ig-checkbox').prop('checked', true).trigger('change');
    });
    $('#cross-ig-clear-all').on('click', function () {
        $('.cross-ig-checkbox').prop('checked', false).trigger('change');
    });

    // 5. "Also Post to Facebook" switch toggle (on IG tab)
    $('#check-cross-post-to-fb').on('change', function () {
        if ($(this).is(':checked')) {
            $('#cross-post-fb-accounts-box').removeClass('d-none');
            $('.cross-fb-checkbox').prop('disabled', false);
            if ($('.cross-fb-checkbox:checked').length === 0) {
                $('.cross-fb-checkbox').first().prop('checked', true);
            }
            updateMultiSelectLabel('.cross-fb-checkbox', '#cross-fb-dropdown-label', '-- Select Facebook Page(s) --', 'Page');
        } else {
            $('#cross-post-fb-accounts-box').addClass('d-none');
            $('.cross-fb-checkbox').prop('checked', false).prop('disabled', true);
            updateMultiSelectLabel('.cross-fb-checkbox', '#cross-fb-dropdown-label', '-- Select Facebook Page(s) --', 'Page');
        }
    });
    $(document).on('change', '.cross-fb-checkbox', function () {
        updateMultiSelectLabel('.cross-fb-checkbox', '#cross-fb-dropdown-label', '-- Select Facebook Page(s) --', 'Page');
    });
    $('#cross-fb-select-all').on('click', function () {
        $('.cross-fb-checkbox').prop('checked', true).trigger('change');
    });
    $('#cross-fb-clear-all').on('click', function () {
        $('.cross-fb-checkbox').prop('checked', false).trigger('change');
    });

    // Platform button click handler
    $('.composer-platform-btn').on('click', function () {
        applyPlatform($(this).data('platform'));
    });

    // Post type pills click handler
    $('.post-type-pill').on('click', function () {
        applyPostType($(this).data('type'));
    });

    // Publish Mode Handler (Schedule, Now, Draft)
    $('input[name="publish_mode"]').on('change', function () {
        let mode = $(this).val();
        if (mode === 'schedule') {
            $('#schedule-datetime-container').show();
            if (accountCounts[currentPlatform] > 0) {
                $('#composer-scheduled-at').prop('required', true);
            }
        } else {
            $('#schedule-datetime-container').hide();
            $('#composer-scheduled-at').prop('required', false);
        }

        if (currentPlatform === 3 && accountCounts[currentPlatform] > 0) {
            $('#youtube-video-file').prop('required', mode !== 'draft');
        }

        updateSubmitButton();
    });

    function updateSubmitButton() {
        let mode = $('input[name="publish_mode"]:checked').val();
        let totalCount = $('input[name="account_ids[]"]:enabled:checked').length;
        if (currentPlatform === 3) {
            totalCount = $('#composer-yt-account-select').val() ? 1 : 0;
        }

        let targetText = totalCount > 1 ? totalCount + ' Accounts' : (currentPlatform === 3 ? 'YouTube Video' : 'Post');

        if (mode === 'now') {
            $('#btn-submit-post').html('<i class="ti ti-send me-1.5"></i>Publish to ' + targetText + ' Now');
        } else if (mode === 'draft') {
            $('#btn-submit-post').html('<i class="ti ti-file-pencil me-1.5"></i>Save Draft (' + targetText + ')');
        } else {
            $('#btn-submit-post').html('<i class="ti ti-calendar-check me-1.5"></i>Schedule for ' + targetText);
        }
    }

    // Form Submission Validation
    $('#form-create-post').on('submit', function (e) {
        if (currentPlatform === 3) {
            if (!$('#composer-yt-account-select').val()) {
                e.preventDefault();
                alert('Please select a YouTube channel.');
                return false;
            }
            // Populate fallback
            $('#composer-account-id-fallback').val($('#composer-yt-account-select').val());
        } else {
            let selectedCount = $('input[name="account_ids[]"]:enabled:checked').length;
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one account to post to.');
                return false;
            }

            // If Instagram is involved, verify media unless draft
            let hasInstagram = false;
            if (currentPlatform === 2) hasInstagram = true;
            if ($('#check-cross-post-to-ig').is(':checked') && $('.cross-ig-checkbox:checked').length > 0) {
                hasInstagram = true;
            }

            let mode = $('input[name="publish_mode"]:checked').val();
            if (hasInstagram && mode !== 'draft') {
                let mediaInput = document.getElementById('composer-media-files');
                let hasUploadedFiles = mediaInput && mediaInput.files && mediaInput.files.length > 0;
                let hasCanvaFiles = $('input[name="canva_media_paths[]"]').length > 0;
                if (!hasUploadedFiles && !hasCanvaFiles) {
                    e.preventDefault();
                    alert('Instagram requires at least one photo or video attachment.');
                    $('#btn-trigger-file-browser').focus();
                    return false;
                }
            }
        }
    });

    // Live character counters
    $('#composer-content').on('input', function () {
        $('#caption-counter').text($(this).val().length + ' / 5000');
    });

    $('#youtube-title').on('input', function () {
        $('#yt-title-counter').text($(this).val().length + ' / 100');
    });

    $('#youtube-description').on('input', function () {
        $('#yt-desc-counter').text($(this).val().length + ' / 5000');
    });

    // AI Description Generator from Title for YouTube
    $('#btn-generate-ai-desc').on('click', function () {
        let title = $.trim($('#youtube-title').val());
        if (!title) {
            alert('Please enter a Video Title first so AI can read it and generate an SEO-friendly description.');
            $('#youtube-title').focus();
            return;
        }

        let $btn = $(this);
        let $btnText = $('#btn-ai-desc-text');
        let origText = $btnText.text();

        $btn.prop('disabled', true);
        $btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating...');

        $.ajax({
            url: "{{ route('admin.posts.generate-ai-description') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                title: title
            },
            success: function (res) {
                if (res.success && res.description) {
                    $('#youtube-description').val(res.description).trigger('input');
                } else {
                    alert(res.message || 'Could not generate description. Please try again.');
                }
            },
            error: function (xhr) {
                let msg = 'Failed to generate AI description. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btnText.text(origText);
            }
        });
    });

    // Initial run
    applyPlatform(currentPlatform);
    $('input[name="publish_mode"]:checked').trigger('change');
});
</script>
@include('admin.posts.partials._ai_caption_script')
@include('admin.posts.partials._canva_studio_script')
@endpush
