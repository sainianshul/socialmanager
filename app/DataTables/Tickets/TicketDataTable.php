<?php

namespace App\DataTables\Tickets;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Yajra\DataTables\Services\DataTable;

class TicketDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->filterColumn('title', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('ticket_number', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('ticket_number', function (Ticket $ticket) {
                return '<span class="badge bg-dark-lt fw-bold font-monospace">#' . e($ticket->ticket_number) . '</span>';
            })

            ->addColumn('user', function (Ticket $ticket) {
                if (!$ticket->user) {
                    return '<span class="text-secondary">—</span>';
                }
                return '
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-xs rounded-circle me-1.5 bg-primary-lt text-primary">' . mb_strtoupper(mb_substr($ticket->user->name, 0, 1)) . '</span>
                        <div class="text-truncate" style="max-width: 140px;">
                            <span class="fw-semibold text-body d-block text-truncate small">' . e($ticket->user->name) . '</span>
                            <span class="text-secondary fs-7 d-block text-truncate">' . e($ticket->user->email) . '</span>
                        </div>
                    </div>
                ';
            })

            ->editColumn('title', function (Ticket $ticket) {
                $title = e($ticket->title);
                $desc = e(Str::limit($ticket->description, 75));
                return '
                    <div>
                        <div class="fw-semibold text-body">' . $title . '</div>
                        <div class="text-secondary small text-truncate" style="max-width: 320px;">' . $desc . '</div>
                    </div>
                ';
            })

            ->addColumn('category', function (Ticket $ticket) {
                return '<span class="badge bg-' . $ticket->category_color . '-lt fw-semibold">' . e($ticket->category_name) . '</span>';
            })

            ->addColumn('priority', function (Ticket $ticket) {
                return '<span class="badge bg-' . $ticket->priority_color . '-lt text-capitalize">' . e($ticket->priority) . '</span>';
            })

            ->addColumn('status', function (Ticket $ticket) {
                return '<span class="badge bg-' . $ticket->status_color . '-lt fw-semibold"><i class="ti ti-circle-filled fs-8 me-1"></i>' . e($ticket->status_name) . '</span>';
            })

            ->editColumn('created_at', function (Ticket $ticket) {
                return '<div class="text-secondary small">' . $ticket->created_at->format('d M Y, h:i A') . '</div>';
            })

            ->addColumn('actions', function (Ticket $ticket) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button"
                            class="btn btn-sm btn-outline-primary btn-view-ticket"
                            data-id="' . $ticket->id . '"
                            data-number="' . e($ticket->ticket_number) . '"
                            data-category="' . e($ticket->category_name) . '"
                            data-category-color="' . $ticket->category_color . '"
                            data-title="' . e($ticket->title) . '"
                            data-description="' . e($ticket->description) . '"
                            data-priority="' . e(ucfirst($ticket->priority)) . '"
                            data-priority-color="' . $ticket->priority_color . '"
                            data-status="' . e($ticket->status_name) . '"
                            data-status-val="' . e($ticket->status) . '"
                            data-status-color="' . $ticket->status_color . '"
                            data-created="' . $ticket->created_at->format('d M Y, h:i A') . '"
                            data-user-name="' . e($ticket->user?->name ?? 'User') . '"
                            data-user-email="' . e($ticket->user?->email ?? '') . '"
                            data-notes="' . e($ticket->admin_notes ?? '') . '">
                            <i class="ti ti-eye me-1"></i>View
                        </button>
                    </div>
                ';
            })

            ->rawColumns([
                'ticket_number',
                'user',
                'title',
                'category',
                'priority',
                'status',
                'created_at',
                'actions',
            ]);
    }

    public function query(Ticket $model)
    {
        $user = auth()->user();
        $query = $model->newQuery()->with('user')->latest();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if (request()->filled('category')) {
            $query->where('category', request('category'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('priority')) {
            $query->where('priority', request('priority'));
        }

        return $query;
    }

    public function filename(): string
    {
        return 'Tickets_' . date('Y_m_d_His');
    }
}
