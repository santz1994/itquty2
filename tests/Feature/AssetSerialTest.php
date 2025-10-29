<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Asset;

class AssetSerialTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_create_assets_with_duplicate_serial_number()
    {
        // Create an existing asset with serial
        Asset::create([
            'asset_tag' => 'TEST1',
            'name' => 'Test Asset 1',
            'serial_number' => 'SN-12345',
            'model_id' => null,
            'division_id' => 1,
            'status_id' => 1
        ]);

        // Attempt to create another with same serial via API
        $this->actingAs($this->createAdminUser())
             ->postJson('/api/assets', [
                 'asset_tag' => 'TEST2',
                 'name' => 'Test Asset 2',
                 'serial_number' => 'SN-12345',
                 'model_id' => null,
                 'division_id' => 1,
                 'status_id' => 1
             ])
             ->assertStatus(422)
             ->assertJsonFragment(['success' => false]);
    }

    protected function createAdminUser()
    {
        // Create a simple user compatible with older apps that may not have factories
        $email = 'admin@example.test';
        $user = \App\User::firstOrCreate([
            'email' => $email
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('secret'),
            'is_active' => 1
        ]);
        // Try to assign admin role if roles package present
        if (method_exists($user, 'assignRole')) {
            try { $user->assignRole('admin'); } catch (\Throwable $e) {}
        }
        return $user;
    }
}
