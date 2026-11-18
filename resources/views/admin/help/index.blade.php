@extends('admin.layouts.app')

@section('title', 'Help Center & Support Tickets')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle text-secondary">Customer & Technical Support</div>
                <h2 class="page-title text-body">Help Center & Support Tickets</h2>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#create-ticket-modal">
                    <i class="ti ti-plus me-1.5 fs-3"></i>Add Ticket
                </button>
            </div>
        </div>
    </div>

    {{-- Top Row: 4 Support KPI Cards --}}
    <div class="row row-deck row-cards mb-3">

        {{-- 1. Total Tickets --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-primary-lt text-primary">
                                <i class="ti ti-ticket fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">TOTAL TICKETS</div>
                            <div class="h2 mb-0 fw-bold text-body" id="kpi-total-tickets">{{ number_format($totalTicketsCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-primary-lt small fw-semibold">Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Open Tickets --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-azure-lt text-azure">
                                <i class="ti ti-clock-play fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">OPEN TICKETS</div>
                            <div class="h2 mb-0 fw-bold text-body" id="kpi-open-tickets">{{ number_format($openTicketsCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-azure-lt small fw-semibold">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. In Progress --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-warning-lt text-warning">
                                <i class="ti ti-progress fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">IN PROGRESS</div>
                            <div class="h2 mb-0 fw-bold text-body" id="kpi-progress-tickets">{{ number_format($inProgressTicketsCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-warning-lt small fw-semibold">Working</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Resolved --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-success-lt text-success">
                                <i class="ti ti-circle-check fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7" style="letter-spacing: 0.05em;">RESOLVED</div>
                            <div class="h2 mb-0 fw-bold text-body" id="kpi-resolved-tickets">{{ number_format($resolvedTicketsCount) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <span class="badge bg-success-lt small fw-semibold">Solved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- DataTable Card --}}
    <div class="card">

        {{-- Toolbar --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">

                {{-- Search Box --}}
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" id="dt-search" class="form-control" style="width: 280px;"
                        placeholder="Search tickets by subject, ID..." />
                </div>

                {{-- Right Filters --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">

                    {{-- Category Filter --}}
                    <select id="filter-category" class="form-select" style="width: 190px;">
                        <option value="">All Categories</option>
                        @foreach ($categories as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Status Filter --}}
                    <select id="filter-status" class="form-select" style="width: 140px;">
                        <option value="">All Status</option>
                        @foreach ($statuses as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Priority Filter --}}
                    <select id="filter-priority" class="form-select" style="width: 130px;">
                        <option value="">All Priority</option>
                        @foreach ($priorities as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-ghost-secondary" id="refresh-table-btn"
                        data-bs-toggle="tooltip" title="Refresh Tickets">
                        <i class="ti ti-refresh"></i>
                    </button>

                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">

            {{-- Loading Spinner --}}
            <div id="tickets-loader" class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading…</span>
                </div>
            </div>

            {{-- Table --}}
            <div id="tickets-table-wrapper" class="table-responsive d-none">
                <table id="tickets-table" class="table table-vcenter table-hover w-100">
                    <thead>
                        <tr>
                            <th class="w-1">Ticket ID</th>
                            <th>Subject &amp; Description</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('admin.layouts.partials._table-empty', [
                'id' => 'tickets-empty',
                'title' => 'No support tickets found',
                'subtitle' => 'You haven\'t created any support tickets yet, or none match your search filters.'
            ])

        </div>
    </div>

    {{-- ───────────────────────────────────────────────────────────── --}}
    {{-- MODAL: CREATE NEW TICKET                                      --}}
    {{-- ───────────────────────────────────────────────────────────── --}}
    <div class="modal modal-blur fade" id="create-ticket-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="create-ticket-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <span class="avatar avatar-xs bg-primary-lt text-primary rounded">
                                <i class="ti ti-ticket fs-3"></i>
                            </span>
                            <span>Create Support Ticket</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Alert Error Container --}}
                        <div id="ticket-modal-alert" class="alert alert-danger d-none" role="alert"></div>

                        {{-- Subject / Title --}}
                        <div class="mb-3">
                            <label class="form-label required">Subject / Title</label>
                            <input type="text" name="title" id="ticket-title" class="form-control"
                                placeholder="Brief summary of the issue (e.g. Meta token expired or Post scheduling failed)" required maxlength="255">
                            <div class="form-hint">A clear subject helps our technical team assist you faster.</div>
                        </div>

                        <div class="row">
                            {{-- Category Dropdown --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Category</label>
                                <select name="category" id="ticket-category" class="form-select" required>
                                    <option value="" disabled selected>Select category...</option>
                                    @foreach ($categories as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Priority Dropdown --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" id="ticket-priority" class="form-select">
                                    <option value="low">Low - General query</option>
                                    <option value="medium" selected>Medium - Normal issue</option>
                                    <option value="high">High - Feature blocked</option>
                                    <option value="urgent">Urgent - Complete outage</option>
                                </select>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label required">Description</label>
                            <textarea name="description" id="ticket-description" rows="5" class="form-control"
                                placeholder="Explain the issue, error message, or question in detail. Mention affected social channels if relevant..." required minlength="10"></textarea>
                            <div class="form-hint">Minimum 10 characters required.</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center" id="btn-submit-ticket">
                            <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status" aria-hidden="true" id="submit-ticket-spinner"></span>
                            <i class="ti ti-send me-1.5" id="submit-ticket-icon"></i>
                            <span>Submit Ticket</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ───────────────────────────────────────────────────────────── --}}
    {{-- MODAL: VIEW TICKET DETAILS                                    --}}
    {{-- ───────────────────────────────────────────────────────────── --}}
    <div class="modal modal-blur fade" id="view-ticket-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-dark-lt font-monospace fs-7 fw-bold" id="vt-number">#TCK</span>
                            <span class="badge" id="vt-status-badge">Open</span>
                            <span class="badge" id="vt-priority-badge">Medium</span>
                        </div>
                        <h4 class="modal-title text-body fw-bold mb-0" id="vt-title">Ticket Subject</h4>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Metadata Row --}}
                    <div class="row g-2 mb-3 py-2 px-3 bg-body-tertiary rounded border border-light">
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block">Category:</span>
                            <span class="badge" id="vt-category-badge">Technical</span>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-secondary small d-block">Submitted:</span>
                            <span class="text-body fw-semibold small" id="vt-created-at">—</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold fs-7 text-uppercase">Description</label>
                        <div class="p-3 rounded bg-body border text-body" id="vt-description" style="white-space: pre-wrap; min-height: 90px; line-height: 1.6;">
                            —
                        </div>
                    </div>

                    {{-- Admin Response / Resolution Notes --}}
                    <div id="vt-resolution-box" class="d-none">
                        <label class="form-label text-success fw-semibold fs-7 text-uppercase d-flex align-items-center gap-1">
                            <i class="ti ti-message-check fs-3"></i>Support Team Response / Resolution
                        </label>
                        <div class="alert alert-success border-success-subtle mb-0" id="vt-admin-notes" style="white-space: pre-wrap; line-height: 1.6;">
                            —
                        </div>
                    </div>

                    <div id="vt-pending-box" class="alert alert-info d-flex align-items-center gap-2 mb-0">
                        <i class="ti ti-info-circle fs-2"></i>
                        <div>Our technical support team is reviewing your ticket and will update you shortly.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Close</button>
                    @if(auth()->user()->isAdmin())
                        <div class="d-flex align-items-center gap-2">
                            <select id="vt-admin-status-select" class="form-select form-select-sm" style="width: 140px;">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary" id="btn-update-ticket-status">Update Status</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('datatables_css')
    @include('admin.layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('admin.layouts.partials._datatable-cdn-js')

    <script>
        $(function () {
            let activeTicketId = null;

            // 1. Initialize DataTable
            let table = $('#tickets-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('admin.help.data') }}',
                    data: function (d) {
                        d.category = $('#filter-category').val();
                        d.status   = $('#filter-status').val();
                        d.priority = $('#filter-priority').val();
                    }
                },
                columns: [
                    { data: 'ticket_number', name: 'ticket_number', className: 'text-nowrap' },
                    { data: 'title', name: 'title' },
                    { data: 'category', name: 'category', className: 'text-nowrap' },
                    { data: 'priority', name: 'priority', className: 'text-nowrap' },
                    { data: 'status', name: 'status', className: 'text-nowrap' },
                    { data: 'created_at', name: 'created_at', className: 'text-nowrap' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end text-nowrap' },
                ],
                order: [[5, 'desc']], // Order by created_at desc
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
                dom: '<"top">rt<"d-flex align-items-center justify-content-between p-3 flex-wrap gap-2"li<"ms-auto"p>><"clear">',
                language: {
                    emptyTable: '',
                    zeroRecords: ''
                },
                initComplete: function () {
                    $('#tickets-loader').addClass('d-none');
                    $('#tickets-table-wrapper').removeClass('d-none');
                },
                drawCallback: function (settings) {
                    $('#tickets-loader').addClass('d-none');
                    let api = this.api();
                    let totalRecords = api.rows().count();

                    if (totalRecords === 0) {
                        $('#tickets-table-wrapper').addClass('d-none');
                        $('#tickets-empty').removeClass('d-none');
                    } else {
                        $('#tickets-table-wrapper').removeClass('d-none');
                        $('#tickets-empty').addClass('d-none');
                    }

                    // Re-init tooltips
                    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
                }
            });

            // 2. Custom Search Debounce
            let searchTimeout = null;
            $('#dt-search').on('keyup', function () {
                clearTimeout(searchTimeout);
                let val = $(this).val();
                searchTimeout = setTimeout(function () {
                    table.search(val).draw();
                }, 350);
            });

            // 3. Filter Change Handlers
            $('#filter-category, #filter-status, #filter-priority').on('change', function () {
                table.draw();
            });

            // 4. Refresh Button
            $('#refresh-table-btn').on('click', function () {
                let $btn = $(this);
                $btn.addClass('disabled').find('i').addClass('ti-spin');
                table.ajax.reload(function () {
                    $btn.removeClass('disabled').find('i').removeClass('ti-spin');
                }, false);
            });

            // 5. Submit New Ticket via AJAX
            $('#create-ticket-form').on('submit', function (e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $('#btn-submit-ticket');
                let $spinner = $('#submit-ticket-spinner');
                let $icon = $('#submit-ticket-icon');
                let $alert = $('#ticket-modal-alert');

                $alert.addClass('d-none').html('');
                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $icon.addClass('d-none');

                $.ajax({
                    url: '{{ route('admin.help.store') }}',
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (res) {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        $icon.removeClass('d-none');

                        if (res.success) {
                            let modalEl = document.getElementById('create-ticket-modal');
                            let modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            $form[0].reset();

                            // Reload table and update counts
                            table.ajax.reload(null, false);

                            let currentTotal = parseInt($('#kpi-total-tickets').text().replace(/,/g, '')) || 0;
                            let currentOpen = parseInt($('#kpi-open-tickets').text().replace(/,/g, '')) || 0;
                            $('#kpi-total-tickets').text(currentTotal + 1);
                            $('#kpi-open-tickets').text(currentOpen + 1);

                            // Optional Tabler Toast or alert
                            if (window.toastr) {
                                toastr.success(res.message);
                            }
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        $icon.removeClass('d-none');

                        let errMsg = 'Failed to submit ticket. Please check input.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = Object.values(xhr.responseJSON.errors).flat();
                            errMsg = errors.join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        $alert.html(errMsg).removeClass('d-none');
                    }
                });
            });

            // 6. View Ticket Modal Trigger
            $(document).on('click', '.btn-view-ticket', function () {
                let $btn = $(this);
                activeTicketId = $btn.data('id');

                $('#vt-number').text('#' + $btn.data('number'));
                $('#vt-title').text($btn.data('title'));
                $('#vt-description').text($btn.data('description'));
                $('#vt-created-at').text($btn.data('created'));

                // Status Badge
                let statusColor = $btn.data('status-color');
                $('#vt-status-badge').text($btn.data('status'))
                    .attr('class', 'badge bg-' + statusColor + '-lt');

                // Priority Badge
                let priorityColor = $btn.data('priority-color');
                $('#vt-priority-badge').text($btn.data('priority'))
                    .attr('class', 'badge bg-' + priorityColor + '-lt');

                // Category Badge
                let categoryColor = $btn.data('category-color');
                $('#vt-category-badge').text($btn.data('category'))
                    .attr('class', 'badge bg-' + categoryColor + '-lt');

                // Admin Notes / Resolution
                let notes = $btn.data('notes');
                if (notes && notes.trim() !== '') {
                    $('#vt-admin-notes').text(notes);
                    $('#vt-resolution-box').removeClass('d-none');
                    $('#vt-pending-box').addClass('d-none');
                } else {
                    $('#vt-resolution-box').addClass('d-none');
                    $('#vt-pending-box').removeClass('d-none');
                }

                @if(auth()->user()->isAdmin())
                    $('#vt-admin-status-select').val($btn.data('status').toLowerCase().replace(' ', '_'));
                @endif

                let modal = new bootstrap.Modal(document.getElementById('view-ticket-modal'));
                modal.show();
            });

            @if(auth()->user()->isAdmin())
                // Admin Status Update
                $('#btn-update-ticket-status').on('click', function () {
                    if (!activeTicketId) return;
                    let newStatus = $('#vt-admin-status-select').val();
                    let $btn = $(this);
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: '/admin/help/' + activeTicketId + '/status',
                        method: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function (res) {
                            $btn.prop('disabled', false);
                            let modal = bootstrap.Modal.getInstance(document.getElementById('view-ticket-modal'));
                            if (modal) modal.hide();
                            table.ajax.reload(null, false);
                        },
                        error: function () {
                            $btn.prop('disabled', false);
                            alert('Failed to update status.');
                        }
                    });
                });
            @endif
        });
    </script>
@endpush
