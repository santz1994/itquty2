<?php

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
    \Illuminate\Support\Facades\Route::get('/', function () {
        return redirect('/home');
    });
}

// Always register admin index routes for priorities, statuses, and types
\Illuminate\Support\Facades\Route::middleware(['web'])->group(function () {
    \Illuminate\Support\Facades\Route::get('/admin/ticket-priorities', \App\Http\Controllers\TicketsPrioritiesController::class . '@index')->name('admin.ticket-priorities.index');
    \Illuminate\Support\Facades\Route::get('/admin/ticket-statuses', \App\Http\Controllers\TicketsStatusesController::class . '@index')->name('admin.ticket-statuses.index');
    \Illuminate\Support\Facades\Route::get('/admin/ticket-types', \App\Http\Controllers\TicketsTypesController::class . '@index')->name('admin.ticket-types.index');
    \Illuminate\Support\Facades\Route::get('/admin/assets-statuses', \App\Http\Controllers\StatusesController::class . '@index')->name('admin.assets-statuses.index');
});
