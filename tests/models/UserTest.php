<?php

namespace Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\Role;

use Illuminate\Support\Facades\Auth;
// Removed duplicate Artisan import
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\TestUsersTableSeeder;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public static function setUpBeforeClass(): void
    {
     parent::setUpBeforeClass();
           // Ensure roles are seeded before users
           try { if (class_exists(\Database\Seeders\RolesTableSeeder::class)) { (new \Database\Seeders\RolesTableSeeder())->run(); } } catch (\Throwable $__e) {}
           try { if (class_exists(\Database\Seeders\TestUsersTableSeeder::class)) { (new \Database\Seeders\TestUsersTableSeeder())->run(); } } catch (\Throwable $__e) {}
    }

    public function testUserCannotAccessUsersView()
    {
      $user = User::where('name', 'User User')->get()->first();

      $this->actingAs($user)
           ->get('/admin/users')
           ->assertResponseStatus('403');
    }

    public function testAdminCannotAccessUsersView()
    {
      $user = User::where('name', 'Admin User')->get()->first();

      $this->actingAs($user)
           ->get('/admin/users')
           ->assertResponseStatus('403');
    }

          public function testUsersViewWithLoggedInSuperAdmin()
          {
                         $user = User::where('name', 'Super Admin User')->first();
                         $user = User::find($user->id); // Reload model to refresh roles
                         // Debug: log user id and roles
                         @file_put_contents(storage_path('logs/user_test_debug.log'), json_encode([
                              'test' => 'testUsersViewWithLoggedInSuperAdmin',
                              'user_id' => $user ? $user->id : null,
                              'roles' => $user ? $user->roles->pluck('name')->toArray() : null
                         ]) . PHP_EOL, FILE_APPEND);

                         $this->actingAs($user)
                                    ->visit('/admin/users')
                                    ->see('Users');
          }

    public function testCreateNewUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();
      // Reload user from DB to ensure roles are loaded
      $user = User::find($user->id);
      // Debug: log roles
      $roles = $user->roles->pluck('name')->toArray();
      file_put_contents(base_path('user_test_debug.log'), "Super Admin User roles: " . json_encode($roles) . "\n", FILE_APPEND);

      $this->actingAs($user)
           ->visit('/admin/users')
           ->see('Users');
    }

    public function testLoginWithNewUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users/')
           ->see('Create New User')
           ->type('Test User', 'name')
           ->type('test@quty.co.id', 'email')
           ->type('secret', 'password')
           ->press('Add New User')
           ->seePageIs('/admin/users')
           ->see('Successfully created')
           ->seeInDatabase('users', ['name' => 'Test User', 'email' => 'test@quty.co.id']);

      Auth::logout();

      $this->visit('/')
         ->see('Sign in to start your session')
         ->type('test@quty.co.id', 'email')
         ->type('secret', 'password')
         ->press('Sign In')
         ->see('Tickets')
         ->seePageIs('/tickets');
    }

    public function testEditNonSuperAdminUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users/')
           ->see('Create New User')
           ->type('Test User', 'name')
           ->type('test@quty.co.id', 'email')
           ->type('secret', 'password')
           ->press('Add New User')
           ->seePageIs('/admin/users')
           ->see('Successfully created')
           ->seeInDatabase('users', ['name' => 'Test User', 'email' => 'test@quty.co.id']);

      $newUser = User::where('email', 'test@quty.co.id')->first();
      $adminRole = Role::where('name', 'admin')->first();

      $this->actingAs($user)
           ->visit('/admin/users/' . $newUser->id . '/edit')
           ->see('Test User')
           ->type('Test Test', 'name')
           ->type('testtest@quty.co.id', 'email')
           ->type('foobar', 'password')
           ->type('foobar', 'password_confirmation')
           ->select($adminRole->id, 'role_id')
           ->press('Edit User')
           ->seePageIs('/admin/users')
           ->see('Successfully updated');

      $updatedUser = User::where('email', 'testtest@quty.co.id')->first();
      $this->seeInDatabase('users', ['name' => 'Test Test', 'email' => 'testtest@quty.co.id']);
      $this->seeInDatabase('model_has_roles', ['model_id' => $updatedUser->id, 'role_id' => $adminRole->id, 'model_type' => 'App\\User']);
    }

    public function testPasswordLengthSixOrMoreCharactersOnCreateNewUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users')
           ->see('Create New User')
           ->type('Test User', 'name')
           ->type('test@quty.co.id', 'email')
           ->type('12345', 'password')
           ->press('Add New User')
           ->see('The password must be a minimum of six (6) characters long.')
           ->type('123456', 'password')
           ->press('Add New User')
           ->seePageIs('/admin/users')
           ->see('Successfully created')
           ->seeInDatabase('users', ['name' => 'Test User', 'email' => 'test@quty.co.id']);
    }

    public function testPasswordLengthSixOrMoreCharactersOnEditUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();
      $adminUser = User::where('name', 'Admin User')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users/' . $adminUser->id . '/edit')
           ->see('Admin User')
           ->type('12345', 'password')
           ->type('12345', 'password_confirmation')
           ->press('Edit User')
           ->see('The password must be a minimum of six (6) characters long.');
    }

    public function testPasswordMatchOnEditUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();
      $adminUser = User::where('name', 'Admin User')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users/' . $adminUser->id . '/edit')
           ->see('Admin User')
           ->type('123456', 'password')
           ->type('654321', 'password_confirmation')
           ->press('Edit User')
           ->see('The passwords do not match.');
    }

    public function testCannotChangeSuperAdminToNonSuperAdminIfThereIsOnlyOneSuperAdminUser()
    {
      $user = User::where('name', 'Super Admin User')->get()->first();
      $adminRole = Role::where('name', 'admin')->get()->first();
      $superAdminRole = Role::where('name', 'super-admin')->get()->first();

      $this->actingAs($user)
           ->visit('/admin/users/' . $user->id . '/edit')
           ->see('Super Admin User')
           ->select($adminRole->id, 'role_id')
           ->press('Edit User')
           ->see('Cannot change role as there must be one (1) or more users with the role of ' . $superAdminRole->display_name . '.');
    }
}
