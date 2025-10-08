<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Spatie\Permission\Models\Role;

echo "🔧 Testing Role Functionality\n";
echo "============================\n\n";

// Test 1: Check if roles exist
echo "📋 Test 1: Checking available roles:\n";
$roles = Role::all();
foreach($roles as $role) {
    echo "  ✅ Role: {$role->name} (ID: {$role->id})\n";
}

// Test 2: Check super admin user
echo "\n👤 Test 2: Checking super admin user:\n";
$superAdminUsers = User::role('super-admin')->get();
if($superAdminUsers->count() > 0) {
    foreach($superAdminUsers as $user) {
        echo "  ✅ Super Admin: {$user->name} ({$user->email})\n";
        echo "     - Has 'super-admin' role: " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "\n";
        echo "     - Can manage users: " . ($user->canManageUsers() ? 'YES' : 'NO') . "\n";
        echo "     - Can view management dashboard: " . ($user->canViewManagementDashboard() ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "  ❌ No super admin users found!\n";
}

// Test 3: Check admin users
echo "\n👤 Test 3: Checking admin users:\n";
$adminUsers = User::role('admin')->get();
if($adminUsers->count() > 0) {
    foreach($adminUsers as $user) {
        echo "  ✅ Admin: {$user->name} ({$user->email})\n";
        echo "     - Has 'admin' role: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "  ❌ No admin users found!\n";
}

// Test 4: Test role-based queries
echo "\n📊 Test 4: Role-based statistics:\n";
$stats = User::getUserStatistics();
echo "  - Total users: {$stats['total']}\n";
echo "  - Active users: {$stats['active']}\n";
echo "  - Admin users: {$stats['admins']}\n";
echo "  - Online users: {$stats['online']}\n";

echo "\n✅ Role functionality test completed!\n";