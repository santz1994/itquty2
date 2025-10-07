<?php
// Quick test file to debug route issues

echo "<h1>Route Debug Test</h1>";

// Test if routes are accessible
$routes_to_test = [
    '/daily-activities/calendar',
    '/tickets/unassigned', 
    '/daily-activities/create',
    '/assets'
];

echo "<h2>Routes to test:</h2>";
foreach ($routes_to_test as $route) {
    echo "<p><a href='" . $route . "' target='_blank'>" . $route . "</a></p>";
}

// Test if Auth is working
if (function_exists('auth') && auth()->check()) {
    echo "<p><strong>User is authenticated:</strong> " . auth()->user()->name . "</p>";
    echo "<p><strong>User roles:</strong> ";
    if (method_exists(auth()->user(), 'getRoleNames')) {
        echo implode(', ', auth()->user()->getRoleNames()->toArray());
    } else {
        echo "Cannot check roles";
    }
    echo "</p>";
} else {
    echo "<p><strong>User is NOT authenticated</strong></p>";
    echo "<p><a href='/login'>Please login first</a></p>";
}

?>