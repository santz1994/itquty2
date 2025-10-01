<?php
// Database Inspection Script

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Database Inspection Script ===\n\n";

// Check if tables exist
echo "Checking database tables...\n";
$tables = ['users', 'roles', 'permissions', 'role_user', 'model_has_roles', 'role_has_permissions', 'model_has_permissions'];
foreach ($tables as $table) {
    $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
    echo "- Table '{$table}': " . ($exists ? "EXISTS" : "DOES NOT EXIST") . "\n";
}

echo "\n=== Users Table ===\n";
try {
    $users = \App\User::all();
    echo "Found " . $users->count() . " users:\n";
    foreach ($users as $user) {
        echo "- User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
        
        // Check roles using Spatie's HasRoles trait
        echo "  Roles: ";
        if (method_exists($user, 'getRoleNames')) {
            echo implode(', ', $user->getRoleNames()->toArray()) . "\n";
        } else {
            echo "Unable to determine roles (method not available)\n";
        }
        
        // Check if user has specific roles
        if (method_exists($user, 'hasRole')) {
            echo "  Has 'super-admin' role: " . ($user->hasRole('super-admin') ? "YES" : "NO") . "\n";
            echo "  Has 'admin' role: " . ($user->hasRole('admin') ? "YES" : "NO") . "\n";
            echo "  Has 'user' role: " . ($user->hasRole('user') ? "YES" : "NO") . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error getting users: " . $e->getMessage() . "\n";
}

echo "\n=== Roles Table ===\n";
try {
    if (class_exists('\App\Role')) {
        $roles = \App\Role::all();
        echo "Found " . $roles->count() . " roles:\n";
        foreach ($roles as $role) {
            echo "- Role ID: {$role->id}, Name: {$role->name}, Display Name: " . 
                ($role->display_name ?? 'N/A') . "\n";
            
            // Get permissions for this role
            if (method_exists($role, 'permissions')) {
                $permissions = $role->permissions()->get();
                echo "  Permissions: " . implode(', ', $permissions->pluck('name')->toArray()) . "\n";
            } elseif (method_exists($role, 'getPermissionNames')) {
                echo "  Permissions: " . implode(', ', $role->getPermissionNames()->toArray()) . "\n";
            } else {
                echo "  Unable to determine permissions (method not available)\n";
            }
        }
    } else {
        echo "App\\Role class not found. Trying direct DB access.\n";
        $roles = DB::table('roles')->get();
        echo "Found " . $roles->count() . " roles:\n";
        foreach ($roles as $role) {
            echo "- Role ID: {$role->id}, Name: {$role->name}, Display Name: " . 
                ($role->display_name ?? 'N/A') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error getting roles: " . $e->getMessage() . "\n";
}

echo "\n=== Permissions Table ===\n";
try {
    if (class_exists('\App\Permission')) {
        $permissions = \App\Permission::all();
        echo "Found " . $permissions->count() . " permissions:\n";
        foreach ($permissions as $permission) {
            echo "- Permission ID: {$permission->id}, Name: {$permission->name}, Display Name: " . 
                ($permission->display_name ?? 'N/A') . "\n";
        }
    } else {
        echo "App\\Permission class not found. Trying direct DB access.\n";
        $permissions = DB::table('permissions')->get();
        echo "Found " . $permissions->count() . " permissions:\n";
        foreach ($permissions as $permission) {
            echo "- Permission ID: {$permission->id}, Name: {$permission->name}, Display Name: " . 
                ($permission->display_name ?? 'N/A') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error getting permissions: " . $e->getMessage() . "\n";
}

// Check for model_has_roles table (Spatie package)
echo "\n=== Model Has Roles Table ===\n";
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('model_has_roles')) {
        $modelRoles = DB::table('model_has_roles')->get();
        echo "Found " . $modelRoles->count() . " model role assignments:\n";
        foreach ($modelRoles as $assignment) {
            $roleName = DB::table('roles')->where('id', $assignment->role_id)->value('name') ?? 'Unknown';
            $userName = ($assignment->model_type == 'App\\User') ? 
                DB::table('users')->where('id', $assignment->model_id)->value('name') ?? 'Unknown' : 
                'Unknown';
            echo "- Role '{$roleName}' assigned to {$assignment->model_type} ID: {$assignment->model_id} ({$userName})\n";
        }
    } else {
        echo "Table 'model_has_roles' does not exist\n";
    }
} catch (\Exception $e) {
    echo "Error checking model_has_roles: " . $e->getMessage() . "\n";
}

// Check for role_has_permissions table
echo "\n=== Role Has Permissions Table ===\n";
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('role_has_permissions')) {
        $rolePermissions = DB::table('role_has_permissions')->get();
        echo "Found " . $rolePermissions->count() . " role permission assignments:\n";
        foreach ($rolePermissions as $assignment) {
            $roleName = DB::table('roles')->where('id', $assignment->role_id)->value('name') ?? 'Unknown';
            $permissionName = DB::table('permissions')->where('id', $assignment->permission_id)->value('name') ?? 'Unknown';
            echo "- Permission '{$permissionName}' assigned to role '{$roleName}'\n";
        }
    } else {
        echo "Table 'role_has_permissions' does not exist\n";
    }
} catch (\Exception $e) {
    echo "Error checking role_has_permissions: " . $e->getMessage() . "\n";
}

echo "\n=== Middleware Check ===\n";
$kernel = app(\Illuminate\Contracts\Http\Kernel::class);
$middlewareGroups = $kernel->getMiddlewareGroups();
$routeMiddleware = $kernel->getRouteMiddleware();

echo "Registered route middleware:\n";
foreach ($routeMiddleware as $name => $class) {
    echo "- {$name}: {$class}\n";
}

echo "\nRegistered middleware groups:\n";
foreach ($middlewareGroups as $name => $middleware) {
    echo "- {$name}: " . implode(', ', $middleware) . "\n";
}

echo "\n=== End of Database Inspection ===\n";