<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PlanoAcaoPermissionScope implements Scope
{
    /**
     *
     * Libera planos de ação
     *
     * - Individual
     *      a) Abrangência do usuário logado
     *
     * - Formulário
     *      a) PDAS originados de formulários que eu tenho acesso
     *
     * - Coletivo
     *      a) Abrangência do usuário logado
     *      b) PDAs que foram criados por usuários que posso enxergar (usuários dentro da minha unidade operacional)
     *      c) regra especifica para edição (hierarquia de usuários - ver em PlanoAcaoPolicy)
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || Auth::user()) {
            $user = AppHelper::getSessionOrAuthUser();

            if ($user->isAdmin() || $user->isAdminLOP()) return;

            //Unidade Operacional/Técnico/Domínio - Limita só unidades produtivas que o usuário tem permissão para ver
            if ($user->can('view same operational units farmers') || $user->can('view same domain farmers')) {
                /**
                 * Individual / Formulário
                 */
                $builder->where(function ($queryIndividual) {
                    $queryIndividual->where('plano_acoes.fl_coletivo', 0);

                    $queryIndividual->where(function ($subQueryIndividual) {
                        //Individual / Coletivo - Permissão através da Abrangência (Mesma lógica do CadernoPermissionScope)
                        $subQueryIndividual->whereHas('unidadeProdutivaScoped');

                        //Formulário - Permissão caso tenha acesso ao Formulário Aplicado
                        $subQueryIndividual->orWhereHas('checklistUnidadeProdutivaScoped');
                    });
                });

                /**
                 * Coletivo
                 */
                $builder->orWhere(function ($queryColetivo) {
                    $queryColetivo->where('plano_acoes.fl_coletivo', 1);

                    $queryColetivo->where(function ($subQueryColetivo) {
                        //Coletivo - Que foram criados por mim ou usuários que tenho permissão p/ visualizar (dentro da minha Unidade Operacional)
                        $subQueryColetivo->whereHas('usuarioScoped');

                        //Coletivo - Filhos são enxergados por causa da relação com a unidade produtiva
                        $subQueryColetivo->orWhereHas('planoAcaoFilhosScoped', function ($q) {
                            $q->whereHas('unidadeProdutivaScoped');
                        });

                        //Coletivo - Pais são enxergados por causa da relação com a unidade produtiva
                        $subQueryColetivo->orWhere(function ($q) {
                            $q->whereNotNull('plano_acao_coletivo_id');
                            $q->whereHas('planoAcaoPaiScoped', function ($qq) {
                                //Replica regra do Coletivo
                                $qq->whereHas('usuarioScoped');
                                $qq->orWhereHas('planoAcaoFilhosScoped', function ($qqq) {
                                    $qqq->whereHas('unidadeProdutivaScoped');
                                });
                            });
                        });
                    });
                });
            }
        }
    }
}
