<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnidadeProdutivaPermissionScope implements Scope
{
    /**
     * Libera unidades produtivas que estão dentro da minha abrangência.
     *
     * A abrangência é definida pelas unidades operacionais que o usuário faz parte.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || Auth::user()) {
            //$user = (session('auth_user_id')) ? User::withoutGlobalScope(UserPermissionScope::class)->findOrFail(session('auth_user_id')) : Auth::user();
            $user = AppHelper::getSessionOrAuthUser();

            if ($user->isAdmin() || $user->isAdminLOP()) return;

            //Usuario do tipo Domínio, Unidade Operacional ou Técnico
            if ($user->can('view same domain productive units') || $user->can('view same operational units productive units')) {
                $builder->whereHas('unidadesOperacionaisScoped');
            }

            //Usuário que cria o registro pode ver mesmo que ele esteja fora do seu domínio.
            $builder->orWhere("unidade_produtivas.owner_id", $user->id);
        }
    }
}
