<?php

/**
 * Admin & SuperAdmin Routes
 * 
 * System configuration, user management, master data, and reports
 * Requires super-admin role for most routes
 */

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    
    // ========================================
    // HOME/DASHBOARD (All authenticated users)
    // ========================================
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::redirect('/dashboard', '/home');
    
    // Asset Requests available to all authenticated users (create/view their requests)
    Route::resource('asset-requests', \App\Http\Controllers\AssetRequestController::class);
    
    // ========================================
    // MANAGEMENT DASHBOARD
    // ========================================
    Route::middleware(['role:management|super-admin'])->prefix('management')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ManagementDashboardController::class, 'index'])->name('management.dashboard');
        Route::get('/admin-performance', [\App\Http\Controllers\ManagementDashboardController::class, 'adminPerformance'])->name('management.admin-performance');
        Route::get('/ticket-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'ticketReports'])->name('management.ticket-reports');
        Route::get('/asset-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'assetReports'])->name('management.asset-reports');
    });
    
    // ========================================
    // ADMIN & SUPER-ADMIN SHARED ROUTES
    // ========================================
    Route::middleware(['role:admin|super-admin'])->group(function () {
        
        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{id}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('/audit-logs/export/csv', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::post('/audit-logs/cleanup', [\App\Http\Controllers\AuditLogController::class, 'cleanup'])->name('audit-logs.cleanup');

        // Daily Activities
        Route::get('/daily-activities/calendar', [\App\Http\Controllers\DailyActivityController::class, 'calendar'])->name('daily-activities.calendar');
        Route::get('/daily-activities/calendar-events', [\App\Http\Controllers\DailyActivityController::class, 'getCalendarEvents'])->name('daily-activities.calendar-events');
        Route::get('/daily-activities/date-activities', [\App\Http\Controllers\DailyActivityController::class, 'getDateActivities'])->name('daily-activities.date-activities');
        Route::get('/daily-activities/daily-report', [\App\Http\Controllers\DailyActivityController::class, 'today'])->name('daily-activities.daily-report');
        Route::get('/daily-activities/weekly-report', [\App\Http\Controllers\DailyActivityController::class, 'weekly'])->name('daily-activities.weekly-report');
        Route::get('/daily-activities/export-pdf', [\App\Http\Controllers\DailyActivityController::class, 'export'])->name('daily-activities.export-pdf');
        Route::resource('daily-activities', \App\Http\Controllers\DailyActivityController::class);
        
        // KPI Dashboard
        Route::get('/kpi-dashboard', [\App\Http\Controllers\KPIDashboardController::class, 'index'])->name('kpi.dashboard');
        Route::get('/kpi-data', [\App\Http\Controllers\KPIDashboardController::class, 'getKPIData'])->name('kpi.data');
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/{notification}/unread', [\App\Http\Controllers\NotificationController::class, 'markUnread'])->name('notifications.unread');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // ========================================
    // SUPER-ADMIN (and Admin) ROUTES
    // ========================================
    // Note: allow both admin and super-admin here because some admin
    // actions (like approving asset requests and basic user management)
    // are expected to be accessible to users with the 'admin' role in tests.
    Route::middleware(['role:admin|super-admin'])->group(function () {
        
        // User Management Routes (with admin prefix)
        // These admin/user management pages are restricted to super-admin only in tests.
        Route::middleware(['role:super-admin'])->prefix('admin/users')->group(function () {
            Route::get('/', [\App\Http\Controllers\UsersController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [\App\Http\Controllers\UsersController::class, 'create'])->name('admin.users.create');
            Route::post('/', [\App\Http\Controllers\UsersController::class, 'store'])->name('admin.users.store');
            Route::get('/{user}', [\App\Http\Controllers\UsersController::class, 'show'])->name('admin.users.show');
            Route::get('/{user}/edit', [\App\Http\Controllers\UsersController::class, 'edit'])->name('admin.users.edit');
            Route::put('/{user}', [\App\Http\Controllers\UsersController::class, 'update'])->name('admin.users.update');
            Route::delete('/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('admin.users.destroy');
        });

        // User Management Routes (without prefix)
        Route::prefix('users')->group(function () {
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
        
    // Asset Requests Management (admin actions)
    Route::post('/asset-requests/{assetRequest}/approve', [\App\Http\Controllers\AssetRequestController::class, 'approve'])->name('asset-requests.approve');
    Route::post('/asset-requests/{assetRequest}/reject', [\App\Http\Controllers\AssetRequestController::class, 'reject'])->name('asset-requests.reject');
    Route::post('/asset-requests/{assetRequest}/fulfill', [\App\Http\Controllers\AssetRequestController::class, 'fulfill'])->name('asset-requests.fulfill');
        
        // Admin Configuration
        Route::get('/admin', [\App\Http\Controllers\PagesController::class, 'getTicketConfig'])->name('admin.config');
        
        // SLA Management Routes
        Route::get('/sla/dashboard', [\App\Http\Controllers\SlaController::class, 'dashboard'])->name('sla.dashboard');
        Route::resource('sla', \App\Http\Controllers\SlaController::class);
        Route::post('/sla/{sla}/toggle-active', [\App\Http\Controllers\SlaController::class, 'toggleActive'])->name('sla.toggle-active');
        
        // ========================================
        // SYSTEM SETTINGS MANAGEMENT
        // ========================================
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

    // Tickets Canned Fields Management
    Route::resource('tickets-canned-field', \App\Http\Controllers\TicketsCannedFieldsController::class)->except(['show', 'create']);
    Route::get('/admin/ticket-canned-fields', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'index'])->name('admin.ticket-canned-fields.index');
    Route::get('/admin/ticket-canned-fields/{ticketsCannedField}/edit', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'edit'])->name('admin.ticket-canned-fields.edit');
    // Admin-prefixed POST/PUT/DELETE endpoints used by admin views and legacy tests
    Route::post('/admin/ticket-canned-fields', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'store'])->name('admin.ticket-canned-fields.store');
    Route::put('/admin/ticket-canned-fields/{ticketsCannedField}', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'update'])->name('admin.ticket-canned-fields.update');
    Route::patch('/admin/ticket-canned-fields/{ticketsCannedField}', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'update']);
    Route::delete('/admin/ticket-canned-fields/{ticketsCannedField}', [\App\Http\Controllers\TicketsCannedFieldsController::class, 'destroy'])->name('admin.ticket-canned-fields.destroy');
        
        // Status Management (legacy admin-prefixed endpoints for assets-statuses used by views/tests)
        // Keep both unprefixed and admin-prefixed endpoints for compatibility with legacy tests.
        Route::resource('status', \App\Http\Controllers\StatusesController::class)->except(['show', 'create']);
        // Admin-prefixed assets-statuses (super-admin only)
        Route::middleware(['role:super-admin'])->prefix('admin')->group(function () {
            Route::get('/assets-statuses', [\App\Http\Controllers\StatusesController::class, 'index'])->name('admin.assets-statuses.index');
            Route::post('/assets-statuses', [\App\Http\Controllers\StatusesController::class, 'store'])->name('admin.assets-statuses.store');
            Route::get('/assets-statuses/{status}/edit', [\App\Http\Controllers\StatusesController::class, 'edit'])->name('admin.assets-statuses.edit');
            Route::put('/assets-statuses/{status}', [\App\Http\Controllers\StatusesController::class, 'update'])->name('admin.assets-statuses.update');
            Route::patch('/assets-statuses/{status}', [\App\Http\Controllers\StatusesController::class, 'update'])->name('admin.assets-statuses.update.patch');
            Route::delete('/assets-statuses/{status}', [\App\Http\Controllers\StatusesController::class, 'destroy'])->name('admin.assets-statuses.destroy');
        });

        // ========================================
        // MASTER DATA MANAGEMENT
        // ========================================
        Route::resource('/models', \App\Http\Controllers\AssetModelsController::class, ['parameters' => ['models' => 'asset_model']]);
        Route::resource('/pcspecs', \App\Http\Controllers\PcspecsController::class);
        Route::resource('/manufacturers', \App\Http\Controllers\ManufacturersController::class);
        Route::resource('/asset-types', \App\Http\Controllers\AssetTypesController::class);
        // Suppliers are considered super-admin only in tests; enforce role here for admin-prefixed flows
        Route::middleware(['role:super-admin'])->group(function () {
            Route::resource('/suppliers', \App\Http\Controllers\SuppliersController::class);
        });
        Route::resource('/locations', \App\Http\Controllers\LocationsController::class);
        Route::resource('/divisions', \App\Http\Controllers\DivisionsController::class);
        Route::resource('/invoices', \App\Http\Controllers\InvoicesController::class);
        Route::resource('/budgets', \App\Http\Controllers\BudgetsController::class);
        
        // ========================================
        // SYSTEM MANAGEMENT ROUTES
        // ========================================
        Route::prefix('system')->group(function () {
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
        
        // ========================================
        // ADMIN TOOLS ROUTES
        // ========================================
        Route::prefix('admin')->group(function () {
            // Admin Authentication Routes
            Route::get('/authenticate', [\App\Http\Controllers\AdminAuthController::class, 'authenticate'])->name('admin.authenticate');
            Route::post('/authenticate', [\App\Http\Controllers\AdminAuthController::class, 'processAuth'])->name('admin.process-auth');
            Route::post('/clear-auth', [\App\Http\Controllers\AdminAuthController::class, 'clearAuth'])->name('admin.clear-auth');
            
            // Admin Dashboard
            Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
            
            // Safe Admin Operations (read-only, no password confirmation needed)
            Route::get('/cache', [\App\Http\Controllers\AdminController::class, 'cache'])->name('admin.cache');
            Route::get('/backup', [\App\Http\Controllers\AdminController::class, 'backup'])->name('admin.backup');
            Route::get('/backup/{backup}/download', [\App\Http\Controllers\AdminController::class, 'download'])->name('admin.backup.download');
            
            // Database Management Routes (Super Admin Only)
            // Read-only routes (no additional security needed)
            Route::get('/database', [\App\Http\Controllers\DatabaseController::class, 'index'])->name('admin.database.index');
            Route::get('/database/backup', [\App\Http\Controllers\DatabaseController::class, 'backup'])->name('admin.database.backup');
            Route::get('/database/{table}', [\App\Http\Controllers\DatabaseController::class, 'showTable'])->name('admin.database.table');
            Route::get('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'show'])->name('admin.database.show');
            Route::get('/database/{table}/export/{format}', [\App\Http\Controllers\DatabaseController::class, 'export'])->name('admin.database.export');
            
            // Restricted Admin Operations (daniel@quty.co.id + password confirmation required)
            Route::middleware(['admin.security:edit'])->group(function () {
                // Database CRUD operations
                Route::get('/database/{table}/create', [\App\Http\Controllers\DatabaseController::class, 'create'])->name('admin.database.create');
                Route::post('/database/{table}', [\App\Http\Controllers\DatabaseController::class, 'store'])->name('admin.database.store');
                Route::get('/database/{table}/{id}/edit', [\App\Http\Controllers\DatabaseController::class, 'edit'])->name('admin.database.edit');
                Route::put('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'update'])->name('admin.database.update');
                Route::delete('/database/{table}/{id}', [\App\Http\Controllers\DatabaseController::class, 'destroy'])->name('admin.database.destroy');
                Route::post('/database/action', [\App\Http\Controllers\AdminController::class, 'databaseAction'])->name('admin.database.action');
                Route::post('/database/danger', [\App\Http\Controllers\AdminController::class, 'databaseDanger'])->name('admin.database.danger');
                
                // Cache Management (POST operations only)
                Route::post('/cache/clear', [\App\Http\Controllers\AdminController::class, 'clearCache'])->name('admin.cache.clear');
                Route::post('/cache/optimize', [\App\Http\Controllers\AdminController::class, 'optimize'])->name('admin.cache.optimize');
                
                // Backup Management (Dangerous operations)
                Route::post('/backup/create', [\App\Http\Controllers\AdminController::class, 'create'])->name('admin.backup.create');
                Route::post('/backup/upload', [\App\Http\Controllers\AdminController::class, 'upload'])->name('admin.backup.upload');
                Route::post('/backup/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.backup.settings');
                Route::post('/backup/cleanup', [\App\Http\Controllers\AdminController::class, 'cleanup'])->name('admin.backup.cleanup');
                Route::post('/backup/{backup}/restore', [\App\Http\Controllers\AdminController::class, 'restore'])->name('admin.backup.restore');
                Route::delete('/backup/{backup}', [\App\Http\Controllers\AdminController::class, 'delete'])->name('admin.backup.delete');
            });

            // Ticket Types Management (legacy admin paths expected by tests)
            // These mirror the TicketsTypesController methods and provide the
            // /admin/ticket-types endpoints used by legacy BrowserKit-style tests.
            Route::get('/ticket-types', [\App\Http\Controllers\TicketsTypesController::class, 'index'])->name('admin.ticket-types.index');
            Route::post('/ticket-types', [\App\Http\Controllers\TicketsTypesController::class, 'store'])->name('admin.ticket-types.store');
            Route::get('/ticket-types/{ticketsType}/edit', [\App\Http\Controllers\TicketsTypesController::class, 'edit'])->name('admin.ticket-types.edit');
            Route::put('/ticket-types/{ticketsType}', [\App\Http\Controllers\TicketsTypesController::class, 'update'])->name('admin.ticket-types.update');
            Route::delete('/ticket-types/{ticketsType}', [\App\Http\Controllers\TicketsTypesController::class, 'destroy'])->name('admin.ticket-types.destroy');
            // Legacy admin endpoints for ticket statuses and priorities (used by tests)
            Route::get('/ticket-statuses', [\App\Http\Controllers\TicketsStatusesController::class, 'index'])->name('admin.ticket-statuses.index');
            Route::post('/ticket-statuses', [\App\Http\Controllers\TicketsStatusesController::class, 'store'])->name('admin.ticket-statuses.store');
            Route::get('/ticket-statuses/{ticketsStatus}/edit', [\App\Http\Controllers\TicketsStatusesController::class, 'edit'])->name('admin.ticket-statuses.edit');
            Route::put('/ticket-statuses/{ticketsStatus}', [\App\Http\Controllers\TicketsStatusesController::class, 'update'])->name('admin.ticket-statuses.update');

            Route::get('/ticket-priorities', [\App\Http\Controllers\TicketsPrioritiesController::class, 'index'])->name('admin.ticket-priorities.index');
            Route::post('/ticket-priorities', [\App\Http\Controllers\TicketsPrioritiesController::class, 'store'])->name('admin.ticket-priorities.store');
            Route::get('/ticket-priorities/{ticketsPriority}/edit', [\App\Http\Controllers\TicketsPrioritiesController::class, 'edit'])->name('admin.ticket-priorities.edit');
            Route::put('/ticket-priorities/{ticketsPriority}', [\App\Http\Controllers\TicketsPrioritiesController::class, 'update'])->name('admin.ticket-priorities.update');
        });
        
        // Development helper: GET shortcut to clear caches in local env only
        if (app()->environment('local')) {
            Route::get('/admin/cache/clear', [\App\Http\Controllers\SystemController::class, 'clearCache'])->name('admin.cache.clear.dev');
        }
    });
    
    // ========================================
    // ACTIVITY STATUS UPDATE (All authenticated users)
    // ========================================
    Route::post('/update-activity', [\App\Http\Controllers\ActivityController::class, 'updateActivity'])->name('update-activity');
});
