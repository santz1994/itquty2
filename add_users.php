<?php
// Add new users: daniel and idol
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== Adding New Super Admin Users ===\n\n";

$newUsers = [
    [
        'name' => 'Daniel',
        'email' => 'daniel@quty.co.id',
        'password' => '123456'
    ],
    [
        'name' => 'Idol',
        'email' => 'idol@quty.co.id',
        'password' => '123456'
    ]
];

// Get super-admin role ID
$superAdminRole = DB::table('roles')->where('name', 'super-admin')->first();

if (!$superAdminRole) {
    echo "❌ Super-admin role not found! Run migrations first.\n";
    exit(1);
}

foreach ($newUsers as $userData) {
    // Check if user already exists
    $existingUser = DB::table('users')->where('email', $userData['email'])->first();
    
    if ($existingUser) {
        echo "⚠️  User already exists: {$userData['email']}\n";
        echo "   Updating password...\n";
        
        DB::table('users')
            ->where('email', $userData['email'])
            ->update(['password' => Hash::make($userData['password'])]);
        
        $userId = $existingUser->id;
    } else {
        echo "✅ Creating new user: {$userData['email']}\n";
        
        $userId = DB::table('users')->insertGetId([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'api_token' => Str::random(60),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    // Check if role is already assigned
    $hasRole = DB::table('model_has_roles')
        ->where('model_id', $userId)
        ->where('model_type', 'App\\User')
        ->where('role_id', $superAdminRole->id)
        ->exists();
    
    if (!$hasRole) {
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_type' => 'App\\User',
            'model_id' => $userId
        ]);
        echo "   ✅ Assigned super-admin role\n";
    } else {
        echo "   ℹ️  Already has super-admin role\n";
    }
    
    // Verify password works
    $user = DB::table('users')->where('id', $userId)->first();
    $passwordCheck = Hash::check($userData['password'], $user->password);
    
    echo "   Email: {$userData['email']}\n";
    echo "   Password: {$userData['password']}\n";
    echo "   Verification: " . ($passwordCheck ? "✅ WORKS" : "❌ FAILED") . "\n";
    echo "\n";
}

echo "\n=== All Users Summary ===\n\n";

$allUsers = DB::table('users')
    ->select('users.id', 'users.name', 'users.email')
    ->get();

foreach ($allUsers as $user) {
    $roles = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('model_has_roles.model_id', $user->id)
        ->where('model_has_roles.model_type', 'App\\User')
        ->pluck('roles.name')
        ->toArray();
    
    echo "{$user->email}\n";
    echo "  Name: {$user->name}\n";
    echo "  Role: " . implode(', ', $roles) . "\n\n";
}

echo "=== Login Credentials ===\n\n";
echo "✅ daniel@quty.co.id / 123456 (Super Admin)\n";
echo "✅ idol@quty.co.id / 123456 (Super Admin)\n";
echo "✅ superadmin@quty.co.id / superadmin (Super Admin)\n";
echo "✅ admin@quty.co.id / admin (Admin)\n";
echo "✅ user@quty.co.id / user (User)\n";
