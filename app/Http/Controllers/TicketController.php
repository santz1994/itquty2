<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateTicketRequest;
use App\Services\TicketService;
use App\Ticket;
use App\Asset;
use App\User;
use App\Location;
use App\TicketsPriority;
use App\TicketsType;
use App\TicketsStatus;

use App\Http\Controllers\Controller as BaseController;

class TicketController extends BaseController
{
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
        $query = Ticket::with(['user', 'assignedTo', 'ticket_status', 'ticket_priority', 'asset']);

        // Role-based filtering
        if ($user->hasRole('user')) {
            // Users can only see their own tickets
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('management')) {
            // Management can see all tickets but only view
            // No additional filtering needed - they see all
        } elseif ($user->hasRole(['admin', 'super-admin'])) {
            // Admin and SuperAdmin can see all tickets
            // No additional filtering needed - they see all
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('ticket_status_id', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('ticket_priority_id', $request->priority);
        }

        // Filter by assigned admin (only for management, admin, super-admin)
        if ($request->has('assigned_to') && $request->assigned_to !== '' && !$user->hasRole('user')) {
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

        // Get filter options
        $statuses = TicketsStatus::all();
        $priorities = TicketsPriority::all();
        $admins = User::role('admin')->get();
        $pageTitle = 'Ticket Management';

        return view('tickets.index', compact('tickets', 'statuses', 'priorities', 'admins', 'pageTitle'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $priorities = TicketsPriority::all();
        $types = TicketsType::all();
        $locations = Location::all();
        $assets = Asset::where('assigned_to', auth()->id())->get(); // Only user's assets
        $pageTitle = 'Create New Ticket';

        return view('tickets.create', compact('priorities', 'types', 'locations', 'assets', 'pageTitle'));
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
        
        return view('tickets.show', compact('ticket', 'pageTitle', 'ticketEntries'));
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
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

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
    public function forceAssign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

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
    public function complete(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution' => 'nullable|string|max:1000'
        ]);

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