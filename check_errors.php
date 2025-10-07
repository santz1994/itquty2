<?php
// Quick error display route
require 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING LARAVEL LOG FOR ERRORS ===\n\n";

$logFile = storage_path('logs/laravel.log');

if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -100); // Last 100 lines
    
    echo "Last errors in log:\n";
    echo str_repeat('-', 80) . "\n";
    
    foreach ($lastLines as $line) {
        if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false || stripos($line, 'fatal') !== false) {
            echo $line;
        }
    }
} else {
    echo "No log file found at: {$logFile}\n";
    echo "Checking alternative location...\n";
    
    $altLog = base_path('storage/logs/laravel.log');
    if (file_exists($altLog)) {
        echo "Found at: {$altLog}\n";
    }
}

echo "\n\n=== TESTING CONTROLLER DIRECTLY ===\n\n";

try {
    $user = \App\User::find(1);
    auth()->login($user);
    
    echo "Logged in as: {$user->name}\n";
    
    // Try to instantiate the controller
    $controller = new \App\Http\Controllers\InventoryController();
    echo "✅ InventoryController instantiated successfully\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}