<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SlackNotifier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SlackNotifier::class, function ($app) {
            return new SlackNotifier();
        });
        
        // Bind Asset Repository
        $this->app->bind(
            \App\Repositories\Assets\AssetRepositoryInterface::class,
            \App\Repositories\Assets\AssetRepository::class
        );
        
        // Bind Ticket Repository
        $this->app->bind(
            \App\Repositories\Tickets\TicketRepositoryInterface::class,
            \App\Repositories\Tickets\TicketRepository::class
        );
        
        // Bind User Repository
        $this->app->bind(
            \App\Repositories\Users\UserRepositoryInterface::class,
            \App\Repositories\Users\UserRepository::class
        );
        
        // Bind Services
        $this->app->singleton(\App\Services\TicketService::class);
        $this->app->singleton(\App\Services\AssetService::class);
        $this->app->singleton(\App\Services\UserService::class);
    }
}
