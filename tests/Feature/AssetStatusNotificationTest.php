<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\User;
use App\Asset;
use App\Status;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssetStatusChangedNotification;

class AssetStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_status_change_sends_notification()
    {
        Notification::fake();
        $user = User::factory()->create();
        $status1 = Status::factory()->create(['name' => 'In Use']);
        $status2 = Status::factory()->create(['name' => 'In Repair']);
        $asset = Asset::factory()->create([
            'assigned_to' => $user->id,
            'status_id' => $status1->id
        ]);

        $asset->status_id = $status2->id;
        $asset->save();

        Notification::assertSentTo(
            [$user],
            AssetStatusChangedNotification::class
        );
    }

    public function test_no_notification_when_no_assigned_user()
    {
        Notification::fake();
        $status1 = Status::factory()->create(['name' => 'Available']);
        $status2 = Status::factory()->create(['name' => 'Deployed']);
        $asset = Asset::factory()->create([
            'assigned_to' => null,
            'status_id' => $status1->id
        ]);

        $asset->status_id = $status2->id;
        $asset->save();

        Notification::assertNothingSent();
    }

    public function test_database_notification_payload_contains_expected_keys()
    {
        Notification::fake();
        $user = User::factory()->create();
        $status1 = Status::factory()->create(['name' => 'Available']);
        $status2 = Status::factory()->create(['name' => 'Assigned']);
        $asset = Asset::factory()->create([
            'assigned_to' => $user->id,
            'status_id' => $status1->id
        ]);

        $asset->status_id = $status2->id;
        $asset->save();

        Notification::assertSentTo($user, AssetStatusChangedNotification::class, function ($notification, $channels) use ($user) {
            $array = $notification->toArray($user);
            $required = ['asset_id', 'asset_name', 'new_status_id', 'new_status_name', 'changed_by_name', 'url', 'message', 'type'];
            foreach ($required as $key) {
                if (!array_key_exists($key, $array)) {
                    return false;
                }
            }
            return true;
        });
    }

    public function test_notification_is_queued()
    {
        Notification::fake();
        $user = User::factory()->create();
        $status1 = Status::factory()->create(['name' => 'Available']);
        $status2 = Status::factory()->create(['name' => 'Assigned']);
        $asset = Asset::factory()->create([
            'assigned_to' => $user->id,
            'status_id' => $status1->id
        ]);

        $asset->status_id = $status2->id;
        $asset->save();

        // Notification should be queued (implements ShouldQueue)
        Notification::assertSentTo($user, AssetStatusChangedNotification::class, function ($notification) {
            return in_array(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements($notification));
        });
    }
}
