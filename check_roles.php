<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$roles = \Spatie\Permission\Models\Role::all();

echo "Existing roles:\n";
foreach ($roles as $role) {
    echo "- {$role->name}\n";
}

$users = \App\User::with('roles')->get();
echo "\nUsers and their roles:\n";
foreach ($users as $user) {
    echo "- {$user->name}: " . $user->roles->pluck('name')->implode(', ') . "\n";
}