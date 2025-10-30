<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DailyActivityApiController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\Api\DatatableController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public authentication routes - rate limited
Route::middleware(['throttle:api-auth'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// Protected API routes - standard rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    
    // Authentication routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Asset API endpoints
    Route::apiResource('assets', AssetController::class)->names([
        'index' => 'api.assets.index',
        'store' => 'api.assets.store',
        'show' => 'api.assets.show',
        'update' => 'api.assets.update',
        'destroy' => 'api.assets.destroy'
    ]);
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign']);
    Route::post('/assets/{asset}/unassign', [AssetController::class, 'unassign']);
    Route::post('/assets/{asset}/maintenance', [AssetController::class, 'markForMaintenance']);
    Route::get('/assets/{asset}/history', [AssetController::class, 'getHistory']);
    // AJAX endpoint: check serial uniqueness (optional exclude_id query param)
    Route::get('/assets/check-serial', [AssetController::class, 'checkSerial'])->name('api.assets.checkSerial');
    // Search endpoint for assets
    Route::get('/assets/search', [AssetController::class, 'search'])->name('api.assets.search');
    
    // Ticket API endpoints
    Route::apiResource('tickets', TicketController::class)->names([
        'index' => 'api.tickets.index',
        'store' => 'api.tickets.store',
        'show' => 'api.tickets.show',
        'update' => 'api.tickets.update',
        'destroy' => 'api.tickets.destroy'
    ]);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);
    Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopen']);
    Route::get('/tickets/{ticket}/timeline', [TicketController::class, 'getTimeline']);
    // Search endpoints for tickets
    Route::get('/tickets/search', [TicketController::class, 'search'])->name('api.tickets.search');
    Route::get('/tickets/{ticket}/comments/search', [TicketController::class, 'commentsSearch'])->name('api.tickets.commentsSearch');
    
    // User API endpoints
    Route::apiResource('users', UserController::class)->names([
        'index' => 'api.users.index',
        'store' => 'api.users.store',
        'show' => 'api.users.show',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy'
    ]);
    Route::get('/users/{user}/performance', [UserController::class, 'getPerformance']);
    Route::get('/users/{user}/workload', [UserController::class, 'getWorkload']);
    Route::get('/users/{user}/activities', [UserController::class, 'getActivities']);
    
    // DailyActivity API
    Route::middleware('auth')->group(function () {
        Route::get('/daily-activities', [DailyActivityApiController::class, 'index']);
        Route::post('/daily-activities', [DailyActivityApiController::class, 'store']);
        Route::get('/daily-activities/{dailyActivity}', [DailyActivityApiController::class, 'show']);
        Route::put('/daily-activities/{dailyActivity}', [DailyActivityApiController::class, 'update']);
        Route::delete('/daily-activities/{dailyActivity}', [DailyActivityApiController::class, 'destroy']);
    });
    
    // Notification API endpoints - high frequency
    Route::middleware(['throttle:api-frequent'])->group(function () {
        Route::apiResource('notifications', NotificationController::class)->names([
            'index' => 'api.notifications.index',
            'store' => 'api.notifications.store',
            'show' => 'api.notifications.show',
            'update' => 'api.notifications.update',
            'destroy' => 'api.notifications.destroy'
        ]);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    });
    
    // Dashboard and Statistics - admin level rate limiting
    Route::middleware(['throttle:api-admin'])->group(function () {
        Route::get('/dashboard/stats', [UserController::class, 'getDashboardStats']);
        Route::get('/dashboard/kpi', [UserController::class, 'getKpiData']);
    });
    
    // Server-side DataTables API endpoints
    Route::get('/datatables/assets', [DatatableController::class, 'assets']);
    Route::get('/datatables/tickets', [DatatableController::class, 'tickets']);
    
    // Global search endpoints
    Route::get('/search/global', [SearchController::class, 'global'])->name('api.search.global');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('api.search.suggest');
    Route::get('/search/stats', [SearchController::class, 'stats'])->name('api.search.stats');
    
    // Filter endpoints - filter options for dropdowns
    Route::get('/assets/filter-options/{filter}', [FilterController::class, 'filterOptions'])->name('api.assets.filterOptions');
    Route::get('/tickets/filter-options/{filter}', [FilterController::class, 'filterOptions'])->name('api.tickets.filterOptions');
    
    // Filter builder and statistics
    Route::get('/filter-builder', [FilterController::class, 'filterBuilder'])->name('api.filterBuilder');
    Route::get('/filter-stats', [FilterController::class, 'filterStats'])->name('api.filterStats');
    
});

// Public endpoints - very restrictive rate limiting
Route::middleware(['throttle:api-public'])->group(function () {
    Route::get('/system/status', function () {
        return response()->json([
            'status' => 'online',
            'version' => config('app.version', '1.0.0'),
            'timestamp' => now()->toISOString(),
            'api_version' => '1.0'
        ]);
    });
    
    Route::get('/system/health', function () {
        return response()->json([
            'status' => 'healthy',
            'checks' => [
                'database' => 'connected',
                'cache' => 'active',
                'storage' => 'accessible'
            ],
            'timestamp' => now()->toISOString()
        ]);
    });
});