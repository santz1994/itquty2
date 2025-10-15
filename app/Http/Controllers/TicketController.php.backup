<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Tickets\AssignTicketRequest;
use App\Http\Requests\Tickets\CompleteTicketRequest;
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
use App\DailyActivity;
use App\Traits\RoleBasedAccessTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    // ========================================
    // USER SELF-SERVICE PORTAL METHODS
    // ========================================

    /**
     * Display user's own tickets
     */
    public function userTickets(Request $request)
    {
        $user = auth()->user();
        $query = Ticket::withRelations()->where('user_id', $user->id);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('ticket_status_id', $request->status);
        }

        // Search by ticket code or subject
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('ticket_code', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = CacheService::getTicketStatuses();
        $pageTitle = 'Tiket Saya';

        return view('tickets.user.index', compact('tickets', 'statuses', 'pageTitle'));
    }

    /**
     * Show user-friendly ticket creation form
     */
    public function userCreate()
    {
        $user = auth()->user();
        
        // Get user's assets
        $userAssets = Asset::where('assigned_to', $user->id)
                          ->with(['model', 'status'])
                          ->orderBy('asset_tag')
                          ->get();
        
        // Get simplified dropdown data for users
        $ticketTypes = CacheService::getTicketTypes();
        $ticketPriorities = CacheService::getTicketPriorities();
        $locations = CacheService::getLocations();
        $pageTitle = 'Buat Tiket Baru';

        return view('tickets.user.create', compact('userAssets', 'ticketTypes', 'ticketPriorities', 'locations', 'pageTitle'));
    }

    /**
     * Store user-created ticket
     */
    public function userStore(Request $request)
    {
        $user = auth()->user();
        
        // Validation rules for user self-service
        $validatedData = $request->validate([
            'ticket_type_id' => 'required|exists:tickets_types,id',
            'ticket_priority_id' => 'required|exists:tickets_priorities,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'asset_id' => 'nullable|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id'
        ]);

        // Ensure user can only assign tickets to their own assets
        if ($request->asset_id) {
            $asset = Asset::where('id', $request->asset_id)
                         ->where('assigned_to', $user->id)
                         ->first();
            if (!$asset) {
                return back()->withErrors(['asset_id' => 'Anda hanya dapat memilih aset yang ditugaskan kepada Anda.'])
                            ->withInput();
            }
        }

        try {
            // Set default values for user-created tickets
            $validatedData['user_id'] = $user->id;
            $validatedData['ticket_status_id'] = 1; // Default to 'Open' status
            $validatedData['location_id'] = $validatedData['location_id'] ?? $user->location_id;
            
            $ticket = $this->ticketService->createTicket($validatedData);
            
            return redirect()->route('tickets.user-show', $ticket->id)
                           ->with('success', 'Tiket berhasil dibuat dengan kode: ' . $ticket->ticket_code);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat tiket: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Show user's specific ticket (read-only for users)
     */
    public function userShow(Ticket $ticket)
    {
        $user = auth()->user();
        
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat melihat tiket Anda sendiri.');
        }

        $ticket->load(['user', 'assignedTo', 'ticket_status', 'ticket_priority', 'ticket_type', 'location', 'asset.model', 'ticket_entries']);
        $pageTitle = 'Detail Tiket - ' . $ticket->ticket_code;
        
        // Get ticket entries for timeline
        $ticketEntries = $ticket->ticket_entries()->with('user')->orderBy('created_at', 'desc')->get();
        
        return view('tickets.user.show', compact('ticket', 'pageTitle', 'ticketEntries'));
    }

    // ========================================
    // TECHNICIAN ACTIVITY TRACKING METHODS
    // ========================================

    /**
     * Add technician response to ticket
     */
    public function addResponse(Request $request, Ticket $ticket)
    {
        $request->validate([
            'response' => 'required|string',
            'status_change' => 'nullable|in:In Progress,Pending,Resolved'
        ]);

        try {
            $userId = auth()->id();

            // Add the response
            $this->ticketService->addFirstResponse($ticket, $userId, $request->response);

            // Update status if requested
            if ($request->status_change) {
                $this->ticketService->updateTicketStatus($ticket, $request->status_change, $userId);
            }

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Respons berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan respons: ' . $e->getMessage()]);
        }
    }

    /**
     * Update ticket status with notes
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $this->ticketService->updateTicketStatus(
                $ticket, 
                $request->status, 
                auth()->id(), 
                $request->notes
            );

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Status tiket berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }

    /**
     * Complete ticket with resolution
     */
    public function completeWithResolution(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution' => 'required|string|min:10'
        ]);

        try {
            $this->ticketService->closeTicket($ticket, $request->resolution, auth()->id());

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Tiket berhasil diselesaikan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyelesaikan tiket: ' . $e->getMessage()]);
        }
    }

    // ========================================
    // TIME TRACKING METHODS
    // ========================================

    /**
     * Start timer for technician work on ticket
     */
    public function startTimer(Request $request, Ticket $ticket)
    {
        $userId = auth()->id();
        $sessionKey = "ticket_timer_{$ticket->id}_{$userId}";
        
        // Check if timer is already running
        if (session()->has($sessionKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Timer sudah berjalan untuk tiket ini'
            ]);
        }

        // Store timer start time in session
        session()->put($sessionKey, [
            'start_time' => now(),
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'description' => $request->input('description', 'Bekerja pada tiket: ' . $ticket->subject)
        ]);

        // Add ticket entry for timer start
        $this->ticketService->addTicketEntry($ticket, $userId, "Timer dimulai: " . $request->input('description', 'Mulai bekerja pada tiket ini'));

        return response()->json([
            'success' => true,
            'message' => 'Timer berhasil dimulai',
            'start_time' => now()->toISOString()
        ]);
    }

    /**
     * Stop timer and log activity
     */
    public function stopTimer(Request $request, Ticket $ticket)
    {
        $userId = auth()->id();
        $sessionKey = "ticket_timer_{$ticket->id}_{$userId}";
        
        // Check if timer is running
        if (!session()->has($sessionKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Timer tidak ditemukan atau sudah dihentikan'
            ]);
        }

        $timerData = session()->get($sessionKey);
        $startTime = Carbon::parse($timerData['start_time']);
        $endTime = now();
        $durationMinutes = $startTime->diffInMinutes($endTime);

        // Validate minimum work time (e.g., at least 1 minute)
        if ($durationMinutes < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Durasi kerja minimal 1 menit'
            ]);
        }

        try {
            DB::transaction(function () use ($ticket, $userId, $timerData, $durationMinutes, $request, $startTime, $endTime) {
                // Create daily activity
                DailyActivity::create([
                    'user_id' => $userId,
                    'activity_date' => $startTime->toDateString(),
                    'description' => $timerData['description'],
                    'ticket_id' => $ticket->id,
                    'type' => 'timer_tracking',
                    'duration_minutes' => $durationMinutes,
                    'notes' => $request->input('notes', ''),
                    'status' => 'completed'
                ]);

                // Add ticket entry with work summary
                $workSummary = $request->input('work_summary', 'Menyelesaikan pekerjaan pada tiket');
                $entryMessage = "Timer dihentikan (Durasi: {$durationMinutes} menit)\n\nRingkasan Pekerjaan:\n{$workSummary}";
                
                if ($request->input('notes')) {
                    $entryMessage .= "\n\nCatatan:\n" . $request->input('notes');
                }

                $this->ticketService->addTicketEntry($ticket, $userId, $entryMessage);

                // Update ticket status if requested
                if ($request->input('status_change')) {
                    $this->ticketService->updateTicketStatus($ticket, $request->input('status_change'), $userId);
                }
            });

            // Remove timer from session
            session()->forget($sessionKey);

            return response()->json([
                'success' => true,
                'message' => 'Timer berhasil dihentikan dan aktivitas telah dicatat',
                'duration_minutes' => $durationMinutes,
                'duration_formatted' => $this->formatDuration($durationMinutes)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghentikan timer: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current timer status
     */
    public function getTimerStatus(Ticket $ticket)
    {
        $userId = auth()->id();
        $sessionKey = "ticket_timer_{$ticket->id}_{$userId}";
        
        if (!session()->has($sessionKey)) {
            return response()->json([
                'is_running' => false
            ]);
        }

        $timerData = session()->get($sessionKey);
        $startTime = Carbon::parse($timerData['start_time']);
        $currentDuration = $startTime->diffInMinutes(now());

        return response()->json([
            'is_running' => true,
            'start_time' => $startTime->toISOString(),
            'duration_minutes' => $currentDuration,
            'duration_formatted' => $this->formatDuration($currentDuration),
            'description' => $timerData['description']
        ]);
    }

    /**
     * Get ticket work summary (time spent by all technicians)
     */
    public function getWorkSummary(Ticket $ticket)
    {
        $activities = DailyActivity::where('ticket_id', $ticket->id)
                                  ->with('user')
                                  ->orderBy('activity_date', 'desc')
                                  ->get();

        $totalMinutes = $activities->sum('duration_minutes');
        $workByTechnician = $activities->groupBy('user_id')
                                     ->map(function ($userActivities) {
                                         $user = $userActivities->first()->user;
                                         return [
                                             'name' => $user->name,
                                             'total_minutes' => $userActivities->sum('duration_minutes'),
                                             'activities_count' => $userActivities->count(),
                                             'last_activity' => $userActivities->first()->activity_date->format('d M Y')
                                         ];
                                     });

        return response()->json([
            'total_minutes' => $totalMinutes,
            'total_formatted' => $this->formatDuration($totalMinutes),
            'work_by_technician' => $workByTechnician,
            'activities' => $activities->map(function ($activity) {
                return [
                    'date' => $activity->activity_date->format('d M Y'),
                    'duration' => $activity->duration_minutes,
                    'duration_formatted' => $this->formatDuration($activity->duration_minutes),
                    'description' => $activity->description,
                    'technician' => $activity->user->name,
                    'notes' => $activity->notes
                ];
            })
        ]);
    }

    /**
     * Format duration in minutes to readable format
     */
    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours == 1) {
            return $mins > 0 ? "1 jam {$mins} menit" : "1 jam";
        }

        return $mins > 0 ? "{$hours} jam {$mins} menit" : "{$hours} jam";
    }
}