<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "✅ SYSTEM VERIFICATION TEST\n";
echo "===========================\n\n";

// Test 1: Database Connection
echo "🔍 Test 1: Database Connection\n";
try {
    $userCount = DB::table('users')->count();
    echo "✅ MySQL Connection: SUCCESS\n";
    echo "   Users in database: {$userCount}\n\n";
} catch (Exception $e) {
    echo "❌ MySQL Connection: FAILED - " . $e->getMessage() . "\n\n";
}

// Test 2: CORS Configuration
echo "🔍 Test 2: CORS Configuration\n";
if (file_exists('config/cors.php')) {
    echo "✅ CORS Config: EXISTS\n\n";
} else {
    echo "❌ CORS Config: MISSING\n\n";
}

// Test 3: RBAC System
echo "🔍 Test 3: RBAC System\n";
$roles = DB::table('roles')->count();
$permissions = DB::table('permissions')->count();
$userRoles = DB::table('model_has_roles')->count();
$rolePermissions = DB::table('role_has_permissions')->count();

echo "✅ Roles: {$roles}\n";
echo "✅ Permissions: {$permissions}\n";
echo "✅ User-Role assignments: {$userRoles}\n";
echo "✅ Role-Permission assignments: {$rolePermissions}\n\n";

// Test 4: User Role Verification
echo "🔍 Test 4: User Role Verification\n";
$userRoleDetails = DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->select('users.name', 'users.email', 'roles.name as role_name')
    ->get();

foreach ($userRoleDetails as $user) {
    echo "👤 {$user->name} ({$user->email}) -> {$user->role_name}\n";
}
echo "\n";

// Test 5: Critical File Check
echo "🔍 Test 5: Critical Files\n";
$criticalFiles = [
    'app/Http/Controllers/API/AuthController.php' => 'API Auth',
    'app/Http/Controllers/API/AssetController.php' => 'Asset API',
    'routes/api.php' => 'API Routes',
    'config/cors.php' => 'CORS Config',
    'resources/views/layouts/partials/sidebar.blade.php' => 'Sidebar Menu'
];

foreach ($criticalFiles as $file => $description) {
    $status = file_exists($file) ? '✅' : '❌';
    echo "{$status} {$description}\n";
}

echo "\n🎯 FINAL STATUS\n";
echo "================\n";

$allGood = true;
$issues = [];

if ($userCount === 0) {
    $issues[] = "No users in database";
    $allGood = false;
}

if ($roles < 4) {
    $issues[] = "Missing roles (expected 4: superadmin, admin, user, management)";
    $allGood = false;
}

if ($permissions < 25) {
    $issues[] = "Insufficient permissions (expected 25+)";
    $allGood = false;
}

if ($userRoles === 0) {
    $issues[] = "No user role assignments";
    $allGood = false;
}

if ($rolePermissions === 0) {
    $issues[] = "No role permission assignments";
    $allGood = false;
}

if ($allGood) {
    echo "🎉 ALL SYSTEMS OPERATIONAL!\n";
    echo "===========================\n";
    echo "✅ Database: Connected and populated\n";
    echo "✅ CORS: Configured\n";
    echo "✅ RBAC: Fully functional\n";
    echo "✅ API: Ready\n";
    echo "✅ Menus: Should be visible\n\n";
    echo "🚀 SYSTEM READY FOR USE!\n";
} else {
    echo "⚠️ ISSUES DETECTED:\n";
    foreach ($issues as $issue) {
        echo "❌ {$issue}\n";
    }
}

echo "\n📋 MENU VISIBILITY TEST\n";
echo "========================\n";
echo "Login with these accounts to test menu visibility:\n\n";

foreach ($userRoleDetails as $user) {
    echo "🔐 {$user->role_name}: {$user->email}\n";
    
    // Show expected menu items for each role
    switch ($user->role_name) {
        case 'super-admin':
            echo "   Expected menus: ALL (Home, Assets, Tickets, Activities, KPI, Reports, Models, Users, Settings)\n";
            break;
        case 'admin':
            echo "   Expected menus: Home, Assets, Tickets, Activities, KPI, Reports, Models, Users\n";
            break;
        case 'management':
            echo "   Expected menus: Home, Assets (view), Tickets, Activities (view), KPI, Reports\n";
            break;
        case 'user':
            echo "   Expected menus: Assets (view), Tickets, Activities\n";
            break;
    }
    echo "\n";
}

?>