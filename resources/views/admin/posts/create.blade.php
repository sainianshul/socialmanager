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
                    {{-- Target Account Selector --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark m-0" id="composer-account-label">Select Account</label>
                            <a href="{{ route('admin.accounts.index') }}" target="_blank" class="text-secondary small">
                                <i class="ti ti-plus me-1"></i>Manage Accounts
                            </a>
                        </div>
                        <select name="account_id" class="form-select" id="composer-account-select" required>
                            <option value="">-- Choose Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" data-type="{{ $acc->account_type }}" {{ (old('account_id', $selectedAccountId) == $acc->id) ? 'selected' : '' }}>
                                    [{{ $acc->account_type_name }}] {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meta Post Types (Facebook & Instagram) --}}
                    <div id="meta-post-types-section" class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Post Type</label>
                        <div class="d-flex flex-wrap gap-2" id="post-type-pills-container">
                            <span class="post-type-pill {{ old('post_type') == 'text' ? 'active' : '' }}" data-type="text" id="pill-type-text"><i class="ti ti-typography"></i> Text</span>
                            <span class="post-type-pill {{ old('post_type', 'photo') == 'photo' ? 'active' : '' }}" data-type="photo"><i class="ti ti-photo"></i> Photo</span>
                            <span class="post-type-pill {{ old('post_type', 'carousel') == 'carousel' ? 'active' : '' }}" data-type="carousel"><i class="ti ti-photo-circle"></i> Carousel</span>
                            <span class="post-type-pill {{ old('post_type', 'video') == 'video' ? 'active' : '' }}" data-type="video"><i class="ti ti-video"></i> Video</span>
                            <span class="post-type-pill {{ old('post_type', 'reel') == 'reel' ? 'active' : '' }}" data-type="reel" id="pill-type-reel"><i class="ti ti-player-play"></i> Reel</span>
                        </div>
                    </div>

                    {{-- Meta Caption / Content --}}
                    <div id="meta-content-section" class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark m-0">Caption</label>
                            <span class="text-secondary small" id="caption-counter">0 / 5000</span>
                        </div>
                        <textarea name="content" id="composer-content" class="form-control" rows="5" placeholder="Write caption...">{{ old('content') }}</textarea>
                    </div>

                    {{-- Meta Media Upload Area --}}
                    <div id="meta-media-section" class="mb-4">
                        <label class="form-label fw-bold text-dark">Media Attachment</label>
                        <input type="file" name="media_files[]" class="form-control" id="composer-media-files" accept="image/*,video/*" multiple>
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
                                <span class="text-secondary small" id="yt-desc-counter">0 / 5000</span>
                            </div>
                            <textarea name="description" id="youtube-description" class="form-control" rows="4" placeholder="Video description (optional)">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label required fw-bold text-dark">Video File</label>
                                <input type="file" name="video_file" id="youtube-video-file" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-dark">Thumbnail</label>
                                <input type="file" name="thumbnail" id="youtube-thumbnail" class="form-control" accept="image/jpeg,image/png">
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
                                    <option value="unlisted" {{ old('privacy_status') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                                    <option value="private" {{ old('privacy_status') == 'private' ? 'selected' : '' }}>Private</option>
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
            // HIDE form fields and card footer submit button
            $('#composer-form-fields').addClass('d-none');
            $('#composer-card-footer').addClass('d-none');

            // Populate and SHOW empty state
            $('#no-account-title').text(config.emptyTitle);
            $('#no-account-desc').text(config.emptyDesc);
            $('#no-account-btn-text').text(config.connectBtnText);
            $('#no-account-state').removeClass('d-none');

            // Remove required flags so they don't block anything
            $('#composer-account-select').prop('required', false);
            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);
            $('#composer-scheduled-at').prop('required', false);
            return;
        }

        // Available count > 0: SHOW form fields and card footer, HIDE empty state
        $('#no-account-state').addClass('d-none');
        $('#composer-form-fields').removeClass('d-none');
        $('#composer-card-footer').removeClass('d-none');
        $('#composer-account-select').prop('required', true);

        // Filter account selector options
        let currentSelectedAcc = $('#composer-account-select').val();
        let selectedStillValid = false;

        $('#composer-account-select option').each(function () {
            let type = parseInt($(this).data('type'));
            if (!type) return; // Keep the placeholder

            if (type === currentPlatform) {
                $(this).show();
                if ($(this).val() == currentSelectedAcc) {
                    selectedStillValid = true;
                }
            } else {
                $(this).hide();
            }
        });

        if (!selectedStillValid) {
            let firstValid = $('#composer-account-select option[data-type="' + currentPlatform + '"]').first().val();
            $('#composer-account-select').val(firstValid || '');
        }

        // Toggle UI Sections based on platform
        if (currentPlatform === 3) { // YouTube
            $('#meta-post-types-section').addClass('d-none');
            $('#meta-content-section').addClass('d-none');
            $('#meta-media-section').addClass('d-none');
            $('#youtube-fields-section').removeClass('d-none');
            $('#youtube-title').prop('required', true);
            $('#youtube-video-file').prop('required', $('#radio-mode-now').is(':checked') || $('#radio-mode-schedule').is(':checked'));
            $('#composer-media-files').prop('required', false);
            $('#composer-account-label').text('Select YouTube Channel');
        } else { // Meta (Facebook / Instagram)
            $('#meta-post-types-section').removeClass('d-none');
            $('#meta-content-section').removeClass('d-none');
            $('#meta-media-section').removeClass('d-none');
            $('#youtube-fields-section').addClass('d-none');
            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);

            if (currentPlatform === 2) { // Instagram
                $('#pill-type-text').hide();
                $('#pill-type-reel').show();
                if ($('#selected-post-type').val() === 'text') {
                    $('#selected-post-type').val('photo');
                    $('.post-type-pill').removeClass('active');
                    $('.post-type-pill[data-type="photo"]').addClass('active');
                }
                $('#composer-account-label').text('Select Instagram Account');
            } else { // Facebook
                $('#pill-type-text').show();
                $('#pill-type-reel').hide();
                $('#composer-account-label').text('Select Facebook Page');
            }
        }

        updateSubmitButton();
    }

    // Platform button click handler
    $('.composer-platform-btn').on('click', function () {
        applyPlatform($(this).data('platform'));
    });

    // Post type pills click handler
    $('.post-type-pill').on('click', function () {
        $('.post-type-pill').removeClass('active');
        $(this).addClass('active');
        $('#selected-post-type').val($(this).data('type'));
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
        let platformName = currentPlatform === 3 ? 'YouTube Video' : (currentPlatform === 2 ? 'Instagram Post' : 'Facebook Post');

        if (mode === 'now') {
            $('#btn-submit-post').html('<i class="ti ti-send me-1.5"></i>Publish ' + platformName + ' Now');
        } else if (mode === 'draft') {
            $('#btn-submit-post').html('<i class="ti ti-file-pencil me-1.5"></i>Save Draft');
        } else {
            $('#btn-submit-post').html('<i class="ti ti-calendar-check me-1.5"></i>Schedule ' + platformName);
        }
    }

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

    // Initial run
    applyPlatform(currentPlatform);
    $('input[name="publish_mode"]:checked').trigger('change');
});
</script>
@endpush
