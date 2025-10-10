<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show system settings page
     */
    public function settings()
    {
        // Authorization handled by middleware (super-admin role required)
        
        $systemInfo = [
            'app_version' => config('app.version', '2.0'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];
        
        return view('system.settings', compact('systemInfo'));
    }

    /**
     * Show permissions management page
     */
    public function permissions()
    {
        // Authorization handled by middleware (super-admin role required)
        
        $permissions = Permission::with('roles')->get();
        $roles = Role::with('permissions')->get();
        
        return view('system.permissions', compact('permissions', 'roles'));
    }

    /**
     * Show roles management page
     */
    public function roles()
    {
        $this->authorize('edit-system-settings');
        
        $roles = Role::withCount(['users', 'permissions'])->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();
        
        return view('system.roles', compact('roles', 'permissions', 'users'));
    }

    /**
     * Show system maintenance page
     */
    public function maintenance()
    {
        $this->authorize('edit-system-settings');
        
        $diskUsage = $this->getDiskUsage();
        $cacheInfo = $this->getCacheInfo();
        $logInfo = $this->getLogInfo();
        
        return view('system.maintenance', compact('diskUsage', 'cacheInfo', 'logInfo'));
    }

    /**
     * Show system logs
     */
    public function logs(Request $request)
    {
        $this->authorize('view-system-settings');
        
        // allow selecting a log file via ?file=filename.log (sanitize with basename)
        $requestedFile = $request->get('file', 'laravel.log');
        $requestedFile = basename($requestedFile);
        $logFile = storage_path('logs/' . $requestedFile);
        $logs = [];
        $stats = [
            'total' => 0,
            'errors' => 0,
            'warnings' => 0,
            'info' => 0,
            'file_size' => '0 KB',
            'last_entry' => 'Never'
        ];
        $log_files = [];
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            // split into lines preserving empty lines
            $logLines = preg_split('/\r\n|\r|\n/', $logContent);

            // Group lines into entries by detecting lines that start with a timestamp
            $entries = [];
            $current = '';
            foreach ($logLines as $line) {
                if (preg_match('/^\[?\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]?/', trim($line))) {
                    if ($current !== '') {
                        $entries[] = $current;
                    }
                    $current = $line;
                } else {
                    // continuation of previous log entry (stacktrace, context, etc.)
                    $current .= "\n" . $line;
                }
            }
            if ($current !== '') $entries[] = $current;

            // take last 100 entries
            $recentEntries = array_slice($entries, -100);
            $recentEntries = array_reverse($recentEntries);

            foreach ($recentEntries as $index => $entry) {
                $timestamp = null;
                if (preg_match('/^\[?(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]?/', $entry, $m)) {
                    $timestamp = $m[1];
                }

                $level = $this->extractLogLevel($entry);
                $message = $this->extractLogMessage($entry);

                $logs[] = [
                    'id' => $index,
                    'timestamp' => $timestamp ?? date('Y-m-d H:i:s', filemtime($logFile)),
                    'level' => $level,
                    'message' => $message,
                    'context' => []
                ];
            }

            // Calculate stats
            $stats['total'] = count($logs);
            $stats['file_size'] = round(filesize($logFile) / 1024, 2) . ' KB';
            $stats['last_entry'] = date('Y-m-d H:i:s', filemtime($logFile));

            foreach ($logs as $log) {
                if ($log['level'] === 'error') $stats['errors']++;
                elseif ($log['level'] === 'warning') $stats['warnings']++;
                elseif ($log['level'] === 'info') $stats['info']++;
            }
        }
        
        // Get available log files
        $logFiles = glob(storage_path('logs/*.log'));
        foreach ($logFiles as $file) {
            $log_files[] = [
                'name' => basename($file),
                'size' => round(filesize($file) / 1024, 2) . ' KB',
                'modified' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        // Apply basic filtering (search, level, date) on the assembled $logs
        $search = $request->get('search');
        $levelFilter = $request->get('level');
        $dateFilter = $request->get('date');

        $filtered = collect($logs)->filter(function ($entry) use ($search, $levelFilter, $dateFilter) {
            // level filter
            if ($levelFilter && strtolower($entry['level']) !== strtolower($levelFilter)) return false;
            // search filter (in message or timestamp)
            if ($search) {
                $s = strtolower($search);
                if (stripos($entry['message'], $search) === false && stripos($entry['timestamp'], $search) === false) {
                    return false;
                }
            }
            // date filter (today, yesterday, week, month)
            if ($dateFilter && isset($entry['timestamp'])) {
                $entryDate = Carbon::parse($entry['timestamp'])->startOfDay();
                $now = Carbon::now();
                if ($dateFilter === 'today' && !$entryDate->isSameDay($now)) return false;
                if ($dateFilter === 'yesterday' && !$entryDate->isSameDay($now->copy()->subDay())) return false;
                if ($dateFilter === 'week' && $entryDate->lt($now->copy()->startOfWeek())) return false;
                if ($dateFilter === 'month' && $entryDate->lt($now->copy()->startOfMonth())) return false;
            }
            return true;
        })->values()->all();

        // Recalculate stats for filtered set
        $filteredStats = [
            'total' => count($filtered),
            'errors' => 0,
            'warnings' => 0,
            'info' => 0,
            'file_size' => $stats['file_size'],
            'last_entry' => $stats['last_entry']
        ];
        foreach ($filtered as $entry) {
            if ($entry['level'] === 'error') $filteredStats['errors']++;
            elseif ($entry['level'] === 'warning') $filteredStats['warnings']++;
            elseif ($entry['level'] === 'info') $filteredStats['info']++;
        }

        return view('system.logs', ['logs' => $filtered, 'stats' => $filteredStats, 'log_files' => $log_files]);
    }
    
    private function extractLogLevel($line)
    {
        if (preg_match('/\.(ERROR|CRITICAL|ALERT|EMERGENCY)/', $line)) return 'error';
        if (preg_match('/\.(WARNING)/', $line)) return 'warning';
        if (preg_match('/\.(INFO|NOTICE)/', $line)) return 'info';
        if (preg_match('/\.(DEBUG)/', $line)) return 'debug';
        return 'info';
    }
    
    private function extractLogMessage($line)
    {
        // Simple extraction - could be more sophisticated
        if (preg_match('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?\.(.*?)$/', $line, $matches)) {
            return trim($matches[1] ?? $line);
        }
        return $line;
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request)
    {
        $this->authorize('edit-system-settings');
        
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Request $request)
    {
        $this->authorize('edit-system-settings');
        
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);
        
        $role = Role::findById($request->role_id);
        $permission = Permission::findById($request->permission_id);
        
        $role->givePermissionTo($permission);
        
        return response()->json([
            'success' => true,
            'message' => "Permission '{$permission->name}' assigned to role '{$role->name}'"
        ]);
    }

    /**
     * Remove permission from role
     */
    public function removePermission(Request $request)
    {
        $this->authorize('edit-system-settings');
        
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);
        
        $role = Role::findById($request->role_id);
        $permission = Permission::findById($request->permission_id);
        
        $role->revokePermissionTo($permission);
        
        return response()->json([
            'success' => true,
            'message' => "Permission '{$permission->name}' removed from role '{$role->name}'"
        ]);
    }

    /**
     * Get disk usage information
     */
    private function getDiskUsage()
    {
        $paths = [
            'storage' => storage_path(),
            'public' => public_path(),
            'logs' => storage_path('logs'),
            'cache' => storage_path('framework/cache'),
        ];
        
        $usage = [];
        foreach ($paths as $name => $path) {
            if (is_dir($path)) {
                $size = $this->getDirSize($path);
                $usage[$name] = [
                    'path' => $path,
                    'size' => $size,
                    'size_human' => $this->formatBytes($size)
                ];
            }
        }
        
        return $usage;
    }

    /**
     * Get cache information
     */
    private function getCacheInfo()
    {
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
            'config_cached' => file_exists(base_path('bootstrap/cache/config.php')),
            'routes_cached' => file_exists(base_path('bootstrap/cache/routes.php')),
            'views_cached' => is_dir(storage_path('framework/views')) && 
                            count(glob(storage_path('framework/views/*'))) > 0,
        ];
    }

    /**
     * Get log information
     */
    private function getLogInfo()
    {
        $logPath = storage_path('logs');
        $logFiles = [];
        
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            foreach ($files as $file) {
                $logFiles[] = [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'size_human' => $this->formatBytes(filesize($file)),
                    'modified' => date('Y-m-d H:i:s', filemtime($file))
                ];
            }
        }
        
        return $logFiles;
    }

    /**
     * Get directory size
     */
    private function getDirSize($path)
    {
        $size = 0;
        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}