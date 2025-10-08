<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 FIXING RBAC SYSTEM\n";
echo "=====================\n\n";

// Clear existing assignments
echo "🧹 Cleaning existing assignments...\n";
DB::table('model_has_roles')->delete();
DB::table('role_has_permissions')->delete();

// Create comprehensive permissions that match sidebar @can directives
echo "🔑 Creating comprehensive permissions...\n";

$permissions = [
    // Assets permissions
    'view-assets',
    'create-assets', 
    'edit-assets',
    'delete-assets',
    'export-assets',
    'import-assets',
    
    // Tickets permissions
    'view-tickets',
    'create-tickets',
    'edit-tickets',
    'delete-tickets',
    'assign-tickets',
    'export-tickets',
    
    // Daily Activities permissions
    'view-daily-activities',
    'create-daily-activities',
    'edit-daily-activities',
    'delete-daily-activities',
    
    // KPI and Reports permissions
    'view-kpi-dashboard',
    'view-reports',
    'view-admin-performance',
    
    // Models permissions (admin/super-admin only)
    'view-models',
    'create-models',
    'edit-models',
    'delete-models',
    
    // Users permissions
    'view-users',
    'create-users',
    'edit-users',
    'delete-users',
    
    // System permissions
    'view-system-settings',
    'edit-system-settings'
];

// Insert permissions
foreach ($permissions as $permission) {
    DB::table('permissions')->updateOrInsert(
        ['name' => $permission, 'guard_name' => 'web'],
        ['created_at' => now(), 'updated_at' => now()]
    );
}

echo "✅ Created " . count($permissions) . " permissions\n\n";

// Assign permissions to roles
echo "🔐 Assigning permissions to roles...\n";

// Get role IDs
$roles = DB::table('roles')->pluck('id', 'name');
$permissionIds = DB::table('permissions')->pluck('id', 'name');

// Super Admin - ALL permissions
$superAdminPerms = array_keys($permissionIds->toArray());
foreach ($superAdminPerms as $perm) {
    DB::table('role_has_permissions')->insert([
        'permission_id' => $permissionIds[$perm],
        'role_id' => $roles['super-admin']
    ]);
}
echo "✅ Super Admin: " . count($superAdminPerms) . " permissions\n";

// Admin - Most permissions except system settings
$adminPerms = [
    'view-assets', 'create-assets', 'edit-assets', 'export-assets', 'import-assets',
    'view-tickets', 'create-tickets', 'edit-tickets', 'assign-tickets', 'export-tickets',
    'view-daily-activities', 'create-daily-activities', 'edit-daily-activities', 
    'view-kpi-dashboard', 'view-reports',
    'view-models', 'create-models', 'edit-models',
    'view-users', 'create-users', 'edit-users'
];
foreach ($adminPerms as $perm) {
    DB::table('role_has_permissions')->insert([
        'permission_id' => $permissionIds[$perm],
        'role_id' => $roles['admin']
    ]);
}
echo "✅ Admin: " . count($adminPerms) . " permissions\n";

// Management - View permissions + reports
$managementPerms = [
    'view-assets', 'export-assets',
    'view-tickets', 'create-tickets', 'export-tickets',
    'view-daily-activities',
    'view-kpi-dashboard', 'view-reports', 'view-admin-performance',
    'view-models',
    'view-users'
];
foreach ($managementPerms as $perm) {
    DB::table('role_has_permissions')->insert([
        'permission_id' => $permissionIds[$perm],
        'role_id' => $roles['management']
    ]);
}
echo "✅ Management: " . count($managementPerms) . " permissions\n";

// User - Basic permissions
$userPerms = [
    'view-assets',
    'view-tickets', 'create-tickets',
    'view-daily-activities', 'create-daily-activities'
];
foreach ($userPerms as $perm) {
    DB::table('role_has_permissions')->insert([
        'permission_id' => $permissionIds[$perm],
        'role_id' => $roles['user']
    ]);
}
echo "✅ User: " . count($userPerms) . " permissions\n\n";

// Assign roles to users
echo "👥 Assigning roles to users...\n";

// Get first user and assign super-admin role
$firstUser = DB::table('users')->first();
if ($firstUser) {
    DB::table('model_has_roles')->insert([
        'role_id' => $roles['super-admin'],  // Changed from management to super-admin
        'model_type' => 'App\User',
        'model_id' => $firstUser->id
    ]);
    echo "✅ User '{$firstUser->name}' assigned 'super-admin' role\n";
}

// Check if there are more users and assign appropriate roles
$allUsers = DB::table('users')->get();
foreach ($allUsers as $index => $user) {
    if ($index === 0) continue; // Skip first user (already assigned super-admin)
    
    // Assign roles based on user patterns or default to 'user'
    $assignRole = 'user';
    if (str_contains(strtolower($user->email), 'admin')) {
        $assignRole = 'admin';
    } elseif (str_contains(strtolower($user->email), 'management')) {
        $assignRole = 'management';
    }
    
    DB::table('model_has_roles')->insert([
        'role_id' => $roles[$assignRole],
        'model_type' => 'App\User',
        'model_id' => $user->id
    ]);
    echo "✅ User '{$user->name}' assigned '{$assignRole}' role\n";
}

echo "\n🎉 RBAC SYSTEM FIXED!\n";
echo "====================\n";
echo "✅ Roles: " . count($roles) . " (superadmin, admin, user, management)\n";
echo "✅ Permissions: " . count($permissions) . " comprehensive permissions\n";
echo "✅ Role-Permission assignments: Complete\n";
echo "✅ User-Role assignments: Updated\n";

echo "\n💾 Next steps:\n";
echo "1. Clear application cache\n";
echo "2. Test menu visibility\n";
echo "3. Verify user permissions\n";

?>