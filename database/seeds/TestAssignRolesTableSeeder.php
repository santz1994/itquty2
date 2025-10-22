<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class TestAssignRolesTableSeeder extends Seeder
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
      $admin = Role::where('name', '=', 'admin')->first();
      $user = Role::where('name', '=', 'user')->first();

                    $superAdminUser = User::where('name', '=', 'Super Admin User')->first();
                    if (! $superAdminUser) {
                        // fallback: create minimal user so role assignment can proceed in tests
                        $superAdminUser = User::create([
                            'name' => 'Super Admin User',
                            'email' => 'superadmin@quty.co.id',
                            'password' => \Illuminate\Support\Facades\Hash::make('superadmin'),
                        ]);
                    }
                    if ($superAdminUser && $superAdmin) {
                        $superAdminUser->assignRole($superAdmin);
                    }

                    $adminUser = User::where('name', '=', 'Admin User')->first();
                    if (! $adminUser) {
                        $adminUser = User::create([
                            'name' => 'Admin User',
                            'email' => 'admin@quty.co.id',
                            'password' => \Illuminate\Support\Facades\Hash::make('admin'),
                        ]);
                    }
                    if ($adminUser && $admin) {
                        $adminUser->assignRole($admin);
                    }

                    $userUser = User::where('name', '=', 'User User')->first();
                    if (! $userUser) {
                        $userUser = User::create([
                            'name' => 'User User',
                            'email' => 'user@quty.co.id',
                            'password' => \Illuminate\Support\Facades\Hash::make('user'),
                        ]);
                    }
                    if ($userUser && $user) {
                        $userUser->assignRole($user);
                    }
    }
}
