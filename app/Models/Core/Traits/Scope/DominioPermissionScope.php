<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Helpers\General\CacheHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DominioPermissionScope implements Scope
{
    /**
     * Libera domínios que o usuário possuí
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

            //Usuário do tipo Domínios
            if ($user->can('view same domains')) {
                $builder->where('dominios.id', CacheHelper::singleDominio($user));
            }

            //Usuário do tipo Unidade Operacional ou Técnico
            if ($user->can('view same operational units')) {
                $builder->where('dominios.id', CacheHelper::singleDominio($user));
            }
        }
    }
}
