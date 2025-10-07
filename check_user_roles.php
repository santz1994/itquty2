<?php
require 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING USER ROLES IN DATABASE ===\n\n";

// Get all users and their roles
$users = DB::table('users')
    ->select('id', 'name', 'email')
    ->get();

echo "All Users in System:\n";
echo str_repeat('-', 80) . "\n";

foreach ($users as $user) {
    echo "\nUser: {$user->name} (ID: {$user->id})\n";
    echo "Email: {$user->email}\n";
    
    // Get roles from model_has_roles
    $roles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', 'App\\User')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->select('roles.id', 'roles.name', 'roles.display_name')
        ->get();
    
    if ($roles->isEmpty()) {
        echo "Roles: ❌ NO ROLES ASSIGNED!\n";
    } else {
        echo "Roles:\n";
        foreach ($roles as $role) {
            echo "  - {$role->name} (ID: {$role->id}) - {$role->display_name}\n";
        }
    }
    
    echo str_repeat('-', 80) . "\n";
}

// Check which roles exist
echo "\n\nAll Roles in System:\n";
echo str_repeat('-', 80) . "\n";
$allRoles = DB::table('roles')->select('id', 'name', 'display_name')->get();
foreach ($allRoles as $role) {
    echo "- {$role->name} (ID: {$role->id}) - {$role->display_name}\n";
}

echo "\n\n=== ISSUE DIAGNOSIS ===\n";
echo "Routes require: 'admin' OR 'super-admin' role\n";
echo "Check if any user above has these exact role names!\n";
echo "\nIf role names don't match exactly, you'll get 403 errors.\n";