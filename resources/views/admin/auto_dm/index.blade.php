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

                    {{-- Channel Filter & Status Filter (No separate Filter button, auto-submits on change) --}}
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
                            <th style="min-width: 200px;">Rule & Account</th>
                            <th style="min-width: 150px;">Target Post</th>
                            <th style="min-width: 180px;">Keywords</th>
                            <th style="min-width: 240px;">DM Message</th>
                            <th style="width: 110px;" class="text-center">Sent</th>
                            <th style="width: 90px;" class="text-center">Status</th>
                            <th style="width: 100px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rule)
                            <tr id="rule-row-{{ $rule->id }}">
                                {{-- 1. Rule Name & Account --}}
                                <td>
                                    <div class="fw-bold text-dark mb-0.5">{{ $rule->name }}</div>
                                    <div class="d-flex align-items-center gap-1.5 small text-secondary">
                                        @if($rule->account->isFacebook())
                                            <span class="badge bg-blue-lt text-blue py-0.5 px-1.5"><i class="ti ti-brand-facebook me-1"></i>Facebook</span>
                                        @elseif($rule->account->isInstagram())
                                            <span class="badge bg-pink-lt text-pink py-0.5 px-1.5"><i class="ti ti-brand-instagram me-1"></i>Instagram</span>
                                        @endif
                                        <span class="text-truncate" style="max-width: 150px;">{{ $rule->account->name }}</span>
                                    </div>
                                </td>

                                {{-- 2. Target Scope (All Posts vs Specific Post) --}}
                                <td>
                                    @if($rule->post_id && $rule->post)
                                        <span class="badge bg-secondary-lt text-dark d-inline-flex align-items-center gap-1 py-1 px-2" title="{{ $rule->post->message ?? 'Specific Post' }}">
                                            <i class="ti ti-file-text fs-3 text-secondary"></i>
                                            <span class="text-truncate" style="max-width: 130px;">
                                                {{ Str::limit($rule->post->message ?? 'Post #' . $rule->post_id, 22) }}
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
                                        {{ $rule->match_type === 'exact' ? 'Exact match' : 'Contains keyword' }}
                                    </div>
                                </td>

                                {{-- 4. DM Preview & Public Reply --}}
                                <td>
                                    <div class="text-dark small mb-1" style="max-width: 280px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="{{ $rule->dm_message }}">
                                        {{ $rule->dm_message }}
                                    </div>
                                    @if($rule->public_reply)
                                        <div class="text-secondary fs-7 text-truncate" style="max-width: 280px;" title="{{ $rule->public_reply }}">
                                            <i class="ti ti-corner-down-right text-muted me-1"></i>Reply: <em>"{{ $rule->public_reply }}"</em>
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Trigger Count & Last Triggered --}}
                                <td class="text-center">
                                    <span class="badge bg-success-lt text-success fs-4 fw-bold px-2 py-0.5 mb-0.5">
                                        {{ number_format($rule->trigger_count) }}
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
                                    <div class="small fs-7 text-muted" id="rule-status-label-{{ $rule->id }}">
                                        {{ $rule->is_active ? 'Active' : 'Paused' }}
                                    </div>
                                </td>

                                {{-- 7. Actions (Dedicated Edit Page & Delete) --}}
                                <td class="text-end">
                                    <div class="btn-group">
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
    // Status Toggle Switch
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

    // Delete Confirmation Modal
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
