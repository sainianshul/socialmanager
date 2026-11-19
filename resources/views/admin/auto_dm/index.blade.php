@extends('admin.layouts.app')

@section('title', 'Auto DM Automation')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle text-secondary">Lead Generation & Engagement</div>
                <h2 class="page-title text-body d-flex align-items-center gap-2">
                    <i class="ti ti-message-chatbot text-purple fs-1"></i>
                    Auto DM (Comment-to-Inbox)
                </h2>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center" id="btn-open-create-modal">
                    <i class="ti ti-plus me-1.5 fs-3"></i>Create Auto DM Rule
                </button>
            </div>
        </div>
    </div>

    {{-- Top Row: 4 KPI Cards --}}
    <div class="row row-deck row-cards mb-4">
        {{-- 1. Total Rules --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded bg-purple-lt text-purple me-3">
                            <i class="ti ti-robot fs-2"></i>
                        </span>
                        <div>
                            <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Total Rules</div>
                            <div class="h2 mb-0 fw-bold mt-0.5 text-body">{{ number_format($totalRules) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Active Rules --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded bg-success-lt text-success me-3">
                            <i class="ti ti-bolt fs-2"></i>
                        </span>
                        <div>
                            <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Active Rules</div>
                            <div class="h2 mb-0 fw-bold mt-0.5 text-body">{{ number_format($activeRules) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Total DMs Sent --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded bg-blue-lt text-blue me-3">
                            <i class="ti ti-send fs-2"></i>
                        </span>
                        <div>
                            <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">DMs Triggered</div>
                            <div class="h2 mb-0 fw-bold mt-0.5 text-body">{{ number_format($totalTriggered) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Monitored Accounts --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded bg-cyan-lt text-cyan me-3">
                            <i class="ti ti-brand-meta fs-2"></i>
                        </span>
                        <div>
                            <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Connected Channels</div>
                            <div class="h2 mb-0 fw-bold mt-0.5 text-body">{{ number_format($accounts->count()) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Rules Card --}}
    <div class="card">
        {{-- Card Header & Filter Bar --}}
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.auto-dm.index') }}" class="w-100">
                <div class="row g-2 align-items-center">
                    {{-- Search Input --}}
                    <div class="col-12 col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search rules, keywords, or DM text..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Channel Filter --}}
                    <div class="col-6 col-md-3">
                        <select name="account_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Channels ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                    [{{ $acc->isFacebook() ? 'FB' : 'IG' }}] {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Paused Only</option>
                        </select>
                    </div>

                    {{-- Submit / Reset --}}
                    <div class="col-12 col-md-2 text-md-end">
                        @if(request()->hasAny(['search', 'account_id', 'status']))
                            <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="ti ti-x me-1"></i>Reset
                            </a>
                        @else
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Rules Table --}}
        @if($rules->isEmpty())
            <div class="card-body p-5 text-center">
                <div class="avatar avatar-xl rounded-circle bg-purple-lt mx-auto mb-3">
                    <i class="ti ti-message-chatbot fs-1 text-purple"></i>
                </div>
                <h3 class="fw-bold mb-2">No Auto DM Rules Found</h3>
                <p class="text-secondary mx-auto mb-4" style="max-width: 480px;">
                    @if(request()->hasAny(['search', 'account_id', 'status']))
                        No automation rules matched your current search filters. Try clearing filters to see all rules.
                    @else
                        Automate your lead generation by sending instant private direct messages whenever someone comments with keywords like <strong>"link"</strong>, <strong>"price"</strong>, or <strong>"details"</strong> on your Facebook & Instagram posts.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'account_id', 'status']))
                    <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="ti ti-refresh me-1"></i>Clear Filters
                    </a>
                @else
                    <button type="button" class="btn btn-primary px-4" onclick="$('#btn-open-create-modal').click()">
                        <i class="ti ti-plus me-1.5"></i>Create Your First Auto DM Rule
                    </button>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th style="min-width: 220px;">Rule & Channel</th>
                            <th style="min-width: 160px;">Target Post</th>
                            <th style="min-width: 200px;">Trigger Keywords</th>
                            <th style="min-width: 240px;">Direct Message (DM)</th>
                            <th style="width: 120px;" class="text-center">Executions</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 120px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rule)
                            <tr id="rule-row-{{ $rule->id }}">
                                {{-- 1. Rule Name & Account --}}
                                <td>
                                    <div class="fw-bold text-dark fs-3 mb-1">{{ $rule->name }}</div>
                                    <div class="d-flex align-items-center gap-1.5 small text-secondary">
                                        @if($rule->account->isFacebook())
                                            <span class="badge bg-blue-lt text-blue py-0.5 px-1.5"><i class="ti ti-brand-facebook me-1"></i>FB Page</span>
                                        @elseif($rule->account->isInstagram())
                                            <span class="badge bg-pink-lt text-pink py-0.5 px-1.5"><i class="ti ti-brand-instagram me-1"></i>Instagram</span>
                                        @endif
                                        <span class="text-truncate" style="max-width: 150px;" title="{{ $rule->account->name }}">{{ $rule->account->name }}</span>
                                    </div>
                                </td>

                                {{-- 2. Target Scope (All Posts vs Specific Post) --}}
                                <td>
                                    @if($rule->post_id && $rule->post)
                                        <span class="badge bg-secondary-lt text-dark d-inline-flex align-items-center gap-1 py-1 px-2" title="{{ $rule->post->message ?? 'Specific Post' }}">
                                            <i class="ti ti-file-text fs-3 text-secondary"></i>
                                            <span class="text-truncate" style="max-width: 130px;">
                                                {{ Str::limit($rule->post->message ?? 'Specific Post #' . $rule->post_id, 22) }}
                                            </span>
                                        </span>
                                    @else
                                        <span class="badge bg-azure-lt text-azure d-inline-flex align-items-center gap-1 py-1 px-2">
                                            <i class="ti ti-world fs-3"></i> All Posts
                                        </span>
                                    @endif
                                </td>

                                {{-- 3. Keywords & Match Type --}}
                                <td>
                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                        @if(is_array($rule->keywords))
                                            @foreach($rule->keywords as $kw)
                                                <span class="badge bg-blue-lt text-blue font-monospace px-1.5 py-0.5">{{ $kw }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="small text-muted fs-7">
                                        Match: <span class="fw-medium text-secondary">{{ $rule->match_type === 'exact' ? 'Exact Match' : 'Contains Keyword' }}</span>
                                    </div>
                                </td>

                                {{-- 4. DM Preview & Public Reply --}}
                                <td>
                                    <div class="text-dark small mb-1" style="max-width: 280px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="{{ $rule->dm_message }}">
                                        <i class="ti ti-message-dots text-primary me-1"></i>{{ $rule->dm_message }}
                                    </div>
                                    @if($rule->public_reply)
                                        <div class="text-secondary fs-7 text-truncate" style="max-width: 280px;" title="{{ $rule->public_reply }}">
                                            <i class="ti ti-corner-down-right text-muted me-1"></i>Reply: <em>"{{ $rule->public_reply }}"</em>
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Trigger Count & Last Triggered --}}
                                <td class="text-center">
                                    <span class="badge bg-success-lt text-success fs-4 fw-bold px-2 py-1 mb-1">
                                        <i class="ti ti-send me-1"></i>{{ number_format($rule->trigger_count) }}
                                    </span>
                                    <div class="small text-muted fs-7">
                                        {{ $rule->last_triggered_at ? $rule->last_triggered_at->diffForHumans() : 'Never' }}
                                    </div>
                                </td>

                                {{-- 6. Status Toggle Switch --}}
                                <td class="text-center">
                                    <label class="form-check form-switch m-0 d-inline-block cursor-pointer">
                                        <input class="form-check-input rule-status-toggle" type="checkbox"
                                               data-id="{{ $rule->id }}"
                                               {{ $rule->is_active ? 'checked' : '' }}
                                               title="Toggle Active / Paused">
                                    </label>
                                    <div class="small fs-7 fw-medium text-muted mt-0.5" id="rule-status-label-{{ $rule->id }}">
                                        {{ $rule->is_active ? 'Active' : 'Paused' }}
                                    </div>
                                </td>

                                {{-- 7. Actions --}}
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary border btn-edit-rule" data-id="{{ $rule->id }}" title="Edit Rule">
                                            <i class="ti ti-pencil fs-3"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-ghost-danger border btn-delete-rule" data-id="{{ $rule->id }}" data-name="{{ $rule->name }}" title="Delete Rule">
                                            <i class="ti ti-trash fs-3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($rules->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between py-2">
                    <p class="m-0 text-secondary small">
                        Showing <span>{{ $rules->firstItem() }}</span> to <span>{{ $rules->lastItem() }}</span> of <span>{{ $rules->total() }}</span> rules
                    </p>
                    {{ $rules->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL: Create & Edit Auto DM Rule                                  --}}
    {{-- ================================================================= --}}
    <div class="modal modal-blur fade" id="rule-modal" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="rule-form" method="POST" action="{{ route('admin.auto-dm.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="rule-form-method" value="POST">
                    <input type="hidden" name="rule_id" id="rule-form-id" value="">

                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded bg-purple-lt text-purple">
                                <i class="ti ti-message-chatbot fs-2"></i>
                            </span>
                            <h5 class="modal-title fw-bold" id="rule-modal-title">Create Auto DM Rule</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- 1. Rule Name & Account Selection --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-7">
                                <label class="form-label fw-bold text-dark required">Rule Name</label>
                                <input type="text" name="name" id="field-name" class="form-control" placeholder="e.g. Free Ebook Download, Price Enquiry" required maxlength="120">
                                <div class="form-text small text-secondary">A recognizable name to identify this automation.</div>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-bold text-dark required">Connected Account</label>
                                <select name="account_id" id="field-account-id" class="form-select" required>
                                    <option value="">-- Select Channel --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-type="{{ $acc->account_type }}">
                                            [{{ $acc->isFacebook() ? 'FB Page' : 'Instagram' }}] {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 2. Target Scope: All Posts vs Specific Post --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2">Target Posts</label>
                            <div class="d-flex gap-2">
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="target_type" value="all" class="form-selectgroup-input" checked id="target-type-all">
                                    <span class="form-selectgroup-label d-flex align-items-center justify-content-center gap-2 p-2.5">
                                        <i class="ti ti-world fs-2 text-azure"></i>
                                        <span class="fw-semibold">All Posts on this Account</span>
                                    </span>
                                </label>
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="target_type" value="specific" class="form-selectgroup-input" id="target-type-specific">
                                    <span class="form-selectgroup-label d-flex align-items-center justify-content-center gap-2 p-2.5">
                                        <i class="ti ti-file-text fs-2 text-primary"></i>
                                        <span class="fw-semibold">Specific Post Only</span>
                                    </span>
                                </label>
                            </div>

                            {{-- Dynamic Specific Post Selector (shown only when 'specific' is selected) --}}
                            <div id="specific-post-box" class="mt-2.5 p-3 rounded-2 border bg-light-subtle d-none">
                                <label class="form-label small fw-bold text-dark mb-1">Select Specific Post</label>
                                <select name="post_id" id="field-post-id" class="form-select">
                                    <option value="">-- Choose Post --</option>
                                </select>
                                <div class="form-text small text-secondary" id="post-loading-hint">Select a channel first to view its recent posts.</div>
                            </div>
                        </div>

                        {{-- 3. Trigger Keywords & Match Condition --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-bold text-dark required">Trigger Keywords</label>
                                <input type="text" name="keywords" id="field-keywords" class="form-control" placeholder="e.g. link, price, ebook, details, info" required>
                                <div class="form-text small text-secondary">
                                    Separate multiple trigger words with commas. Case-insensitive.
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold text-dark required">Match Condition</label>
                                <select name="match_type" id="field-match-type" class="form-select">
                                    <option value="contains">Contains Keyword</option>
                                    <option value="exact">Exact Match Only</option>
                                </select>
                                <div class="form-text small text-secondary">
                                    <em>Contains</em> triggers even inside a full sentence.
                                </div>
                            </div>
                        </div>

                        {{-- 4. Direct Message (DM) to Send --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark m-0 required">Private Direct Message (DM)</label>
                                <span class="text-secondary small" id="dm-counter">0 / 1000</span>
                            </div>
                            <textarea name="dm_message" id="field-dm-message" class="form-control" rows="4" maxlength="1000" placeholder="Hey! Thanks for commenting. Here is the link you requested: https://..." required></textarea>
                            <div class="form-text small text-secondary">
                                This message is delivered directly into the commenter's Facebook Messenger or Instagram Direct inbox.
                            </div>
                        </div>

                        {{-- 5. Optional Public Comment Reply --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">
                                Public Comment Reply <span class="text-secondary fw-normal small">(Optional)</span>
                            </label>
                            <input type="text" name="public_reply" id="field-public-reply" class="form-control" placeholder="e.g. Sent you a DM! Check your inbox 📥" maxlength="500">
                            <div class="form-text small text-secondary">
                                Recommended by Meta: Leave a quick public reply so the commenter is notified to open their inbox.
                            </div>
                        </div>

                        {{-- 6. Active Switch --}}
                        <div class="form-check form-switch m-0 pt-1">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="field-is-active" checked>
                            <label class="form-check-label fw-semibold text-dark" for="field-is-active">
                                Activate rule immediately upon saving
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer bg-light-subtle">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-auto" id="btn-submit-rule">
                            <i class="ti ti-check me-1"></i>Save Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL: Delete Rule Confirmation                                   --}}
    {{-- ================================================================= --}}
    <div class="modal modal-blur fade" id="delete-rule-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="delete-rule-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <i class="ti ti-alert-triangle text-danger fs-1 mb-2"></i>
                        <h3 class="fw-bold">Delete Auto DM Rule?</h3>
                        <div class="text-secondary small">
                            Are you sure you want to delete <strong id="delete-rule-name" class="text-dark">this rule</strong>? Future comments will no longer trigger direct messages.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-danger w-100">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const storeUrl = "{{ route('admin.auto-dm.store') }}";
    let currentLoadedAccountId = null;

    // --- Helper: Update DM character counter ---
    function updateDmCounter() {
        const len = $('#field-dm-message').val().length;
        $('#dm-counter').text(len + ' / 1000');
    }
    $('#field-dm-message').on('input', updateDmCounter);

    // --- Target Type Radio change: All vs Specific Post ---
    $('input[name="target_type"]').on('change', function () {
        if ($('#target-type-specific').is(':checked')) {
            $('#specific-post-box').removeClass('d-none');
            loadAccountPosts($('#field-account-id').val());
        } else {
            $('#specific-post-box').addClass('d-none');
        }
    });

    // --- Account Dropdown change in Modal ---
    $('#field-account-id').on('change', function () {
        if ($('#target-type-specific').is(':checked')) {
            loadAccountPosts($(this).val());
        }
    });

    // --- Function to fetch recent posts via AJAX ---
    function loadAccountPosts(accountId, preselectPostId = null) {
        if (!accountId) {
            $('#field-post-id').html('<option value="">-- Choose Post --</option>');
            $('#post-loading-hint').text('Please select a connected channel above first.');
            return;
        }

        if (currentLoadedAccountId === accountId && !preselectPostId) {
            return; // Already loaded for this account
        }

        $('#post-loading-hint').html('<span class="spinner-border spinner-border-sm me-1 text-primary"></span> Loading recent posts...');

        $.ajax({
            url: "/admin/auto-dm/accounts/" + accountId + "/posts",
            type: "GET",
            dataType: "json",
            success: function (res) {
                currentLoadedAccountId = accountId;
                let options = '<option value="">-- Choose Post --</option>';
                if (res.posts && res.posts.length > 0) {
                    res.posts.forEach(function (p) {
                        const sel = (preselectPostId && String(preselectPostId) === String(p.id)) ? 'selected' : '';
                        options += `<option value="${p.id}" ${sel}>${p.title}</option>`;
                    });
                    $('#post-loading-hint').text('Loaded ' + res.posts.length + ' recent posts.');
                } else {
                    options += '<option value="" disabled>No published posts found on this account</option>';
                    $('#post-loading-hint').text('No posts found for this account.');
                }
                $('#field-post-id').html(options);
            },
            error: function () {
                $('#post-loading-hint').text('Unable to load posts for this account.');
            }
        });
    }

    // --- Open Create Modal ---
    $('#btn-open-create-modal').on('click', function () {
        $('#rule-modal-title').text('Create Auto DM Rule');
        $('#rule-form').attr('action', storeUrl);
        $('#rule-form-method').val('POST');
        $('#rule-form-id').val('');

        // Reset fields
        $('#field-name').val('');
        $('#field-account-id').val('').trigger('change');
        $('#target-type-all').prop('checked', true).trigger('change');
        $('#field-keywords').val('');
        $('#field-match-type').val('contains');
        $('#field-dm-message').val('');
        $('#field-public-reply').val('');
        $('#field-is-active').prop('checked', true);
        updateDmCounter();

        $('#rule-modal').modal('show');
    });

    // --- Open Edit Modal ---
    $(document).on('click', '.btn-edit-rule', function () {
        const ruleId = $(this).data('id');
        const editUrl = "/admin/auto-dm/" + ruleId + "/edit";
        const updateUrl = "/admin/auto-dm/" + ruleId;

        $('#rule-modal-title').text('Edit Auto DM Rule');
        $('#rule-form').attr('action', updateUrl);
        $('#rule-form-method').val('PUT');
        $('#rule-form-id').val(ruleId);

        $.ajax({
            url: editUrl,
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (res.success && res.rule) {
                    const r = res.rule;
                    $('#field-name').val(r.name);
                    $('#field-account-id').val(r.account_id);
                    $('#field-keywords').val(r.keywords_string);
                    $('#field-match-type').val(r.match_type);
                    $('#field-dm-message').val(r.dm_message);
                    $('#field-public-reply').val(r.public_reply);
                    $('#field-is-active').prop('checked', r.is_active);
                    updateDmCounter();

                    if (r.target_type === 'specific' && r.post_id) {
                        $('#target-type-specific').prop('checked', true);
                        $('#specific-post-box').removeClass('d-none');
                        loadAccountPosts(r.account_id, r.post_id);
                    } else {
                        $('#target-type-all').prop('checked', true);
                        $('#specific-post-box').addClass('d-none');
                    }

                    $('#rule-modal').modal('show');
                } else {
                    showToast('Failed to load rule details.', 'error');
                }
            },
            error: function () {
                showToast('Unable to fetch rule details.', 'error');
            }
        });
    });

    // --- Status Toggle Switch ---
    $(document).on('change', '.rule-status-toggle', function () {
        const checkbox = $(this);
        const ruleId = checkbox.data('id');
        const labelEl = $('#rule-status-label-' + ruleId);

        $.ajax({
            url: "/admin/auto-dm/" + ruleId + "/toggle",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    labelEl.text(res.is_active ? 'Active' : 'Paused');
                    showToast(res.message, 'success');
                } else {
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    showToast('Failed to update status.', 'error');
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                showToast('Server error while toggling rule.', 'error');
            }
        });
    });

    // --- Delete Confirmation Modal ---
    $(document).on('click', '.btn-delete-rule', function () {
        const ruleId = $(this).data('id');
        const ruleName = $(this).data('name');
        $('#delete-rule-name').text(ruleName);
        $('#delete-rule-form').attr('action', "/admin/auto-dm/" + ruleId);
        $('#delete-rule-modal').modal('show');
    });
});
</script>
@endpush
