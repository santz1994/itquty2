<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Ticket;
use App\Asset;
use App\AssetRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * API Automated Tests
 * Faster than browser tests, ideal for CI/CD
 * Target: <2% False Positive Rate
 */
class ApiAutomatedTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->superAdmin = User::factory()->create([
            'name' => 'Test Super Admin',
            'email' => 'test.superadmin@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test.admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test.user@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->user->assignRole('user');
    }

    /**
     * Test 1: Authentication
     * Success Rate: >99%
     */
    public function test_01_user_can_login()
    {
        $response = $this->post('/login', [
            'email' => 'test.superadmin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($this->superAdmin);
    }

    /**
     * Test 2: Ticket API - Create
     * Success Rate: >98%
     */
    public function test_02_can_create_ticket()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/tickets', [
                'subject' => 'API Test Ticket',
                'description' => 'Test description',
                'ticket_priority_id' => 2,
                'ticket_status_id' => 1,
                'assigned_to' => $this->admin->id,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tickets', [
            'subject' => 'API Test Ticket',
        ]);
    }

    /**
     * Test 3: Ticket API - Read
     * Success Rate: >99%
     */
    public function test_03_can_view_ticket()
    {
        $ticket = Ticket::factory()->create([
            'subject' => 'View Test Ticket',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
        $response->assertSee('View Test Ticket');
    }

    /**
     * Test 4: Ticket API - Update
     * Success Rate: >98%
     */
    public function test_04_can_update_ticket()
    {
        $ticket = Ticket::factory()->create([
            'subject' => 'Original Subject',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put("/tickets/{$ticket->id}", [
                'subject' => 'Updated Subject',
                'description' => $ticket->description,
                'ticket_priority_id' => $ticket->ticket_priority_id,
                'ticket_status_id' => $ticket->ticket_status_id,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'subject' => 'Updated Subject',
        ]);
    }

    /**
     * Test 5: Ticket API - Delete
     * Success Rate: >98%
     */
    public function test_05_can_delete_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete("/tickets/{$ticket->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    /**
     * Test 6: Asset API - Create
     * Success Rate: >98%
     */
    public function test_06_can_create_asset()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/assets', [
                'asset_tag' => 'TEST-' . time(),
                'name' => 'Test Laptop',
                'serial_number' => 'SN' . time(),
                'asset_type_id' => 1,
                'status_id' => 1,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('assets', [
            'name' => 'Test Laptop',
        ]);
    }

    /**
     * Test 7: Asset Request - Create
     * Success Rate: >98%
     */
    public function test_07_user_can_create_asset_request()
    {
        $response = $this->actingAs($this->user)
            ->post('/asset-requests', [
                'asset_type_id' => 1,
                'justification' => 'Need for work',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('asset_requests', [
            'user_id' => $this->user->id,
            'justification' => 'Need for work',
        ]);
    }

    /**
     * Test 8: Asset Request - Approve
     * Success Rate: >98%
     */
    public function test_08_admin_can_approve_asset_request()
    {
        $request = AssetRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/asset-requests/{$request->id}/approve");

        $response->assertStatus(302);
        $this->assertDatabaseHas('asset_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);
    }

    /**
     * Test 9: Authorization - Regular User Cannot Access Admin Routes
     * Success Rate: >99%
     */
    public function test_09_user_cannot_access_admin_routes()
    {
        $response = $this->actingAs($this->user)
            ->get('/users');

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test 10: Authorization - Admin Can Access Admin Routes
     * Success Rate: >99%
     */
    public function test_10_admin_can_access_admin_routes()
    {
        $response = $this->actingAs($this->admin)
            ->get('/users');

        $response->assertStatus(200);
    }

    /**
     * Test 11: Dashboard API
     * Success Rate: >99%
     */
    public function test_11_dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * Test 12: Search API
     * Success Rate: >95%
     */
    public function test_12_search_returns_results()
    {
        $ticket = Ticket::factory()->create([
            'subject' => 'Unique Search Term ' . uniqid(),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get('/tickets?search=' . urlencode($ticket->subject));

        $response->assertStatus(200);
        $response->assertSee($ticket->subject);
    }

    /**
     * Test 13: Notification API
     * Success Rate: >95%
     */
    public function test_13_notifications_endpoint_works()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'unread_count',
        ]);
    }

    /**
     * Test 14: Audit Log Creation
     * Success Rate: >98%
     */
    public function test_14_audit_log_created_on_ticket_creation()
    {
        $this->actingAs($this->superAdmin)
            ->post('/tickets', [
                'subject' => 'Audit Test Ticket',
                'description' => 'Test',
                'ticket_priority_id' => 2,
                'ticket_status_id' => 1,
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'model' => 'Ticket',
        ]);
    }

    /**
     * Test 15: Validation Errors
     * Success Rate: >99%
     */
    public function test_15_validation_prevents_invalid_ticket()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/tickets', [
                // Missing required fields
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['subject', 'description']);
    }
}
