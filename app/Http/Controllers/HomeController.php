<?php

namespace App\Http\Controllers;

use App\Movement;
use App\Asset;
use App\Location;
use App\Division;
use Illuminate\Support\Facades\Auth;
use App\Traits\RoleBasedAccessTrait;

class HomeController extends Controller
{
    use RoleBasedAccessTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
      $pageTitle = 'Dashboard';

  // Get Authenticated User
  /** @var \App\User $user */
  $user = Auth::user();

      // If the user is in the management role, show KPI Dashboard as their home
      if ($this->hasRole('management')) {
          return redirect()->route('kpi.dashboard');
      }

      if ($this->hasRole('user')) {
        return redirect()->route('tickets.index');
      } else {
        // Get summary statistics instead of all records for better performance
        $assetStats = [
          'total_assets' => Asset::count(),
          'active_assets' => Asset::inUse()->count(),
          'available_assets' => Asset::inStock()->count(),
          'maintenance_assets' => Asset::inRepair()->count(),
        ];
        
        $locationCount = Location::count();
        $divisionCount = Division::count();
        $year = \Carbon\Carbon::now()->year;
        
        // Load only recent movements with relationships
        $movements = Movement::with(['asset', 'location', 'user'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        // Get recent assets with their relationships
        $recentAssets = Asset::withRelations()
                           ->orderBy('created_at', 'desc')
                           ->take(10)
                           ->get();
                            
        return view('home', compact('assetStats', 'movements', 'recentAssets', 'locationCount', 'divisionCount', 'year', 'pageTitle'));
      }
    }
}
