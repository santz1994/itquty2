<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
  /**
   * Display a listing of tickets
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    $query = Ticket::withNestedRelations(); // Use optimized nested loading

    // Apply filters using scopes
    if ($request->has('status_id')) {
      $query->byStatus($request->status_id);
    }

    if ($request->has('priority_id')) {
      $query->byPriority($request->priority_id);
    }

    if ($request->has('type_id')) {
      $query->byType($request->type_id);
    }

    if ($request->has('assigned_to') && $request->assigned_to !== '') {
      if ($request->assigned_to === 'unassigned') {
        $query->unassigned();
      } else {
        $query->assignedTo($request->assigned_to);
      }
    }

    if ($request->has('user_id')) {
      $query->forUser($request->user_id);
    }

    // Status filters using scopes
    if ($request->has('open') && $request->open === 'true') {
      $query->open();
    }

    if ($request->has('closed') && $request->closed === 'true') {
      $query->closed();
    }

    if ($request->has('search')) {
      $search = $request->search;
      $query->where(function($q) use ($search) {
        $q->where('subject', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
      });
    }

    // Date filters
    if ($request->has('date_from') && $request->has('date_to')) {
      $query->createdBetween($request->date_from, $request->date_to);
    }

    // Overdue filter
    if ($request->has('overdue') && $request->overdue === 'true') {
      $query->overdue();
    }

    // Near deadline filter
    if ($request->has('near_deadline') && $request->near_deadline === 'true') {
      $query->nearDeadline($request->get('deadline_hours', 2));
    }

    // Order by created_at DESC by default, allow override
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->sortBy($sortBy, $sortOrder);

    // Pagination with validation
    $perPage = min((int)$request->get('per_page', 15), 100); // Max 100 per page
    $perPage = max(1, $perPage); // Min 1 per page

    $tickets = $query->paginate($perPage);

    // Transform data: map over paginator items and rebuild LengthAwarePaginator to avoid calling collection methods on the paginator contract
    $transformedItems = collect($tickets->items())->map(function ($ticket) {
      return $this->transformTicket($ticket);
    })->values()->all();

    $tickets = new \Illuminate\Pagination\LengthAwarePaginator(
      $transformedItems,
      $tickets->total(),
      $tickets->perPage(),
      $tickets->currentPage(),
      [
        'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
        'query' => request()->query()
      ]
    );

    return response()->json([
      'success' => true,
      'data' => $tickets,
      'message' => 'Tickets retrieved successfully'
    ]);
  }

  /**
     * Store a newly created ticket
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'asset_id' => 'nullable|exists:assets,id',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
            'ticket_type_id' => 'required|exists:tickets_types,id',
            'ticket_priority_id' => 'required|exists:tickets_priorities,id',
            'assigned_to' => 'nullable|exists:users,id',
            'sla_due' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticketData = $request->all();
        $ticketData['user_id'] = auth()->user()->id;
        $ticketData['ticket_status_id'] = 1; // Open status

        // Calculate SLA due if not provided
        if (!$request->sla_due && $request->ticket_priority_id) {
            $priority = \App\TicketsPriority::find($request->ticket_priority_id);
            if ($priority && $priority->sla_hours) {
                $ticketData['sla_due'] = now()->addHours($priority->sla_hours);
            }
        }

        $ticket = Ticket::create($ticketData);
        // Attach assets if provided (supporting asset_ids array or single asset_id)
        try {
            if ($request->has('asset_ids') && is_array($request->asset_ids)) {
                $ticket->assets()->sync($request->asset_ids);
            } elseif (!empty($request->asset_id)) {
                $ticket->assets()->syncWithoutDetaching([$request->asset_id]);
            }
        } catch (\Exception $e) {
            // don't fail creation for pivot sync errors - log and continue
            \Log::warning('Failed to sync ticket assets in API store: ' . $e->getMessage());
        }
        $ticket->load(['user', 'asset', 'status', 'priority', 'assignedUser']);

        // Auto-assign if assigned_to is provided
        if ($request->assigned_to) {
            $user = User::find($request->assigned_to);
            $ticket->assignTo($user, 'api');
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket, true),
            'message' => 'Ticket created successfully'
        ], 201);
    }

    /**
     * Display the specified ticket
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'asset', 'status', 'priority', 'assignedUser', 'entries.user']);

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket, true),
            'message' => 'Ticket retrieved successfully'
        ]);
    }

    /**
     * Update the specified ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Ticket $ticket)
    {
        if (!$this->canUpdateTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this ticket'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'asset_id' => 'nullable|exists:assets,id',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
            'ticket_type_id' => 'sometimes|exists:tickets_types,id',
            'ticket_priority_id' => 'sometimes|exists:tickets_priorities,id',
            'ticket_status_id' => 'sometimes|exists:tickets_statuses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'sla_due' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket->update($request->all());
        // Sync assets if provided
        try {
            if ($request->has('asset_ids') && is_array($request->asset_ids)) {
                $ticket->assets()->sync($request->asset_ids);
            } elseif ($request->has('asset_id')) {
                // keep single-asset behavior if only asset_id provided
                $ticket->assets()->syncWithoutDetaching([$request->asset_id]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to sync ticket assets in API update: ' . $e->getMessage());
        }

        $ticket->load(['user', 'asset', 'status', 'priority', 'assignedUser']);

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket, true),
            'message' => 'Ticket updated successfully'
        ]);
    }

    /**
     * Remove the specified ticket
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Ticket $ticket)
    {
        if (!user_has_any_role(auth()->user(), ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete tickets'
            ], 403);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }

    /**
     * Assign ticket to user
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, Ticket $ticket)
    {
        if (!$this->canAssignTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to assign this ticket'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);
        $result = $ticket->assignTo($user, 'api');

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket->fresh(['assignedUser'])),
            'message' => 'Ticket assigned successfully'
        ]);
    }

    /**
     * Resolve ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolve(Request $request, Ticket $ticket)
    {
        if (!$this->canUpdateTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to resolve this ticket'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'resolution_notes' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $ticket->resolve($request->resolution_notes);

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket->fresh(['status'])),
            'message' => 'Ticket resolved successfully'
        ]);
    }

    /**
     * Close ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function close(Request $request, Ticket $ticket)
    {
        if (!$this->canUpdateTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to close this ticket'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'closure_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $ticket->close($request->closure_notes);

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket->fresh(['status'])),
            'message' => 'Ticket closed successfully'
        ]);
    }

    /**
     * Reopen ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function reopen(Request $request, Ticket $ticket)
    {
        if (!$this->canUpdateTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to reopen this ticket'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'reopen_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $ticket->reopen($request->reopen_reason);

        return response()->json([
            'success' => true,
            'data' => $this->transformTicket($ticket->fresh(['status'])),
            'message' => 'Ticket reopened successfully'
        ]);
    }

    /**
     * Get ticket timeline
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimeline(Ticket $ticket)
    {
        $entries = $ticket->entries()->with('user')->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'timeline' => $entries->map(function($entry) {
                    return [
                        'id' => $entry->id,
                        'entry' => $entry->entry,
                        'user' => [
                            'id' => $entry->user->id,
                            'name' => $entry->user->name
                        ],
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at
                    ];
                })
            ],
            'message' => 'Ticket timeline retrieved successfully'
        ]);
    }

    /**
     * Transform ticket data
     *
     * @param Ticket $ticket
     * @param bool $detailed
     * @return array
     */
    private function transformTicket(Ticket $ticket, $detailed = false)
    {
        $data = [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'user' => [
                'id' => $ticket->user->id,
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'asset' => $ticket->asset ? [
                'id' => $ticket->asset->id,
                'asset_tag' => $ticket->asset->asset_tag,
                'name' => $ticket->asset->name
            ] : null,
            'status' => [
                'id' => $ticket->ticket_status_id,
                'name' => $ticket->status->name ?? null,
                'badge' => $ticket->status_badge
            ],
            'priority' => [
                'id' => $ticket->ticket_priority_id,
                'name' => $ticket->priority->name ?? null,
                'color' => $ticket->priority_color
            ],
            'assigned_user' => $ticket->assignedUser ? [
                'id' => $ticket->assignedUser->id,
                'name' => $ticket->assignedUser->name,
                'email' => $ticket->assignedUser->email
            ] : null,
            'sla_due' => $ticket->sla_due,
            'is_overdue' => $ticket->is_overdue,
            'time_to_sla' => $ticket->time_to_sla,
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at
        ];

        if ($detailed) {
            $data['entries'] = $ticket->entries->map(function($entry) {
                return [
                    'id' => $entry->id,
                    'entry' => $entry->entry,
                    'user' => [
                        'id' => $entry->user->id,
                        'name' => $entry->user->name
                    ],
                    'created_at' => $entry->created_at
                ];
            });
        }

        return $data;
    }

    /**
     * Check if user can update ticket
     *
     * @param Ticket $ticket
     * @return bool
     */
    private function canUpdateTicket(Ticket $ticket)
    {
        $user = auth()->user();
        
        return user_has_any_role($user, ['admin', 'super-admin', 'management']) || 
               $ticket->user_id == $user->id || 
               $ticket->assigned_to == $user->id;
    }

    /**
     * Check if user can assign ticket
     *
     * @param Ticket $ticket
     * @return bool
     */
    private function canAssignTicket(Ticket $ticket)
    {
        $user = auth()->user();
        
        return user_has_any_role($user, ['admin', 'super-admin', 'management']) || 
               $ticket->assigned_to == $user->id;
    }
}
