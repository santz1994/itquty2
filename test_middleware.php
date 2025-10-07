<?php
require 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING SPECIFIC ROUTE MATCH ===\n\n";

$testUrls = [
    '/assets',
    '/spares',
    '/tickets',
    '/daily-activities',
];

foreach ($testUrls as $url) {
    echo "Testing: {$url}\n";
    try {
        $request = \Illuminate\Http\Request::create($url, 'GET');
        $route = app('router')->getRoutes()->match($request);
        
        echo "  ✅ Route found\n";
        echo "  Controller: " . $route->getActionName() . "\n";
        echo "  Middleware: " . implode(', ', $route->middleware()) . "\n";
        echo "  Name: " . ($route->getName() ?? 'unnamed') . "\n";
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "\n=== TESTING MIDDLEWARE CHECK ===\n\n";

// Simulate being logged in as super-admin
$user = \App\User::find(1); // Super Admin User
if ($user) {
    auth()->login($user);
    echo "Logged in as: {$user->name}\n";
    echo "Has super-admin role: " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "\n";
    echo "Has admin role: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";
    
    // Test the middleware
    echo "\nTesting middleware 'role:admin|super-admin':\n";
    $middleware = app(\Spatie\Permission\Middlewares\RoleMiddleware::class);
    
    try {
        $request = \Illuminate\Http\Request::create('/assets', 'GET');
        $response = $middleware->handle($request, function($req) {
            return 1;
        }, 'admin|super-admin');
        echo "  ✅ Middleware passed!\n";
    } catch (\Exception $e) {
        echo "  ❌ Middleware failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Could not find user ID 1\n";
}