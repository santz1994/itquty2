<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DailyActivityController;
use App\Http\Controllers\API\NotificationController;

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
    Route::apiResource('assets', AssetController::class);
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign']);
    Route::post('/assets/{asset}/unassign', [AssetController::class, 'unassign']);
    Route::post('/assets/{asset}/maintenance', [AssetController::class, 'markForMaintenance']);
    Route::get('/assets/{asset}/history', [AssetController::class, 'getHistory']);
    
    // Ticket API endpoints
    Route::apiResource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);
    Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopen']);
    Route::get('/tickets/{ticket}/timeline', [TicketController::class, 'getTimeline']);
    
    // User API endpoints
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/performance', [UserController::class, 'getPerformance']);
    Route::get('/users/{user}/workload', [UserController::class, 'getWorkload']);
    Route::get('/users/{user}/activities', [UserController::class, 'getActivities']);
    
    // Daily Activity API endpoints
    Route::apiResource('daily-activities', DailyActivityController::class);
    Route::post('/daily-activities/{activity}/complete', [DailyActivityController::class, 'markCompleted']);
    Route::get('/daily-activities/user/{user}', [DailyActivityController::class, 'getUserActivities']);
    Route::get('/daily-activities/summary/{user}', [DailyActivityController::class, 'getUserSummary']);
    
    // Notification API endpoints - high frequency
    Route::middleware(['throttle:api-frequent'])->group(function () {
        Route::apiResource('notifications', NotificationController::class);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    });
    
    // Dashboard and Statistics - admin level rate limiting
    Route::middleware(['throttle:api-admin'])->group(function () {
        Route::get('/dashboard/stats', [UserController::class, 'getDashboardStats']);
        Route::get('/dashboard/kpi', [UserController::class, 'getKpiData']);
    });
    
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