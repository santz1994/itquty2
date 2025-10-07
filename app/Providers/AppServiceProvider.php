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
    }
}
