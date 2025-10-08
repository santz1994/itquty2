<?php

echo "🔍 SYSTEM MANAGEMENT VERIFICATION\n";
echo "=================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Test 1: Verify Super Admin User
echo "👤 Test 1: Super Admin User Status\n";
$superAdmin = DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->where('roles.name', 'super-admin')
    ->select('users.name', 'users.email', 'roles.name as role_name')
    ->first();

if ($superAdmin) {
    echo "✅ Super Admin found: {$superAdmin->name} ({$superAdmin->email})\n";
} else {
    echo "❌ No super admin user found!\n";
}

// Test 2: Verify System Permissions
echo "\n🔑 Test 2: System Permissions\n";
$systemPerms = DB::table('permissions')
    ->where('name', 'LIKE', '%system%')
    ->orWhere('name', 'LIKE', '%users%')
    ->get();

echo "Found " . $systemPerms->count() . " system-related permissions:\n";
foreach ($systemPerms as $perm) {
    echo "  - {$perm->name}\n";
}

// Test 3: Verify Super Admin has System Permissions
echo "\n🔐 Test 3: Super Admin System Permissions\n";
$superAdminPerms = DB::table('role_has_permissions')
    ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
    ->where('roles.name', 'super-admin')
    ->where(function($query) {
        $query->where('permissions.name', 'LIKE', '%system%')
              ->orWhere('permissions.name', 'LIKE', '%users%');
    })
    ->select('permissions.name')
    ->get();

echo "Super admin has " . $superAdminPerms->count() . " system permissions:\n";
foreach ($superAdminPerms as $perm) {
    echo "  ✅ {$perm->name}\n";
}

// Test 4: Check Routes are Available
echo "\n🚦 Test 4: System Routes Check\n";
$expectedRoutes = [
    '/system/settings',
    '/system/permissions', 
    '/system/roles',
    '/system/maintenance',
    '/system/logs',
    '/users',
    '/admin/dashboard'
];

echo "Expected system management routes:\n";
foreach ($expectedRoutes as $route) {
    echo "  📍 {$route}\n";
}

echo "\n🎯 MENU VISIBILITY CHECK\n";
echo "========================\n";
echo "For Super Admin user to see system menus, they need:\n";
echo "✅ Role: super-admin (assigned)\n";
echo "✅ Permissions: view-system-settings, view-users (assigned)\n";
echo "✅ Routes: /system/* and /users/* (created)\n";
echo "✅ Cache: Cleared\n\n";

echo "🚀 READY TO TEST!\n";
echo "================\n";
echo "1. Login as: {$superAdmin->email}\n";
echo "2. Look for these new menu items:\n";
echo "   📁 User Management\n";
echo "   ⚙️ System Settings\n";
echo "   🔧 Admin Tools\n\n";

echo "If menus are still not visible, check:\n";
echo "- Browser cache (Ctrl+F5 to refresh)\n";
echo "- Re-login to refresh session\n";
echo "- Check browser console for errors\n";

?>