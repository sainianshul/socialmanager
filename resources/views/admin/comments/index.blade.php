@extends('admin.layouts.app')

@section('title', 'Comments')

@push('datatables_css')
    @include('admin.layouts.partials._datatable-cdn-css')
@endpush

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Comments</h2>
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

    {{-- Main DataTable Card --}}
    <div class="card">

        {{-- Toolbar --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">

                {{-- Left: Live Search --}}
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" id="dt-search" class="form-control" style="width: 260px;"
                        placeholder="Search comments..." />
                </div>

                {{-- Right: Simple Filters & Controls --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-ghost-secondary" id="refresh-table-btn"
                        data-bs-toggle="tooltip" title="Refresh">
                        <i class="ti ti-refresh"></i>
                    </button>

                    {{-- Channel Filter --}}
                    <select id="filter-account" class="form-select" style="width: 175px;">
                        <option value="">All Channels</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>

                    {{-- Status Filter --}}
                    <select id="filter-status" class="form-select" style="width: 155px;">
                        <option value="">All Status</option>
                        @foreach($statusList as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Sentiment Filter --}}
                    <select id="filter-sentiment" class="form-select" style="width: 145px;">
                        <option value="">All Sentiments</option>
                        <option value="positive">Positive</option>
                        <option value="neutral">Neutral</option>
                        <option value="negative">Negative</option>
                    </select>

                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            {{-- Loading Spinner --}}
            <div id="comments-loader" class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading…</span>
                </div>
            </div>

            {{-- DataTable Container --}}
            <div id="comments-table-wrapper" class="table-responsive d-none">
                <table id="comments-table" class="table table-vcenter w-100">
                    <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th style="min-width: 170px;">Post</th>
                            <th style="min-width: 360px;">Conversation & AI Reply</th>
                            <th style="min-width: 110px;">Sentiment</th>
                            <th style="min-width: 110px;">Status</th>
                            <th class="text-end" style="min-width: 135px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- Empty State Partial --}}
            @include('admin.layouts.partials._table-empty', [
                'id' => 'comments-empty',
                'title' => 'No Comments Found',
                'subtitle' => 'No constituent comments have been synced or match the selected filters.'
            ])

        </div>
    </div>

    {{-- Modal: Post Manual Comment on a Post --}}
    <div class="modal modal-blur fade" id="modal-manual-comment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.comments.store-manual') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-message-plus me-1 text-primary"></i>Post Comment as Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Select Post</label>
                            <select name="post_id" class="form-select" required>
                                <option value="">-- Choose Post to Comment On --</option>
                                @foreach($posts as $post)
                                    @php
                                        $postSnippet = !empty($post->message) ? \Illuminate\Support\Str::limit($post->message, 55) : 'Post #' . substr($post->meta_post_id ?? '', -6);
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
                            <textarea name="message" class="form-control" rows="4" placeholder="Write your comment as Page..." required></textarea>
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

    {{-- Modal: Reply Directly to a Constituent Comment --}}
    <div class="modal modal-blur fade" id="modal-reply-comment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="reply-comment-form" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-arrow-back-up me-1 text-primary"></i>Reply to Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-sm mb-3 bg-light-subtle">
                            <div class="card-body">
                                <div class="fw-semibold mb-1" id="reply-modal-name">Constituent</div>
                                <div class="text-secondary small" id="reply-modal-comment">...</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Your Reply (Published as Page)</label>
                            <textarea name="reply_message" id="reply-modal-textarea" class="form-control" rows="4" placeholder="Type your reply message..." required></textarea>
                            <small class="form-hint">This response will be posted as an official reply on Meta/Facebook.</small>
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
            let table = $('#comments-table').DataTable({
                serverSide: true,
                processing: false,

                ajax: {
                    url: '{{ route('admin.comments.data') }}',
                    data: function (d) {
                        d.account_id = $('#filter-account').val();
                        d.status = $('#filter-status').val();
                        d.sentiment = $('#filter-sentiment').val();
                    }
                },

                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'post', name: 'post', orderable: false },
                    { data: 'message', name: 'message' },
                    { data: 'sentiment', name: 'sentiment' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
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
            $('#filter-account, #filter-status, #filter-sentiment').on('change', function () {
                table.draw();
            });

            // Refresh Button
            $('#refresh-table-btn').on('click', function () {
                table.draw();
            });

            // Reply Modal Dynamic Setup
            $(document).on('click', '.btn-reply-comment', function () {
                let name = $(this).data('name') || 'Constituent';
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
