<?php
require 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ASSIGNING SUPER-ADMIN ROLE TO USER ID 3 ===\n\n";

// Get User ID 3
$user = DB::table('users')->where('id', 3)->first();

if (!$user) {
    echo "❌ User ID 3 not found!\n";
    exit(1);
}

echo "User: {$user->name}\n";
echo "Email: {$user->email}\n\n";

// Get super-admin role
$role = DB::table('roles')->where('name', 'super-admin')->first();

if (!$role) {
    echo "❌ Super-admin role not found!\n";
    exit(1);
}

// Check if already assigned
$existing = DB::table('model_has_roles')
    ->where('model_id', $user->id)
    ->where('model_type', 'App\\User')
    ->where('role_id', $role->id)
    ->first();

if ($existing) {
    echo "✅ User already has super-admin role\n";
} else {
    // Assign role
    DB::table('model_has_roles')->insert([
        'role_id' => $role->id,
        'model_type' => 'App\\User',
        'model_id' => $user->id
    ]);
    
    echo "✅ Super-admin role assigned to {$user->name}!\n";
}

echo "\nDone! Now logout and login again to refresh your session.\n";