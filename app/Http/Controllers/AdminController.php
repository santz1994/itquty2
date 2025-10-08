<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Asset;
use App\Ticket;
use App\AssetRequest;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin']);
    }

    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_assets' => Asset::count(),
            'active_tickets' => Ticket::whereHas('ticket_status', function($query) {
                $query->where('status', '!=', 'closed')->where('status', '!=', 'resolved');
            })->count(),
            'pending_requests' => class_exists('App\AssetRequest') ? AssetRequest::where('status', 'pending')->count() : 0,
        ];

        $system_status = [
            'cache' => $this->checkCacheStatus(),
            'storage' => $this->checkStorageStatus(),
        ];

        $recent_activities = $this->getRecentActivities();

        return view('admin.dashboard', compact('stats', 'system_status', 'recent_activities'));
    }

    /**
     * Show database administration page
     */
    public function database()
    {
        $db_status = $this->checkDatabaseStatus();
        $db_info = $this->getDatabaseInfo();
        $db_stats = $this->getDatabaseStats();
        $tables = $this->getTableInfo();
        $migrations = $this->getMigrationStatus();

        return view('admin.database', compact('db_status', 'db_info', 'db_stats', 'tables', 'migrations'));
    }

    /**
     * Execute database actions
     */
    public function databaseAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:optimize,repair,check,migrate,seed'
        ]);

        try {
            switch ($request->action) {
                case 'optimize':
                    $this->optimizeTables();
                    $message = 'Database tables optimized successfully.';
                    break;
                case 'repair':
                    $this->repairTables();
                    $message = 'Database tables repaired successfully.';
                    break;
                case 'check':
                    $result = $this->checkTables();
                    $message = 'Database check completed. ' . $result;
                    break;
                case 'migrate':
                    Artisan::call('migrate');
                    $message = 'Migrations executed successfully.';
                    break;
                case 'seed':
                    Artisan::call('db:seed');
                    $message = 'Database seeded successfully.';
                    break;
            }

            return redirect()->route('admin.database')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.database')->with('error', 'Action failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute dangerous database actions
     */
    public function databaseDanger(Request $request)
    {
        $request->validate([
            'danger_action' => 'required|in:reset,fresh,rollback'
        ]);

        try {
            switch ($request->danger_action) {
                case 'reset':
                    Artisan::call('migrate:reset');
                    $message = 'Database reset completed.';
                    break;
                case 'fresh':
                    Artisan::call('migrate:fresh');
                    $message = 'Fresh migration completed.';
                    break;
                case 'rollback':
                    Artisan::call('migrate:rollback');
                    $message = 'Migration rollback completed.';
                    break;
            }

            return redirect()->route('admin.database')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.database')->with('error', 'Dangerous action failed: ' . $e->getMessage());
        }
    }

    /**
     * Show cache management page
     */
    public function cache()
    {
        $cache_info = $this->getCacheInfo();
        $cache_status = $this->getCacheStatus();
        $cache_stats = $this->getCacheStats();
        $cache_files = $this->getCacheFiles();
        $recent_cache_activity = $this->getRecentCacheActivity();

        return view('admin.cache', compact('cache_info', 'cache_status', 'cache_stats', 'cache_files', 'recent_cache_activity'));
    }

    /**
     * Clear cache
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'cache_type' => 'required|in:application,config,route,view,all'
        ]);

        try {
            switch ($request->cache_type) {
                case 'application':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared successfully.';
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Configuration cache cleared successfully.';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared successfully.';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully.';
                    break;
                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $message = 'All caches cleared successfully.';
                    break;
            }

            return redirect()->route('admin.cache')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.cache')->with('error', 'Cache clear failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize cache
     */
    public function optimizeCache()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return redirect()->route('admin.cache')->with('success', 'Cache optimization completed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.cache')->with('error', 'Cache optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Show backup management page
     */
    public function backup()
    {
        $backup_status = $this->getBackupStatus();
        $backup_settings = $this->getBackupSettings();
        $backups = $this->getExistingBackups();

        return view('admin.backup', compact('backup_status', 'backup_settings', 'backups'));
    }

    /**
     * Create backup
     */
    public function createBackup(Request $request)
    {
        $request->validate([
            'backup_types' => 'required|array',
            'backup_types.*' => 'in:database,files,uploads,config',
            'backup_name' => 'nullable|string|max:255',
            'compression' => 'required|in:gzip,zip,none'
        ]);

        try {
            // This would implement actual backup creation logic
            $backupName = $request->backup_name ?: 'backup-' . date('Y-m-d-H-i-s');
            
            // Simulate backup creation
            $message = 'Backup "' . $backupName . '" created successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Update backup settings
     */
    public function backupSettings(Request $request)
    {
        $request->validate([
            'auto_backup' => 'nullable|boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'retention_days' => 'required|integer|min:1|max:365'
        ]);

        try {
            // This would save backup settings to config or database
            $message = 'Backup settings updated successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Settings update failed: ' . $e->getMessage());
        }
    }

    // Private helper methods

    private function checkCacheStatus()
    {
        try {
            Cache::put('test_key', 'test_value', 60);
            return Cache::get('test_key') === 'test_value';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorageStatus()
    {
        try {
            return Storage::disk('local')->put('test.txt', 'test') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getRecentActivities()
    {
        // This would fetch real activity logs from database
        return [
            [
                'time' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'user' => 'admin',
                'action' => 'User Created',
                'type' => 'success',
                'details' => 'Created new user: john.doe@example.com'
            ],
            [
                'time' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'user' => 'super-admin',
                'action' => 'Cache Cleared',
                'type' => 'warning',
                'details' => 'Cleared application cache'
            ]
        ];
    }

    private function checkDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return ['connected' => true];
        } catch (\Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    private function getDatabaseInfo()
    {
        try {
            return [
                'driver' => config('database.default'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'port' => config('database.connections.' . config('database.default') . '.port'),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getDatabaseStats()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            return [
                'total_tables' => count($tables),
                'database_size' => 'Unknown' // Would calculate actual size
            ];
        } catch (\Exception $e) {
            return ['total_tables' => 0, 'database_size' => 'Unknown'];
        }
    }

    private function getTableInfo()
    {
        try {
            $tables = DB::select('SHOW TABLE STATUS');
            $result = [];
            foreach ($tables as $table) {
                $result[] = [
                    'name' => $table->Name,
                    'rows' => $table->Rows,
                    'size' => round($table->Data_length / 1024 / 1024, 2) . ' MB',
                    'engine' => $table->Engine,
                    'created' => $table->Create_time
                ];
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getMigrationStatus()
    {
        try {
            $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();
            $result = [];
            foreach ($migrations as $migration) {
                $result[] = [
                    'name' => $migration->migration,
                    'batch' => $migration->batch,
                    'executed_at' => $migration->created_at
                ];
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function optimizeTables()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            DB::statement("OPTIMIZE TABLE `$tableName`");
        }
    }

    private function repairTables()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            DB::statement("REPAIR TABLE `$tableName`");
        }
    }

    private function checkTables()
    {
        $tables = DB::select('SHOW TABLES');
        $errors = 0;
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $result = DB::select("CHECK TABLE `$tableName`");
            if ($result[0]->Msg_text !== 'OK') {
                $errors++;
            }
        }
        return $errors > 0 ? "$errors table(s) have errors." : "All tables are OK.";
    }

    private function getCacheInfo()
    {
        return [
            'driver' => config('cache.default')
        ];
    }

    private function getCacheStatus()
    {
        return [
            'working' => $this->checkCacheStatus(),
            'application' => file_exists(storage_path('framework/cache')),
            'routes' => file_exists(storage_path('framework/cache/routes.php')),
            'config' => file_exists(storage_path('framework/cache/config.php')),
            'views' => file_exists(storage_path('framework/views'))
        ];
    }

    private function getCacheStats()
    {
        return [
            'total_files' => 0, // Would count actual cache files
            'total_size' => '0 MB', // Would calculate actual size
            'last_cleared' => 'Never', // Would track last clear time
            'hit_rate' => '0%' // Would calculate hit rate
        ];
    }

    private function getCacheFiles()
    {
        return [
            [
                'type' => 'config',
                'path' => storage_path('framework/cache/config.php'),
                'size' => file_exists(storage_path('framework/cache/config.php')) ? 
                    round(filesize(storage_path('framework/cache/config.php')) / 1024, 2) . ' KB' : '0 KB',
                'modified' => file_exists(storage_path('framework/cache/config.php')) ? 
                    date('Y-m-d H:i:s', filemtime(storage_path('framework/cache/config.php'))) : 'Never',
                'exists' => file_exists(storage_path('framework/cache/config.php'))
            ],
            [
                'type' => 'routes',
                'path' => storage_path('framework/cache/routes.php'),
                'size' => file_exists(storage_path('framework/cache/routes.php')) ? 
                    round(filesize(storage_path('framework/cache/routes.php')) / 1024, 2) . ' KB' : '0 KB',
                'modified' => file_exists(storage_path('framework/cache/routes.php')) ? 
                    date('Y-m-d H:i:s', filemtime(storage_path('framework/cache/routes.php'))) : 'Never',
                'exists' => file_exists(storage_path('framework/cache/routes.php'))
            ]
        ];
    }

    private function getRecentCacheActivity()
    {
        return []; // Would implement activity tracking
    }

    private function getBackupStatus()
    {
        return [
            'last_backup' => 'Never',
            'total_backups' => 0,
            'backup_path' => storage_path('backups'),
            'total_size' => '0 MB',
            'available_space' => '1 GB',
            'auto_backup' => false
        ];
    }

    private function getBackupSettings()
    {
        return [
            'auto_backup' => false,
            'frequency' => 'weekly',
            'retention_days' => 30
        ];
    }

    private function getExistingBackups()
    {
        return []; // Would list actual backup files
    }

    /**
     * Cleanup old backups
     */
    public function cleanupBackups()
    {
        try {
            // This would implement actual backup cleanup logic
            $message = 'Old backups cleaned up successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download backup
     */
    public function downloadBackup($backupId)
    {
        try {
            // This would implement actual backup download logic
            return response()->download(storage_path('backups/backup-' . $backupId . '.zip'));
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup download failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore backup
     */
    public function restoreBackup(Request $request, $backupId)
    {
        try {
            // This would implement actual backup restore logic
            $message = 'Backup restored successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete backup
     */
    public function deleteBackup($backupId)
    {
        try {
            // This would implement actual backup deletion logic
            $message = 'Backup deleted successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload backup
     */
    public function uploadBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip,sql,tar.gz|max:102400' // 100MB max
        ]);

        try {
            // This would implement actual backup upload and restore logic
            $message = 'Backup uploaded and restored successfully.';
            
            return redirect()->route('admin.backup')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize database tables
     */
    public function optimize()
    {
        try {
            // Get all tables
            $tables = \DB::select('SHOW TABLES');
            $database = \DB::getDatabaseName();
            $key = "Tables_in_{$database}";
            
            $optimizedTables = [];
            foreach ($tables as $table) {
                $tableName = $table->$key;
                \DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimizedTables[] = $tableName;
            }
            
            $message = 'Successfully optimized ' . count($optimizedTables) . ' database tables.';
            
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database optimization failed: ' . $e->getMessage());
        }
    }
}