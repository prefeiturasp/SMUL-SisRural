<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CadernoPermissionScope implements Scope
{
    /**
     * Libera cadernos de campo dentro da abrangência do usuário logado.
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
                $builder->whereHas('unidadeProdutivaScoped', function ($q) use ($user) {
                    $q->whereHas('unidadesOperacionaisScoped');

                    //OU Usuário que cria a unidade produtiva/produtor pode ver o caderno de campo... mesmo que ele esteja fora do seu domínio. (Resolve um registro criado pelo mobile na abrangência errada).
                    $q->orWhere("unidade_produtivas.owner_id", $user->id);
                });
            }
        }
    }
}
