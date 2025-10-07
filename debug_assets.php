<?php

// Debug script for assets route
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Test the controller directly
    $controller = new \App\Http\Controllers\InventoryController();
    
    echo "<h2>Testing InventoryController@index</h2>";
    
    // Check if models exist
    echo "<h3>Model Checks:</h3>";
    echo "Asset model: " . (class_exists('\App\Asset') ? 'EXISTS' : 'MISSING') . "<br>";
    echo "AssetType model: " . (class_exists('\App\AssetType') ? 'EXISTS' : 'MISSING') . "<br>";
    echo "Status model: " . (class_exists('\App\Status') ? 'EXISTS' : 'MISSING') . "<br>";
    echo "Location model: " . (class_exists('\App\Location') ? 'EXISTS' : 'MISSING') . "<br>";
    echo "Division model: " . (class_exists('\App\Division') ? 'EXISTS' : 'MISSING') . "<br>";
    echo "AssetRequest model: " . (class_exists('\App\AssetRequest') ? 'EXISTS' : 'MISSING') . "<br>";
    
    // Test database connection
    echo "<h3>Database Connection:</h3>";
    
    try {
        $pdo = new PDO('sqlite:' . database_path('database.sqlite'));
        echo "Database connection: SUCCESS<br>";
        
        // Check if tables exist
        $tables = ['assets', 'asset_types', 'statuses', 'locations', 'divisions', 'asset_requests'];
        foreach ($tables as $table) {
            $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
            echo "Table '{$table}': " . ($result && $result->fetch() ? 'EXISTS' : 'MISSING') . "<br>";
        }
        
        // Check asset count
        $result = $pdo->query("SELECT COUNT(*) as count FROM assets");
        if ($result) {
            $row = $result->fetch();
            echo "Assets in database: " . $row['count'] . "<br>";
        }
        
    } catch (Exception $e) {
        echo "Database error: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "<h3>View Check:</h3>";
echo "View file exists: " . (file_exists(resource_path('views/inventory/index.blade.php')) ? 'YES' : 'NO') . "<br>";

?>