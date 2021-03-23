<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ChecklistUnidadeProdutivaPermissionScope implements Scope
{
    /**
     *
     * Libera formulários dentro da abrangência do usuário logado
     *
     * a) Formulários que foram criados para unidades produtivas que estão dentro da minha abrangência.
     * b) Formulários que possuem "templates" que foram liberados para mim "analisar".
     * c) Formulários que eu sou dono (dono do template). Essa regra é para usuários do tipo "Domínio".
     *
     * ------
     *
     * Estudo de caso mais complexo dentro do sistema:
     *
     * Domínio: "Dominio RJ"
     *
     * Aplicar: Aplica "Técnico Ater" e "Unidade Operacional RJ"
     *
     * Analisa: Aprova "Técnico PSA"
     *
     * Usuários: Técnico Ater, Técnico PSA, Unidade Operacional ATER, Unidade Operacional RJ e Domínio RJ
     *
     * Formulário "Formulário RJ" do domínio "Dominio RJ" liberado para "Técnico Ater" e "Unid. Operacional RJ" aplicar. Liberado para "Técnico PSA" analisar.
     *
     * - Apliquei como Técnico ATER em minha abrangência (São Paulo), Técnico Ater enxerga, Unidade Operacional RJ não enxerga, Técnico PSA enxerga, Dominio RJ enxerga, Unidade Operacional ATER enxerga (não edita)
     * - Apliquei como Unidade Operacional RJ em minha abrangência (Rio de Janeiro), Técnico Ater não enxerga, Unidade Operacional RJ enxerga, Técnico PSA enxerga, Dominio RJ enxerga, Unidade Operacional ATER não enxerga
     * - Tecnico e Unidade Operacional, todos formulários criados em sua abrangência + formulários que ele pode analisar
     * - Domínio, todos formulários criados em sua abrangência + formulários que ele pode analisar + formulários criados por ele com permissão para Técnicos/Unidades Operacionais fora de sua abrangência aplicarem
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

                //Permissão através da Abrangência (Mesma lógica do CadernoPermissionScope)
                $builder->where(function ($queryUnid) {
                    $queryUnid->whereHas('unidadeProdutivaScoped');

                    //Permissão através dos Formulários que o usuário pode enxergar (formulários que ele criou ou que foram liberados para ele ver)
                    //Se eu vejo a unidade produtiva, posso ver qualquer formulário, então não pode ficar aqui
                    //No final, não sei porque que foi adicionado essa linha nos testes, antes ele estava la no final (deixei comentado p/ lembrança)
                    //$queryUnid->whereHas('checklistScoped');
                });

                //Permissão através dos Formulários que podem ser Analisados pelo usuário, ignorando as permissões básicas do Template do Formulário (PermissionScope)
                $builder->orWhereHas('checklist', function ($q) use ($user) {
                    $q->withoutGlobalScopes();

                    $q->where('fl_fluxo_aprovacao', 1);

                    $q->whereHas('usuariosAprovacao', function ($q2)  use ($user) {
                        $q2->where('user_id', $user->id);
                    });
                });

                //$queryUnid->orWhereHas('checklistScoped');
            }
        }
    }
}
