@extends('admin.layouts.app')

@section('title', 'Edit Auto DM Rule')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title text-body">Edit Auto DM Rule</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to Rules
                </a>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <form method="POST" action="{{ route('admin.auto-dm.update', $autoDmRule->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">
                        {{-- 1. Rule Name & Account --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-7">
                                <label class="form-label required">Rule Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Price Enquiry" value="{{ old('name', $autoDmRule->name) }}" required maxlength="120">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label required">Account</label>
                                <select name="account_id" id="account-select" class="form-select @error('account_id') is-invalid @enderror" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('account_id', $autoDmRule->account_id) == $acc->id ? 'selected' : '' }}>
                                            [{{ $acc->isFacebook() ? 'Facebook' : 'Instagram' }}] {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- 2. Target Posts --}}
                        @php
                            $currentTargetType = old('target_type', $autoDmRule->post_id ? 'specific' : 'all');
                        @endphp
                        <div class="mb-3">
                            <label class="form-label mb-2">Target Post</label>
                            <div class="d-flex gap-2">
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="target_type" value="all" class="form-selectgroup-input" id="target-all" {{ $currentTargetType === 'all' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label p-2">All Posts</span>
                                </label>
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="target_type" value="specific" class="form-selectgroup-input" id="target-specific" {{ $currentTargetType === 'specific' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label p-2">Specific Post</span>
                                </label>
                            </div>

                            <div id="specific-post-container" class="mt-2 {{ $currentTargetType === 'specific' ? '' : 'd-none' }}">
                                <select name="post_id" id="post-select" class="form-select">
                                    <option value="">Select Post</option>
                                    @foreach($posts as $p)
                                        <option value="{{ $p->id }}" {{ old('post_id', $autoDmRule->post_id) == $p->id ? 'selected' : '' }}>
                                            [{{ $p->published_at ? $p->published_at->format('M d') : 'Recent' }}] {{ Str::limit($p->message ?? 'Post #' . $p->id, 70) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 3. Keywords & Match Condition --}}
                        @php
                            $keywordsVal = is_array($autoDmRule->keywords) ? implode(', ', $autoDmRule->keywords) : $autoDmRule->keywords;
                        @endphp
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label required">Trigger Keywords</label>
                                <input type="text" name="keywords" class="form-control @error('keywords') is-invalid @enderror" placeholder="e.g. link, price, details" value="{{ old('keywords', $keywordsVal) }}" required>
                                <div class="form-text small text-muted">Comma-separated</div>
                                @error('keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label required">Match Type</label>
                                <select name="match_type" class="form-select">
                                    <option value="contains" {{ old('match_type', $autoDmRule->match_type) === 'contains' ? 'selected' : '' }}>Contains keyword</option>
                                    <option value="exact" {{ old('match_type', $autoDmRule->match_type) === 'exact' ? 'selected' : '' }}>Exact match</option>
                                </select>
                            </div>
                        </div>

                        {{-- 4. DM Message --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label required m-0">DM Message</label>
                                <span class="text-muted small" id="dm-counter">0 / 1000</span>
                            </div>
                            <textarea name="dm_message" id="dm-message" class="form-control @error('dm_message') is-invalid @enderror" rows="4" placeholder="Write message to send in DM..." required maxlength="1000">{{ old('dm_message', $autoDmRule->dm_message) }}</textarea>
                            @error('dm_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. Optional Public Reply --}}
                        <div class="mb-3">
                            <label class="form-label">Public Reply <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="public_reply" class="form-control" placeholder="e.g. Sent you a DM! 📥" value="{{ old('public_reply', $autoDmRule->public_reply) }}" maxlength="500">
                        </div>

                        {{-- 6. Active Switch --}}
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $autoDmRule->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between py-3 bg-light-subtle">
                        <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-link link-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>Update Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Character counter
    function updateCounter() {
        const len = $('#dm-message').val().length;
        $('#dm-counter').text(len + ' / 1000');
    }
    $('#dm-message').on('input', updateCounter);
    updateCounter();

    // Toggle specific post selector
    $('input[name="target_type"]').on('change', function () {
        if ($('#target-specific').is(':checked')) {
            $('#specific-post-container').removeClass('d-none');
            loadPosts($('#account-select').val());
        } else {
            $('#specific-post-container').addClass('d-none');
        }
    });

    // Account change -> load posts
    $('#account-select').on('change', function () {
        if ($('#target-specific').is(':checked')) {
            loadPosts($(this).val());
        }
    });

    function loadPosts(accountId) {
        if (!accountId) {
            $('#post-select').html('<option value="">Select Post</option>');
            return;
        }

        $.ajax({
            url: "/admin/auto-dm/accounts/" + accountId + "/posts",
            type: "GET",
            dataType: "json",
            success: function (res) {
                let options = '<option value="">Select Post</option>';
                if (res.posts && res.posts.length > 0) {
                    res.posts.forEach(function (p) {
                        options += `<option value="${p.id}">${p.title}</option>`;
                    });
                } else {
                    options += '<option value="" disabled>No posts found</option>';
                }
                $('#post-select').html(options);
            }
        });
    }
});
</script>
@endpush
