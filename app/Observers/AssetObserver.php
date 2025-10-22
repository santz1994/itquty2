<?php

namespace App\Observers;

use App\Asset;
use App\Status;
use App\Events\AssetStatusChanged;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    public function updating(Asset $asset)
    {
        if ($asset->isDirty('status_id')) {
            $oldStatus = Status::find($asset->getOriginal('status_id'));
            $newStatus = Status::find($asset->status_id);
            $changedBy = Auth::user();
            $notes = $asset->notes ?? null;
            event(new AssetStatusChanged($asset, $oldStatus, $newStatus, $changedBy, $notes));
        }
    }
}
