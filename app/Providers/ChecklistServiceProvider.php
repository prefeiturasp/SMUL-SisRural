<?php

namespace App\Providers;

use App\Services\ChecklistNotificationService;
use Illuminate\Support\ServiceProvider;




class ChecklistServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(ChecklistNotificationService::class, function ($app) {
            return new ChecklistNotificationService($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
