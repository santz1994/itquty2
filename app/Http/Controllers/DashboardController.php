<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\Asset;
use App\Services\AssetService;

class DashboardController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $stats = [];
        $stats['open_tickets'] = Ticket::where('status_id', '!=', 3)->count();
        $stats['overdue_tickets'] = Ticket::where('due_date', '<', now())->count();
        $assetStats = $this->assetService->getAssetStatistics();
        $stats['total_assets'] = $assetStats['total'] ?? 0;
        $maintenanceDue = $this->assetService->getAssetsNeedingMaintenance();
        $recentTickets = Ticket::orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboard.integrated-dashboard', compact('stats', 'recentTickets', 'assetStats', 'maintenanceDue'));
    }
}
