<?php

namespace App\Listeners;

use App\Events\AssetStatusChanged;
use App\Notifications\AssetStatusChangedNotification;

class SendAssetStatusNotification
{
    public function handle(AssetStatusChanged $event)
    {
        $asset = $event->asset;
        $user = $asset->assignedTo;
        if ($user) {
            $user->notify(new AssetStatusChangedNotification(
                $asset,
                $event->oldStatus,
                $event->newStatus,
                $event->changedBy,
                $event->notes
            ));
        }
    }
}
