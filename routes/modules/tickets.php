<?php

/**
 * Ticket Management Routes
 * 
 * All ticket-related routes for admin and super-admin users
 * Controllers refactored into specialized classes:
 * - TicketController: Core CRUD operations
 * - TicketAssignmentController: Assignment operations
 * - TicketStatusController: Status management
 * - TicketTimerController: Time tracking
 * - UserTicketController: User portal (see user-portal.php)
 */

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin|super-admin'])->group(function () {
    
    // ========================================
    // MAIN CRUD ROUTES (TicketController)
    // ========================================
    Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::get('/tickets/unassigned', [\App\Http\Controllers\TicketController::class, 'unassigned'])->name('tickets.unassigned');
    Route::get('/tickets/overdue', [\App\Http\Controllers\TicketController::class, 'overdue'])->name('tickets.overdue');
    Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [\App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
    Route::patch('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');
    
    // ========================================
    // ASSIGNMENT ROUTES (TicketAssignmentController)
    // ========================================
    Route::post('/tickets/{ticket}/self-assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'selfAssign'])->name('tickets.self-assign');
    Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/force-assign', [\App\Http\Controllers\Tickets\TicketAssignmentController::class, 'forceAssign'])->name('tickets.force-assign');
    
    // ========================================
    // STATUS MANAGEMENT ROUTES (TicketStatusController)
    // ========================================
    Route::post('/tickets/{ticket}/complete', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'complete'])->name('tickets.complete');
    Route::post('/tickets/{ticket}/update-status', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('/tickets/{ticket}/complete-with-resolution', [\App\Http\Controllers\Tickets\TicketStatusController::class, 'completeWithResolution'])->name('tickets.complete-with-resolution');
    
    // ========================================
    // USER INTERACTION ROUTES (UserTicketController)
    // ========================================
    Route::post('/tickets/{ticket}/add-response', [\App\Http\Controllers\Tickets\UserTicketController::class, 'addResponse'])->name('tickets.add-response');
    
    // ========================================
    // TIME TRACKING ROUTES (TicketTimerController)
    // ========================================
    Route::post('/tickets/{ticket}/start-timer', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'startTimer'])->name('tickets.start-timer');
    Route::post('/tickets/{ticket}/stop-timer', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'stopTimer'])->name('tickets.stop-timer');
    Route::get('/tickets/{ticket}/timer-status', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'getTimerStatus'])->name('tickets.timer-status');
    Route::get('/tickets/{ticket}/work-summary', [\App\Http\Controllers\Tickets\TicketTimerController::class, 'getWorkSummary'])->name('tickets.work-summary');

    // ========================================
    // BULK OPERATIONS
    // ========================================
    Route::post('/tickets/bulk/assign', [\App\Http\Controllers\BulkOperationController::class, 'bulkAssign'])->name('tickets.bulk.assign');
    Route::post('/tickets/bulk/update-status', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdateStatus'])->name('tickets.bulk.update-status');
    Route::post('/tickets/bulk/update-priority', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdatePriority'])->name('tickets.bulk.update-priority');
    Route::post('/tickets/bulk/update-category', [\App\Http\Controllers\BulkOperationController::class, 'bulkUpdateCategory'])->name('tickets.bulk.update-category');
    Route::post('/tickets/bulk/delete', [\App\Http\Controllers\BulkOperationController::class, 'bulkDelete'])->name('tickets.bulk.delete');
    Route::get('/tickets/bulk/options', [\App\Http\Controllers\BulkOperationController::class, 'getBulkOptions'])->name('tickets.bulk.options');

    // ========================================
    // EXPORT/PRINT ROUTES
    // ========================================
    Route::get('/tickets/export', [\App\Http\Controllers\TicketController::class, 'export'])->name('tickets.export');
    Route::get('/tickets/{ticket}/print', [\App\Http\Controllers\TicketController::class, 'print'])->name('tickets.print');
});

// ========================================
// ENHANCED TICKET CREATION (Multi-role access)
// ========================================
Route::middleware(['web', 'auth', 'role:management|admin|super-admin'])->group(function () {
    Route::get('/tickets/create-with-asset', [\App\Http\Controllers\TicketController::class, 'createWithAsset'])->name('tickets.create-with-asset');
});
