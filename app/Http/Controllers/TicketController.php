<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Tickets\AssignTicketRequest;
use App\Http\Requests\Tickets\CompleteTicketRequest;
use App\Http\Requests\CreateTicketRequest;
use App\Services\TicketService;
use App\Ticket;
use App\Asset;
use App\User;
use App\Location;
use App\TicketsPriority;
use App\TicketsType;
use App\TicketsStatus;
use App\Traits\RoleBasedAccessTrait;

use App\Http\Controllers\Controller as BaseController;

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

        // Search by ticket code or subject
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('ticket_code', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options - ViewComposer handles most dropdown data
        $statuses = TicketsStatus::orderBy('status')->get();
        $priorities = TicketsPriority::orderBy('priority')->get();
        $admins = User::admins()->orderBy('name')->get();
        $pageTitle = 'Ticket Management';

        return view('tickets.index', compact('tickets', 'statuses', 'priorities', 'admins', 'pageTitle'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        // Get dropdown data with correct variable names expected by the view
        $users = User::select('id', 'name')->orderBy('name')->get();
        $locations = Location::select('id', 'location_name')->orderBy('location_name')->get();
        $ticketsStatuses = TicketsStatus::select('id', 'status')->orderBy('status')->get();
        $ticketsTypes = TicketsType::select('id', 'type')->orderBy('type')->get();
        $ticketsPriorities = TicketsPriority::select('id', 'priority')->orderBy('priority')->get();
        $assets = Asset::where('assigned_to', auth()->id())->get(); // Only user's assets
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
     * Show unassigned tickets for admin self-assignment
     */
    public function unassigned()
    {
        $tickets = $this->ticketService->getUnassignedTickets();
        
        return view('tickets.unassigned', compact('tickets'));
    }

    /**
     * Self-assign ticket to current admin
     */
    public function selfAssign(Ticket $ticket)
    {
        try {
            $this->ticketService->selfAssignTicket($ticket, auth()->id());
            
            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket berhasil diambil dan ditugaskan kepada Anda');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengambil ticket: ' . $e->getMessage());
        }
    }

    /**
     * Assign ticket to specific admin (SuperAdmin only)
     */
    public function assign(AssignTicketRequest $request, Ticket $ticket)
    {
        try {
            $this->ticketService->assignTicket($ticket, $request->admin_id, 'super_admin');
            
            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket berhasil ditugaskan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan ticket: ' . $e->getMessage());
        }
    }

    /**
     * Force assign ticket (SuperAdmin override)
     */
    public function forceAssign(AssignTicketRequest $request, Ticket $ticket)
    {
        try {
            $this->ticketService->assignTicket($ticket, $request->admin_id, 'super_admin');
            
            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket berhasil di-reassign');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reassign ticket: ' . $e->getMessage());
        }
    }

    /**
     * Complete ticket
     */
    public function complete(CompleteTicketRequest $request, Ticket $ticket)
    {
        try {
            $this->ticketService->completeTicket($ticket, $request->resolution);
            
            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket berhasil diselesaikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan ticket: ' . $e->getMessage());
        }
    }

    /**
     * Show overdue tickets
     */
    public function overdue()
    {
        $tickets = $this->ticketService->getOverdueTickets();
        
        return view('tickets.overdue', compact('tickets'));
    }


}