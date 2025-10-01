<?php
namespace Database\Seeders;

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    // If roles table contains guard_name column, use the Role model (Spatie) which expects it.
    if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
      $attrs = [
        'display_name' => 'Super Administrator',
        'description' => 'Permission to everything.',
        'guard_name' => config('auth.defaults.guard', 'web'),
      ];
      $superAdmin = Role::firstOrCreate(['name' => 'super-admin'], $attrs);
    } else {
      // Legacy Entrust-style roles table - insert/update directly without guard_name
      \Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(
        ['name' => 'super-admin'],
        ['display_name' => 'Super Administrator', 'description' => 'Permission to everything.', 'updated_at' => now(), 'created_at' => now()]
      );
    }

    if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
      $attrs = [
        'display_name' => 'Administrator',
        'description' => 'Permission to view assets, but not edit them. Plus, same permissions as User role.',
        'guard_name' => config('auth.defaults.guard', 'web'),
      ];
      $admin = Role::firstOrCreate(['name' => 'admin'], $attrs);
    } else {
      \Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(
        ['name' => 'admin'],
        ['display_name' => 'Administrator', 'description' => 'Permission to view assets, but not edit them. Plus, same permissions as User role.', 'updated_at' => now(), 'created_at' => now()]
      );
    }

    if (\Illuminate\Support\Facades\Schema::hasColumn('roles', 'guard_name')) {
      $attrs = [
        'display_name' => 'User',
        'description' => 'Can create tickets and view knowledgebase articles.',
        'guard_name' => config('auth.defaults.guard', 'web'),
      ];
      $user = Role::firstOrCreate(['name' => 'user'], $attrs);
    } else {
      \Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(
        ['name' => 'user'],
        ['display_name' => 'User', 'description' => 'Can create tickets and view knowledgebase articles.', 'updated_at' => now(), 'created_at' => now()]
      );
    }
    }
}
