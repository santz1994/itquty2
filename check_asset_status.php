<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$assets = \App\Asset::selectRaw('status_id, COUNT(*) as count')
    ->groupBy('status_id')
    ->orderBy('status_id')
    ->get();

echo "Status Distribution:\n";
echo "===================\n";
foreach ($assets as $row) {
    $status = \App\Status::find($row->status_id);
    echo "Status ID {$row->status_id} ({$status->name}): {$row->count} assets\n";
}

echo "\n\nAsset Statistics from Service:\n";
echo "==============================\n";
$service = app(\App\Services\AssetService::class);
$stats = $service->getAssetStatistics();
foreach ($stats as $key => $value) {
    echo "$key: $value\n";
}

echo "\n\nAssets with status_id = 2 (Deployed):\n";
echo "====================================\n";
$deployed = \App\Asset::where('status_id', 2)->get();
echo "Count: " . $deployed->count() . "\n";
$deployed->each(function($asset) {
    echo "- {$asset->asset_tag} ({$asset->name})\n";
});
