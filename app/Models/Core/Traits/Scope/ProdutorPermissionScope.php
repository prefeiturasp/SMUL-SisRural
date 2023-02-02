<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProdutorPermissionScope implements Scope
{
    /**
     * Libera produtores que estão dentro da minha abrangência.
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
            $user = AppHelper::getSessionOrAuthUser();

            if ($user->isAdmin() || $user->isAdminLOP()) return;

            if ($user->can('view same domain farmers') || $user->can('view same operational units farmers')) {
                $builder->has('unidadesProdutivasScoped')
                ->orDoesntHave('unidadesProdutivasNS');
            }
        }
    }
}
