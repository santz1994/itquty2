<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Movement;
use App\Asset;
use App\Location;
use App\Status;
use App\Budget;
use App\Invoice;
use App\Division;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Http\Request;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
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
      $user = Auth::user();
      if ($user->hasRole('user')) {
        return redirect()->route('tickets.index');
      } else {
        // Use eager loading for better performance
        $assets = Asset::with(['assetModel', 'status', 'division'])->get();
        $locations = Location::all();
        $statuses = Status::all();
        $budgets = Budget::all();
        $invoices = Invoice::all();
        $divisions = Division::all();
        $year = \Carbon\Carbon::now()->year;
        
        // Load movements with relationships
        $movements = Movement::with(['asset', 'location', 'user'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                            
        return view('home', compact('assets', 'movements', 'locations', 'statuses', 'budgets', 'invoices', 'divisions', 'year', 'pageTitle'));
      }
    }
}
