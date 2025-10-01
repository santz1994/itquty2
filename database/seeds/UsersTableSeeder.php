<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $now = new Carbon();

      // Create Super Admin
      User::firstOrCreate(
          ['email' => 'superadmin@quty.co.id'],
          [
              'name' => 'Super Admin User',
              'password' => Hash::make('superadmin'),
              'api_token' => Str::random(60),
              'created_at' => $now,
          ]
      );

      // Create Admin user (used by tests)
      User::firstOrCreate(
          ['email' => 'admin@quty.co.id'],
          [
              'name' => 'Admin User',
              'password' => Hash::make('admin'),
              'api_token' => Str::random(60),
              'created_at' => $now,
          ]
      );

      // Create regular User (used by tests)
      User::firstOrCreate(
          ['email' => 'user@quty.co.id'],
          [
              'name' => 'User User',
              'password' => Hash::make('user'),
              'api_token' => Str::random(60),
              'created_at' => $now,
          ]
      );
    }
}
