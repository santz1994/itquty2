<?php
// This file simulates the sidebar.blade.php to test menu visibility logic

require 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

// Get the user
$isLoggedIn = Auth::check();

if (!$isLoggedIn) {
    echo "<!DOCTYPE html><html><body><h1>You are not logged in!</h1>";
    echo "<p>Please login first to test the menu visibility.</p></body></html>";
    exit;
}

$user = Auth::user();
$userName = $user->name;
$userId = $user->id;

// Get user's roles for display
$roles = [];
try {
    // This will only work if the trait is properly loaded
    if (method_exists($user, 'getRoleNames')) {
        $roles = $user->getRoleNames()->toArray();
    }
} catch (\Exception $e) {
    // If any error, fall back to direct DB query
    $roles = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->where('model_id', $userId)
        ->where('model_type', 'App\\User')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->pluck('roles.name')
        ->toArray();
}

// Start HTML output
echo "<!DOCTYPE html>
<html>
<head>
    <title>Menu Visibility Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
        }
        .content {
            margin-left: 20px;
            flex: 1;
        }
        ul.sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            padding: 8px 0;
            border-bottom: 1px solid #444;
        }
        .sidebar-menu a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar-menu .treeview > a {
            display: block;
        }
        .sidebar-menu .treeview-menu {
            list-style: none;
            padding-left: 20px;
            margin-top: 5px;
        }
        .sidebar-menu .treeview-menu li {
            padding: 5px 0;
            border-bottom: none;
        }
        .sidebar-menu .header {
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px 0;
            color: #ccc;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .user-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class='sidebar'>
        <h3>ITQuty Asset Manager</h3>
        
        <!-- Sidebar Menu -->
        <ul class='sidebar-menu'>
            <li class='header'>Navigation</li>";

// Simulate the @role(['super-admin', 'admin']) directive
$showSuperAdminOrAdmin = false;
try {
    $showSuperAdminOrAdmin = $user->hasRole(['super-admin', 'admin']);
} catch (\Exception $e) {
    echo "<!-- Error in hasRole(['super-admin', 'admin']): {$e->getMessage()} -->";
}

if ($showSuperAdminOrAdmin) {
    echo "
            <li><a href='#'>Home</a></li>
            <li><a href='#'>Assets</a></li>
            <li><a href='#'>Spares</a></li>";
}

echo "
            <li class='treeview'>
                <a href='#'>Tickets</a>
            </li>";

// Simulate the @role(['super-admin']) directive
$showSuperAdmin = false;
try {
    $showSuperAdmin = $user->hasRole(['super-admin']);
} catch (\Exception $e) {
    echo "<!-- Error in hasRole(['super-admin']): {$e->getMessage()} -->";
}

if ($showSuperAdmin) {
    echo "
            <li class='treeview'>
                <a href='#'>Models</a>
                <ul class='treeview-menu'>
                    <li><a href='#'>Models</a></li>
                    <li><a href='#'>PC Specifications</a></li>
                    <li><a href='#'>Manufacturers</a></li>
                    <li><a href='#'>Asset Types</a></li>
                </ul>
            </li>
            <li class='treeview'>
                <a href='#'>Suppliers</a>
            </li>
            <li class='treeview'>
                <a href='#'>Locations</a>
            </li>
            <li class='treeview'>
                <a href='#'>Divisions</a>
            </li>
            <li class='treeview'>
                <a href='#'>Invoices and Budgets</a>
                <ul class='treeview-menu'>
                    <li><a href='#'>Invoices</a></li>
                    <li><a href='#'>Budgets</a></li>
                </ul>
            </li>
            <li class='treeview'>
                <a href='#'>Admin</a>
            </li>";
}

echo "
        </ul>
    </div>
    
    <div class='content'>
        <div class='user-info'>
            <h2>Current User Information</h2>
            <p><strong>Name:</strong> {$userName}</p>
            <p><strong>ID:</strong> {$userId}</p>
            <p><strong>Roles:</strong> ";

foreach ($roles as $role) {
    echo "<span class='role-badge'>{$role}</span>";
}

echo "</p>
        </div>
        
        <h2>Menu Visibility Test Results</h2>";

if ($showSuperAdminOrAdmin) {
    echo "<p>✅ Admin/Super-Admin Menu Items: <strong>VISIBLE</strong></p>";
} else {
    echo "<p>❌ Admin/Super-Admin Menu Items: <strong>HIDDEN</strong></p>";
}

if ($showSuperAdmin) {
    echo "<p>✅ Super-Admin Only Menu Items: <strong>VISIBLE</strong></p>";
} else {
    echo "<p>❌ Super-Admin Only Menu Items: <strong>HIDDEN</strong></p>";
}

echo "
        <div class='debug-info'>
            <h3>Debugging Information</h3>
            <p>Directive conditions:</p>
            <ul>
                <li>@role(['super-admin', 'admin']) = " . ($showSuperAdminOrAdmin ? "TRUE" : "FALSE") . "</li>
                <li>@role(['super-admin']) = " . ($showSuperAdmin ? "TRUE" : "FALSE") . "</li>
            </ul>
            
            <p>HasRole method:</p>
            <ul>";

$methods = [
    'super-admin',
    'admin',
    'user',
    ['super-admin', 'admin'],
    ['super-admin'],
    ['admin']
];

foreach ($methods as $method) {
    $result = "ERROR";
    
    try {
        if (is_array($method)) {
            $result = $user->hasRole($method) ? "TRUE" : "FALSE";
            $methodStr = "['".implode("', '", $method)."']";
        } else {
            $result = $user->hasRole($method) ? "TRUE" : "FALSE";
            $methodStr = "'{$method}'";
        }
        
        echo "<li>hasRole({$methodStr}) = {$result}</li>";
    } catch (\Exception $e) {
        echo "<li>hasRole({$methodStr}) = ERROR: {$e->getMessage()}</li>";
    }
}

echo "
            </ul>
            
            <h3>Fix Instructions</h3>
            <ol>
                <li>If menu items are not visible but you have the correct roles, run the clear_all_cache.php script</li>
                <li>Restart your PHP server after clearing the cache</li>
                <li>If still not working, check the HasDualRoles trait implementation</li>
                <li>Ensure your browser is not caching the old page (try Ctrl+F5 or incognito mode)</li>
            </ol>
        </div>
    </div>
</body>
</html>";
?>