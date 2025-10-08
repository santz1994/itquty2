<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🛡️ RBAC SYSTEM ANALYSIS\n";
echo "========================\n\n";

// Check Roles
echo "📋 CURRENT ROLES:\n";
echo "-----------------\n";
$roles = DB::table('roles')->get();
if ($roles->count() > 0) {
    foreach ($roles as $role) {
        echo "✅ ID: {$role->id} | Name: '{$role->name}' | Guard: {$role->guard_name}\n";
    }
} else {
    echo "❌ No roles found!\n";
}

echo "\n🔑 CURRENT PERMISSIONS:\n";
echo "-----------------------\n";
$permissions = DB::table('permissions')->get();
if ($permissions->count() > 0) {
    $permGroups = [];
    foreach ($permissions as $perm) {
        $group = explode('-', $perm->name)[1] ?? 'other';
        $permGroups[$group][] = $perm->name;
    }
    
    foreach ($permGroups as $group => $perms) {
        echo "📁 {$group}: " . implode(', ', $perms) . "\n";
    }
} else {
    echo "❌ No permissions found!\n";
}

echo "\n👥 USER ROLE ASSIGNMENTS:\n";
echo "-------------------------\n";
$userRoles = DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name')
    ->get();

if ($userRoles->count() > 0) {
    foreach ($userRoles as $ur) {
        echo "👤 User {$ur->id}: {$ur->name} ({$ur->email}) -> Role: {$ur->role_name}\n";
    }
} else {
    echo "❌ No user role assignments found!\n";
}

echo "\n🎯 ROLE PERMISSION ASSIGNMENTS:\n";
echo "-------------------------------\n";
$rolePermissions = DB::table('role_has_permissions')
    ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
    ->select('roles.name as role_name', 'permissions.name as permission')
    ->get();

if ($rolePermissions->count() > 0) {
    $rolePermsGrouped = [];
    foreach ($rolePermissions as $rp) {
        $rolePermsGrouped[$rp->role_name][] = $rp->permission;
    }
    
    foreach ($rolePermsGrouped as $roleName => $perms) {
        echo "🔐 {$roleName}: " . count($perms) . " permissions\n";
        echo "   " . implode(', ', array_slice($perms, 0, 5)) . (count($perms) > 5 ? '...' : '') . "\n";
    }
} else {
    echo "❌ No role-permission assignments found!\n";
}

echo "\n🚨 DIAGNOSIS:\n";
echo "=============\n";

$issues = [];

if ($roles->count() === 0) {
    $issues[] = "No roles exist in database";
}

if ($permissions->count() === 0) {
    $issues[] = "No permissions exist in database";
}

if ($userRoles->count() === 0) {
    $issues[] = "No users have assigned roles";
}

if ($rolePermissions->count() === 0) {
    $issues[] = "No permissions assigned to roles";
}

if (empty($issues)) {
    echo "✅ RBAC system appears to be properly configured\n";
} else {
    foreach ($issues as $issue) {
        echo "❌ ISSUE: {$issue}\n";
    }
}

echo "\n💡 RECOMMENDED ACTIONS:\n";
echo "=======================\n";
echo "1. Run database seeders to create permissions\n";
echo "2. Assign permissions to roles\n";
echo "3. Assign roles to users\n";
echo "4. Clear cache after changes\n";

?>