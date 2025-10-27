<?php
require 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Support\Facades\Auth;
use App\User;

// Set up basic Laravel environment
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

try {
    // Get first user
    $user = User::first();
    
    if ($user) {
        echo "User: {$user->name} (ID: {$user->id})\n";
        echo "Email: {$user->email}\n";
        
        $roles = $user->getRoleNames();
        echo "Roles: " . implode(', ', $roles->toArray()) . "\n";
        
        echo "\nRole Details:\n";
        foreach ($user->roles as $role) {
            echo "- {$role->name}\n";
        }
    } else {
        echo "No user found\n";
    }
    
    // List all roles in database
    echo "\n\nAll Roles in Database:\n";
    $allRoles = \Spatie\Permission\Models\Role::all();
    foreach ($allRoles as $role) {
        echo "- {$role->name}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
