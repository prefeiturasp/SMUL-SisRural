<?php

namespace App\Providers;

use App\Models\Auth\Traits\Policy\UserPolicy;
use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Models\Core\ChecklistCategoriaModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\DadoModel;
use App\Models\Core\DominioModel;
use App\Models\Core\PerguntaModel;
use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\RegiaoModel;
use App\Models\Core\RespostaModel;
use App\Models\Core\SobreModel;
use App\Models\Core\SoloCategoriaModel;
use App\Models\Core\Traits\Policy\CadernoPolicy;
use App\Models\Core\Traits\Policy\DominioPolicy;
use App\Models\Core\Traits\Policy\ProdutorPolicy;
use App\Models\Core\Traits\Policy\TemplatePolicy;
use App\Models\Core\Traits\Policy\UnidadeOperacionalPolicy;
use App\Models\Core\Traits\Policy\UnidadeProdutivaPolicy;
use App\Models\Core\UnidadeOperacionalModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Models\Core\TemplateModel;
use App\Models\Core\TemplateRespostaModel;
use App\Models\Core\TermosDeUsoModel;
use App\Models\Core\Traits\Policy\ChecklistCategoriasPolicy;
use App\Models\Core\Traits\Policy\ChecklistPerguntaPolicy;
use App\Models\Core\Traits\Policy\ChecklistPolicy;
use App\Models\Core\Traits\Policy\ChecklistUnidadeProdutivaPolicy;
use App\Models\Core\Traits\Policy\DadoPolicy;
use App\Models\Core\Traits\Policy\PerguntaPolicy;
use App\Models\Core\Traits\Policy\PlanoAcaoHistoricoPolicy;
use App\Models\Core\Traits\Policy\PlanoAcaoItemHistoricoPolicy;
use App\Models\Core\Traits\Policy\PlanoAcaoItemPolicy;
use App\Models\Core\Traits\Policy\PlanoAcaoPolicy;
use App\Models\Core\Traits\Policy\RegiaoPolicy;
use App\Models\Core\Traits\Policy\RespostaPolicy;
use App\Models\Core\Traits\Policy\SobrePolicy;
use App\Models\Core\Traits\Policy\SoloCategoriaPolicy;
use App\Models\Core\Traits\Policy\TemplateRespostaPolicy;
use App\Models\Core\Traits\Policy\TermosDeUsoPolicy;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        UnidadeOperacionalModel::class => UnidadeOperacionalPolicy::class,
        DominioModel::class => DominioPolicy::class,
        UnidadeProdutivaModel::class => UnidadeProdutivaPolicy::class,
        ProdutorModel::class => ProdutorPolicy::class,
        TemplateModel::class => TemplatePolicy::class,
        CadernoModel::class => CadernoPolicy::class,
        ChecklistUnidadeProdutivaModel::class => ChecklistUnidadeProdutivaPolicy::class,
        ChecklistModel::class => ChecklistPolicy::class,
        PlanoAcaoModel::class => PlanoAcaoPolicy::class,
        PlanoAcaoHistoricoModel::class => PlanoAcaoHistoricoPolicy::class,
        PlanoAcaoItemModel::class => PlanoAcaoItemPolicy::class,
        PlanoAcaoItemHistoricoModel::class => PlanoAcaoItemHistoricoPolicy::class,
        TemplateRespostaModel::class => TemplateRespostaPolicy::class,
        RespostaModel::class => RespostaPolicy::class,
        PerguntaModel::class => PerguntaPolicy::class,
        ChecklistCategoriaModel::class => ChecklistCategoriasPolicy::class,
        ChecklistPerguntaModel::class => ChecklistPerguntaPolicy::class,
        TermosDeUsoModel::class => TermosDeUsoPolicy::class,
        RegiaoModel::class => RegiaoPolicy::class,
        DadoModel::class => DadoPolicy::class,
        SobreModel::class => SobrePolicy::class,
        SoloCategoriaModel::class => SoloCategoriaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()

        Gate::before(function ($user, $permission) {
            return $user->hasRole(config('access.users.admin_role')) && $permission != 'report restricted' ? true : null;
        });

        Passport::routes();
    }
}
