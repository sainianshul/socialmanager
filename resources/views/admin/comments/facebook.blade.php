@extends('admin.layouts.app')

@section('title', 'Facebook Comments')

@push('datatables_css')
    @include('admin.layouts.partials._datatable-cdn-css')
    <style>
        /* Modern Social Inbox Card & Feed View Styling */
        #comments-table thead {
            display: none !important;
        }
        #comments-table,
        #comments-table tbody,
        #comments-table tbody tr,
        #comments-table tbody td {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        #comments-table tbody tr {
            display: block;
            margin-bottom: 0.95rem;
        }
        #comments-table tbody td {
            display: block;
            width: 100% !important;
        }
        .social-inbox-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease-in-out;
        }
        .social-inbox-card:hover {
            box-shadow: 0 4px 10px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1 !important;
        }
        .comment-text-expandable a {
            font-weight: 600;
        }

        /* Card Action Buttons Hover Styling */
        .btn-action-platform {
            border-color: #cbd5e1;
            color: #334155;
            background-color: #ffffff;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .btn-action-platform:hover {
            background-color: #eff6ff;
            border-color: #93c5fd;
            color: #1d4ed8;
        }
        .btn-action-reply {
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .btn-action-reply:hover {
            background-color: #206bc4;
            color: #ffffff;
        }
        .btn-action-hide {
            border-color: #fca5a5;
            color: #dc2626;
            background-color: #ffffff;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .btn-action-hide:hover {
            background-color: #fef2f2;
            border-color: #f87171;
            color: #b91c1c;
        }
    </style>
@endpush

@section('content')

    {{-- Clean Formal Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Facebook Comments</h2>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-manual-comment">
                    <i class="ti ti-plus me-1"></i>New Comment
                </button>
                <form id="sync-comments-form" method="POST" action="{{ route('admin.comments.sync') }}" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="btn-sync-comments">
                        <i class="ti ti-refresh me-1" id="sync-icon"></i>Sync Comments
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div id="flash-alerts-container"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-check fs-2 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle fs-2 me-2"></i>
                <div>{{ session('warning') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    {{-- Metric Stat Cards (Formal & Clean) --}}
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-light text-primary">
                                <i class="ti ti-brand-facebook fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-secondary fw-medium small">Total Comments</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalComments) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-light text-warning">
                                <i class="ti ti-clock fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-secondary fw-medium small">Pending Review</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($pendingReview) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-light text-success">
                                <i class="ti ti-check fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-secondary fw-medium small">Replied</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($repliedCount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded-2 bg-light text-secondary">
                                <i class="ti ti-eye-off fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-secondary fw-medium small">Hidden</div>
                            <div class="h2 mb-0 fw-bold text-dark">{{ number_format($hiddenCount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card">

        {{-- Toolbar: Search (Left), Status / Page / Sentiment Select Filters (Right) --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                {{-- Left: Search Input --}}
                <div class="d-flex align-items-center">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="dt-search" class="form-control" style="width: 240px;"
                            placeholder="Search comments..." />
                    </div>
                </div>

                {{-- Right: Status, Page, Sentiment Selects & Refresh --}}
                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                    {{-- Status Select Filter --}}
                    <select id="filter-status" class="form-select" style="width: 170px;">
                        <option value="">All Statuses ({{ $totalComments }})</option>
                        <option value="pending">Pending Review ({{ $pendingReview }})</option>
                        <option value="replied">Replied ({{ $repliedCount }})</option>
                        <option value="hidden">Hidden ({{ $hiddenCount }})</option>
                        <option value="flagged">Flagged ({{ $flaggedCount }})</option>
                    </select>

                    {{-- Facebook Page Filter --}}
                    <select id="filter-account" class="form-select" style="width: 180px;">
                        <option value="">All Pages ({{ $accounts->count() }})</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>

                    {{-- Sentiment Filter --}}
                    <select id="filter-sentiment" class="form-select" style="width: 140px;">
                        <option value="">All Sentiments</option>
                        <option value="positive">Positive</option>
                        <option value="neutral">Neutral</option>
                        <option value="negative">Negative</option>
                    </select>

                    {{-- Date Filter --}}
                    <div class="input-icon" style="width: 155px;" data-bs-toggle="tooltip" title="Filter by date">
                        <span class="input-icon-addon"><i class="ti ti-calendar"></i></span>
                        <input type="date" id="filter-date" class="form-control" title="Filter by date">
                    </div>
                    <button type="button" class="btn btn-icon btn-ghost-secondary d-none" id="btn-clear-date" data-bs-toggle="tooltip" title="Clear Date Filter">
                        <i class="ti ti-x"></i>
                    </button>

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-outline-secondary" id="refresh-table-btn"
                        data-bs-toggle="tooltip" title="Refresh Comments">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            {{-- Loading Spinner --}}
            <div id="comments-loader" class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading comments…</span>
                </div>
            </div>

            {{-- DataTable Container --}}
            <div id="comments-table-wrapper" class="table-responsive d-none view-feed">
                <table id="comments-table" class="table table-vcenter w-100">
                    <thead id="comments-table-head">
                        <tr class="d-none">
                            <th>Card</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- Empty State Partial --}}
            @include('admin.layouts.partials._table-empty', [
                'id' => 'comments-empty',
                'title' => 'No Facebook Comments Found',
                'subtitle' => 'No comments have been synced for the selected Facebook Page or filters.'
            ])

        </div>
    </div>

    {{-- Modal: Post Manual Comment on a Facebook Post --}}
    <div class="modal modal-blur fade" id="modal-manual-comment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.comments.store-manual') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-brand-facebook me-1 text-facebook"></i>Post Comment as Facebook Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Select Facebook Post</label>
                            <select name="post_id" class="form-select" required>
                                <option value="">-- Choose Facebook Post --</option>
                                @foreach($posts as $post)
                                    @php
                                        $postSnippet = !empty($post->message) ? \Illuminate\Support\Str::limit($post->message, 50) : 'Post #' . substr($post->platform_post_id ?? $post->meta_post_id ?? '', -6);
                                        $date = $post->published_at ? $post->published_at->format('d M') : '';
                                    @endphp
                                    <option value="{{ $post->id }}">
                                        [{{ $post->account->name }}] {{ $postSnippet }} ({{ $date }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-hint">Comment will be published directly under this post on Facebook.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Comment Message</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Write your public comment..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Publish to Facebook
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Reply Directly to a Facebook Comment --}}
    <div class="modal modal-blur fade" id="modal-reply-comment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="reply-comment-form" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-arrow-back-up me-1 text-primary"></i>Reply to Facebook Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-sm mb-3 bg-light-subtle">
                            <div class="card-body">
                                <div class="fw-semibold mb-1 d-flex align-items-center">
                                    <i class="ti ti-brand-facebook text-facebook me-1"></i>
                                    <span id="reply-modal-name">User</span>
                                </div>
                                <div class="text-secondary small" id="reply-modal-comment">...</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Your Reply</label>
                            <textarea name="reply_message" id="reply-modal-textarea" class="form-control" rows="4" placeholder="Type your response..." required></textarea>
                            <small class="form-hint">This response will be posted as an official reply to this comment on Facebook.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('datatables_js')
    @include('admin.layouts.partials._datatable-cdn-js')

    <script>
        $(function () {
            let activeStatus = '';

            function showFlashAlert(type, message) {
                let icon = (type === 'success') ? 'ti-check' : 'ti-alert-triangle';
                let alertHtml = `
                    <div class="alert alert-${type} alert-dismissible mb-3 animate-fade-in" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ${icon} fs-2 me-2"></i>
                            <div>${message}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                `;
                $('#flash-alerts-container').html(alertHtml);
            }

            let table = $('#comments-table').DataTable({
                serverSide: true,
                processing: false,

                ajax: {
                    url: '{{ route('admin.comments.facebook.data') }}',
                    data: function (d) {
                        d.account_id = $('#filter-account').val();
                        d.status = activeStatus;
                        d.sentiment = $('#filter-sentiment').val();
                        d.date = $('#filter-date').val();
                    }
                },

                columns: [
                    { data: 'card', name: 'card', orderable: false, searchable: false }
                ],

                order: [],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],

                dom:
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center mt-3 pt-3 flex-nowrap'" +
                    "<'col-sm-12 col-md-5'i>" +
                    "<'col-sm-12 col-md-7 d-flex justify-content-md-end align-items-center gap-3'lp>>",

                language: {
                    emptyTable: ' ',
                    zeroRecords: ' ',
                    loadingRecords: ' ',
                    info: 'Showing _START_–_END_ of _TOTAL_ comments',
                    infoEmpty: 'No comments to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                    },
                },

                initComplete: function () {
                    $('#comments-loader').remove();
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#comments-table-wrapper').addClass('d-none');
                        $('#comments-empty').removeClass('d-none');
                    } else {
                        $('#comments-empty').addClass('d-none');
                        $('#comments-table-wrapper').removeClass('d-none');
                    }
                },

                drawCallback: function () {
                    if ($('#comments-loader').length === 0) {
                        let total = this.api().page.info().recordsDisplay;
                        if (total === 0) {
                            $('#comments-table-wrapper').addClass('d-none');
                            $('#comments-empty').removeClass('d-none');
                        } else {
                            $('#comments-empty').addClass('d-none');
                            $('#comments-table-wrapper').removeClass('d-none');
                        }
                    }
                    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                }
            });

            // Live Search with Debounce
            let searchTimer;
            $('#dt-search').on('input', function () {
                clearTimeout(searchTimer);
                let query = $(this).val();
                searchTimer = setTimeout(function () {
                    table.search(query).draw();
                }, 350);
            });

            // Live Dropdown Filter Changes
            $('#filter-status, #filter-account, #filter-sentiment').on('change', function () {
                activeStatus = $('#filter-status').val();
                table.draw();
            });

            // Date Filter Change
            $('#filter-date').on('change', function () {
                let val = $(this).val();
                if (val) {
                    $('#btn-clear-date').removeClass('d-none');
                } else {
                    $('#btn-clear-date').addClass('d-none');
                }
                table.draw();
            });

            // Clear Date Filter
            $('#btn-clear-date').on('click', function () {
                $('#filter-date').val('');
                $(this).addClass('d-none');
                table.draw();
            });

            // Refresh Button
            $('#refresh-table-btn').on('click', function () {
                table.draw();
            });

            // Reply Modal Dynamic Setup
            $(document).on('click', '.btn-reply-comment', function () {
                let name = $(this).data('name') || 'Facebook User';
                let commentText = $(this).data('comment') || '';
                let draftReply = $(this).data('reply') || '';
                let actionUrl = $(this).data('action');

                $('#reply-comment-form').attr('action', actionUrl);
                $('#reply-modal-name').text(name);
                $('#reply-modal-comment').text(commentText);
                $('#reply-modal-textarea').val(draftReply);

                let replyModal = new bootstrap.Modal(document.getElementById('modal-reply-comment'));
                replyModal.show();
            });

            // 1-Click Interactive AJAX Approve & Send Live
            $(document).on('submit', '.form-approve-reply', function (e) {
                e.preventDefault();
                let form = $(this);
                let btn = form.find('.btn-approve-action');
                let originalHtml = btn.html();

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (res) {
                        showFlashAlert('success', res.message || 'AI reply successfully approved and published live!');
                        table.draw(false);
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Failed to publish approved reply.';
                        showFlashAlert('danger', 'Error: ' + msg);
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // 1-Click Interactive AJAX Toggle Hide / Unhide
            $(document).on('submit', '.form-toggle-hide', function (e) {
                e.preventDefault();
                let form = $(this);
                let btn = form.find('button[type="submit"]');
                btn.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (res) {
                        showFlashAlert('success', res.message || 'Comment visibility updated successfully.');
                        table.draw(false);
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Failed to update visibility.';
                        showFlashAlert('danger', 'Error: ' + msg);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Expand / Collapse long comment text
            $(document).on('click', '.btn-comment-more', function (e) {
                e.preventDefault();
                let container = $(this).closest('.comment-text-expandable');
                container.find('.comment-text-short').addClass('d-none');
                container.find('.comment-text-full').removeClass('d-none');
            });

            $(document).on('click', '.btn-comment-less', function (e) {
                e.preventDefault();
                let container = $(this).closest('.comment-text-expandable');
                container.find('.comment-text-full').addClass('d-none');
                container.find('.comment-text-short').removeClass('d-none');
            });

            // Sync Button Spinner Feedback
            $('#sync-comments-form').on('submit', function () {
                let btn = $('#btn-sync-comments');
                btn.prop('disabled', true);
                $('#sync-icon').removeClass('ti-refresh').addClass('ti-loader animate-spin');
                btn.contents().last()[0].textContent = ' Syncing...';
            });
        });
    </script>
@endpush

