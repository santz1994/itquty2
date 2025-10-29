<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasColumn('assets', 'serial_number')) {
            // nothing to do
            return;
        }

        // Detect duplicates first
        $duplicates = DB::table('assets')
            ->select('serial_number', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->groupBy('serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->count() > 0) {
            // Export duplicates for manual review
            $path = storage_path('app/serial_duplicates_before_unique.csv');
            $fh = fopen($path, 'w');
            fputcsv($fh, ['serial_number', 'count']);
            foreach ($duplicates as $d) {
                fputcsv($fh, [$d->serial_number, $d->cnt]);
            }
            fclose($fh);
            throw new \RuntimeException('Cannot create unique index: duplicate serial_number values exist. Exported to storage/serial_duplicates_before_unique.csv');
        }

        // Safe to add unique index
        // Check for existing index first (MySQL)
        $indexExists = false;
        try {
            $existing = DB::select("SHOW INDEX FROM `assets` WHERE Key_name = ?", ['assets_serial_number_unique']);
            if (is_array($existing) && count($existing) > 0) {
                $indexExists = true;
            }
        } catch (\Exception $e) {
            // ignore - assume index doesn't exist
        }

        if ($indexExists) {
            // Index already present, nothing to do
            return;
        }

        try {
            Schema::table('assets', function (Blueprint $table) {
                $name = 'assets_serial_number_unique';
                $table->unique('serial_number', $name);
            });
        } catch (\Exception $e) {
            // If index creation failed, throw with context
            throw new \RuntimeException('Failed to add unique index on assets.serial_number: ' . $e->getMessage());
        }
    }

    public function down()
    {
        if (Schema::hasColumn('assets', 'serial_number')) {
            try {
                Schema::table('assets', function (Blueprint $table) {
                    $table->dropUnique('assets_serial_number_unique');
                });
            } catch (\Exception $e) {
                // ignore
            }
        }
    }
};
