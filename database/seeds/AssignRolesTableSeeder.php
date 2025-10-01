<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class AssignRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Roles
    $superAdmin = Role::where('name', '=', 'super-admin')->first();

    $superAdminUser = User::where('name', '=', 'Super Admin User')->first();

    // If roles table has guard_name column, use Spatie API. Otherwise insert into model_has_roles directly.
    if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
      if ($superAdminUser && $superAdmin) {
        $superAdminUser->assignRole($superAdmin);
      }
    } else {
      if ($superAdminUser && $superAdmin) {
        \Illuminate\Support\Facades\DB::table('model_has_roles')->updateOrInsert([
          'role_id' => $superAdmin->id,
          'model_type' => 'App\\User',
          'model_id' => $superAdminUser->id,
        ], []);
      }
    }

    // Also ensure Admin User and regular User have roles for tests
    $adminRole = Role::where('name', '=', 'admin')->first();
    $userRole = Role::where('name', '=', 'user')->first();

    $adminUser = User::where('name', '=', 'Admin User')->first();
    $regularUser = User::where('name', '=', 'User User')->first();

    if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
      if ($adminUser && $adminRole) { $adminUser->assignRole($adminRole); }
      if ($regularUser && $userRole) { $regularUser->assignRole($userRole); }
    } else {
      if ($adminUser && $adminRole) {
        \Illuminate\Support\Facades\DB::table('model_has_roles')->updateOrInsert([
          'role_id' => $adminRole->id,
          'model_type' => 'App\\User',
          'model_id' => $adminUser->id,
        ], []);
      }
      if ($regularUser && $userRole) {
        \Illuminate\Support\Facades\DB::table('model_has_roles')->updateOrInsert([
          'role_id' => $userRole->id,
          'model_type' => 'App\\User',
          'model_id' => $regularUser->id,
        ], []);
      }
    }
    }
}
