<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        // Define model bindings
        $this->registerModelBindings();
        
        // Register the application's routes. In newer Laravel versions
        // route registration happens during `boot()` via $this->routes().
        $this->routes(function () {
            // Use the legacy controller namespace so older route files that
            // reference controllers by short class name continue to work.
            \Illuminate\Support\Facades\Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }
    
    /**
     * Register model bindings for route parameters
     *
     * @return void
     */
    protected function registerModelBindings()
    {
        \Illuminate\Support\Facades\Route::model('ticket', \App\Ticket::class);
        \Illuminate\Support\Facades\Route::model('asset', \App\Asset::class);
        \Illuminate\Support\Facades\Route::model('user', \App\User::class);
        \Illuminate\Support\Facades\Route::model('location', \App\Location::class);
        \Illuminate\Support\Facades\Route::model('division', \App\Division::class);
        \Illuminate\Support\Facades\Route::model('manufacturer', \App\Manufacturer::class);
        \Illuminate\Support\Facades\Route::model('assetModel', \App\AssetModel::class);
        \Illuminate\Support\Facades\Route::model('supplier', \App\Supplier::class);
        \Illuminate\Support\Facades\Route::model('budget', \App\Budget::class);
        \Illuminate\Support\Facades\Route::model('invoice', \App\Invoice::class);
        \Illuminate\Support\Facades\Route::model('dailyActivity', \App\DailyActivity::class);
        \Illuminate\Support\Facades\Route::model('ticketStatus', \App\TicketsStatus::class);
        \Illuminate\Support\Facades\Route::model('ticketPriority', \App\TicketsPriority::class);
        \Illuminate\Support\Facades\Route::model('ticketType', \App\TicketsType::class);
    }
}
