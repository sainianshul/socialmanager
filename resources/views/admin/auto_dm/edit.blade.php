@extends('admin.layouts.app')

@section('title', 'Edit Auto DM Rule')

@section('breadcrumbs')
    <ol class="breadcrumb m-0" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-home fs-3"></i></a></li>
        <li class="breadcrumb-item">Automation</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.auto-dm.index') }}">Auto DM</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Rule</li>
    </ol>
@endsection

@php
    $initialPlatform = $autoDmRule->account->account_type ?? \App\Models\Account::TYPE_FACEBOOK;
    if (old('account_id')) {
        $found = $accounts->firstWhere('id', old('account_id'));
        if ($found) {
            $initialPlatform = $found->account_type;
        }
    }
@endphp

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title text-dark">Edit Auto DM Rule</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to Rules
                </a>
            </div>
        </div>
    </div>

    {{-- Validation Error Banner --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                <div>
                    <h4 class="alert-title mb-1">Please correct the following errors:</h4>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    {{-- Main Form Card (Styled like Add Post composer) --}}
    <div class="card shadow-xs mb-4">
        <div class="card-header py-3 px-4">
            <h3 class="card-title fw-bold m-0 text-dark">Edit Rule Configuration</h3>
        </div>

        <form method="POST" action="{{ route('admin.auto-dm.update', $autoDmRule->id) }}" id="form-auto-dm">
            @csrf
            @method('PUT')

            <div class="card-body p-4">
                {{-- 1. Rule Name --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2 required">Rule Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Price Enquiry" value="{{ old('name', $autoDmRule->name) }}" required maxlength="120">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2. Target Platform Selection (Add Post style buttons) --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">Target Platform</label>
                    <div class="d-flex gap-2 flex-wrap" id="platform-btn-group">
                        <button type="button" class="btn composer-platform-btn platform-fb {{ $initialPlatform === \App\Models\Account::TYPE_FACEBOOK ? 'active' : '' }}" data-platform="1">
                            <i class="ti ti-brand-facebook fs-2 me-1.5"></i>Facebook
                        </button>
                        <button type="button" class="btn composer-platform-btn platform-ig {{ $initialPlatform === \App\Models\Account::TYPE_INSTAGRAM ? 'active' : '' }}" data-platform="2">
                            <i class="ti ti-brand-instagram fs-2 me-1.5"></i>Instagram
                        </button>
                    </div>
                </div>

                {{-- 3. Account Selector --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2 required" id="account-field-label">
                        Select {{ $initialPlatform === \App\Models\Account::TYPE_FACEBOOK ? 'Facebook Page' : 'Instagram Account' }}
                    </label>
                    <select name="account_id" id="account-select" class="form-select @error('account_id') is-invalid @enderror" required>
                        <option value="">-- Choose Account --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" data-platform="{{ $acc->account_type }}"
                                    {{ old('account_id', $autoDmRule->account_id) == $acc->id ? 'selected' : '' }}>
                                [{{ $acc->isFacebook() ? 'Facebook' : 'Instagram' }}] {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 4. Target Posts (All Posts vs Specific Post pills) --}}
                @php
                    $currentTargetType = old('target_type', $autoDmRule->post_id ? 'specific' : 'all');
                @endphp
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">Target Posts</label>
                    <input type="hidden" name="target_type" id="target-type-input" value="{{ $currentTargetType }}">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="post-type-pill {{ $currentTargetType === 'all' ? 'active' : '' }}" data-target="all">
                            <i class="ti ti-world"></i> All Posts
                        </span>
                        <span class="post-type-pill {{ $currentTargetType === 'specific' ? 'active' : '' }}" data-target="specific">
                            <i class="ti ti-file-text"></i> Specific Post
                        </span>
                    </div>

                    <div id="specific-post-container" class="mt-2.5 {{ $currentTargetType === 'specific' ? '' : 'd-none' }}">
                        <label class="form-label fw-bold text-dark small mb-1">Choose Post</label>
                        <select name="post_id" id="post-select" class="form-select">
                            <option value="">-- Select Post --</option>
                            @foreach($posts as $p)
                                <option value="{{ $p->id }}" {{ old('post_id', $autoDmRule->post_id) == $p->id ? 'selected' : '' }}>
                                    [{{ $p->published_at ? $p->published_at->format('M d') : 'Recent' }}] {{ Str::limit($p->message ?? 'Post #' . $p->id, 70) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 5. Trigger Keywords & Match Condition --}}
                @php
                    $keywordsVal = is_array($autoDmRule->keywords) ? implode(', ', $autoDmRule->keywords) : $autoDmRule->keywords;
                    $currentMatchType = old('match_type', $autoDmRule->match_type);
                @endphp
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-7">
                        <label class="form-label fw-bold text-dark mb-2 required">Trigger Keywords</label>
                        <input type="text" name="keywords" class="form-control @error('keywords') is-invalid @enderror" placeholder="e.g. link, price, details" value="{{ old('keywords', $keywordsVal) }}" required>
                        <div class="form-text text-muted small mt-1">Comma-separated</div>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-5">
                        <label class="form-label fw-bold text-dark mb-2">Match Condition</label>
                        <input type="hidden" name="match_type" id="match-type-input" value="{{ $currentMatchType }}">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="post-type-pill {{ $currentMatchType === 'contains' ? 'active' : '' }}" data-match="contains">
                                <i class="ti ti-search"></i> Contains Keyword
                            </span>
                            <span class="post-type-pill {{ $currentMatchType === 'exact' ? 'active' : '' }}" data-match="exact">
                                <i class="ti ti-check"></i> Exact Match
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 6. DM Message --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark m-0 required">Direct Message (DM)</label>
                        <span class="text-secondary small" id="dm-counter">0 / 1000</span>
                    </div>
                    <textarea name="dm_message" id="dm-message" class="form-control @error('dm_message') is-invalid @enderror" rows="4" placeholder="Write message to send in DM..." required maxlength="1000">{{ old('dm_message', $autoDmRule->dm_message) }}</textarea>
                    @error('dm_message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 7. Optional Public Comment Reply --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">Public Comment Reply <span class="text-secondary fw-normal small">(Optional)</span></label>
                    <input type="text" name="public_reply" class="form-control" placeholder="e.g. Sent you a direct message." value="{{ old('public_reply', $autoDmRule->public_reply) }}" maxlength="500">
                </div>

                {{-- 8. Active Switch --}}
                <div class="mb-2">
                    <label class="form-check form-switch m-0 d-inline-flex align-items-center cursor-pointer">
                        <input class="form-check-input me-2" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $autoDmRule->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold text-dark">Active</span>
                    </label>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center py-3 px-4">
                <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ti ti-check me-1.5"></i>Update Rule
                </button>
            </div>
        </form>
    </div>

@endsection

@push('styles')
<style>
    /* Add Post Style Buttons & Pills */
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    // 1. Character counter
    function updateCounter() {
        const len = $('#dm-message').val().length;
        $('#dm-counter').text(len + ' / 1000');
    }
    $('#dm-message').on('input', updateCounter);
    updateCounter();

    // 2. Platform Filter logic
    function filterAccountsByPlatform(platformId) {
        let firstAvailableVal = null;
        $('#account-select option').each(function () {
            const opt = $(this);
            const val = opt.val();
            if (!val) return;

            const p = opt.data('platform');
            if (String(p) === String(platformId)) {
                opt.show();
                if (!firstAvailableVal) {
                    firstAvailableVal = val;
                }
            } else {
                opt.hide();
            }
        });

        const currentSelectedOpt = $('#account-select option:selected');
        if (currentSelectedOpt.val() && String(currentSelectedOpt.data('platform')) !== String(platformId)) {
            if (firstAvailableVal) {
                $('#account-select').val(firstAvailableVal).trigger('change');
            } else {
                $('#account-select').val('');
            }
        }
    }

    $('.composer-platform-btn').on('click', function () {
        $('.composer-platform-btn').removeClass('active');
        $(this).addClass('active');

        const platformId = $(this).data('platform');
        const platformName = platformId === 1 ? 'Facebook Page' : 'Instagram Account';
        $('#account-field-label').text('Select ' + platformName);

        filterAccountsByPlatform(platformId);
    });

    const initialPlatform = $('.composer-platform-btn.active').data('platform');
    if (initialPlatform) {
        filterAccountsByPlatform(initialPlatform);
    }

    // 3. Target Post Pills
    $('.post-type-pill[data-target]').on('click', function () {
        $('.post-type-pill[data-target]').removeClass('active');
        $(this).addClass('active');

        const target = $(this).data('target');
        $('#target-type-input').val(target);

        if (target === 'specific') {
            $('#specific-post-container').removeClass('d-none');
            loadPosts($('#account-select').val());
        } else {
            $('#specific-post-container').addClass('d-none');
        }
    });

    // 4. Match Condition Pills
    $('.post-type-pill[data-match]').on('click', function () {
        $('.post-type-pill[data-match]').removeClass('active');
        $(this).addClass('active');

        const match = $(this).data('match');
        $('#match-type-input').val(match);
    });

    // 5. Account change -> load posts
    $('#account-select').on('change', function () {
        if ($('#target-type-input').val() === 'specific') {
            loadPosts($(this).val());
        }
    });

    function loadPosts(accountId) {
        if (!accountId) {
            $('#post-select').html('<option value="">-- Select Post --</option>');
            return;
        }

        $.ajax({
            url: "/admin/auto-dm/accounts/" + accountId + "/posts",
            type: "GET",
            dataType: "json",
            success: function (res) {
                let options = '<option value="">-- Select Post --</option>';
                if (res.posts && res.posts.length > 0) {
                    res.posts.forEach(function (p) {
                        options += `<option value="${p.id}">${p.title}</option>`;
                    });
                } else {
                    options += '<option value="" disabled>No published posts found</option>';
                }
                $('#post-select').html(options);
            }
        });
    }
});
</script>
@endpush
