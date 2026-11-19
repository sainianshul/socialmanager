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
    $totalCount = $accounts->count();
    $prefillDate = request('date', now()->addHours(2)->format('Y-m-d\TH:i'));
@endphp

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">New Post</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Calendar
                </a>
            </div>
        </div>
    </div>

    {{-- Error / Alert Messages --}}
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
                <strong>Please resolve the following:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if($totalCount === 0)
        {{-- Empty State when user has no connected accounts --}}
        <div class="card p-5 text-center shadow-xs">
            <div class="empty">
                <div class="empty-icon text-muted mb-3">
                    <i class="ti ti-plug-connected fs-1" style="font-size: 3rem;"></i>
                </div>
                <h3 class="empty-title fw-bold mb-2">No Connected Accounts Found</h3>
                <p class="empty-subtitle text-secondary mx-auto mb-4" style="max-width: 480px;">
                    To create or schedule posts, you need to connect at least one social media channel (Facebook Page, Instagram, or YouTube).
                </p>
                <div class="empty-action">
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary px-4">
                        <i class="ti ti-plus me-1.5"></i>Connect a Social Channel
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Clean Modern Post Composer --}}
        <form method="POST" action="{{ route('admin.posts.schedule.store') }}" enctype="multipart/form-data" id="form-create-post">
            @csrf
            <input type="hidden" name="platform" id="selected-platform" value="1">
            <input type="hidden" name="post_type" id="selected-post-type" value="{{ old('post_type', 'photo') }}">
            <input type="hidden" name="account_id" id="composer-account-id-fallback" value="{{ old('account_id', $selectedAccountId) }}">

            <div class="card shadow-xs border-0">
                <div class="card-body p-4">

                    {{-- 1. Publish to: Modern Streamlined Account Chips --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label fw-bold text-dark m-0">Publish to</label>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill small" id="selected-accounts-badge">
                                    0 selected
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 small">
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-primary" id="btn-select-all">Select all</button>
                                <span class="text-muted">·</span>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-secondary" id="btn-deselect-all">Clear</button>
                            </div>
                        </div>

                        {{-- One-click Account Selection Chips --}}
                        <div class="d-flex flex-wrap gap-2 align-items-center pt-1" id="account-chips-list">
                            @foreach($accounts as $acc)
                                @php
                                    $isFb = $acc->account_type === \App\Models\Account::TYPE_FACEBOOK;
                                    $isIg = $acc->account_type === \App\Models\Account::TYPE_INSTAGRAM;
                                    $isYt = $acc->account_type === \App\Models\Account::TYPE_YOUTUBE;
                                    $isSelected = (old('account_id', $selectedAccountId) == $acc->id) ||
                                                  (isset($selectedAccountIds) && in_array($acc->id, $selectedAccountIds)) ||
                                                  (is_array(old('account_ids')) && in_array($acc->id, old('account_ids')));
                                @endphp
                                <label class="account-chip {{ $isSelected ? 'active' : '' }}" data-account-id="{{ $acc->id }}" data-platform="{{ $acc->account_type }}">
                                    <input type="checkbox" name="account_ids[]" value="{{ $acc->id }}"
                                           class="account-checkbox d-none"
                                           data-platform="{{ $acc->account_type }}"
                                           data-name="{{ $acc->name }}"
                                           {{ $isSelected ? 'checked' : '' }}>

                                    <span class="account-chip-icon">
                                        @if($isFb)
                                            <span class="avatar-pill bg-blue text-white"><i class="ti ti-brand-facebook"></i></span>
                                        @elseif($isIg)
                                            <span class="avatar-pill bg-pink text-white"><i class="ti ti-brand-instagram"></i></span>
                                        @elseif($isYt)
                                            <span class="avatar-pill bg-red text-white"><i class="ti ti-brand-youtube"></i></span>
                                        @else
                                            <span class="avatar-pill bg-secondary text-white"><i class="ti ti-social"></i></span>
                                        @endif
                                    </span>
                                    <span class="account-chip-name">{{ $acc->name }}</span>
                                    <span class="account-chip-check"><i class="ti ti-check"></i></span>
                                </label>
                            @endforeach

                            <a href="{{ route('admin.accounts.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill d-inline-flex align-items-center gap-1 px-3 py-1.5" title="Connect another account">
                                <i class="ti ti-plus"></i> <span class="small">Add Channel</span>
                            </a>
                        </div>
                    </div>

                    {{-- 2. YouTube Specific Fields (Visible ONLY when YouTube account is selected) --}}
                    <div id="section-youtube" class="d-none mb-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required fw-semibold text-dark m-0">Video Title</label>
                                <span class="text-secondary small" id="yt-title-counter">0 / 100</span>
                            </div>
                            <input type="text" name="title" id="youtube-title" class="form-control" placeholder="Add an eye-catching video title..." maxlength="100" value="{{ old('title') }}">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold text-dark">Video File</label>
                                <input type="file" name="video_file" id="youtube-video-file" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                                <div class="form-text text-muted">Supports MP4, MOV, or WebM (up to 100MB)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Custom Thumbnail</label>
                                <input type="file" name="thumbnail" id="youtube-thumbnail" class="form-control" accept="image/jpeg,image/png">
                                <div class="form-text text-muted">Recommended: 1280x720 (optional)</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Tags</label>
                                <input type="text" name="tags" id="youtube-tags" class="form-control" placeholder="news, update, technology" value="{{ old('tags') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Privacy</label>
                                <select name="privacy_status" class="form-select" id="youtube-privacy-status">
                                    <option value="public" {{ old('privacy_status', 'public') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="unlisted" {{ old('privacy_status') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                                    <option value="private" {{ old('privacy_status') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-1">Video Description</label>
                            <textarea name="description" id="youtube-description" class="form-control" rows="3" placeholder="Tell viewers about your video...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- 3. Meta (Facebook & Instagram) Composer Area --}}
                    <div id="section-meta" class="mb-4">
                        {{-- Caption Textarea --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark m-0">Post Caption</label>
                                <span class="text-secondary small" id="caption-counter">0 / 5000</span>
                            </div>
                            <textarea name="content" id="composer-content" class="form-control" rows="5"
                                      placeholder="What's on your mind? Write your post caption here...">{{ old('content') }}</textarea>
                        </div>

                        {{-- Media Attachment --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-1">Photos / Videos</label>
                            <input type="file" name="media_files[]" class="form-control" id="composer-media-files" accept="image/*,video/*" multiple>
                            <div class="form-text text-muted" id="media-help-text">
                                Attach photos (JPG, PNG) or videos (MP4, MOV). Instagram posts require at least one photo or video.
                            </div>
                        </div>

                        {{-- Minimal Post Type selector (optional override) --}}
                        <div class="d-flex align-items-center gap-2 pt-1">
                            <span class="text-muted small fw-medium">Format:</span>
                            <div class="d-flex gap-1" id="post-type-selector">
                                <button type="button" class="btn btn-sm btn-ghost-secondary post-format-btn active" data-type="photo">
                                    <i class="ti ti-photo me-1"></i>Photo
                                </button>
                                <button type="button" class="btn btn-sm btn-ghost-secondary post-format-btn" data-type="carousel">
                                    <i class="ti ti-photo-circle me-1"></i>Carousel
                                </button>
                                <button type="button" class="btn btn-sm btn-ghost-secondary post-format-btn" data-type="video">
                                    <i class="ti ti-video me-1"></i>Video
                                </button>
                                <button type="button" class="btn btn-sm btn-ghost-secondary post-format-btn" data-type="reel" id="btn-format-reel">
                                    <i class="ti ti-player-play me-1"></i>Reel
                                </button>
                                <button type="button" class="btn btn-sm btn-ghost-secondary post-format-btn" data-type="text" id="btn-format-text">
                                    <i class="ti ti-typography me-1"></i>Text Only
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Publishing Schedule Mode --}}
                    <div class="pt-3 border-top">
                        <label class="form-label fw-bold text-dark mb-2">Publishing Mode</label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <label class="mode-pill {{ old('publish_mode', 'schedule') == 'schedule' ? 'active' : '' }}">
                                <input type="radio" name="publish_mode" value="schedule" class="d-none" {{ old('publish_mode', 'schedule') == 'schedule' ? 'checked' : '' }} id="radio-mode-schedule">
                                <i class="ti ti-clock me-1 text-primary"></i>Schedule for Later
                            </label>
                            <label class="mode-pill {{ old('publish_mode') == 'now' ? 'active' : '' }}">
                                <input type="radio" name="publish_mode" value="now" class="d-none" {{ old('publish_mode') == 'now' ? 'checked' : '' }} id="radio-mode-now">
                                <i class="ti ti-send me-1 text-success"></i>Publish Immediately
                            </label>
                            <label class="mode-pill {{ old('publish_mode') == 'draft' ? 'active' : '' }}">
                                <input type="radio" name="publish_mode" value="draft" class="d-none" {{ old('publish_mode') == 'draft' ? 'checked' : '' }} id="radio-mode-draft">
                                <i class="ti ti-file-pencil me-1 text-secondary"></i>Save as Draft
                            </label>
                        </div>

                        {{-- Date & Time picker for scheduling --}}
                        <div id="schedule-datetime-container" style="max-width: 320px;">
                            <label class="form-label small text-secondary mb-1">Choose Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="composer-scheduled-at" class="form-control"
                                   value="{{ old('scheduled_at', $prefillDate) }}">
                        </div>
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="card-footer d-flex justify-content-between align-items-center py-3 px-4 bg-transparent border-top">
                    <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4" id="btn-submit-post">
                        <i class="ti ti-calendar-check me-1.5"></i>Schedule Post
                    </button>
                </div>
            </div>
        </form>
    @endif

@endsection

@push('styles')
<style>
    /* Modern Buffer-style Account Chips */
    .account-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.85rem;
        border-radius: 50px;
        border: 1.5px solid rgba(4, 32, 69, 0.12);
        background: #ffffff;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease-in-out;
    }
    .account-chip:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }
    .account-chip.active {
        border-color: #206bc4 !important;
        background: rgba(32, 107, 196, 0.08) !important;
        color: #1e40af !important;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(32, 107, 196, 0.15);
    }
    .avatar-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        font-size: 0.72rem;
    }
    .account-chip-check {
        display: none;
        font-size: 0.85rem;
        color: #206bc4;
        margin-left: 1px;
    }
    .account-chip.active .account-chip-check {
        display: inline-flex;
    }

    /* Minimal Publishing Mode Pills */
    .mode-pill {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.14);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        background: #ffffff;
        color: #334155;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        user-select: none;
    }
    .mode-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .mode-pill.active {
        background: #ffffff;
        border-color: #206bc4 !important;
        color: #0f172a !important;
        font-weight: 600;
        box-shadow: 0 0 0 1px #206bc4, 0 1px 3px rgba(32, 107, 196, 0.15);
    }

    /* Post format subtle pills */
    .post-format-btn {
        border-radius: 6px;
        font-size: 0.8rem;
        padding: 0.25rem 0.6rem;
    }
    .post-format-btn.active {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        font-weight: 600;
    }

    /* Dark Mode Theme Support */
    [data-bs-theme="dark"] .account-chip {
        background: #111a2d;
        border-color: rgba(255, 255, 255, 0.12);
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .account-chip:hover {
        background: #162035;
        border-color: rgba(255, 255, 255, 0.25);
    }
    [data-bs-theme="dark"] .account-chip.active {
        background: rgba(59, 130, 246, 0.15) !important;
        border-color: #3b82f6 !important;
        color: #60a5fa !important;
    }
    [data-bs-theme="dark"] .account-chip.active .account-chip-check {
        color: #60a5fa;
    }
    [data-bs-theme="dark"] .mode-pill {
        background: #111a2d;
        border-color: rgba(255, 255, 255, 0.12);
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .mode-pill:hover {
        background: #162035;
    }
    [data-bs-theme="dark"] .mode-pill.active {
        background: #162035;
        border-color: #3b82f6 !important;
        color: #f8fafc !important;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    [data-bs-theme="dark"] .post-format-btn.active {
        background: #1e293b !important;
        color: #f8fafc !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Account Selection Handling (Click anywhere on chip toggles selection)
    $(document).on('click', '.account-chip', function (e) {
        if ($(e.target).is('input[type="checkbox"]')) {
            $(this).toggleClass('active', $(e.target).is(':checked'));
            updateComposerState();
            return;
        }

        let cb = $(this).find('.account-checkbox');
        let willCheck = !cb.is(':checked');
        cb.prop('checked', willCheck).trigger('change');
        $(this).toggleClass('active', willCheck);
        updateComposerState();
    });

    $(document).on('change', '.account-checkbox', function () {
        let chip = $(this).closest('.account-chip');
        chip.toggleClass('active', $(this).is(':checked'));
        updateComposerState();
    });

    // Select All / Deselect All
    $('#btn-select-all').on('click', function () {
        $('.account-checkbox').prop('checked', true);
        $('.account-chip').addClass('active');
        updateComposerState();
    });

    $('#btn-deselect-all').on('click', function () {
        $('.account-checkbox').prop('checked', false);
        $('.account-chip').removeClass('active');
        updateComposerState();
    });

    // 2. Format / Post Type Selector Buttons
    $('.post-format-btn').on('click', function () {
        $('.post-format-btn').removeClass('active');
        $(this).addClass('active');
        $('#selected-post-type').val($(this).data('type'));
    });

    // 3. Publishing Mode Selector (Schedule, Now, Draft)
    $('.mode-pill').on('click', function () {
        $('.mode-pill').removeClass('active');
        $(this).addClass('active');
        let radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true).trigger('change');
    });

    $('input[name="publish_mode"]').on('change', function () {
        let mode = $(this).val();
        if (mode === 'schedule') {
            $('#schedule-datetime-container').show();
            $('#composer-scheduled-at').prop('required', true);
        } else {
            $('#schedule-datetime-container').hide();
            $('#composer-scheduled-at').prop('required', false);
        }

        let hasYoutube = false;
        $('.account-checkbox:checked').each(function () {
            if (parseInt($(this).data('platform')) === 3) hasYoutube = true;
        });

        if (hasYoutube) {
            $('#youtube-video-file').prop('required', mode !== 'draft');
        }

        updateSubmitButton();
    });

    // 4. Dynamic Composer State (adapts to selected accounts)
    function updateComposerState() {
        let checked = $('.account-checkbox:checked');
        let count = checked.length;

        // Update count badge
        if (count === 0) {
            $('#selected-accounts-badge').text('0 selected')
                .removeClass('bg-primary-subtle text-primary border-primary-subtle')
                .addClass('bg-secondary-subtle text-secondary border-secondary-subtle');
            $('#composer-account-id-fallback').val('');
        } else {
            let txt = count === 1 ? '1 account selected' : count + ' accounts selected';
            $('#selected-accounts-badge').text(txt)
                .removeClass('bg-secondary-subtle text-secondary border-secondary-subtle')
                .addClass('bg-primary-subtle text-primary border-primary-subtle');
            $('#composer-account-id-fallback').val(checked.first().val());
        }

        // Detect platforms of checked accounts
        let hasFacebook = false;
        let hasInstagram = false;
        let hasYoutube = false;

        checked.each(function () {
            let p = parseInt($(this).data('platform'));
            if (p === 1) hasFacebook = true;
            if (p === 2) hasInstagram = true;
            if (p === 3) hasYoutube = true;
        });

        // Set fallback platform value
        if (hasYoutube && !hasFacebook && !hasInstagram) {
            $('#selected-platform').val(3);
        } else if (hasInstagram && !hasFacebook) {
            $('#selected-platform').val(2);
        } else {
            $('#selected-platform').val(1);
        }

        // Toggle YouTube and Meta Sections smoothly
        if (hasYoutube && !hasFacebook && !hasInstagram) {
            // ONLY YouTube accounts selected
            $('#section-youtube').removeClass('d-none');
            $('#section-meta').addClass('d-none');
            $('#youtube-title').prop('required', true);
            let mode = $('input[name="publish_mode"]:checked').val();
            $('#youtube-video-file').prop('required', mode !== 'draft');
        } else if (hasYoutube && (hasFacebook || hasInstagram)) {
            // Mixed Meta + YouTube selected
            $('#section-youtube').removeClass('d-none');
            $('#section-meta').removeClass('d-none');
            $('#youtube-title').prop('required', true);
        } else {
            // ONLY Meta (Facebook / Instagram) selected
            $('#section-youtube').addClass('d-none');
            $('#section-meta').removeClass('d-none');
            $('#youtube-title').prop('required', false);
            $('#youtube-video-file').prop('required', false);
        }

        // Instagram rules: disable text-only if Instagram is selected
        if (hasInstagram) {
            $('#btn-format-text').hide();
            if ($('#selected-post-type').val() === 'text') {
                $('#selected-post-type').val('photo');
                $('.post-format-btn').removeClass('active');
                $('.post-format-btn[data-type="photo"]').addClass('active');
            }
        } else {
            $('#btn-format-text').show();
        }

        updateSubmitButton();
    }

    // 5. Update Submit Button text dynamically
    function updateSubmitButton() {
        let mode = $('input[name="publish_mode"]:checked').val();
        let count = $('.account-checkbox:checked').length;
        let targetText = count > 1 ? count + ' Accounts' : 'Post';

        if (mode === 'now') {
            $('#btn-submit-post').html('<i class="ti ti-send me-1.5"></i>Publish to ' + targetText + ' Now');
        } else if (mode === 'draft') {
            $('#btn-submit-post').html('<i class="ti ti-file-pencil me-1.5"></i>Save Draft (' + targetText + ')');
        } else {
            $('#btn-submit-post').html('<i class="ti ti-calendar-check me-1.5"></i>Schedule for ' + targetText);
        }
    }

    // 6. Form Submit Validation
    $('#form-create-post').on('submit', function (e) {
        let count = $('.account-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Please select at least one social account to publish or schedule.');
            $('html, body').animate({
                scrollTop: $('#account-chips-list').offset().top - 80
            }, 250);
            return false;
        }
    });

    // Character counters
    $('#composer-content').on('input', function () {
        $('#caption-counter').text($(this).val().length + ' / 5000');
    });

    $('#youtube-title').on('input', function () {
        $('#yt-title-counter').text($(this).val().length + ' / 100');
    });

    // Auto-select first account if none is checked on first load
    if ($('.account-checkbox:checked').length === 0) {
        let firstChip = $('.account-chip').first();
        if (firstChip.length) {
            firstChip.find('.account-checkbox').prop('checked', true);
            firstChip.addClass('active');
        }
    }

    // Initial state trigger
    updateComposerState();
    $('input[name="publish_mode"]:checked').trigger('change');
});
</script>
@endpush
