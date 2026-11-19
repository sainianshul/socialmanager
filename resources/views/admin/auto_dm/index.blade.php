@extends('admin.layouts.app')

@section('title', 'Auto DM Rules')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title text-body">Auto DM Rules</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.auto-dm.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-plus me-1.5 fs-3"></i>Create Rule
                </a>
            </div>
        </div>
    </div>

    {{-- Main Rules Card --}}
    <div class="card">
        {{-- Card Header & Filter Bar (Minimal & Simple) --}}
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.auto-dm.index') }}" class="w-100 m-0">
                <div class="row g-2 align-items-center">
                    {{-- Search Input --}}
                    <div class="col-12 col-md-5">
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search rules or keywords..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Channel Filter & Status Filter (Auto-submits on change, no filter button) --}}
                    <div class="col-12 col-md-7 d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                        <select name="account_id" class="form-select" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                            <option value="">All Accounts ({{ $accounts->count() }})</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                    [{{ $acc->isFacebook() ? 'FB' : 'IG' }}] {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="form-select" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Paused</option>
                        </select>

                        @if(request()->hasAny(['search', 'account_id', 'status']))
                            <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-ghost-secondary btn-icon" title="Reset Filters">
                                <i class="ti ti-x"></i>
                            </a>
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
                <p class="text-secondary mx-auto mb-3" style="max-width: 460px;">
                    @if(request()->hasAny(['search', 'account_id', 'status']))
                        No automation rules match your current filter criteria.
                    @else
                        No rules have been created yet. Create a rule to automatically reply to post comments via direct message.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'account_id', 'status']))
                    <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="ti ti-refresh me-1"></i>Clear Filters
                    </a>
                @else
                    <a href="{{ route('admin.auto-dm.create') }}" class="btn btn-primary px-3">
                        <i class="ti ti-plus me-1"></i>Create Rule
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Rule Name</th>
                            <th style="min-width: 180px;">Account</th>
                            <th style="min-width: 130px;">Target</th>
                            <th style="min-width: 150px;">Keywords</th>
                            <th style="width: 80px;" class="text-center">Sent</th>
                            <th style="width: 80px;" class="text-center">Status</th>
                            <th style="width: 110px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rule)
                            @php
                                $ruleData = [
                                    'id' => $rule->id,
                                    'name' => $rule->name,
                                    'platform' => $rule->account->isFacebook() ? 'Facebook' : 'Instagram',
                                    'account_name' => $rule->account->name,
                                    'target_type' => $rule->post_id ? 'Specific Post' : 'All Posts',
                                    'post_title' => $rule->post ? ($rule->post->message ? Str::limit($rule->post->message, 120) : 'Post #' . $rule->post->id) : null,
                                    'keywords' => $rule->keywords ?? [],
                                    'match_type' => $rule->match_type === 'exact' ? 'Exact Match' : 'Contains Keyword',
                                    'dm_message' => $rule->dm_message,
                                    'public_reply' => $rule->public_reply,
                                    'trigger_count' => $rule->trigger_count,
                                    'last_triggered' => $rule->last_triggered_at ? $rule->last_triggered_at->diffForHumans() : 'Never',
                                    'is_active' => (bool) $rule->is_active,
                                    'edit_url' => route('admin.auto-dm.edit', $rule->id),
                                ];
                            @endphp
                            <tr id="rule-row-{{ $rule->id }}">
                                {{-- 1. Rule Name (Single Line) --}}
                                <td>
                                    <span class="fw-semibold text-dark">{{ $rule->name }}</span>
                                </td>

                                {{-- 2. Account (Single Line) --}}
                                <td>
                                    <div class="d-inline-flex align-items-center">
                                        @if($rule->account->isFacebook())
                                            <span class="badge bg-blue-lt text-blue py-0.5 px-1.5 me-1.5"><i class="ti ti-brand-facebook me-1"></i>FB</span>
                                        @elseif($rule->account->isInstagram())
                                            <span class="badge bg-pink-lt text-pink py-0.5 px-1.5 me-1.5"><i class="ti ti-brand-instagram me-1"></i>IG</span>
                                        @endif
                                        <span class="text-secondary text-truncate" style="max-width: 140px;">{{ $rule->account->name }}</span>
                                    </div>
                                </td>

                                {{-- 3. Target Post (Single Line) --}}
                                <td>
                                    @if($rule->post_id && $rule->post)
                                        <span class="badge bg-secondary-lt text-dark" title="{{ $rule->post->message ?? 'Specific Post' }}">
                                            <i class="ti ti-file-text me-1"></i>Specific Post
                                        </span>
                                    @else
                                        <span class="badge bg-azure-lt text-azure">
                                            <i class="ti ti-world me-1"></i>All Posts
                                        </span>
                                    @endif
                                </td>

                                {{-- 4. Keywords (Single Line) --}}
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(is_array($rule->keywords))
                                            @foreach($rule->keywords as $kw)
                                                <span class="badge bg-blue-lt text-blue font-monospace px-1.5 py-0.5">{{ $kw }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>

                                {{-- 5. Trigger Count (Single Line) --}}
                                <td class="text-center">
                                    <span class="badge bg-success-lt text-success fw-bold px-2 py-0.5">
                                        {{ number_format($rule->trigger_count) }}
                                    </span>
                                </td>

                                {{-- 6. Status Toggle Switch (Single Line, no text below) --}}
                                <td class="text-center">
                                    <label class="form-check form-switch m-0 d-inline-block cursor-pointer">
                                        <input class="form-check-input rule-status-toggle" type="checkbox"
                                               data-id="{{ $rule->id }}"
                                               {{ $rule->is_active ? 'checked' : '' }}
                                               title="Toggle Active / Paused">
                                    </label>
                                </td>

                                {{-- 7. Actions (View Modal, Edit Page, Delete Modal) --}}
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary border btn-view-rule"
                                                data-rule='@json($ruleData)'
                                                title="View Details">
                                            <i class="ti ti-eye fs-3"></i>
                                        </button>
                                        <a href="{{ route('admin.auto-dm.edit', $rule->id) }}" class="btn btn-sm btn-icon btn-ghost-secondary border" title="Edit Rule">
                                            <i class="ti ti-pencil fs-3"></i>
                                        </a>
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

    {{-- MODAL: View Rule Details --}}
    <div class="modal modal-blur fade" id="view-rule-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded bg-purple-lt text-purple">
                            <i class="ti ti-message-chatbot fs-2"></i>
                        </span>
                        <h5 class="modal-title fw-bold" id="view-modal-title">Rule Details</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Key Info Table --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-borderless m-0">
                            <tbody>
                                <tr>
                                    <td class="text-secondary fw-medium py-1 px-0" style="width: 130px;">Account:</td>
                                    <td class="py-1 px-0" id="view-account-val"></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-medium py-1 px-0">Target Post:</td>
                                    <td class="py-1 px-0" id="view-target-val"></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-medium py-1 px-0">Keywords:</td>
                                    <td class="py-1 px-0" id="view-keywords-val"></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-medium py-1 px-0">Match Type:</td>
                                    <td class="py-1 px-0 text-dark fw-medium" id="view-match-val"></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-medium py-1 px-0">Executions:</td>
                                    <td class="py-1 px-0" id="view-executions-val"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- DM Message Block --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1 small">Direct Message (DM)</label>
                        <div class="p-3 rounded-2 border bg-light-subtle text-dark small" id="view-dm-val" style="white-space: pre-wrap; word-break: break-word;"></div>
                    </div>

                    {{-- Public Reply Block --}}
                    <div id="view-reply-container" class="mb-2">
                        <label class="form-label fw-bold text-dark mb-1 small">Public Reply</label>
                        <div class="p-2.5 rounded-2 border bg-light-subtle text-dark small" id="view-reply-val"></div>
                    </div>
                </div>
                <div class="modal-footer py-2.5 px-4 bg-light-subtle d-flex justify-content-between">
                    <a href="#" id="view-modal-edit-btn" class="btn btn-primary">
                        <i class="ti ti-pencil me-1"></i>Edit Rule
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Delete Rule Confirmation --}}
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
    // 1. View Details Modal Handler
    $(document).on('click', '.btn-view-rule', function () {
        const data = $(this).data('rule');
        if (!data) return;

        $('#view-modal-title').text(data.name);

        const badgeClass = data.platform === 'Facebook' ? 'bg-blue-lt text-blue' : 'bg-pink-lt text-pink';
        const icon = data.platform === 'Facebook' ? 'ti ti-brand-facebook' : 'ti ti-brand-instagram';
        $('#view-account-val').html(`
            <span class="badge ${badgeClass} me-1"><i class="${icon} me-1"></i>${data.platform}</span>
            <span class="text-dark fw-medium">${data.account_name}</span>
        `);

        if (data.post_title) {
            $('#view-target-val').html(`<span class="badge bg-secondary-lt text-dark">${data.post_title}</span>`);
        } else {
            $('#view-target-val').html(`<span class="badge bg-azure-lt text-azure"><i class="ti ti-world me-1"></i>All Posts</span>`);
        }

        let kwHtml = '';
        if (data.keywords && data.keywords.length > 0) {
            data.keywords.forEach(function (k) {
                kwHtml += `<span class="badge bg-blue-lt text-blue font-monospace me-1 mb-1">${k}</span>`;
            });
        }
        $('#view-keywords-val').html(kwHtml || '<span class="text-muted">None</span>');

        $('#view-match-val').text(data.match_type);

        $('#view-executions-val').html(`
            <span class="badge bg-success-lt text-success fw-bold me-1">${data.trigger_count} sent</span>
            <span class="text-muted small">Last: ${data.last_triggered}</span>
        `);

        $('#view-dm-val').text(data.dm_message);

        if (data.public_reply) {
            $('#view-reply-container').show();
            $('#view-reply-val').text(data.public_reply);
        } else {
            $('#view-reply-container').hide();
        }

        $('#view-modal-edit-btn').attr('href', data.edit_url);

        $('#view-rule-modal').modal('show');
    });

    // 2. Status Toggle Switch
    $(document).on('change', '.rule-status-toggle', function () {
        const checkbox = $(this);
        const ruleId = checkbox.data('id');

        $.ajax({
            url: "/admin/auto-dm/" + ruleId + "/toggle",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
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

    // 3. Delete Confirmation Modal
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
