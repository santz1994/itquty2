<?php

// Add this test route to check what's causing the assets page to fail
Route::get('/test-inventory', function() {
    try {
        echo "<h2>Testing Inventory Controller Data</h2>";
        
        // Test each model one by one
        echo "<h3>Model Tests:</h3>";
        
        echo "Asset count: " . \App\Asset::count() . "<br>";
        echo "AssetType count: " . \App\AssetType::count() . "<br>";
        echo "Status count: " . \App\Status::count() . "<br>";
        echo "Location count: " . \App\Location::count() . "<br>";
        echo "Division count: " . \App\Division::count() . "<br>";
        
        // Test the problematic queries
        echo "<h3>Stats Queries:</h3>";
        
        $activeCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'Active');
        })->count();
        echo "Active assets: " . $activeCount . "<br>";
        
        $maintenanceCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'like', '%repair%');
        })->count();
        echo "Maintenance assets: " . $maintenanceCount . "<br>";
        
        // Check if AssetRequest model exists
        if (class_exists('\App\AssetRequest')) {
            $pendingCount = \App\AssetRequest::where('status', 'pending')->count();
            echo "Pending requests: " . $pendingCount . "<br>";
        } else {
            echo "AssetRequest model not found<br>";
        }
        
        echo "<h3>Controller Test:</h3>";
        
        // Test the actual controller
        $controller = new \App\Http\Controllers\InventoryController();
        $request = new \Illuminate\Http\Request();
        
        echo "Calling controller index method...<br>";
        $result = $controller->index($request);
        
        if ($result instanceof \Illuminate\View\View) {
            echo "Controller returned a view successfully!<br>";
            $viewData = $result->getData();
            echo "View data keys: " . implode(', ', array_keys($viewData)) . "<br>";
        } else {
            echo "Controller returned: " . gettype($result) . "<br>";
        }
        
    } catch (\Exception $e) {
        echo "<h3>Error:</h3>";
        echo "Message: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

?>