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
        // Register view composers
        $this->registerViewComposers();
    }
    
    /**
     * Register view composers for common form data
     */
    protected function registerViewComposers()
    {
        // General form data composer for admin forms
        view()->composer([
            'admin.users.create',
            'admin.users.edit',
        ], \App\Http\ViewComposers\FormDataComposer::class);
        
        // Ticket-specific composer
        view()->composer([
            'tickets.create',
            'tickets.edit',
            'tickets.show',
        ], \App\Http\ViewComposers\TicketFormComposer::class);
        
        // Asset-specific composer
        view()->composer([
            'assets.create',
            'assets.edit',
            'admin.assets.create',
            'admin.assets.edit',
        ], \App\Http\ViewComposers\AssetFormComposer::class);
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
