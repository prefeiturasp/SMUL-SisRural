<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Helpers\General\CacheHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnidadeOperacionalPermissionScope implements Scope
{
    /**
     * Libera unidades operacionais:
     *
     * a) que fazem parte do meu domínio (Usuário do tipo Domínio) ou
     * b) que fazem parte das minhas unidades operacionais (Usuário do tipo Técnico/Unidade Operacional)
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || Auth::user()) {
            $user = AppHelper::getSessionOrAuthUser(); //true

            if ($user->isAdmin() || $user->isAdminLOP()) return;

            //Domínio
            if ($user->can('view same domain operational units')) {
                $builder->where('dominio_id', CacheHelper::singleDominio($user));
            }

            //Unidade Operacional e Técnico
            if ($user->can('view same operational units')) {
                $builder->whereIn('unidade_operacionais.id', $this->unidadesOperacionais($user));
            }
        }
    }

    /**
     * Otimização p/ sync mobile
     */
    private function unidadesOperacionais($user)
    {
        return \Cache::store('array')->remember("UnidadeOperacionalPermissionScope-unidadesOperacionais-{$user->id}", 60, function () use ($user) {
            return $user->unidadesOperacionais()->withoutGlobalScopes()->pluck('unidade_operacional_id');
        });
    }
}
