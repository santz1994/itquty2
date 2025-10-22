<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateTicketRequest;
use App\Services\TicketService;
use App\Services\CacheService;
use App\Ticket;
use App\Asset;
use App\User;
use App\Location;
use App\TicketsPriority;
use App\TicketsType;
use App\TicketsStatus;
use App\Traits\RoleBasedAccessTrait;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller as BaseController;

/**
 * TicketController
 * 
 * Handles core CRUD operations for tickets.
 * Specialized operations moved to:
 * - TicketTimerController (time tracking)
 * - TicketAssignmentController (assignment operations)
 * - TicketStatusController (status updates)
 * - UserTicketController (user self-service portal)
 */
class TicketController extends BaseController
{
    use RoleBasedAccessTrait;
    
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        // Removed parent::__construct() since parent doesn't have a constructor
        if (method_exists($this, 'middleware')) {
            $this->middleware('auth');
        }
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ticket::withRelations();

        // Role-based filtering using trait method
        $query = $this->applyRoleBasedFilters($query, $user);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('ticket_status_id', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('ticket_priority_id', $request->priority);
        }

        // Filter by assigned admin (only for management, admin, super-admin)  
        if ($request->has('assigned_to') && $request->assigned_to !== '' && !$this->hasRole('user')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by asset
        if ($request->has('asset_id') && $request->asset_id !== '') {
            $query->where('asset_id', $request->asset_id);
        }

        // Search by ticket code or subject
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('ticket_code', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options using cache
        $statuses = CacheService::getTicketStatuses();
        $priorities = CacheService::getTicketPriorities();
        $admins = User::admins()->orderBy('name')->get();
        $assets = Asset::select('assets.id', 'assets.asset_tag', 'asset_models.asset_model as model_name')
                      ->leftJoin('asset_models', 'assets.model_id', '=', 'asset_models.id')
                      ->orderBy('assets.asset_tag')
                      ->get();
        $pageTitle = 'Ticket Management';

        return view('tickets.index', compact('tickets', 'statuses', 'priorities', 'admins', 'assets', 'pageTitle'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        // Get dropdown data with correct variable names expected by the view using cache
        // Only show users with admin role as agents
        $users = User::select('id', 'name')
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'admin');
                    })
                    ->orderBy('name')
                    ->get();
        
        $locations = CacheService::getLocations();
        $ticketsStatuses = CacheService::getTicketStatuses();
        $ticketsTypes = CacheService::getTicketTypes();
        $ticketsPriorities = CacheService::getTicketPriorities();
        $assets = Asset::select('assets.id', 'assets.asset_tag', 'asset_models.asset_model as model_name')
                      ->leftJoin('asset_models', 'assets.model_id', '=', 'asset_models.id')
                      ->orderBy('assets.asset_tag')
                      ->get();
        $pageTitle = 'Create New Ticket';
        // Provide canned fields so the view can render the right-hand column
        $ticketsCannedFields = \App\TicketsCannedField::all();

        return view('tickets.create', compact('users', 'locations', 'ticketsStatuses', 'ticketsTypes', 
                                            'ticketsPriorities', 'assets', 'pageTitle', 'ticketsCannedFields'));
    }

    /**
     * Show the form for creating a ticket with pre-selected asset
     */
    public function createWithAsset(Request $request)
    {
        $asset = null;
        if ($request->has('asset_id')) {
            $asset = Asset::find($request->asset_id);
        }

        $priorities = TicketsPriority::all();
        $types = TicketsType::all();
        $locations = Location::all();
        $assets = Asset::where('assigned_to', auth()->id())->get();

        return view('tickets.create-with-asset', compact('priorities', 'types', 'locations', 'assets', 'asset'));
    }

    /**
     * Store a newly created ticket
     */
    public function store(CreateTicketRequest $request)
    {
        try {
            $ticket = $this->ticketService->createTicket($request->validated());
            
            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket berhasil dibuat dengan kode: ' . $ticket->ticket_code);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat ticket: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'ticket_status', 'ticket_priority', 'ticket_type', 'location', 'asset', 'ticket_entries']);
        $pageTitle = 'Ticket Details - ' . $ticket->ticket_code;
        
        // Get ticket entries for the view (the view expects this variable)
        $ticketEntries = $ticket->ticket_entries;
        
        // Get dropdown data required by the view
        $users = User::select('id', 'name')->orderBy('name')->get();
        $locations = Location::select('id', 'location_name')->orderBy('location_name')->get();
        $ticketsStatuses = TicketsStatus::select('id', 'status')->orderBy('status')->get();
        $ticketsTypes = TicketsType::select('id', 'type')->orderBy('type')->get();
        $ticketsPriorities = TicketsPriority::select('id', 'priority')->orderBy('priority')->get();
        
        return view('tickets.show', compact('ticket', 'pageTitle', 'ticketEntries', 
                                          'users', 'locations', 'ticketsStatuses', 'ticketsTypes', 'ticketsPriorities'));
    }

    /**
     * Show the form for editing the specified ticket
     */
    public function edit(Ticket $ticket)
    {
        $user = auth()->user();
        
        Log::info('Accessing ticket edit', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id
        ]);
        
        // Check if user has permission to edit this ticket
        $hasRole = $this->hasAnyRole(['super-admin', 'admin']);
        $isAssigned = $ticket->assigned_to === $user->id;
        
        Log::info('Checking edit permissions', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_to' => $ticket->assigned_to,
            'has_admin_role' => $hasRole,
            'is_assigned' => $isAssigned,
            'can_edit' => $hasRole || $isAssigned
        ]);
        
        if (!$hasRole && !$isAssigned) {
            Log::warning('User denied access to edit ticket', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'assigned_to' => $ticket->assigned_to
            ]);
            return redirect()->route('tickets.show', $ticket)
                           ->with('error', 'You do not have permission to edit this ticket.');
        }

        $ticket->load(['user', 'assignedTo', 'ticket_status', 'ticket_priority', 'ticket_type', 'location', 'asset']);
        
        // Get dropdown data for the edit form
        $users = User::select('id', 'name')->orderBy('name')->get();
        $locations = Location::select('id', 'location_name')->orderBy('location_name')->get();
        $ticketsStatuses = TicketsStatus::select('id', 'status')->orderBy('status')->get();
        $ticketsTypes = TicketsType::select('id', 'type')->orderBy('type')->get();
        $ticketsPriorities = TicketsPriority::select('id', 'priority')->orderBy('priority')->get();
        $assets = Asset::select('assets.id', 'assets.asset_tag', 'asset_models.asset_model as model_name')
                      ->leftJoin('asset_models', 'assets.model_id', '=', 'asset_models.id')
                      ->orderBy('assets.asset_tag')
                      ->get();
        
        return view('tickets.edit', compact('ticket', 'users', 'locations', 'ticketsStatuses', 
                                          'ticketsTypes', 'ticketsPriorities', 'assets'));
    }

    /**
     * Update the specified ticket in storage
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        
        // Check if user has permission to update this ticket
        if (!$this->hasAnyRole(['super-admin', 'admin']) && $ticket->assigned_to !== $user->id) {
            return redirect()->route('tickets.show', $ticket)
                           ->with('error', 'You do not have permission to update this ticket.');
        }

        // Log detailed request info
        Log::info('Ticket update request received', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'method' => $request->method(),
            'request_data' => $request->all(),
            'has_subject' => $request->has('subject'),
            'subject_value' => $request->input('subject'),
            'has_description' => $request->has('description'),  
            'description_value' => $request->input('description')
        ]);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'ticket_priority_id' => 'required|exists:tickets_priorities,id',
            'ticket_type_id' => 'required|exists:tickets_types,id',
            'ticket_status_id' => 'required|exists:tickets_statuses,id',
            'location_id' => 'nullable|exists:locations,id',
            'asset_id' => 'nullable|exists:assets,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            Log::info('Attempting to update ticket', [
                'ticket_id' => $ticket->id,
                'validated_data' => $validated,
                'user_id' => $user->id
            ]);
            
            $ticket->update($validated);
            
            Log::info('Ticket updated successfully', ['ticket_id' => $ticket->id]);
            
            return redirect()->route('tickets.show', $ticket)
                           ->with('success', 'Ticket updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()
                        ->with('error', 'Failed to update ticket: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ticket
     */
    public function destroy(Ticket $ticket)
    {
        $user = auth()->user();

        if (!$this->hasAnyRole(['super-admin', 'admin']) && $ticket->assigned_to !== $user->id) {
            return redirect()->route('tickets.index')->with('error', 'You do not have permission to delete this ticket.');
        }

        try {
            $ticket->delete();
            return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ticket: ' . $e->getMessage());
        }
    }

    /**
     * Show unassigned tickets for admin self-assignment
     */
    public function unassigned()
    {
        $tickets = $this->ticketService->getUnassignedTickets();
        
        return view('tickets.unassigned', compact('tickets'));
    }

    /**
     * Show overdue tickets
     */
    public function overdue()
    {
        $tickets = $this->ticketService->getOverdueTickets();
        
        return view('tickets.overdue', compact('tickets'));
    }

    /**
     * Export tickets to Excel
     */
    public function export()
    {
        try {
            $excel = app(\Maatwebsite\Excel\Excel::class);
            return $excel->download(new \App\Exports\TicketsExport, 'tickets_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Print ticket details to PDF
     */
    public function print($id)
    {
        $ticket = Ticket::with(['user', 'assignedTo', 'location', 'asset', 'ticket_status', 'ticket_priority', 'ticket_type', 'ticket_entries'])
                       ->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tickets.print', compact('ticket'));
        
        return $pdf->stream('ticket_' . $ticket->ticket_code . '.pdf');
    }
}
