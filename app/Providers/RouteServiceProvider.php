<?php

namespace App\Providers;

use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        // Register route model bindings

        $this->bind('userAll', function ($value) {
            $user = new User;

            return User::withoutGlobalScopes()->withTrashed()->where($user->getRouteKeyName(), $value)->first();
        });

        // Allow this to select all users regardless of status
        $this->bind('user', function ($value) {
            $user = new User;

            return User::withTrashed()->where($user->getRouteKeyName(), $value)->first();
        });

        //Para todas as rotas que tiverem "planoAcao", retorna também os pdas removidos
        $this->bind('planoAcao', function ($value) {
            return PlanoAcaoModel::withTrashed()->findOrFail($value);
        });

        //Para todas as rotas que tiverem "planoAcaoColetivo", retorna também os pdas removidos
        $this->bind('planoAcaoColetivo', function ($value) {
            return PlanoAcaoModel::withTrashed()->findOrFail($value);
        });

        //Para todas as rotas que tiverem "checklistUnidadeProdutiva", retorna também os formulários aplicados removidos
        $this->bind('checklistUnidadeProdutiva', function ($value) {
            return ChecklistUnidadeProdutivaModel::withTrashed()->findOrFail($value);
        });

        //Para todas as rotas que tiverem "caderno", retorna também os cadernos removidos
        $this->bind('caderno', function ($value) {
            return CadernoModel::withTrashed()->findOrFail($value);
        });

        //Para todas as rotas que tiverem "caderno", retorna também os cadernos removidos
        $this->bind('produtorSemUnidade', function ($value) {
            return ProdutorModel::withTrashed()->withoutGlobalScopes()->findOrFail($value);
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        // For the 'Login As' functionality from the 404labfr/laravel-impersonate package
        Route::middleware('web')
            ->group(function (Router $router) {
                $router->impersonate();
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
