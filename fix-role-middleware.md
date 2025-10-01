# Fix Role Middleware Issue

This document provides instructions on how to fix the role middleware issue in the application.

## Problem

The application is using two role systems:
1. Legacy Entrust system (role_user table)
2. Spatie Laravel Permission (model_has_roles table)

The role middleware may be failing because user roles are not properly assigned in both systems.

## Solution

Run the following steps in Laravel Tinker to fix the role assignments:

1. Open a terminal in the project root directory
2. Run Tinker:
   ```
   php artisan tinker
   ```

3. Execute the following commands in Tinker:

```php
// Get all test users
$users = \App\User::whereIn('name', ['Super Admin User', 'Admin User', 'User User'])->get();
echo "Found " . $users->count() . " test users\n";

// Get all roles
$roles = \Spatie\Permission\Models\Role::all();
echo "Found " . $roles->count() . " roles\n";

// Fix roles for each user
foreach ($users as $user) {
    echo "\nProcessing user: " . $user->name . "\n";
    
    // Determine which role to assign
    $roleName = null;
    if (str_contains($user->name, 'Super Admin')) {
        $roleName = 'super-admin';
    } elseif (str_contains($user->name, 'Admin')) {
        $roleName = 'admin';
    } else {
        $roleName = 'user';
    }
    
    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
    if (!$role) {
        echo "- ERROR: Role not found: " . $roleName . "\n";
        continue;
    }
    
    echo "- Assigning role: " . $role->name . " to user: " . $user->name . "\n";
    
    // 1. Fix legacy role_user table
    if (Schema::hasTable('role_user')) {
        $existingRoleUser = DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();
            
        if (!$existingRoleUser) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $role->id
            ]);
            echo "- Added entry to role_user table\n";
        } else {
            echo "- Entry already exists in role_user table\n";
        }
    }
    
    // 2. Fix Spatie model_has_roles table using the assignRole method
    try {
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role);
            echo "- Added role using Spatie: " . $role->name . "\n";
        } else {
            echo "- User already has Spatie role: " . $role->name . "\n";
        }
    } catch (\Exception $e) {
        echo "- ERROR assigning Spatie role: " . $e->getMessage() . "\n";
        
        // Fallback to direct database insertion if method fails
        if (Schema::hasTable('model_has_roles')) {
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => get_class($user),
                'model_id' => $user->id
            ]);
            echo "- Added entry to model_has_roles table (fallback method)\n";
        }
    }
}

// Ensure guard_name is set correctly for all roles
if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'guard_name')) {
    foreach ($roles as $role) {
        if (empty($role->guard_name)) {
            $role->guard_name = 'web';
            $role->save();
            echo "- Updated role '{$role->name}' with guard_name 'web'\n";
        }
    }
}

echo "\nRole Assignment Fix Completed!\n";
```

4. Exit Tinker by typing `exit` and pressing Enter

## Verification

After running the fix, you can verify that the roles are properly assigned:

1. Run Tinker again:
   ```
   php artisan tinker
   ```

2. Check user roles:
   ```php
   $user = \App\User::where('name', 'Super Admin User')->first();
   echo $user->name . " has roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";

   $user = \App\User::where('name', 'Admin User')->first();
   echo $user->name . " has roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";

   $user = \App\User::where('name', 'User User')->first();
   echo $user->name . " has roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
   ```

3. Check role middleware:
   ```php
   // Check that Spatie's role middleware is registered
   $kernel = app(\App\Http\Kernel::class);
   $middleware = $kernel->getRouteMiddleware();
   echo "Role middleware: " . ($middleware['role'] ?? 'Not found') . "\n";
   ```

4. Run the tests to verify that role middleware is working:
   ```
   php artisan test --filter=AdminPageTest
   ```

## Additional Notes

- This fix ensures that roles are correctly assigned in both the legacy `role_user` table and the Spatie `model_has_roles` table.
- The `guard_name` for roles is set to 'web', which is the default guard in Laravel.
- If tests still fail after running this fix, check the test code to ensure it correctly tests for authorization failures.