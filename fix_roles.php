<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "🔧 Fixing Role Names\n";
echo "==================\n\n";

// Check current roles
echo "Current roles:\n";
$roles = Role::all();
foreach($roles as $role) {
    echo "- {$role->name} (ID: {$role->id})\n";
}

echo "\n🔄 Updating role names...\n";

// Update role names to lowercase with hyphens
$roleUpdates = [
    'Super Admin' => 'super-admin',
    'Admin' => 'admin', 
    'Management' => 'management',
    'User' => 'user'
];

foreach($roleUpdates as $oldName => $newName) {
    $role = Role::where('name', $oldName)->first();
    if($role) {
        echo "✅ Updating '{$oldName}' to '{$newName}'\n";
        $role->name = $newName;
        $role->save();
    } else {
        echo "ℹ️  Role '{$oldName}' not found, creating '{$newName}'\n";
        Role::firstOrCreate(['name' => $newName]);
    }
}

echo "\n✅ Updated roles:\n";
$roles = Role::all();
foreach($roles as $role) {
    echo "- {$role->name} (ID: {$role->id})\n";
}

echo "\n🎉 Role names fixed successfully!\n";