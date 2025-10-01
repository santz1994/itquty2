<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // If permissions table has guard_name, use the Permission model (Spatie style), otherwise fallback to legacy insert
      if (\Illuminate\Support\Facades\Schema::hasColumn('permissions', 'guard_name')) {
        Permission::firstOrCreate(
          [
            'name' => 'create-user',
            'guard_name' => config('auth.defaults.guard', 'web'),
          ],
          [
            'display_name' => 'Create Users',
            'description' => 'Create new users',
          ]
        );

        Permission::firstOrCreate(
          [
            'name' => 'edit-user',
            'guard_name' => config('auth.defaults.guard', 'web'),
          ],
          [
            'display_name' => 'Edit Users',
            'description' => 'Edit existing users',
          ]
        );

        Permission::firstOrCreate(
          [
            'name' => 'change-role',
            'guard_name' => config('auth.defaults.guard', 'web'),
          ],
          [
            'display_name' => 'Change Role',
            'description' => 'Change a user\'s role',
          ]
        );

        Permission::firstOrCreate(
          [
            'name' => 'create-asset',
            'guard_name' => config('auth.defaults.guard', 'web'),
          ],
          [
            'display_name' => 'Create Asset',
            'description' => 'Create new assets',
          ]
        );

        Permission::firstOrCreate(
          [
            'name' => 'edit-asset',
            'guard_name' => config('auth.defaults.guard', 'web'),
          ],
          [
            'display_name' => 'Edit Asset',
            'description' => 'Edit assets',
          ]
        );
      } else {
        // Legacy Entrust-style permissions table without guard_name
        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
          ['name' => 'create-user'],
          ['display_name' => 'Create Users', 'description' => 'Create new users', 'updated_at' => now(), 'created_at' => now()]
        );

        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
          ['name' => 'edit-user'],
          ['display_name' => 'Edit Users', 'description' => 'Edit existing users', 'updated_at' => now(), 'created_at' => now()]
        );

        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
          ['name' => 'change-role'],
          ['display_name' => 'Change Role', 'description' => 'Change a user\'s role', 'updated_at' => now(), 'created_at' => now()]
        );

        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
          ['name' => 'create-asset'],
          ['display_name' => 'Create Asset', 'description' => 'Create new assets', 'updated_at' => now(), 'created_at' => now()]
        );

        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
          ['name' => 'edit-asset'],
          ['display_name' => 'Edit Asset', 'description' => 'Edit assets', 'updated_at' => now(), 'created_at' => now()]
        );
      }
    }
}
