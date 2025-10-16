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

        // Insert Super Admin users (Daniel, Idol, Ridwan)
        $danielId = DB::table('users')->insertGetId([
          'name' => 'Daniel',
          'email' => 'daniel@quty.co.id',
          'password' => bcrypt('123456'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $idolId = DB::table('users')->insertGetId([
          'name' => 'Idol',
          'email' => 'idol@quty.co.id',
          'password' => bcrypt('123456'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $ridwanId = DB::table('users')->insertGetId([
          'name' => 'Ridwan',
          'email' => 'ridwan@quty.co.id',
          'password' => bcrypt('123456'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        // Insert management user
        $managementId = DB::table('users')->insertGetId([
          'name' => 'Management',
          'email' => 'management@quty.co.id',
          'password' => bcrypt('management'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        // Insert original test users
        $superAdminId = DB::table('users')->insertGetId([
          'name' => 'Super Admin',
          'email' => 'superadmin@quty.co.id',
          'password' => bcrypt('superadmin'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $adminId = DB::table('users')->insertGetId([
          'name' => 'Admin',
          'email' => 'admin@quty.co.id',
          'password' => bcrypt('admin'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        $userId = DB::table('users')->insertGetId([
          'name' => 'User',
          'email' => 'user@quty.co.id',
          'password' => bcrypt('user'),
          'api_token' => Str::random(60),
          'created_at' => $now
        ]);

        // Assign roles using Spatie model_has_roles
        $superAdminRole = DB::table('roles')->where('name', 'super-admin')->first();
        $managementRole = DB::table('roles')->where('name', 'management')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $userRole = DB::table('roles')->where('name', 'user')->first();

        // Assign super-admin role to Daniel, Idol, Ridwan, and Super Admin test user
        if ($superAdminRole) {
          DB::table('model_has_roles')->insert([
            ['role_id' => $superAdminRole->id, 'model_type' => 'App\\User', 'model_id' => $danielId],
            ['role_id' => $superAdminRole->id, 'model_type' => 'App\\User', 'model_id' => $idolId],
            ['role_id' => $superAdminRole->id, 'model_type' => 'App\\User', 'model_id' => $ridwanId],
            ['role_id' => $superAdminRole->id, 'model_type' => 'App\\User', 'model_id' => $superAdminId]
          ]);
        }
        
        // Assign management role to Management user
        if ($managementRole) {
          DB::table('model_has_roles')->insert([
            'role_id' => $managementRole->id,
            'model_type' => 'App\\User',
            'model_id' => $managementId
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
