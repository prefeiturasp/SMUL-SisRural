<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use DB;
use Log;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Observers\ChecklistUnidadeProdutivaObserver;
use App\Observers\PlanoAcaoObserver;
use Illuminate\Support\Facades\Schema;
use Validator;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Sets third party service providers that are only needed on local/testing environments
        if ($this->app->environment() !== 'production') {
            /**
             * Loader for registering facades.
             */
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();

            // Load third party local aliases
            $loader->alias('Debugbar', \Barryvdh\Debugbar\Facade::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->environment() === 'production') {
            error_reporting(0);
        }

        // Schema::defaultStringLength(191);

        /*
         * Application locale defaults for various components
         *
         * These will be overridden by LocaleMiddleware if the session local is set
         */

        // setLocale for php. Enables ->formatLocalized() with localized values for dates
        setlocale(LC_TIME, config('app.locale_php'));

        // setLocale to use Carbon source locales. Enables diffForHumans() localized
        Carbon::setLocale(config('app.locale'));

        /*
         * Set the session variable for whether or not the app is using RTL support
         * For use in the blade directive in BladeServiceProvider
         */
        if (!app()->runningInConsole()) {
            if (config('locale.languages')[config('app.locale')][2]) {
                session(['lang-rtl' => true]);
            } else {
                session()->forget('lang-rtl');
            }
        }

        // Force SSL in production
        /*if ($this->app->environment() === 'production') {
            URL::forceScheme('https');
        }*/

        // Set the default template for Pagination to use the included Bootstrap 4 template
        \Illuminate\Pagination\AbstractPaginator::defaultView('pagination::bootstrap-4');
        \Illuminate\Pagination\AbstractPaginator::defaultSimpleView('pagination::simple-bootstrap-4');

        // Custom Blade Directives

        /*
         * The block of code inside this directive indicates
         * the project is currently running in read only mode.
         */
        Blade::if('readonly', function () {
            return config('app.read_only');
        });

        Blade::if('admin', function () {
            if (auth()->user() && auth()->user()->isAdmin()) {
                return 1;
            }

            return 0;
        });

        Blade::if('adminLOP', function () {
            if (auth()->user() && auth()->user()->isAdminLOP()) {
                return 1;
            }

            return 0;
        });

        /*
         * The block of code inside this directive indicates
         * the chosen language requests RTL support.
         */
        Blade::if('langrtl', function ($session_identifier = 'lang-rtl') {
            return session()->has($session_identifier);
        });

        Blade::component('backend.components.card-ater.index', 'cardater');
        Blade::component('backend.components.card-square.index', 'cardsquare');
        Blade::component('backend.components.card-add-view.index', 'cardaddview');
        Blade::component('backend.components.modal.index', 'modal');
        Blade::component('backend.components.loading.index', 'loading');

        // log de queries
        // DB::listen(function ($query) {
        //     Log::info(
        //         $query->sql,
        //         $query->bindings,
        //         $query->time
        //     );
        // });

        Validator::extend('formula', 'App\Rules\FormulaRule@passes');

        ChecklistUnidadeProdutivaModel::observe(ChecklistUnidadeProdutivaObserver::class);
        PlanoAcaoModel::observe(PlanoAcaoObserver::class);
    }
}
