<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\User;
use App\DailyActivity;

class DailyActivityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_daily_activity()
    {
    $user = User::factory()->create();
    $this->actingAs($user);
        $payload = [
            'user_id' => $user->id,
            'title' => 'Testing API',
            'activity' => 'Testing API',
            'activity_type' => 'ticket_work',
            'activity_date' => now()->toDateString(),
        ];
        $response = $this->postJson('/api/daily-activities', $payload);
    $response->assertStatus(201)
         ->assertJsonFragment(['title' => 'Testing API', 'activity_type' => 'ticket_work']);
    $this->assertDatabaseHas('daily_activities', ['title' => 'Testing API', 'activity_type' => 'ticket_work', 'activity' => 'Testing API']);
    }

    public function test_can_get_daily_activities()
    {
    $user = User::factory()->create();
    $this->actingAs($user);
        DailyActivity::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test List',
            'activity' => 'Test List',
            'activity_type' => 'ticket_work',
            'activity_date' => now()->toDateString(),
        ]);
        $response = $this->getJson('/api/daily-activities');
    $response->assertStatus(200)
         ->assertJsonFragment(['title' => 'Test List', 'activity_type' => 'ticket_work']);
    }

    public function test_can_update_daily_activity()
    {
    $user = User::factory()->create();
    $this->actingAs($user);
        $activity = DailyActivity::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old',
            'activity' => 'Old',
            'activity_type' => 'ticket_work',
            'activity_date' => now()->toDateString(),
        ]);
        $response = $this->putJson('/api/daily-activities/' . $activity->id, [
            'title' => 'Updated',
            'activity' => 'Updated',
            'activity_type' => 'ticket_work',
            'activity_date' => now()->toDateString(),
        ]);
    $response->assertStatus(200)
         ->assertJsonFragment(['title' => 'Updated', 'activity_type' => 'ticket_work']);
    $this->assertDatabaseHas('daily_activities', ['title' => 'Updated', 'activity_type' => 'ticket_work', 'activity' => 'Updated']);
    }

    public function test_can_delete_daily_activity()
    {
    $user = User::factory()->create();
    $this->actingAs($user);
        $activity = DailyActivity::factory()->create([
            'user_id' => $user->id,
            'title' => 'To Delete',
            'activity' => 'To Delete',
            'activity_type' => 'ticket_work',
            'activity_date' => now()->toDateString(),
        ]);
        $response = $this->deleteJson('/api/daily-activities/' . $activity->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Daily activity deleted successfully']);
        $this->assertDatabaseMissing('daily_activities', ['id' => $activity->id]);
    }
}
