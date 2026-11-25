@extends('admin.layouts.app')

@section('title', 'Help Center & Support Tickets')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
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
                    <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Total Tickets</div>
                    <div class="h2 mb-0 fw-bold mt-1 text-body" id="kpi-total-tickets">{{ number_format($totalTicketsCount) }}</div>
                </div>
            </div>
        </div>

        {{-- 2. Open Tickets --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Open Tickets</div>
                    <div class="h2 mb-0 fw-bold mt-1 text-body" id="kpi-open-tickets">{{ number_format($openTicketsCount) }}</div>
                </div>
            </div>
        </div>

        {{-- 3. In Progress --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">In Progress</div>
                    <div class="h2 mb-0 fw-bold mt-1 text-body" id="kpi-progress-tickets">{{ number_format($inProgressTicketsCount) }}</div>
                </div>
            </div>
        </div>

        {{-- 4. Resolved --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary small fw-medium text-uppercase" style="letter-spacing: 0.04em;">Resolved</div>
                    <div class="h2 mb-0 fw-bold mt-1 text-body" id="kpi-resolved-tickets">{{ number_format($resolvedTicketsCount) }}</div>
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
                        placeholder="Search tickets..." />
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
                <table id="tickets-table" class="table table-vcenter w-100">
                    <thead>
                        <tr>
                            <th class="w-1">Ticket ID</th>
                            @if(auth()->user()->canManageTickets())
                                <th>User</th>
                                <th>Email</th>
                            @endif
                            <th>Title</th>
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
                        <h5 class="modal-title">Create Support Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Alert Error Container --}}
                        <div id="ticket-modal-alert" class="alert alert-danger d-none" role="alert"></div>

                        {{-- Subject / Title --}}
                        <div class="mb-3">
                            <label class="form-label required">Subject / Title</label>
                            <input type="text" name="title" id="ticket-title" class="form-control"
                                placeholder="Enter title" required maxlength="255">
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
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label required">Description</label>
                            <textarea name="description" id="ticket-description" rows="5" class="form-control"
                                placeholder="Enter description..." required minlength="10"></textarea>
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
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;" role="document">
            <div class="modal-content">
                {{-- Modal Header: Ticket #ID --}}
                <div class="modal-header">
                    <h3 class="modal-title fw-bold">Ticket <span class="text-primary font-monospace" id="vt-number">#TCK-XXXX</span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body: Clean Key-Value Structure --}}
                <div class="modal-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @if(auth()->user()->canManageTickets())
                            <tr>
                                <th class="text-secondary fw-medium small ps-0 py-1.5 align-middle" style="width: 130px;">Submitted by :</th>
                                <td class="text-body fw-semibold py-1.5" id="vt-user-info">—</td>
                            </tr>
                            @endif
                            <tr>
                                <th class="text-secondary fw-medium small ps-0 py-1.5 align-middle" style="width: 130px;">Submitted At :</th>
                                <td class="text-body py-1.5" id="vt-created-at">—</td>
                            </tr>
                            <tr>
                                <th class="text-secondary fw-medium small ps-0 py-1.5 align-middle" style="width: 130px;">Status :</th>
                                <td class="py-1.5">
                                    <span class="badge badge-outline" id="vt-status-badge">Open</span>
                                    <span class="badge badge-outline ms-1" id="vt-priority-badge">Medium</span>
                                    <span class="badge badge-outline text-secondary ms-1" id="vt-category-badge">Category</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-secondary fw-medium small ps-0 py-1.5 align-middle" style="width: 130px;">Title :</th>
                                <td class="text-body fw-semibold py-1.5" id="vt-title">—</td>
                            </tr>
                            <tr>
                                <th class="text-secondary fw-medium small ps-0 py-2 align-top" style="width: 130px;">Description :</th>
                                <td class="text-body py-2" id="vt-description" style="white-space: pre-wrap; line-height: 1.6;">—</td>
                            </tr>
                        </tbody>
                    </table>

                    @if(auth()->user()->canManageTickets())
                        <div class="border-top pt-3 mt-3">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-medium mb-1.5">Change Status :</label>
                                <div class="form-selectgroup form-selectgroup-pills">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="vt_status_radio" value="open" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label"><i class="ti ti-circle-filled text-primary me-1 fs-6"></i>Open</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="vt_status_radio" value="in_progress" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label"><i class="ti ti-circle-filled text-warning me-1 fs-6"></i>In Progress</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="vt_status_radio" value="resolved" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label"><i class="ti ti-circle-filled text-success me-1 fs-6"></i>Resolved</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="vt_status_radio" value="closed" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label"><i class="ti ti-circle-filled text-secondary me-1 fs-6"></i>Closed</span>
                                    </label>
                                </div>
                                <input type="hidden" id="vt-admin-status-select" value="open">
                            </div>

                            <div class="mb-0">
                                <label class="form-label text-secondary small fw-medium mb-1" for="vt-admin-notes-input">Resolution Note <span class="text-muted fw-normal">(optional)</span> :</label>
                                <textarea id="vt-admin-notes-input" class="form-control" rows="3" placeholder="Enter resolution details or reply to user..."></textarea>
                            </div>
                        </div>
                    @else
                        <div id="vt-resolution-box" class="d-none border-top pt-3 mt-3">
                            <div class="d-flex gap-2">
                                <div class="text-secondary small fw-medium" style="width: 130px; min-width: 130px;">Support Reply :</div>
                                <div class="text-body small" id="vt-admin-notes" style="white-space: pre-wrap; line-height: 1.6;">—</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-link link-secondary me-auto text-decoration-none" data-bs-dismiss="modal">Close</button>
                    @if(auth()->user()->canManageTickets())
                        <button type="button" class="btn btn-primary d-inline-flex align-items-center" id="btn-update-ticket-status">
                            <i class="ti ti-check me-1.5"></i>Save Changes
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('datatables_css')
    @include('admin.layouts.partials._datatable-cdn-css')
    <style>
        #tickets-table,
        #tickets-table tbody,
        #tickets-table tbody tr,
        #tickets-table tbody td,
        #tickets-table.table > :not(caption) > * > * {
            background-color: transparent !important;
            background: transparent !important;
            --tblr-table-hover-bg: transparent !important;
            --tblr-table-accent-bg: transparent !important;
            --bs-table-hover-bg: transparent !important;
            --bs-table-accent-bg: transparent !important;
        }
        #tickets-table tbody tr:hover,
        #tickets-table tbody tr:hover td {
            background-color: transparent !important;
            background: transparent !important;
        }
    </style>
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
                    @if(auth()->user()->canManageTickets())
                    { data: 'user', name: 'user.name', className: 'text-nowrap' },
                    { data: 'email', name: 'user.email', className: 'text-nowrap' },
                    @endif
                    { data: 'title', name: 'title' },
                    { data: 'category', name: 'category', className: 'text-nowrap' },
                    { data: 'priority', name: 'priority', className: 'text-nowrap' },
                    { data: 'status', name: 'status', className: 'text-nowrap' },
                    { data: 'created_at', name: 'created_at', className: 'text-nowrap' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end text-nowrap' },
                ],
                order: [[ {{ auth()->user()->canManageTickets() ? 7 : 5 }}, 'desc']],
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

                let ticketNumber = $btn.data('number');
                let title = $btn.data('title');
                let description = $btn.data('description');
                let created = $btn.data('created');
                let statusName = $btn.data('status');
                let statusVal = $btn.data('status-val');
                let statusColor = $btn.data('status-color');
                let priorityName = $btn.data('priority');
                let priorityColor = $btn.data('priority-color');
                let categoryName = $btn.data('category');
                let userName = $btn.data('user-name') || 'User';
                let userEmail = $btn.data('user-email') || '';
                let userInitial = userName.trim().charAt(0).toUpperCase() || 'U';
                let notes = $btn.data('notes') || '';

                $('#vt-number').text(ticketNumber.startsWith('#') ? ticketNumber : '#' + ticketNumber);
                $('#vt-user-info').text(userEmail ? userName + ' (' + userEmail + ')' : userName);
                $('#vt-created-at').text(created);
                $('#vt-title').text(title);
                $('#vt-description').text(description);

                // Badges
                $('#vt-status-badge').text(statusName)
                    .attr('class', 'badge badge-outline text-' + statusColor);

                $('#vt-priority-badge').text(priorityName)
                    .attr('class', 'badge badge-outline text-' + priorityColor);

                $('#vt-category-badge').text(categoryName);

                // Admin Controls vs Regular User view
                @if(auth()->user()->canManageTickets())
                    let currentStatus = statusVal || 'open';
                    $('input[name="vt_status_radio"][value="' + currentStatus + '"]').prop('checked', true);
                    $('#vt-admin-status-select').val(currentStatus);
                    $('#vt-admin-notes-input').val(notes);
                @else
                    if (notes && notes.trim() !== '') {
                        $('#vt-admin-notes').text(notes);
                        $('#vt-resolution-box').removeClass('d-none');
                    } else {
                        $('#vt-resolution-box').addClass('d-none');
                    }
                @endif

                let modal = new bootstrap.Modal(document.getElementById('view-ticket-modal'));
                modal.show();
            });

            @if(auth()->user()->canManageTickets())
                // Status radio selection change
                $(document).on('change', 'input[name="vt_status_radio"]', function () {
                    $('#vt-admin-status-select').val($(this).val());
                });

                // Admin Status Update
                $('#btn-update-ticket-status').on('click', function () {
                    if (!activeTicketId) return;
                    let newStatus = $('#vt-admin-status-select').val();
                    let adminNotes = $('#vt-admin-notes-input').val();
                    let $btn = $(this);
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: '/admin/help/' + activeTicketId + '/status',
                        method: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus,
                            admin_notes: adminNotes
                        },
                        success: function (res) {
                            $btn.prop('disabled', false);
                            let modalEl = document.getElementById('view-ticket-modal');
                            let modal = bootstrap.Modal.getInstance(modalEl);
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
