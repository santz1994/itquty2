<?php

use Illuminate\Support\Facades\Route;

// Simple test route to debug the assets issue
Route::get('/test-assets', function() {
    try {
        // Test database connection first
        $assetCount = \App\Asset::count();
        
        // Test the InventoryController directly
        $controller = new \App\Http\Controllers\InventoryController();
        $request = new \Illuminate\Http\Request();
        
        // This should show us what happens
        $result = $controller->index($request);
        
        if ($result instanceof \Illuminate\View\View) {
            return "View returned successfully with data: " . json_encode($result->getData());
        } else {
            return "Controller returned: " . gettype($result);
        }
        
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
    }
})->name('test-assets');

?>