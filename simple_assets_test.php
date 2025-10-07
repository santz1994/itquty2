// Quick fix - Create a simple working version of the assets index
// Add this to test the basic functionality

Route::get('/assets-simple', function() {
    try {
        $pageTitle = 'Simple Assets Test';
        
        // Simple stats without complex relationships
        $stats = [
            'total_assets' => \App\Asset::count(),
            'active_assets' => 0, // Temporarily simplified
            'maintenance_assets' => 0, // Temporarily simplified  
            'pending_requests' => 0 // Temporarily simplified
        ];
        
        // Get basic data
        $assets = \App\Asset::with(['assetModel', 'status', 'division'])->paginate(25);
        $categories = \App\AssetType::all();
        $statuses = \App\Status::all();
        $locations = \App\Location::all();
        $divisions = \App\Division::all();
        $categoryStats = collect(); // Empty for now
        
        return view('inventory.index', compact(
            'assets', 'categories', 'statuses', 'locations', 'divisions', 
            'stats', 'categoryStats', 'pageTitle'
        ));
        
    } catch (\Exception $e) {
        return response("Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine(), 500);
    }
});