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
    // SUPER-ADMIN ONLY ROUTES
    // ========================================
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
        
        // Status Management
        Route::resource('status', \App\Http\Controllers\StatusesController::class)->except(['show', 'create']);

        // ========================================
        // MASTER DATA MANAGEMENT
        // ========================================
        Route::resource('/models', \App\Http\Controllers\AssetModelsController::class, ['parameters' => ['models' => 'asset_model']]);
        Route::resource('/pcspecs', \App\Http\Controllers\PcspecsController::class);
        Route::resource('/manufacturers', \App\Http\Controllers\ManufacturersController::class);
        Route::resource('/asset-types', \App\Http\Controllers\AssetTypesController::class);
        Route::resource('/suppliers', \App\Http\Controllers\SuppliersController::class);
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
            
            // Database Management (Read-only)
            Route::get('/database', [\App\Http\Controllers\AdminController::class, 'database'])->name('admin.database');
            
            // Restricted Admin Operations (daniel@quty.co.id + password confirmation required)
            Route::middleware(['admin.security:edit'])->group(function () {
                Route::post('/backup/upload', [\App\Http\Controllers\AdminController::class, 'upload'])->name('admin.backup.upload');
            });
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
