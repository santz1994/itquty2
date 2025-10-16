<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

// Bridge for legacy apps: if the old app/Http/routes.php exists, load it
// so older route definitions (from Laravel 5.x era) are picked up.
$legacy = base_path('app/Http/routes.php');
if (file_exists($legacy)) {
    require $legacy;
} else {
    // fallback: no legacy routes found — define a minimal home route so
    // that the application URL works during tests. Explicit auth routes
    // (login/register) are not required for the test-suite since tests
    // use actingAs.
    Route::get('/', function () {
        if (Auth::check()) {
            // Redirect users based on their role
            /** @var \App\User|null $user */
            $user = Auth::user();
            if (user_has_role($user, 'user')) {
                return redirect('/tickets');
            }
            return redirect('/home');
        }
        return redirect('/login');
    });
}

// REMOVED DUPLICATE ADMIN ROUTES - Consolidated below in proper middleware groups

// Authentication Routes (Laravel 10 compatible)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/home');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Password Reset Routes
Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Session Extension Route for AJAX requests
Route::post('/extend-session', function (\Illuminate\Http\Request $request) {
    if ($request->ajax() && Auth::check()) {
        $request->session()->put('last_activity', time());
        return response()->json(['status' => 'success', 'message' => 'Session extended']);
    }
    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
})->middleware('auth')->name('extend-session');

// QR Code Routes (Public access for mobile scanning)
Route::get('/assets/qr/{qrCode}', [\App\Http\Controllers\QRCodeController::class, 'showAssetByQR'])->name('assets.qr');

// Authenticated Routes
Route::middleware(['web', 'auth'])->group(function () {
    
    // Global Search API Routes
    Route::get('/api/search', [\App\Http\Controllers\SearchController::class, 'search'])->name('api.search');
    Route::get('/api/quick-search', [\App\Http\Controllers\SearchController::class, 'quickSearch'])->name('api.quick-search');
    
    // Validation API Routes (for AJAX form validation)
    Route::get('/api/validate/asset-tag', [\App\Http\Controllers\ValidationController::class, 'validateAssetTag'])->name('api.validate.asset-tag');
    Route::get('/api/validate/serial-number', [\App\Http\Controllers\ValidationController::class, 'validateSerialNumber'])->name('api.validate.serial-number');
    Route::get('/api/validate/email', [\App\Http\Controllers\ValidationController::class, 'validateEmail'])->name('api.validate.email');
    Route::get('/api/validate/ip-address', [\App\Http\Controllers\ValidationController::class, 'validateIpAddress'])->name('api.validate.ip-address');
    Route::get('/api/validate/mac-address', [\App\Http\Controllers\ValidationController::class, 'validateMacAddress'])->name('api.validate.mac-address');
    Route::post('/api/validate/batch', [\App\Http\Controllers\ValidationController::class, 'validateBatch'])->name('api.validate.batch');
    
    // SLA API Routes (for AJAX SLA status checks)
    Route::get('/api/sla/ticket/{ticket}/status', [\App\Http\Controllers\SlaController::class, 'getTicketSlaStatus'])->name('api.sla.ticket.status');
    Route::get('/api/sla/ticket/{ticket}/breach', [\App\Http\Controllers\SlaController::class, 'checkBreach'])->name('api.sla.ticket.breach');
    Route::get('/api/sla/metrics', [\App\Http\Controllers\SlaController::class, 'getMetrics'])->name('api.sla.metrics');
    
    // Home/Dashboard Routes
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::redirect('/dashboard', '/home');
    
    // Management Dashboard Routes
    Route::middleware(['role:management|super-admin'])->prefix('management')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ManagementDashboardController::class, 'index'])->name('management.dashboard');
        Route::get('/admin-performance', [\App\Http\Controllers\ManagementDashboardController::class, 'adminPerformance'])->name('management.admin-performance');
        Route::get('/ticket-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'ticketReports'])->name('management.ticket-reports');
        Route::get('/asset-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'assetReports'])->name('management.asset-reports');
    });

    // Admin & SuperAdmin Routes (combined to avoid conflicts)
    Route::middleware(['role:admin|super-admin'])->group(function () {
        
        // ========================================
        // TICKETS ROUTES (REFACTORED)
        // ========================================
        
        // Main CRUD Routes
        Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
        Route::get('/tickets/unassigned', [\App\Http\Controllers\TicketController::class, 'unassigned'])->name('tickets.unassigned');
        Route::get('/tickets/overdue', [\App\Http\Controllers\TicketController::class, 'overdue'])->name('tickets.overdue');
        Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
        Route::get('/tickets/{ticket}/edit', [\App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
        Route::patch('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update']);
        
        // Assignment Routes (TicketAssignmentController)
        Route::post('/tickets/{ticket}/self-assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'selfAssign'])->name('tickets.self-assign');
        Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'assign'])->name('tickets.assign');
        Route::post('/tickets/{ticket}/force-assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'forceAssign'])->name('tickets.force-assign');
        
        // Status Management Routes (TicketStatusController)
        Route::post('/tickets/{ticket}/complete', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'complete'])->name('tickets.complete');
        Route::post('/tickets/{ticket}/update-status', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'updateStatus'])->name('tickets.update-status');
        Route::post('/tickets/{ticket}/complete-with-resolution', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'completeWithResolution'])->name('tickets.complete-with-resolution');
        
        // User Portal Routes (UserTicketController)
        Route::post('/tickets/{ticket}/add-response', [\App\Http\Controllers\Tickets\UserTicketController::class, 'addResponse'])->name('tickets.add-response');
        
        // Time Tracking Routes (TicketTimerController)
        Route::post('/tickets/{ticket}/start-timer', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'startTimer'])->name('tickets.start-timer');
        Route::post('/tickets/{ticket}/stop-timer', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'stopTimer'])->name('tickets.stop-timer');
        Route::get('/tickets/{ticket}/timer-status', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'getTimerStatus'])->name('tickets.timer-status');
        Route::get('/tickets/{ticket}/work-summary', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'getWorkSummary'])->name('tickets.work-summary');

        // Bulk operations for tickets
        Route::post('/tickets/bulk/assign', [\App\Http\Controllers\BulkOperationController::class, 'bulkAssign'])->name('tickets.bulk.assign');
        Route::post('/tickets/bulk/update-status', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdateStatus'])->name('tickets.bulk.update-status');
        Route::post('/tickets/bulk/update-priority', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdatePriority'])->name('tickets.bulk.update-priority');
        Route::post('/tickets/bulk/update-category', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdateCategory'])->name('tickets.bulk.update-category');
        Route::post('/tickets/bulk/delete', [\App\Http\Controllers\BulkOperationController::class, 'bulkDelete'])->name('tickets.bulk.delete');
        Route::get('/tickets/bulk/options', [\App\Http\Controllers\BulkOperationController::class, 'getBulkOptions'])->name('tickets.bulk.options');

        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{id}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('/audit-logs/export/csv', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::post('/audit-logs/cleanup', [\App\Http\Controllers\AuditLogController::class, 'cleanup'])->name('audit-logs.cleanup');
        
        // Audit Logs API endpoints
        Route::get('/api/audit-logs/model', [\App\Http\Controllers\AuditLogController::class, 'getModelLogs'])->name('api.audit-logs.model');
        Route::get('/api/audit-logs/my-logs', [\App\Http\Controllers\AuditLogController::class, 'getMyLogs'])->name('api.audit-logs.my-logs');
        Route::get('/api/audit-logs/statistics', [\App\Http\Controllers\AuditLogController::class, 'getStatistics'])->name('api.audit-logs.statistics');

        // Daily Activities - Specific routes BEFORE resource route
        Route::get('/daily-activities/calendar', [\App\Http\Controllers\DailyActivityController::class, 'calendar'])->name('daily-activities.calendar');
        Route::get('/daily-activities/calendar-events', [\App\Http\Controllers\DailyActivityController::class, 'getCalendarEvents'])->name('daily-activities.calendar-events');
        Route::get('/daily-activities/date-activities', [\App\Http\Controllers\DailyActivityController::class, 'getDateActivities'])->name('daily-activities.date-activities');
        Route::get('/daily-activities/daily-report', [\App\Http\Controllers\DailyActivityController::class, 'today'])->name('daily-activities.daily-report');
        Route::get('/daily-activities/weekly-report', [\App\Http\Controllers\DailyActivityController::class, 'weekly'])->name('daily-activities.weekly-report');
        Route::get('/daily-activities/export-pdf', [\App\Http\Controllers\DailyActivityController::class, 'export'])->name('daily-activities.export-pdf');
        Route::resource('daily-activities', \App\Http\Controllers\DailyActivityController::class);

        // Assets Routes
        Route::get('/assets/categories', [\App\Http\Controllers\InventoryController::class, 'categories'])->name('assets.categories');
        Route::get('/assets/requests', [\App\Http\Controllers\InventoryController::class, 'requests'])->name('assets.requests');
        Route::get('/assets/scan-qr', [\App\Http\Controllers\AssetsController::class, 'scanQR'])->name('assets.scan-qr');
        Route::post('/assets/process-scan', [\App\Http\Controllers\AssetsController::class, 'processScan'])->name('assets.process-scan');
        Route::get('/assets/my-assets', [\App\Http\Controllers\AssetsController::class, 'myAssets'])->name('assets.my-assets');
        Route::get('/assets/{asset}/qr-code', [\App\Http\Controllers\AssetsController::class, 'generateQR'])->name('assets.qr-code');
        Route::get('/assets/{asset}/qr-download', [\App\Http\Controllers\AssetsController::class, 'downloadQR'])->name('assets.qr-download');
        Route::get('/assets/{asset}/ticket-history', [\App\Http\Controllers\AssetsController::class, 'history'])->name('assets.ticket-history');
        Route::get('/assets/{asset}/movements', [\App\Http\Controllers\AssetsController::class, 'movements'])->name('assets.movements');
        Route::post('/assets/{asset}/assign', [\App\Http\Controllers\AssetsController::class, 'assign'])->name('assets.assign');
        Route::post('/assets/{asset}/unassign', [\App\Http\Controllers\AssetsController::class, 'unassign'])->name('assets.unassign');
        
        // New Export/Import/Print Routes for Assets
        Route::get('/assets/export', [\App\Http\Controllers\AssetsController::class, 'export'])->name('assets.export');
        Route::get('/assets/import-form', [\App\Http\Controllers\AssetsController::class, 'importForm'])->name('assets.import-form');
        Route::post('/assets/import', [\App\Http\Controllers\AssetsController::class, 'import'])->name('assets.import');
        Route::get('/assets/download-template', [\App\Http\Controllers\AssetsController::class, 'downloadTemplate'])->name('assets.download-template');
        Route::get('/assets/{asset}/print', [\App\Http\Controllers\AssetsController::class, 'print'])->name('assets.print');
        
        // New Export/Print Routes for Tickets
        Route::get('/tickets/export', [\App\Http\Controllers\TicketController::class, 'export'])->name('tickets.export');
        Route::get('/tickets/{ticket}/print', [\App\Http\Controllers\TicketController::class, 'print'])->name('tickets.print');
        
        // KPI Dashboard Routes
        Route::get('/kpi-dashboard', [\App\Http\Controllers\KPIDashboardController::class, 'index'])->name('kpi.dashboard');
        Route::get('/kpi-data', [\App\Http\Controllers\KPIDashboardController::class, 'getKPIData'])->name('kpi.data');
        
        // Notification Routes
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/{notification}/unread', [\App\Http\Controllers\NotificationController::class, 'markUnread'])->name('notifications.unread');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        
        // File Attachments Routes
        Route::post('/attachments/upload', [\App\Http\Controllers\AttachmentController::class, 'upload'])->name('attachments.upload');
        Route::post('/attachments/bulk-upload', [\App\Http\Controllers\AttachmentController::class, 'bulkUpload'])->name('attachments.bulk-upload');
        Route::get('/attachments', [\App\Http\Controllers\AttachmentController::class, 'index'])->name('attachments.index');
        Route::get('/attachments/{id}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
        Route::delete('/attachments/{id}', [\App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');
        
        Route::post('/assets/bulk-qr-codes', [\App\Http\Controllers\QRCodeController::class, 'bulkGenerateQRCodes'])->name('assets.bulk-qr-codes');
        Route::post('/assets/{asset}/change-status', [\App\Http\Controllers\InventoryController::class, 'changeStatus'])->name('assets.change-status');
        Route::get('/assets', [\App\Http\Controllers\AssetsController::class, 'index'])->name('assets.index');
        
        // Assets CRUD Routes
        Route::resource('assets', \App\Http\Controllers\AssetsController::class)->except(['index']);

        // Asset Maintenance Logs
        Route::resource('maintenance', \App\Http\Controllers\AssetMaintenanceLogController::class)->names([
            'index' => 'maintenance.index',
            'create' => 'maintenance.create',
            'store' => 'maintenance.store',
            'show' => 'maintenance.show',
            'edit' => 'maintenance.edit',
            'update' => 'maintenance.update',
            'destroy' => 'maintenance.destroy'
        ]);
        Route::get('/maintenance/asset/{asset}', [\App\Http\Controllers\AssetMaintenanceLogController::class, 'getByAsset'])->name('maintenance.by-asset');
        
        // Asset Maintenance (Legacy)
        Route::get('/asset-maintenance', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('asset-maintenance.index');
        Route::get('/asset-maintenance/analytics', [\App\Http\Controllers\AssetMaintenanceController::class, 'analytics'])->name('asset-maintenance.analytics');
        Route::get('/asset-maintenance/{asset}', [\App\Http\Controllers\AssetMaintenanceController::class, 'show'])->name('asset-maintenance.show');

        // Spares Management
        Route::get('/spares', [\App\Http\Controllers\SparesController::class, 'index'])->name('spares.index');
    });

    // SuperAdmin Only Routes
    Route::middleware(['role:super-admin'])->group(function () {
        
        // Asset Requests Management
        Route::resource('asset-requests', \App\Http\Controllers\AssetRequestController::class);
        Route::post('/asset-requests/{assetRequest}/approve', [\App\Http\Controllers\AssetRequestController::class, 'approve'])->name('asset-requests.approve');
        Route::post('/asset-requests/{assetRequest}/reject', [\App\Http\Controllers\AssetRequestController::class, 'reject'])->name('asset-requests.reject');
        Route::post('/asset-requests/{assetRequest}/fulfill', [\App\Http\Controllers\AssetRequestController::class, 'fulfill'])->name('asset-requests.fulfill');
        
        // Admin Configuration
        Route::get('/admin', [\App\Http\Controllers\PagesController::class, 'getTicketConfig'])->name('admin.config');
        
        // SLA Management Routes
        Route::get('/sla/dashboard', [\App\Http\Controllers\SlaController::class, 'dashboard'])->name('sla.dashboard');
        Route::resource('sla', \App\Http\Controllers\SlaController::class);
        Route::post('/sla/{sla}/toggle-active', [\App\Http\Controllers\SlaController::class, 'toggleActive'])->name('sla.toggle-active');
        
        // System Settings Management
        Route::prefix('system-settings')->name('system-settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SystemSettingsController::class, 'index'])->name('index');
            
            // Ticket Configuration
            Route::get('/canned-fields', [\App\Http\Controllers\SystemSettingsController::class, 'cannedFields'])->name('canned-fields');
            Route::get('/ticket-statuses', [\App\Http\Controllers\SystemSettingsController::class, 'ticketStatuses'])->name('ticket-statuses');
            Route::get('/ticket-types', [\App\Http\Controllers\SystemSettingsController::class, 'ticketTypes'])->name('ticket-types');
            Route::get('/ticket-priorities', [\App\Http\Controllers\SystemSettingsController::class, 'ticketPriorities'])->name('ticket-priorities');
            
            // Asset Configuration
            Route::get('/asset-statuses', [\App\Http\Controllers\SystemSettingsController::class, 'assetStatuses'])->name('asset-statuses');
            Route::get('/divisions', [\App\Http\Controllers\SystemSettingsController::class, 'divisions'])->name('divisions');
            Route::get('/suppliers', [\App\Http\Controllers\SystemSettingsController::class, 'suppliers'])->name('suppliers');
            Route::get('/invoices', [\App\Http\Controllers\SystemSettingsController::class, 'invoices'])->name('invoices');
            Route::get('/warranty-types', [\App\Http\Controllers\SystemSettingsController::class, 'warrantyTypes'])->name('warranty-types');
            
            // Storeroom Configuration
            Route::get('/storeroom', [\App\Http\Controllers\SystemSettingsController::class, 'storeroom'])->name('storeroom');
        });

        // Resource routes for system settings management
        Route::resource('tickets-priority', \App\Http\Controllers\TicketsPrioritiesController::class)->except(['show', 'create', 'index']);
        Route::resource('tickets-status', \App\Http\Controllers\TicketsStatusesController::class)->except(['show', 'create', 'index']);
        Route::resource('warranty-types', \App\Http\Controllers\WarrantyTypesController::class)->except(['show', 'create', 'index']);

        // Tickets Canned Fields Management (Super Admin only)
        Route::resource('tickets-canned-field', \App\Http\Controllers\TicketsCannedFieldsController::class)->except(['show', 'create']);
        Route::get('/admin/ticket-canned-fields', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'index'])->name('admin.ticket-canned-fields.index');
        Route::get('/admin/ticket-canned-fields/{ticketsCannedField}/edit', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'edit'])->name('admin.ticket-canned-fields.edit');
        
        // Status Management (Super Admin only)
        Route::resource('status', \App\Http\Controllers\StatusesController::class)->except(['show', 'create']);

        // Master Data Management
        Route::resource('/models', \App\Http\Controllers\AssetModelsController::class, ['parameters' => ['models' => 'asset_model']]);
        Route::resource('/pcspecs', \App\Http\Controllers\PcspecsController::class);
        Route::resource('/manufacturers', \App\Http\Controllers\ManufacturersController::class);
        Route::resource('/asset-types', \App\Http\Controllers\AssetTypesController::class);
        Route::resource('/suppliers', \App\Http\Controllers\SuppliersController::class);
        Route::resource('/locations', \App\Http\Controllers\LocationsController::class);
        Route::resource('/divisions', \App\Http\Controllers\DivisionsController::class);
        Route::resource('/invoices', \App\Http\Controllers\InvoicesController::class);
        Route::resource('/budgets', \App\Http\Controllers\BudgetsController::class);
    });

    // NOTE: User and Management routes are now handled by the admin|super-admin group above
    // to avoid route conflicts and 403 errors. The controller handles role-based filtering internally.

    // User Self-Service Portal Routes (UserTicketController)
    Route::middleware(['role:user'])->group(function () {
        // Self-service ticket creation for users
        Route::get('/tiket-saya', [\App\Http\Controllers\Tickets\UserTicketController::class, 'userTickets'])->name('tickets.user-index');
        Route::get('/tiket-saya/buat', [\App\Http\Controllers\Tickets\UserTicketController::class, 'userCreate'])->name('tickets.user-create');
        Route::post('/tiket-saya/buat', [\App\Http\Controllers\Tickets\UserTicketController::class, 'userStore'])->name('tickets.user-store');
        Route::get('/tiket-saya/{ticket}', [\App\Http\Controllers\Tickets\UserTicketController::class, 'userShow'])->name('tickets.user-show');
        
        // User can view their assets
        Route::get('/aset-saya', [\App\Http\Controllers\AssetsController::class, 'userAssets'])->name('assets.user-index');
        Route::get('/aset-saya/{asset}', [\App\Http\Controllers\AssetsController::class, 'userShow'])->name('assets.user-show');
    });

    // Multi-role Routes (Management, Admin, SuperAdmin)
    Route::middleware(['role:management|admin|super-admin'])->group(function () {
        
        // Enhanced Ticket Creation (with asset selection)
        Route::get('/tickets/create-with-asset', [\App\Http\Controllers\TicketController::class, 'createWithAsset'])->name('tickets.create-with-asset');
    });

    // Activity Status Update (for all authenticated users)
    Route::post('/update-activity', [\App\Http\Controllers\ActivityController::class, 'updateActivity'])->name('update-activity');
});

// Debug and Test Routes (only available in local environment)
if (app()->environment('local')) {
    // Debug route for assets issue
    Route::get('/test-inventory-debug', function() {
    try {
        echo "<h2>Testing Inventory Controller Data</h2>";
        
        // Test each model one by one
        echo "<h3>Model Tests:</h3>";
        
        echo "Asset count: " . \App\Asset::count() . "<br>";
        echo "AssetType count: " . \App\AssetType::count() . "<br>";
        echo "Status count: " . \App\Status::count() . "<br>";
        echo "Location count: " . \App\Location::count() . "<br>";
        echo "Division count: " . \App\Division::count() . "<br>";
        
        // Test the problematic queries
        echo "<h3>Stats Queries:</h3>";
        
        $activeCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'Active');
        })->count();
        echo "Active assets: " . $activeCount . "<br>";
        
        $maintenanceCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'like', '%repair%');
        })->count();
        echo "Maintenance assets: " . $maintenanceCount . "<br>";
        
        // Check if AssetRequest model exists
        if (class_exists('\App\AssetRequest')) {
            $pendingCount = \App\AssetRequest::where('status', 'pending')->count();
            echo "Pending requests: " . $pendingCount . "<br>";
        } else {
            echo "AssetRequest model not found<br>";
        }
        
        echo "<h3>Controller Test:</h3>";
        
        // Test the actual controller
        $controller = new \App\Http\Controllers\InventoryController();
        $request = new \Illuminate\Http\Request();
        
        echo "Calling controller index method...<br>";
        $result = $controller->index($request);
        
        if ($result instanceof \Illuminate\View\View) {
            echo "Controller returned a view successfully!<br>";
            $viewData = $result->getData();
            echo "View data keys: " . implode(', ', array_keys($viewData)) . "<br>";
        } else {
            echo "Controller returned: " . gettype($result) . "<br>";
        }
        
    } catch (\Exception $e) {
        echo "<h3>Error:</h3>";
        echo "Message: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Debug authentication route (outside middleware)
Route::get('/debug-auth', function() {
    $user = auth()->user();
    if (!$user) {
        return '<h1>NOT LOGGED IN</h1><p>You need to log in first</p><a href="/login">Go to Login</a>';
    }
    
    $roles = $user->roles->pluck('name')->toArray() ?? [];
    
    return '<h1>Authentication Debug</h1>' .
           '<p><strong>User:</strong> ' . $user->name . '</p>' .
           '<p><strong>Email:</strong> ' . $user->email . '</p>' .
           '<p><strong>Roles:</strong> ' . implode(', ', $roles) . '</p>' .
           '<p><strong>Has admin role:</strong> ' . (user_has_role($user, 'admin') ? 'YES' : 'NO') . '</p>' .
           '<p><strong>Has super-admin role:</strong> ' . (user_has_role($user, 'super-admin') ? 'YES' : 'NO') . '</p>';
});

// Simple assets test route
Route::get('/assets-simple', function() {
    try {
        $pageTitle = 'Simple Assets Test';
        
        // Simple stats without complex relationships
        $stats = [
            'total_assets' => \App\Asset::count(),
            'active_assets' => 0, // Temporarily simplified
            'maintenance_assets' => 0, // Temporarily simplified  
            'pending_requests' => 0 // Temporarily simplified
        ];
        
        // Get basic data
        $assets = \App\Asset::with(['assetModel', 'status', 'division'])->paginate(25);
        $categories = \App\AssetType::all();
        $statuses = \App\Status::all();
        $locations = \App\Location::all();
        $divisions = \App\Division::all();
        $categoryStats = collect(); // Empty for now
        
        return view('inventory.index', compact(
            'assets', 'categories', 'statuses', 'locations', 'divisions', 
            'stats', 'categoryStats', 'pageTitle'
        ));
        
    } catch (\Exception $e) {
        return response("Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine(), 500);
    }
});

// Test Routes (untuk development)
Route::prefix('test')->group(function () {
    Route::get('/qr', [\App\Http\Controllers\TestController::class, 'testQrCode'])->name('test.qr');
    Route::get('/asset-qr', [\App\Http\Controllers\TestController::class, 'testAssetQrCode'])->name('test.asset-qr');
    Route::get('/status', [\App\Http\Controllers\TestController::class, 'systemStatus'])->name('test.status');
    
    // UAC Test Routes
    Route::get('/super-admin-test', function() {
        return 'Super Admin Access Working!';
    })->middleware(['auth', 'role:super-admin']);
    
    Route::get('/admin-test', function() {
        return 'Admin Access Working!';
    })->middleware(['auth', 'role:admin']);
    
    Route::get('/management-test', function() {
        return 'Management Access Working!';
    })->middleware(['auth', 'role:management']);
    
    // Debug current user roles
    Route::get('/debug-my-roles', function() {
        $user = auth()->user();
        if (!$user) {
            return 'Not logged in';
        }
        
        $output = "<h2>Current User Debug Info</h2>";
        $output .= "<p><strong>Name:</strong> " . $user->name . "</p>";
        $output .= "<p><strong>Email:</strong> " . $user->email . "</p>";
        $output .= "<p><strong>ID:</strong> " . $user->id . "</p>";
        
        // Check roles using different methods
    $output .= "<h3>Role Check Methods:</h3>";
    /** @var \App\User|null $user */
    $output .= "<p><strong>hasRole('super-admin'):</strong> " . (user_has_role($user, 'super-admin') ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>hasRole('admin'):</strong> " . (user_has_role($user, 'admin') ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>hasRole('user'):</strong> " . (user_has_role($user, 'user') ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>hasRole('management'):</strong> " . (user_has_role($user, 'management') ? 'YES' : 'NO') . "</p>";
        
        // Get all roles from database
        $roles = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\User')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->pluck('roles.name')
            ->toArray();
        
        $output .= "<h3>Database Roles:</h3>";
        $output .= "<pre>" . print_r($roles, true) . "</pre>";
        
        return $output;
    })->middleware(['auth']);
});


// Test routes for role middleware verification
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/test-role', function() {
    /** @var \App\User|null $user */
    $user = Auth::user();
        echo "<h1>User Role Test</h1>";
        echo "Logged in as: " . $user->name . "<br>";
        echo "Email: " . $user->email . "<br>";
        echo "<hr>";
        
    echo "<h2>Role Check</h2>";
    /** @var \App\User|null $user */
    echo "Has super-admin role: " . (user_has_role($user, 'super-admin') ? 'YES' : 'NO') . "<br>";
    echo "Has admin role: " . (user_has_role($user, 'admin') ? 'YES' : 'NO') . "<br>";
    echo "Has management role: " . (user_has_role($user, 'management') ? 'YES' : 'NO') . "<br>";
    echo "Has user role: " . (user_has_role($user, 'user') ? 'YES' : 'NO') . "<br>";
        
        echo "<h2>Database Role Assignments</h2>";
        $spatieRoles = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\User')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name')
            ->get();
            
        $legacyRoles = DB::table('role_user')
            ->where('user_id', $user->id)
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('roles.name')
            ->get();
            
        echo "Spatie Roles: ";
        if ($spatieRoles->isEmpty()) {
            echo "None";
        } else {
            echo implode(", ", $spatieRoles->pluck('name')->toArray());
        }
        echo "<br>";
        
        echo "Legacy Roles: ";
        if ($legacyRoles->isEmpty()) {
            echo "None";
        } else {
            echo implode(", ", $legacyRoles->pluck('name')->toArray());
        }
        echo "<br>";
        
        echo "<h2>Protected Route Tests</h2>";
        echo "The following links test if the role middleware is working correctly:<br>";
        echo "<ul>";
        echo "<li><a href='/test-super-admin'>Test Super Admin Route</a> (requires super-admin role)</li>";
        echo "<li><a href='/test-admin'>Test Admin Route</a> (requires admin role)</li>";
        echo "<li><a href='/test-management'>Test Management Route</a> (requires management role)</li>";
        echo "<li><a href='/test-user'>Test User Route</a> (requires user role)</li>";
        echo "</ul>";
        
        return "";
    });
    
    // Test routes with different role middleware requirements
    Route::get('/test-super-admin', function() {
        return "You have the super-admin role!";
    })->middleware('role:super-admin');
    
    Route::get('/test-admin', function() {
        return "You have the admin role!";
    })->middleware('role:admin');
    
    Route::get('/test-management', function() {
        return "You have the management role!";
    })->middleware('role:management');
    
    Route::get('/test-user', function() {
        return "You have the user role!";
    })->middleware('role:user');
});
// Debug routes for role middleware issues
Route::get('/debug-roles', function () {
    $user = auth()->user();
    if (!$user) {
        return '<h1>❌ NOT LOGGED IN</h1><p>Please login first at: <a href="/login">/login</a></p>';
    }

    $html = '<h1>Role Debug Information</h1>';
    $html .= '<p><strong>User:</strong> ' . $user->name . ' (ID: ' . $user->id . ')</p>';
    $html .= '<p><strong>Email:</strong> ' . $user->email . '</p>';

    // Test if user has role directly from DB
    $directDbRoles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', 'App\\User')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->pluck('roles.name')
        ->toArray();

    $html .= '<h2>Roles from DB:</h2>';
    if (empty($directDbRoles)) {
        $html .= '<p style="color:red;"><strong>❌ NO ROLES ASSIGNED TO THIS USER!</strong></p>';
        $html .= '<p>This is why you\'re getting 403 errors!</p>';
    } else {
        $html .= '<ul>';
        foreach ($directDbRoles as $role) {
            $html .= '<li><strong>' . $role . '</strong></li>';
        }
        $html .= '</ul>';
    }

    // Test HasRole method
    $html .= '<h2>Role Checks:</h2>';
    $html .= '<ul>';
    /** @var \App\User|null $user */
    $html .= '<li>Has role "super-admin": ' . (user_has_role($user, 'super-admin') ? '✅ YES' : '❌ NO') . '</li>';
    /** @var \App\User|null $user */
    $html .= '<li>Has role "admin": ' . (user_has_role($user, 'admin') ? '✅ YES' : '❌ NO') . '</li>';
    /** @var \App\User|null $user */
    $html .= '<li>Has any role ["super-admin", "admin"]: ' . (user_has_any_role($user, ['super-admin', 'admin']) ? '✅ YES' : '❌ NO') . '</li>';
    $html .= '</ul>';
    
    $html .= '<h2>Expected Result:</h2>';
    $html .= '<p>For routes to work, you need: <strong>admin</strong> OR <strong>super-admin</strong> role</p>';
    
    if (in_array('super-admin', $directDbRoles) || in_array('admin', $directDbRoles)) {
        $html .= '<p style="color:green;"><strong>✅ YOU HAVE CORRECT ROLES - Routes should work!</strong></p>';
        $html .= '<p>If still getting 403, clear browser cache and try again.</p>';
    } else {
        $html .= '<p style="color:red;"><strong>❌ YOU DON\'T HAVE REQUIRED ROLES - This is why you get 403!</strong></p>';
        $html .= '<p>Login as: superadmin@quty.co.id or admin@quty.co.id</p>';
    }

    return $html;
});

// Test route protected by role middleware
Route::get('/test-super-admin', function () {
    return '<h1>You have super-admin access!</h1>';
})->middleware('role:super-admin');

// Debug menu - shows all available debug routes
Route::get('/debug-menu', function() {
    $html = '<h2>🔧 Debug Menu</h2>';
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Authentication Debug:</h3>';
    $html .= '<p><a href="/debug-current-user" style="background: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔍 Check Current User</a></p>';
    $html .= '<p><a href="/check-users" style="background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">👥 List All Users</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Quick Login (for testing):</h3>';
    $html .= '<p><a href="/quick-login-superadmin" style="background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as Super Admin</a></p>';
    $html .= '<p><a href="/quick-login-admin" style="background: #ffc107; color: black; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as Admin</a></p>';
    $html .= '<p><a href="/quick-login-user" style="background: #28a745; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as User</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Page Access:</h3>';
    $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">📦 Assets Page</a></p>';
    $html .= '<p><a href="/tickets" style="background: #6f42c1; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🎫 Tickets Page</a></p>';
    $html .= '<p><a href="/daily-activities" style="background: #fd7e14; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">📅 Daily Activities</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Logout:</h3>';
    $html .= '<p><a href="/force-relogin" style="background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🚪 Logout & Clear Session</a></p>';
    $html .= '</div>';
    
    return $html;
});

// Check users in database
Route::get('/check-users', function() {
    $users = \App\User::take(5)->get(['id', 'name', 'email']);
    $html = '<h2>Users in Database (' . \App\User::count() . ' total)</h2>';
    foreach($users as $user) {
        $html .= '<p>ID: ' . $user->id . ', Name: ' . $user->name . ', Email: ' . $user->email . '</p>';
    }
    return $html;
});

// Test hasRole method
Route::get('/test-has-role', function() {
    $html = '<h2>Test hasRole Method</h2>';
    
    try {
        $user = \App\User::find(1);
        if ($user) {
            $html .= '<p>User found: ' . $user->name . '</p>';
            $html .= '<p>hasRole method exists: ' . (method_exists($user, 'hasRole') ? '✅ YES' : '❌ NO') . '</p>';
            
            if (method_exists($user, 'hasRole')) {
                $hasSuperAdmin = user_has_role($user, 'super-admin');
                $hasAdmin = user_has_role($user, 'admin');
                $hasUser = user_has_role($user, 'user');

                $html .= '<p>Has super-admin role: ' . ($hasSuperAdmin ? '✅ YES' : '❌ NO') . '</p>';
                $html .= '<p>Has admin role: ' . ($hasAdmin ? '✅ YES' : '❌ NO') . '</p>';
                $html .= '<p>Has user role: ' . ($hasUser ? '✅ YES' : '❌ NO') . '</p>';
            }
            
            // Test getRoleNames if available
            if (method_exists($user, 'getRoleNames')) {
                    /** @var \App\User|null $user */
                    $roles = user_get_role_names($user);
                        $html .= '<p>All roles: ' . $roles->implode(', ') . '</p>';
                }
        } else {
            $html .= '<p>❌ User not found</p>';
        }
    } catch (\Exception $e) {
        $html .= '<p>❌ Error: ' . $e->getMessage() . '</p>';
    }
    
    return $html;
});

// Debug current user info
Route::get('/debug-current-user', function() {
    // Log the authentication check
    \Illuminate\Support\Facades\Log::info('Debug route accessed', [
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'session_id' => session()->getId()
    ]);
    
    $html = '<h2>Current User Debug Info</h2>';
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Log detailed user info
        \Illuminate\Support\Facades\Log::info('User authenticated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);
        
        $html .= '<div style="background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;">';
        $html .= '<h3>✅ LOGGED IN</h3>';
        $html .= '<p><strong>User ID:</strong> ' . $user->id . '</p>';
        $html .= '<p><strong>Name:</strong> ' . $user->name . '</p>';
        $html .= '<p><strong>Email:</strong> ' . $user->email . '</p>';
        
        // Check roles if available
        if (method_exists($user, 'getRoleNames')) {
            $roles = user_get_role_names($user);
            $html .= '<p><strong>Roles:</strong> ' . ($roles->count() > 0 ? $roles->implode(', ') : 'None') . '</p>';
        }

        if (method_exists($user, 'hasRole')) {
            /** @var \App\User|null $user */
            $hasSuperAdmin = user_has_role($user, 'super-admin');
            $hasAdmin = user_has_role($user, 'admin');
            $hasManagement = user_has_role($user, 'management');

            $html .= '<p><strong>Has super-admin role:</strong> ' . ($hasSuperAdmin ? '✅ YES' : '❌ NO') . '</p>';
            $html .= '<p><strong>Has admin role:</strong> ' . ($hasAdmin ? '✅ YES' : '❌ NO') . '</p>';
            $html .= '<p><strong>Has management role:</strong> ' . ($hasManagement ? '✅ YES' : '❌ NO') . '</p>';
        }
        
        $html .= '</div>';
        $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none;">Go to Assets Page</a></p>';
        $html .= '<p><a href="/tickets" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none;">Go to Tickets Page</a></p>';
    } else {
        $html .= '<div style="background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;">';
        $html .= '<h3>❌ NOT LOGGED IN</h3>';
        $html .= '<p>You need to log in first</p>';
        $html .= '</div>';
        $html .= '<p><a href="/login" style="background: #dc3545; color: white; padding: 10px 20px; text-decoration: none;">Go to Login</a></p>';
    }
    
    $html .= '<hr>';
    $html .= '<p><strong>Session ID:</strong> ' . session()->getId() . '</p>';
    $html .= '<p><strong>CSRF Token:</strong> ' . csrf_token() . '</p>';
    
    return $html;
});

// Quick login routes for testing
Route::get('/quick-login-superadmin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    \Illuminate\Support\Facades\Log::info('Quick login as Super Admin', ['user_id' => 1]);
    return redirect('/debug-current-user')->with('message', 'Logged in as Super Admin');
});

Route::get('/quick-login-admin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(2);
    \Illuminate\Support\Facades\Log::info('Quick login as Admin', ['user_id' => 2]);
    return redirect('/debug-current-user')->with('message', 'Logged in as Admin');
});

Route::get('/quick-login-user', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(3);
    \Illuminate\Support\Facades\Log::info('Quick login as User', ['user_id' => 3]);
    return redirect('/debug-current-user')->with('message', 'Logged in as User');
});

// Browser-friendly login and redirect
Route::get('/browser-login-superadmin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    \Illuminate\Support\Facades\Log::info('Browser login as Super Admin', ['user_id' => 1]);
    return redirect('/assets')->with('success', 'Logged in as Super Admin - you should now see the assets page!');
});

Route::get('/browser-login-admin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(2);
    \Illuminate\Support\Facades\Log::info('Browser login as Admin', ['user_id' => 2]);
    return redirect('/assets')->with('success', 'Logged in as Admin - you should now see the assets page!');
});

// Test all major controllers
Route::get('/test-all-controllers', function() {
    // Login first
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    
    $html = '<h2>Controller Tests (as Super Admin)</h2>';
    
    $controllers = [
        'InventoryController@index' => '/assets',
        'TicketController@index' => '/tickets', 
        'DailyActivityController@index' => '/daily-activities',
        'HomeController@index' => '/home'
    ];
    
    foreach($controllers as $controller => $route) {
        try {
            $request = \Illuminate\Http\Request::create($route, 'GET');
            $request->setUserResolver(function() {
                return \Illuminate\Support\Facades\Auth::user();
            });
            
            // Parse controller and method
            list($controllerClass, $method) = explode('@', $controller);
            $fullControllerClass = "\\App\\Http\\Controllers\\{$controllerClass}";
            
            if (class_exists($fullControllerClass)) {
                $controllerInstance = app($fullControllerClass);
                
                if (method_exists($controllerInstance, $method)) {
                    $response = $controllerInstance->$method($request);
                    $html .= "<p>✅ <strong>{$controller}</strong> ({$route}): SUCCESS</p>";
                } else {
                    $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): Method not found</p>";
                }
            } else {
                $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): Controller not found</p>";
            }
            
        } catch (\Exception $e) {
            $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): ERROR - " . $e->getMessage() . "</p>";
        }
    }
    
    return $html;
});

// Test login and immediately access assets in same request
Route::get('/test-login-and-assets', function() {
    $html = '<h2>Login and Assets Test (Single Request)</h2>';
    
    // Step 1: Login
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    $html .= '<h3>Step 1: Login as Super Admin</h3>';
    $html .= '<p>Auth::check(): ' . (\Illuminate\Support\Facades\Auth::check() ? '✅ TRUE' : '❌ FALSE') . '</p>';
    $html .= '<p>Auth::id(): ' . (\Illuminate\Support\Facades\Auth::id() ?? 'NULL') . '</p>';
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $html .= '<p>User: ' . $user->name . ' (' . $user->email . ')</p>';
    }
    
    // Step 2: Test Assets Controller
    $html .= '<h3>Step 2: Test Assets Controller</h3>';
    try {
        $controller = new \App\Http\Controllers\InventoryController();
        $request = \Illuminate\Http\Request::create('/assets', 'GET');
        
        // Manually set the authenticated user for the request
        $request->setUserResolver(function() {
            return \Illuminate\Support\Facades\Auth::user();
        });
        
        $response = $controller->index($request);
        $html .= '<p>✅ SUCCESS: Controller returned response</p>';
        $html .= '<p>Response type: ' . get_class($response) . '</p>';
        
        // Try to get the view content
        if (method_exists($response, 'render')) {
            $content = $response->render();
            $html .= '<p>Content length: ' . strlen($content) . ' characters</p>';
            $html .= '<p>Contains "Total Assets": ' . (strpos($content, 'Total Assets') !== false ? '✅ YES' : '❌ NO') . '</p>';
        }
        
    } catch (\Exception $e) {
        $html .= '<p>❌ ERROR: ' . $e->getMessage() . '</p>';
        $html .= '<p>File: ' . $e->getFile() . ':' . $e->getLine() . '</p>';
    }
    
    // Step 3: Direct link test
    $html .= '<h3>Step 3: Test Links</h3>';
    $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 10px; text-decoration: none;">Go to Assets Page</a></p>';
    $html .= '<p><strong>Note:</strong> If the link above shows login page, then the session is not persisting between requests.</p>';
    
    return $html;
});

// Temporary test route to see assets page without authentication (REMOVE IN PRODUCTION)
Route::get('/test-assets-no-auth', function() {
    // Temporarily login as super admin
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    return redirect('/assets');
});

// Test multiple pages with auth
Route::get('/test-all-pages', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    
    $pages = [
        '/assets' => 'Assets Page',
        '/daily-activities' => 'Daily Activities', 
        '/tickets' => 'Tickets',
        '/home' => 'Home/Dashboard'
    ];
    
    $html = '<h2>Testing Pages (logged in as Super Admin)</h2>';
    foreach($pages as $url => $name) {
        try {
            $response = file_get_contents('http://192.168.1.122' . $url);
            $status = strpos($response, 'form-control name=email') === false ? '✅ WORKS' : '❌ LOGIN PAGE';
            $html .= '<p><strong>' . $name . '</strong> (' . $url . '): ' . $status . '</p>';
        } catch (Exception $e) {
            $html .= '<p><strong>' . $name . '</strong> (' . $url . '): ❌ ERROR</p>';
        }
    }
    return $html;
});

// Force re-login route to clear session
Route::get('/force-relogin', function() {
    Auth::logout();
    session()->flush();
    session()->regenerate();
    return redirect('/login')->with('message', '<strong>Session cleared!</strong> Please login again to refresh your roles.');
});

// System Management Routes (Super Admin only)  
Route::middleware(['auth', 'role:super-admin'])->prefix('system')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\SystemController::class, 'settings'])->name('system.settings');
    Route::get('/permissions', [\App\Http\Controllers\SystemController::class, 'permissions'])->name('system.permissions');
    Route::get('/roles', [\App\Http\Controllers\SystemController::class, 'roles'])->name('system.roles');
    Route::get('/maintenance', [\App\Http\Controllers\SystemController::class, 'maintenance'])->name('system.maintenance');
    Route::get('/logs', [\App\Http\Controllers\SystemController::class, 'logs'])->name('system.logs');
    
    // AJAX endpoints for system management
    Route::post('/cache/clear', [\App\Http\Controllers\SystemController::class, 'clearCache'])->name('system.cache.clear');
    Route::post('/permissions/assign', [\App\Http\Controllers\SystemController::class, 'assignPermission'])->name('system.permissions.assign');
    Route::post('/permissions/remove', [\App\Http\Controllers\SystemController::class, 'removePermission'])->name('system.permissions.remove');
    Route::post('/permissions/create', [\App\Http\Controllers\SystemController::class, 'createPermission'])->name('system.permissions.create');
    Route::post('/logs/clear', [\App\Http\Controllers\SystemController::class, 'clearLogs'])->name('system.logs.clear');
    Route::get('/logs/download', [\App\Http\Controllers\SystemController::class, 'downloadLogs'])->name('system.logs.download');
});

// Admin Tools Routes (Super Admin only)
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->group(function () {
    // Admin Authentication Routes (no additional middleware needed)
    Route::get('/authenticate', [\App\Http\Controllers\AdminAuthController::class, 'authenticate'])->name('admin.authenticate');
    Route::post('/authenticate', [\App\Http\Controllers\AdminAuthController::class, 'processAuth'])->name('admin.process-auth');
    Route::post('/clear-auth', [\App\Http\Controllers\AdminAuthController::class, 'clearAuth'])->name('admin.clear-auth');
    
    // Main admin config page
    Route::get('/', [\App\Http\Controllers\PagesController::class, 'getTicketConfig'])->name('admin.config');
    
    // Admin Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Database Management (Old routes - read-only)
    Route::get('/database', [\App\Http\Controllers\AdminController::class, 'database'])->name('admin.database');
    
    // Restricted Admin Operations (daniel@quty.co.id + password confirmation required)
    Route::middleware(['admin.security:edit'])->group(function () {
        Route::post('/database/action', [\App\Http\Controllers\AdminController::class, 'databaseAction'])->name('admin.database.action');
        Route::post('/database/danger', [\App\Http\Controllers\AdminController::class, 'databaseDanger'])->name('admin.database.danger');
        
        // Cache Management (POST operations only)
        Route::post('/cache/clear', [\App\Http\Controllers\AdminController::class, 'clearCache'])->name('admin.cache.clear');
        Route::post('/cache/optimize', [\App\Http\Controllers\AdminController::class, 'optimize'])->name('admin.cache.optimize');
        
        // Backup Management (Dangerous operations)
        Route::post('/backup/create', [\App\Http\Controllers\AdminController::class, 'create'])->name('admin.backup.create');
        Route::post('/backup/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.backup.settings');
        Route::post('/backup/cleanup', [\App\Http\Controllers\AdminController::class, 'cleanup'])->name('admin.backup.cleanup');
        Route::post('/backup/{backup}/restore', [\App\Http\Controllers\AdminController::class, 'restore'])->name('admin.backup.restore');
        Route::delete('/backup/{backup}', [\App\Http\Controllers\AdminController::class, 'delete'])->name('admin.backup.delete');
        Route::post('/backup/upload', [\App\Http\Controllers\AdminController::class, 'upload'])->name('admin.backup.upload');
    });
    
    // Safe Admin Operations (read-only, no password confirmation needed)
    Route::get('/cache', [\App\Http\Controllers\AdminController::class, 'cache'])->name('admin.cache');
    Route::get('/backup', [\App\Http\Controllers\AdminController::class, 'backup'])->name('admin.backup');
    Route::get('/backup/{backup}/download', [\App\Http\Controllers\AdminController::class, 'download'])->name('admin.backup.download');
    

// Development helper: allow a GET shortcut to clear caches in local env only.
// This is intentionally restricted to local environment and requires auth + super-admin role.
if (app()->environment('local')) {
    Route::middleware(['auth', 'role:super-admin'])->get('/cache/clear-dev', function() {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return redirect('/admin/cache')->with('success', 'Caches cleared (dev-only).');
        } catch (\Exception $e) {
            return redirect('/admin/cache')->with('error', 'Cache clear failed: ' . $e->getMessage());
        }
    })->name('admin.cache.clear.dev');
}
    // Admin Tools - Ticket & Asset Management
    Route::get('/assets', [\App\Http\Controllers\InventoryController::class, 'index'])->name('admin.assets.index');
    Route::get('/assets-statuses', [\App\Http\Controllers\StatusesController::class, 'index'])->name('admin.assets-statuses.index');
    Route::post('/assets-statuses', [\App\Http\Controllers\StatusesController::class, 'store'])->name('admin.assets-statuses.store');
    Route::get('/assets-statuses/{status}/edit', [\App\Http\Controllers\StatusesController::class, 'edit'])->name('admin.assets-statuses.edit');
    Route::put('/assets-statuses/{status}', [\App\Http\Controllers\StatusesController::class, 'update'])->name('admin.assets-statuses.update');
    Route::delete('/assets-statuses/{status}', [\App\Http\Controllers\StatusesController::class, 'destroy'])->name('admin.assets-statuses.destroy');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('admin.tickets.show');
    Route::get('/ticket-priorities', [\App\Http\Controllers\TicketsPrioritiesController::class, 'index'])->name('admin.ticket-priorities.index');
    Route::get('/ticket-statuses', [\App\Http\Controllers\TicketsStatusesController::class, 'index'])->name('admin.ticket-statuses.index');
    Route::get('/ticket-types', [\App\Http\Controllers\TicketsTypesController::class, 'index'])->name('admin.ticket-types.index');
    Route::post('/ticket-types', [\App\Http\Controllers\TicketsTypesController::class, 'store'])->name('tickets-type.store');
    Route::get('/ticket-types/{ticketsType}/edit', [\App\Http\Controllers\TicketsTypesController::class, 'edit'])->name('tickets-type.edit');
    Route::put('/ticket-types/{ticketsType}', [\App\Http\Controllers\TicketsTypesController::class, 'update'])->name('tickets-type.update');
    Route::delete('/ticket-types/{ticketsType}', [\App\Http\Controllers\TicketsTypesController::class, 'destroy'])->name('tickets-type.destroy');
    
    // Database Management Routes (Super Admin Only)
    // Read-only routes (no additional security needed)
    Route::get('/database', [\App\Http\Controllers\DatabaseController::class, 'index'])->name('admin.database.index');
    Route::get('/database/backup', [\App\Http\Controllers\DatabaseController::class, 'backup'])->name('admin.database.backup');
    Route::get('/database/{table}', [\App\Http\Controllers\DatabaseController::class, 'showTable'])->name('admin.database.table');
    Route::get('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'show'])->name('admin.database.show');
    Route::get('/database/{table}/export/{format}', [\App\Http\Controllers\DatabaseController::class, 'export'])->name('admin.database.export');
    
    // Edit operations (daniel@quty.co.id + password confirmation required)
    Route::middleware(['admin.security:edit'])->group(function () {
        Route::get('/database/{table}/create', [\App\Http\Controllers\DatabaseController::class, 'create'])->name('admin.database.create');
        Route::post('/database/{table}', [\App\Http\Controllers\DatabaseController::class, 'store'])->name('admin.database.store');
        Route::get('/database/{table}/{id}/edit', [\App\Http\Controllers\DatabaseController::class, 'edit'])->name('admin.database.edit');
        Route::put('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'update'])->name('admin.database.update');
        Route::delete('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'destroy'])->name('admin.database.destroy');
        Route::delete('/database/{table}/truncate', [\App\Http\Controllers\DatabaseController::class, 'truncate'])->name('admin.database.truncate');
    });
    
});

// Admin User Management Routes (Admin and Super Admin with admin prefix)
Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin/users')->group(function () {
    Route::get('/', [\App\Http\Controllers\UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/create', [\App\Http\Controllers\UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/', [\App\Http\Controllers\UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/{user}', [\App\Http\Controllers\UsersController::class, 'show'])->name('admin.users.show');
    Route::get('/{user}/edit', [\App\Http\Controllers\UsersController::class, 'edit'])->name('admin.users.edit');
    Route::put('/{user}', [\App\Http\Controllers\UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('admin.users.destroy');
});

// User Management Routes (Admin and Super Admin)
Route::middleware(['auth', 'role:admin|super-admin'])->prefix('users')->group(function () {
    Route::get('/', [\App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
    Route::get('/create', [\App\Http\Controllers\UsersController::class, 'create'])->name('users.create');
    
    // Role Management - Must be before /{user} routes to avoid conflicts
    Route::get('/roles', [\App\Http\Controllers\UsersController::class, 'roles'])->name('users.roles');
    
    Route::post('/', [\App\Http\Controllers\UsersController::class, 'store'])->name('users.store');
    Route::get('/{user}', [\App\Http\Controllers\UsersController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [\App\Http\Controllers\UsersController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [\App\Http\Controllers\UsersController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('users.destroy');
});

// Reports Routes (Management, Admin, Super Admin)
Route::middleware(['auth', 'role:management|admin|super-admin'])->prefix('reports')->group(function () {
    // Asset Reports
    Route::get('/assets', [\App\Http\Controllers\ManagementDashboardController::class, 'assetReports'])->name('reports.assets');
    Route::get('/assets/export', [\App\Http\Controllers\AssetsController::class, 'export'])->name('reports.assets.export');
    
    // Ticket Reports
    Route::get('/tickets', [\App\Http\Controllers\ManagementDashboardController::class, 'ticketReports'])->name('reports.tickets');
    Route::get('/tickets/export', [\App\Http\Controllers\TicketController::class, 'export'])->name('reports.tickets.export');
    
    // Performance Reports
    Route::get('/performance', [\App\Http\Controllers\ManagementDashboardController::class, 'adminPerformance'])->name('reports.performance');
    
    // Daily Activity Reports
    Route::get('/daily-activities', [\App\Http\Controllers\DailyActivityController::class, 'export'])->name('reports.daily-activities');
    
    // KPI Reports
    Route::get('/kpi', [\App\Http\Controllers\KPIDashboardController::class, 'getKPIData'])->name('reports.kpi');
});

// DEBUG SIMPLE TEST ROUTE FOR BLANK PAGE ISSUE
Route::get('/debug-blank-page', function() {
    try {
        return '<h1>✅ DEBUG ROUTE WORKS</h1>' .
               '<p>Laravel is running properly</p>' .
               '<p>Current time: ' . now() . '</p>' .
               '<p><a href="/login">Go to Login</a></p>' .
               '<p><a href="/dashboard">Go to Dashboard</a></p>' .
               '<p><strong>If this shows but dashboard is blank, the issue is in the controller or view</strong></p>';
    } catch (Exception $e) {
        return '<h1>❌ ERROR</h1><p>' . $e->getMessage() . '</p>';
    }
});

// DEBUG VIEW WITH LAYOUT TEST
Route::get('/debug-view-test', function() {
    try {
        return view('debug-view-test');
    } catch (Exception $e) {
        return '<h1>❌ VIEW ERROR</h1><p>' . $e->getMessage() . '</p><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// DEBUG CONTENT WITHOUT LAYOUT
Route::get('/debug-content-only', function() {
    return '<html><body><h1>CONTENT ONLY TEST</h1><p>This bypasses all layouts</p></body></html>';
});

// DEBUG HOME CONTROLLER STEP BY STEP
Route::get('/debug-home-controller', function() {
    try {
        // Step 1: Login as admin
        Auth::loginUsingId(1);
        
        $html = '<h1>🔍 Home Controller Debug</h1>';
        $html .= '<h3>Step 1: Authentication</h3>';
        $html .= '<p>✅ Logged in as: ' . Auth::user()->name . '</p>';
        
        // Step 2: Check role
        $user = Auth::user();
        $hasRole = user_has_role($user, 'admin') || user_has_role($user, 'super-admin');
        $html .= '<h3>Step 2: Role Check</h3>';
        $html .= '<p>' . ($hasRole ? '✅' : '❌') . ' Has admin/super-admin role: ' . ($hasRole ? 'YES' : 'NO') . '</p>';
        
        // Step 3: Test data queries one by one
        $html .= '<h3>Step 3: Data Queries</h3>';
        
        try {
            $assetCount = App\Asset::count();
            $html .= '<p>✅ Total assets: ' . $assetCount . '</p>';
        } catch (Exception $e) {
            $html .= '<p>❌ Asset query error: ' . $e->getMessage() . '</p>';
        }
        
        try {
            $locationCount = App\Location::count();
            $html .= '<p>✅ Location count: ' . $locationCount . '</p>';
        } catch (Exception $e) {
            $html .= '<p>❌ Location query error: ' . $e->getMessage() . '</p>';
        }
        
        try {
            $movements = App\Movement::with(['asset', 'location', 'user'])->take(5)->get();
            $html .= '<p>✅ Recent movements: ' . $movements->count() . '</p>';
        } catch (Exception $e) {
            $html .= '<p>❌ Movement query error: ' . $e->getMessage() . '</p>';
        }
        
        // Step 4: Test view compilation
        $html .= '<h3>Step 4: View Test</h3>';
        try {
            $testData = [
                'assetStats' => [
                    'total_assets' => 10,
                    'active_assets' => 5,
                    'available_assets' => 3,
                    'maintenance_assets' => 2,
                ],
                'movements' => collect(),
                'recentAssets' => collect(),
                'locationCount' => 5,
                'divisionCount' => 3,
                'year' => 2025,
                'pageTitle' => 'Debug Dashboard'
            ];
            
            $view = view('home', $testData);
            $content = $view->render();
            $html .= '<p>✅ View compilation successful (length: ' . strlen($content) . ' chars)</p>';
            
            // Check if content contains expected elements
            if (strpos($content, 'dashboard') !== false) {
                $html .= '<p>✅ View contains dashboard content</p>';
            } else {
                $html .= '<p>❌ View missing dashboard content</p>';
            }
            
        } catch (Exception $e) {
            $html .= '<p>❌ View error: ' . $e->getMessage() . '</p>';
            $html .= '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        
        $html .= '<hr><p><a href="/debug-view-test">Test Simple View</a> | <a href="/dashboard">Try Dashboard</a></p>';
        
        return $html;
        
    } catch (Exception $e) {
        return '<h1>❌ DEBUG ERROR</h1><p>' . $e->getMessage() . '</p><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

} // End of local environment debug routes

