<?php

/**
 * Asset Management Routes
 * 
 * All asset-related routes for admin and super-admin users
 * Includes: Assets, Maintenance, QR Codes, Spares
 */

use Illuminate\Support\Facades\Route;

// QR Code Routes (Public access for mobile scanning)
Route::get('/assets/qr/{qrCode}', [\App\Http\Controllers\QRCodeController::class, 'showAssetByQR'])->name('assets.qr');

Route::middleware(['web', 'auth', 'role:admin|super-admin'])->group(function () {
    
    // ========================================
    // ASSET CRUD ROUTES
    // ========================================
    Route::get('/assets/categories', [\App\Http\Controllers\InventoryController::class, 'categories'])->name('assets.categories');
    Route::get('/assets/requests', [\App\Http\Controllers\InventoryController::class, 'requests'])->name('assets.requests');
    Route::get('/assets/scan-qr', [\App\Http\Controllers\AssetsController::class, 'scanQR'])->name('assets.scan-qr');
    Route::post('/assets/process-scan', [\App\Http\Controllers\AssetsController::class, 'processScan'])->name('assets.process-scan');
    Route::get('/assets/my-assets', [\App\Http\Controllers\AssetsController::class, 'myAssets'])->name('assets.my-assets');
    Route::get('/assets/{asset}/qr-code', [\App\Http\Controllers\AssetsController::class, 'generateQR'])->name('assets.qr-code');
    Route::get('/assets/{asset}/qr-download', [\App\Http\Controllers\AssetsController::class, 'downloadQR'])->name('assets.qr-download');
    Route::get('/assets/{asset}/history', [\App\Http\Controllers\AssetsController::class, 'show'])->name('assets.history');
    Route::get('/assets/{asset}/ticket-history', [\App\Http\Controllers\AssetsController::class, 'show'])->name('assets.ticket-history');
    Route::get('/assets/{asset}/move', [\App\Http\Controllers\AssetsController::class, 'movements'])->name('assets.move');
    Route::get('/assets/{asset}/movements', [\App\Http\Controllers\AssetsController::class, 'movements'])->name('assets.movements');
    Route::post('/assets/{asset}/assign', [\App\Http\Controllers\AssetsController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/unassign', [\App\Http\Controllers\AssetsController::class, 'unassign'])->name('assets.unassign');
    
    // ========================================
    // EXPORT/IMPORT/PRINT ROUTES
    // ========================================
    Route::get('/assets/export', [\App\Http\Controllers\AssetsController::class, 'export'])->name('assets.export');
    Route::get('/assets/import-form', [\App\Http\Controllers\AssetsController::class, 'importForm'])->name('assets.import-form');
    Route::post('/assets/import', [\App\Http\Controllers\AssetsController::class, 'import'])->name('assets.import');
    Route::get('/assets/import-errors-download', [\App\Http\Controllers\AssetsController::class, 'downloadImportErrors'])->name('assets.import-errors-download');
    Route::get('/assets/download-template', [\App\Http\Controllers\AssetsController::class, 'downloadTemplate'])->name('assets.download-template');
    Route::get('/assets/{asset}/print', [\App\Http\Controllers\AssetsController::class, 'print'])->name('assets.print');
    
    // ========================================
    // BULK OPERATIONS
    // ========================================
    Route::post('/assets/bulk-qr-codes', [\App\Http\Controllers\QRCodeController::class, 'bulkGenerateQRCodes'])->name('assets.bulk-qr-codes');
    Route::post('/assets/{asset}/change-status', [\App\Http\Controllers\InventoryController::class, 'changeStatus'])->name('assets.change-status');
    
    // Assets Index
    Route::get('/assets', [\App\Http\Controllers\AssetsController::class, 'index'])->name('assets.index');
    
    // Assets Resource Routes (except index)
    Route::resource('assets', \App\Http\Controllers\AssetsController::class)->except(['index']);

    // ========================================
    // ASSET MAINTENANCE LOGS
    // ========================================
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
    
    // ========================================
    // ASSET MAINTENANCE (Legacy)
    // ========================================
    Route::get('/asset-maintenance', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('asset-maintenance.index');
    Route::get('/asset-maintenance/analytics', [\App\Http\Controllers\AssetMaintenanceController::class, 'analytics'])->name('asset-maintenance.analytics');
    Route::get('/asset-maintenance/{asset}', [\App\Http\Controllers\AssetMaintenanceController::class, 'show'])->name('asset-maintenance.show');

    // ========================================
    // SPARES MANAGEMENT
    // ========================================
    Route::get('/spares', [\App\Http\Controllers\SparesController::class, 'index'])->name('spares.index');
    
    // ========================================
    // FILE ATTACHMENTS
    // ========================================
    Route::post('/attachments/upload', [\App\Http\Controllers\AttachmentController::class, 'upload'])->name('attachments.upload');
    Route::post('/attachments/bulk-upload', [\App\Http\Controllers\AttachmentController::class, 'bulkUpload'])->name('attachments.bulk-upload');
    Route::get('/attachments', [\App\Http\Controllers\AttachmentController::class, 'index'])->name('attachments.index');
    Route::get('/attachments/{id}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('/attachments/{id}', [\App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');
});
