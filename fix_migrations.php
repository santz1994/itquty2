#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Insert migration record for bulk_operations
DB::table('migrations')->where('migration', '2025_10_30_000001_create_bulk_operations_table')->delete();
DB::table('migrations')->insert([
    'migration' => '2025_10_30_000001_create_bulk_operations_table',
    'batch' => 1,
]);

echo "Migration recorded successfully\n";
