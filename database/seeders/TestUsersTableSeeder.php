<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $now = Carbon::now();

        // Clear users table (delete, not truncate, to avoid FK errors)
        DB::table('users')->delete();

        // Insert users and fetch IDs
        $superAdminId = DB::table('users')->insertGetId([
          'name' => 'Super Admin User',
          'email' => 'superadmin@quty.co.id',
          'password' => bcrypt('superadmin'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $adminId = DB::table('users')->insertGetId([
          'name' => 'Admin User',
          'email' => 'adminuser@quty.co.id',
          'password' => bcrypt('adminuser'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $userId = DB::table('users')->insertGetId([
          'name' => 'User User',
          'email' => 'useruser@quty.co.id',
          'password' => bcrypt('useruser'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        // Assign roles using Spatie model_has_roles
        $superAdminRole = DB::table('roles')->where('name', 'super-admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $userRole = DB::table('roles')->where('name', 'user')->first();

        if ($superAdminRole) {
          DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_type' => 'App\\User',
            'model_id' => $superAdminId
          ]);
        }
        if ($adminRole) {
          DB::table('model_has_roles')->insert([
            'role_id' => $adminRole->id,
            'model_type' => 'App\\User',
            'model_id' => $adminId
          ]);
        }
        if ($userRole) {
          DB::table('model_has_roles')->insert([
            'role_id' => $userRole->id,
            'model_type' => 'App\\User',
            'model_id' => $userId
          ]);
        }
    }
}
