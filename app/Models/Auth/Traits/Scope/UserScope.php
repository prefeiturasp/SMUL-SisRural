<?php

namespace App\Models\Auth\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Models\Auth\User;

/**
 * Class UserScope.
 */
trait UserScope
{
    /**
     * @param $query
     * @param bool $confirmed
     *
     * @return mixed
     */
    public function scopeConfirmed($query, $confirmed = true)
    {
        return $query->where('confirmed', $confirmed);
    }

    /**
     * @param $query
     * @param bool $status
     *
     * @return mixed
     */
    public function scopeActive($query, $status = true)
    {
        return $query->where('active', $status);
    }

    /**
     * Escopo utilizado para técnicos conseguirem enxergar suas unidades operacionais.
     *
     * Por padrão o técnico esta abaixo de uma unidade operacional, consequentemente ele não enxerga as unidades operacionais horizontais do "pai".
     *
     * Com esse escopo ele passa a enxergar todas as unidades operacionais horizontais das "unidades operacionais" que ele faz parte.
     */
    public function scopeUnidadesOperacionaisComTecnicos($query)
    {
        if (!session('auth_user_id') && !\Auth::user()) {
            return;
        }

        //->with() porque estava retornando todas "roles" e "unidadesOperacionais", como se o belongsToMany ignorasse o relacionamento.
        // $user = (session('auth_user_id')) ? User::withoutGlobalScope(UserPermissionScope::class)->with(['unidadesOperacionais', 'roles'])->findOrFail(session('auth_user_id')) : \Auth::user();
        $user = AppHelper::getSessionOrAuthUser(); //true

        return $query->where(function ($q) use ($user) {
            $q->whereHas('roles', function ($q2) {
                $q2->where('name', config('access.users.technician_role'))
                    ->orWhere('name', config('access.users.operational_unit_role'));
            });

            $q->whereHas('unidadesOperacionais', function ($q2) use ($user) {
                $q2->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
            });
        });
    }
}
