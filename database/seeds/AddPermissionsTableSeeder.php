<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class AddPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Permissions
      $createUser = Permission::where('name', '=', 'create-user')->first();
      $editUser = Permission::where('name', '=', 'edit-user')->first();
      $changeRole = Permission::where('name', '=', 'change-role')->first();
      $createAsset = Permission::where('name', '=', 'create-asset')->first();
      $editAsset = Permission::where('name', '=', 'edit-asset')->first();

      // Super Administrator
      $superAdmin = Role::where('name', '=', 'super-admin')->first();

      // If roles has guard_name, assume Spatie package and use givePermissionTo
      if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
        if ($superAdmin) {
          $superAdmin->givePermissionTo([$createUser, $editUser, $changeRole, $createAsset, $editAsset]);
        }

        // Administrator
        $admin = Role::where('name', '=', 'admin')->first();
        if ($admin) {
          $admin->givePermissionTo([$createAsset, $editAsset]);
        }
      } else {
        // Legacy Entrust-like pivot table role_has_permissions
        if ($superAdmin) {
          foreach (array($createUser, $editUser, $changeRole, $createAsset, $editAsset) as $perm) {
            if ($perm) {
              \Illuminate\Support\Facades\DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $perm->id,
                'role_id' => $superAdmin->id,
              ], []);
            }
          }
        }

        $admin = Role::where('name', '=', 'admin')->first();
        if ($admin) {
          foreach (array($createAsset, $editAsset) as $perm) {
            if ($perm) {
              \Illuminate\Support\Facades\DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $perm->id,
                'role_id' => $admin->id,
              ], []);
            }
          }
        }
      }
    }
}
