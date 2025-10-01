<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolesTableSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'TestUsersTableSeeder', '--force' => true]);
    }
  use DatabaseTransactions;

  public function testVisitHomepageWhenNotLoggedIn()
  {
    // Directly request the login page and verify the expected text is present
    $response = $this->call('GET', '/login');
    $content = $response->getContent();
    $this->assertStringContainsString('Sign in to start your session', $content);

    // Simulate submitting an empty login form and verify validation errors were flashed
    $post = $this->call('POST', '/login', ['_token' => csrf_token()]);
    // The app redirects back with validation errors; assert the session has errors for email
    $this->assertTrue(function_exists('session') && session()->has('errors'));
    $errors = session('errors');
    $this->assertNotNull($errors);
    $this->assertStringContainsString('These credentials do not match our records.', $errors->first());
  }


  public function testLogin()
  {
    // Perform login via POST and assert authentication succeeds
    $post = $this->call('POST', '/login', [
      'email' => 'superadmin@quty.co.id',
      'password' => 'superadmin',
      '_token' => csrf_token(),
    ]);

    // Auth should be active in the test process after successful login
    $this->assertTrue(\Illuminate\Support\Facades\Auth::check());
    // Optionally, check that the user has been redirected to the home page
    $status = $post->getStatusCode();
    $this->assertTrue(in_array($status, [200, 302]));
  }
}
