<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$migrations = [
    '2025_10_30_000001_create_bulk_operations_table',
    '2025_10_30_000002_create_bulk_operation_logs_table',
    '2025_10_30_000003_create_exports_table',
    '2025_10_30_000004_create_export_logs_table',
    '2025_10_30_000005_create_imports_table',
    '2025_10_30_000006_create_import_logs_table',
    '2025_10_30_000007_create_import_conflicts_table',
    '2025_10_30_000008_create_resolution_choices_table',
];

// Get existing migrations
$existing = DB::table('migrations')->pluck('migration')->toArray();

// Insert missing migrations with batch 13
$batch = DB::table('migrations')->max('batch') + 1;

foreach ($migrations as $migration) {
    if (!in_array($migration, $existing)) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
        ]);
        echo "Inserted: $migration\n";
    } else {
        echo "Already exists: $migration\n";
    }
}

echo "Done!\n";
?>
