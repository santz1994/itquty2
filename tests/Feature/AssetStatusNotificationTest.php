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
}
