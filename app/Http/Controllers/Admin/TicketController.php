<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Tickets\TicketDataTable;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display Help Center & Support Tickets index
     */
    public function index(TicketDataTable $dataTable)
    {
        $user = auth()->user();

        $baseQuery = Ticket::query();
        if (!$user->isAdmin()) {
            $baseQuery->where('user_id', $user->id);
        }

        $totalTicketsCount = (clone $baseQuery)->count();
        $openTicketsCount = (clone $baseQuery)->where('status', Ticket::STATUS_OPEN)->count();
        $inProgressTicketsCount = (clone $baseQuery)->where('status', Ticket::STATUS_IN_PROGRESS)->count();
        $resolvedTicketsCount = (clone $baseQuery)->where('status', Ticket::STATUS_RESOLVED)->count();

        $categories = Ticket::getCategoryList();
        $priorities = Ticket::getPriorityList();
        $statuses = Ticket::getStatusList();

        return $dataTable->render('admin.help.index', compact(
            'totalTicketsCount',
            'openTicketsCount',
            'inProgressTicketsCount',
            'resolvedTicketsCount',
            'categories',
            'priorities',
            'statuses'
        ));
    }

    /**
     * AJAX endpoint for DataTable
     */
    public function data(TicketDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    /**
     * Store a newly created Support Ticket
     */
    public function store(Request $request)
    {
        $validCategories = implode(',', array_keys(Ticket::getCategoryList()));
        $validPriorities = implode(',', array_keys(Ticket::getPriorityList()));

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => "required|string|in:{$validCategories}",
            'description' => 'required|string|min:10',
            'priority'    => "nullable|string|in:{$validPriorities}",
        ]);

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id'       => auth()->id(),
            'category'      => $validated['category'],
            'title'         => trim($validated['title']),
            'description'   => trim($validated['description']),
            'priority'      => $validated['priority'] ?? Ticket::PRIORITY_MEDIUM,
            'status'        => Ticket::STATUS_OPEN,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Ticket #{$ticket->ticket_number} created successfully. Our team will look into it.",
                'ticket'  => $ticket,
            ]);
        }

        return redirect()->route('admin.help.index')->with('success', "Ticket #{$ticket->ticket_number} created successfully.");
    }

    /**
     * Fetch ticket details for modal view
     */
    public function show(Ticket $ticket)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        return response()->json([
            'success' => true,
            'ticket'  => [
                'id'             => $ticket->id,
                'ticket_number'  => $ticket->ticket_number,
                'title'          => $ticket->title,
                'category'       => $ticket->category_name,
                'category_color' => $ticket->category_color,
                'description'    => $ticket->description,
                'priority'       => ucfirst($ticket->priority),
                'priority_color' => $ticket->priority_color,
                'status'         => $ticket->status_name,
                'status_color'   => $ticket->status_color,
                'admin_notes'    => $ticket->admin_notes,
                'created_at'     => $ticket->created_at->format('d M Y, h:i A'),
                'resolved_at'    => $ticket->resolved_at?->format('d M Y, h:i A'),
                'user_name'      => $ticket->user?->name,
                'user_email'     => $ticket->user?->email,
            ],
        ]);
    }

    /**
     * Update ticket status (Admin or user close)
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status'      => 'required|string|in:' . implode(',', array_keys(Ticket::getStatusList())),
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === Ticket::STATUS_RESOLVED && !$ticket->resolved_at) {
            $updateData['resolved_at'] = now();
        }

        if ($user->isAdmin() && $request->filled('admin_notes')) {
            $updateData['admin_notes'] = trim($request->admin_notes);
        }

        $ticket->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Ticket #{$ticket->ticket_number} status updated to {$ticket->status_name}.",
        ]);
    }
}
