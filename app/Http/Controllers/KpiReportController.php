<?php

namespace App\Http\Controllers;

use App\Asset;
use Illuminate\Http\Request;

class KpiReportController extends Controller
{
    /**
     * Endpoint: GET /api/kpi-assets
     * Return KPI report for assets (utilization, downtime, etc)
     */
    public function index(Request $request)
    {
        // Utilization: assets in use / total assets
        $total = Asset::count();
        $inUse = Asset::whereHas('status', fn($q) => $q->where('name', 'In Use'))->count();
        $inStock = Asset::whereHas('status', fn($q) => $q->where('name', 'In Stock'))->count();
        $inRepair = Asset::whereHas('status', fn($q) => $q->where('name', 'In Repair'))->count();
        $disposed = Asset::whereHas('status', fn($q) => $q->where('name', 'Disposed'))->count();

        // Downtime: count of assets in repair
        $downtime = $inRepair;

        // Utilization rate
        $utilizationRate = $total > 0 ? round($inUse / $total * 100, 2) : 0;

        return response()->json([
            'total_assets' => $total,
            'in_use' => $inUse,
            'in_stock' => $inStock,
            'in_repair' => $inRepair,
            'disposed' => $disposed,
            'downtime' => $downtime,
            'utilization_rate' => $utilizationRate,
        ]);
    }
}
