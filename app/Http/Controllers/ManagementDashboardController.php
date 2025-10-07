<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TicketService;
use App\Services\AssetService;
use App\Services\KPIService;
use App\Ticket;
use App\Asset;
use App\User;
use App\DailyActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagementDashboardController extends Controller
{
    protected $ticketService;
    protected $assetService;
    protected $kpiService;

    public function __construct(
        TicketService $ticketService,
        AssetService $assetService, 
        KPIService $kpiService
    ) {
        $this->ticketService = $ticketService;
        $this->assetService = $assetService;
        $this->kpiService = $kpiService;
        
        $this->middleware('permission:view_kpi_dashboard');
    }

    public function index()
    {
        $data = [
            'overview' => $this->getOverviewData(),
            'admin_performance' => $this->getAdminPerformanceData(),
            'ticket_trends' => $this->getTicketTrends(),
            'asset_overview' => $this->getAssetOverview(),
            'sla_compliance' => $this->getSLACompliance()
        ];

        return view('management.dashboard', $data);
    }

    public function adminPerformance(Request $request)
    {
        $period = $request->get('period', 'month'); // month, quarter, year
        $adminId = $request->get('admin_id');

        $data = $this->kpiService->getAdminPerformanceReport($period, $adminId);

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('management.admin-performance', $data);
    }

    public function ticketReports(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());

        $data = [
            'ticket_summary' => $this->getTicketSummary($startDate, $endDate),
            'priority_breakdown' => $this->getPriorityBreakdown($startDate, $endDate),
            'status_breakdown' => $this->getStatusBreakdown($startDate, $endDate),
            'resolution_times' => $this->getResolutionTimes($startDate, $endDate)
        ];

        return view('management.ticket-reports', $data);
    }

    public function assetReports()
    {
        $data = [
            'asset_summary' => $this->getAssetSummary(),
            'asset_by_status' => $this->getAssetsByStatus(),
            'asset_by_location' => $this->getAssetsByLocation(),
            'lemon_assets' => $this->getLemonAssets(),
            'warranty_expiring' => $this->getWarrantyExpiring()
        ];

        return view('management.asset-reports', $data);
    }

    private function getOverviewData()
    {
        return [
            'total_tickets_today' => Ticket::whereDate('created_at', today())->count(),
            'total_tickets_month' => Ticket::whereMonth('created_at', now()->month)->count(),
            'overdue_tickets' => $this->ticketService->getOverdueTickets()->count(),
            'unassigned_tickets' => $this->ticketService->getUnassignedTickets()->count(),
            'total_assets' => Asset::count(),
            'active_admins' => User::role('admin')->whereHas('adminOnlineStatus', function($q) {
                $q->where('last_activity', '>=', now()->subHours(8));
            })->count()
        ];
    }

    private function getAdminPerformanceData()
    {
        $admins = User::role('admin')->get();
        $performance = [];

        foreach ($admins as $admin) {
            $metrics = $this->ticketService->getAdminPerformance($admin->id);
            $performance[] = [
                'admin' => $admin,
                'metrics' => $metrics
            ];
        }

        return $performance;
    }

    private function getTicketTrends()
    {
        $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date' => $date->format('Y-m-d'),
                'created' => Ticket::whereDate('created_at', $date)->count(),
                'resolved' => Ticket::whereDate('resolved_at', $date)->count()
            ];
        });

        return $last30Days;
    }

    private function getAssetOverview()
    {
        return [
            'total' => Asset::count(),
            'in_use' => Asset::inUse()->count(),
            'in_stock' => Asset::inStock()->count(),
            'in_repair' => Asset::inRepair()->count(),
            'disposed' => Asset::disposed()->count()
        ];
    }

    private function getSLACompliance()
    {
        $totalTickets = Ticket::whereMonth('created_at', now()->month)->count();
        $onTimeTickets = Ticket::whereMonth('created_at', now()->month)
                              ->where(function($q) {
                                  $q->whereNull('resolved_at')
                                    ->where('sla_due', '>', now())
                                    ->orWhere(function($subQ) {
                                        $subQ->whereNotNull('resolved_at')
                                             ->whereColumn('resolved_at', '<=', 'sla_due');
                                    });
                              })->count();

        return $totalTickets > 0 ? round(($onTimeTickets / $totalTickets) * 100, 2) : 0;
    }

    private function getTicketSummary($startDate, $endDate)
    {
        return [
            'total_created' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_resolved' => Ticket::whereBetween('resolved_at', [$startDate, $endDate])->count(),
            'avg_resolution_time' => $this->getAverageResolutionTime($startDate, $endDate),
            'sla_compliance' => $this->getSLAComplianceForPeriod($startDate, $endDate)
        ];
    }

    private function getPriorityBreakdown($startDate, $endDate)
    {
        return Ticket::select('ticket_priority_id', DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with('ticket_priority')
                    ->groupBy('ticket_priority_id')
                    ->get();
    }

    private function getStatusBreakdown($startDate, $endDate)
    {
        return Ticket::select('ticket_status_id', DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with('ticket_status')
                    ->groupBy('ticket_status_id')
                    ->get();
    }

    private function getResolutionTimes($startDate, $endDate)
    {
        return Ticket::whereBetween('resolved_at', [$startDate, $endDate])
                    ->whereNotNull('resolved_at')
                    ->select(DB::raw('
                        AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours,
                        MIN(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as min_hours,
                        MAX(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as max_hours
                    '))->first();
    }

    private function getAssetSummary()
    {
        return [
            'total_assets' => Asset::count(),
            'by_type' => Asset::select('asset_types.name', DB::raw('count(*) as count'))
                             ->join('asset_models', 'assets.model_id', '=', 'asset_models.id')
                             ->join('asset_types', 'asset_models.asset_type_id', '=', 'asset_types.id')
                             ->groupBy('asset_types.name')
                             ->get(),
            'by_status' => Asset::select('statuses.name', DB::raw('count(*) as count'))
                               ->join('statuses', 'assets.status_id', '=', 'statuses.id')
                               ->groupBy('statuses.name')
                               ->get()
        ];
    }

    private function getAssetsByStatus()
    {
        return Asset::select('status_id', DB::raw('count(*) as count'))
                   ->with('status')
                   ->groupBy('status_id')
                   ->get();
    }

    private function getAssetsByLocation()
    {
        return Asset::select('locations.name', DB::raw('count(*) as count'))
                   ->join('asset_models', 'assets.model_id', '=', 'asset_models.id')
                   ->join('divisions', 'assets.division_id', '=', 'divisions.id')
                   ->join('locations', 'divisions.location_id', '=', 'locations.id')
                   ->groupBy('locations.name')
                   ->get();
    }

    private function getLemonAssets()
    {
        return Asset::whereHas('tickets', function($q) {
                    $q->where('created_at', '>=', now()->subMonths(6));
                }, '>=', 3)
                ->with(['model', 'tickets' => function($q) {
                    $q->where('created_at', '>=', now()->subMonths(6));
                }])
                ->get();
    }

    private function getWarrantyExpiring()
    {
        return Asset::whereNotNull('purchase_date')
                   ->whereNotNull('warranty_months')
                   ->whereRaw('DATE_ADD(purchase_date, INTERVAL warranty_months MONTH) <= ?', [now()->addMonth()])
                   ->whereRaw('DATE_ADD(purchase_date, INTERVAL warranty_months MONTH) > ?', [now()])
                   ->with(['model', 'assignedTo'])
                   ->get();
    }

    private function getAverageResolutionTime($startDate, $endDate)
    {
        $resolved = Ticket::whereBetween('resolved_at', [$startDate, $endDate])
                         ->whereNotNull('resolved_at')
                         ->get();

        if ($resolved->isEmpty()) return 0;

        $totalHours = $resolved->sum(function($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return round($totalHours / $resolved->count(), 2);
    }

    private function getSLAComplianceForPeriod($startDate, $endDate)
    {
        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        $onTimeTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
                              ->where(function($q) {
                                  $q->whereNull('resolved_at')
                                    ->where('sla_due', '>', now())
                                    ->orWhere(function($subQ) {
                                        $subQ->whereNotNull('resolved_at')
                                             ->whereColumn('resolved_at', '<=', 'sla_due');
                                    });
                              })->count();

        return $totalTickets > 0 ? round(($onTimeTickets / $totalTickets) * 100, 2) : 0;
    }
}