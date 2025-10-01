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

            // Register the application's routes. In older Laravel versions the
            // RouteServiceProvider used a `map()` method; modern frameworks expect
            // route registration to happen during `boot()` via $this->routes().
            $this->routes(function () {
                // Use the legacy controller namespace so older route files that
                // reference controllers by short class name continue to work.
                \Illuminate\Support\Facades\Route::namespace($this->namespace)->group(function () {
                    require base_path('routes/web.php');
                });
            });
        }
}
