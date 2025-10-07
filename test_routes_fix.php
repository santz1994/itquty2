<?php
/**
 * Route Testing Script for Fixed Issues
 * This script tests the routes that were causing errors
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🧪 Testing Fixed Routes\n";
echo "=====================\n\n";

// Test routes that were failing
$routes_to_test = [
    '/daily-activities/create' => 'Daily Activities Create',
    '/daily-activities/calendar' => 'Daily Activities Calendar',
    '/tickets/create' => 'Tickets Create', 
    '/assets/create' => 'Assets Create'
];

foreach ($routes_to_test as $route => $name) {
    echo "Testing {$name} ({$route})...\n";
    
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $kernel->handle($request);
        
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 200) {
            echo "✅ SUCCESS: {$name} - Status {$statusCode}\n";
        } elseif ($statusCode === 302) {
            echo "🔄 REDIRECT: {$name} - Status {$statusCode} (likely auth redirect)\n";
        } else {
            echo "❌ ERROR: {$name} - Status {$statusCode}\n";
        }
        
    } catch (Exception $e) {
        echo "💥 EXCEPTION: {$name} - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "📋 Test Complete!\n";
echo "=================\n";
echo "Next steps:\n";
echo "1. Login to the application\n";
echo "2. Test each route manually to verify view rendering\n";
echo "3. Check for any remaining property access errors\n";