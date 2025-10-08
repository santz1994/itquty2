<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create roles directly using DB
\Illuminate\Support\Facades\DB::table('roles')->insert([
    [
        'name' => 'super-admin',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'admin', 
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'user',
        'guard_name' => 'web', 
        'created_at' => now(),
        'updated_at' => now()
    ],
        [
        'name' => 'management',
        'guard_name' => 'web', 
        'created_at' => now(),
        'updated_at' => now()
    ]
]);

// Assign role to first user (Super Admin User)
\Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
    'role_id' => 1, // super-admin
    'model_type' => 'App\User',
    'model_id' => 1
]);

echo "Roles created and assigned successfully!\n";