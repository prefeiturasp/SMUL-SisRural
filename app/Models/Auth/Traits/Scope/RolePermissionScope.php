<?php

namespace App\Models\Auth\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RolePermissionScope implements Scope
{
    /**
     * Aplica um escopo no Role (App\Models\Auth\Role)
     *
     * Serve para retornar apenas as "roles" que podem ser "visualizadas" pelo usuário
     *
     * Isso é utilizado na hora do cadastro/edição do usuário.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || \Auth::user()) {
            // $user = (session('auth_user_id')) ? User::withoutGlobalScope(UserPermissionScope::class)->findOrFail(session('auth_user_id')) : \Auth::user();
            $user = AppHelper::getSessionOrAuthUser(); //true

            $auth_user_role = session('auth_user_role') ? session('auth_user_role') : $user->roles->first()->name;

            if ($auth_user_role === config('access.users.app_admin_role')) {
                $builder->where('name', config('access.users.app_admin_role'))
                    ->orWhere('name', config('access.users.domain_role'));
            } elseif ($auth_user_role  === config('access.users.domain_role')) {
                $builder->where('name', config('access.users.domain_role'))
                    ->orWhere('name', config('access.users.operational_unit_role'))
                    ->orWhere('name', config('access.users.technician_role'));
            } elseif ($auth_user_role  === config('access.users.operational_unit_role')) {
                $builder->where('name', config('access.users.operational_unit_role'))
                    ->orWhere('name', config('access.users.technician_role'));
            } elseif ($auth_user_role  === config('access.users.technician_role')) {
                $builder->where('name', config('access.users.technician_role'));
            }
        }
    }
}
