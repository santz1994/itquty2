<?php

namespace App\Http\Controllers;

use App\Asset;
use App\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetMaintenanceControllerSimple extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display asset maintenance dashboard
     */
    public function index()
    {
        // Get assets with maintenance data
        $assets = Asset::with(['status', 'location', 'tickets' => function($query) {
            $query->whereIn('ticket_type_id', [1, 2]); // Maintenance ticket types
        }])->get();

        // Simple statistics
        $stats = [
            'total_assets' => Asset::count(),
            'maintenance_tickets' => Ticket::where('ticket_type_id', 1)->count(),
            'pending_maintenance' => Ticket::where('ticket_type_id', 1)
                                          ->where('ticket_status_id', 1)
                                          ->count(),
            'completed_maintenance' => Ticket::where('ticket_type_id', 1)
                                            ->where('ticket_status_id', 3)
                                            ->count(),
        ];

        return view('asset-maintenance.index', compact('assets', 'stats'));
    }

    /**
     * Show specific asset maintenance history
     */
    public function show(Asset $asset)
    {
        $history = Ticket::where('asset_id', $asset->id)
                        ->with(['status', 'priority', 'type', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('asset-maintenance.show', compact('asset', 'history'));
    }

    /**
     * Create maintenance ticket for an asset
     */
    public function createMaintenanceTicket(Request $request, Asset $asset)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority_id' => 'required|integer|exists:tickets_priorities,id'
        ]);

        $ticketService = app(\App\Services\TicketService::class);
        $ticket = $ticketService->createTicket([
            'user_id' => Auth::id(),
            'asset_id' => $asset->id,
            'location_id' => $asset->location_id,
            'ticket_status_id' => 1, // Open
            'ticket_type_id' => 1, // Maintenance
            'ticket_priority_id' => $request->priority_id,
            'subject' => $request->subject,
            'description' => $request->description,
        ]);

        return redirect()
               ->route('asset-maintenance.show', $asset)
               ->with('success', 'Maintenance ticket created successfully!');
    }

    /**
     * Display analytics dashboard
     */
    public function analytics()
    {
        $data = [
            'monthly_tickets' => Ticket::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('ticket_type_id', 1)
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->get(),
            
            'asset_conditions' => Asset::select('status_id', DB::raw('COUNT(*) as count'))
                                      ->groupBy('status_id')
                                      ->get(),
        ];

        return view('asset-maintenance.analytics', compact('data'));
    }
}