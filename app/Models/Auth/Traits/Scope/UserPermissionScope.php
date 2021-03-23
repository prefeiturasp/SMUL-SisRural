<?php

namespace App\Models\Auth\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserPermissionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || \Auth::user()) {
            $user = AppHelper::getSessionOrAuthUser(); //true

            $auth_user_role = session('auth_user_role') ? session('auth_user_role') : $user->roles->first()->name;

            if ($auth_user_role === config('access.users.app_admin_role')) {
                $builder->whereHas('roles', function ($q) {
                    $q->where('name', '<>', config('access.users.admin_role'));
                });
            } elseif ($auth_user_role === config('access.users.domain_role')) {
                $builder->where(function ($q) use ($user) {
                    $q->whereHas('roles', function ($q2) {
                        $q2->where('name', config('access.users.domain_role'));
                    });
                    //AQUI
                    $q->whereHas('dominios', function ($q2) use ($user) {
                        $q2->whereIn('dominio_id', $user->dominios->pluck('id'));
                    });
                })->orWhere(function ($q) use ($user) {
                    $q->whereHas('roles', function ($q2) {
                        $q2->where('name', config('access.users.operational_unit_role'));
                    });

                    $q->where(function ($q2) use ($user) {
                        $q2->whereHas('unidadesOperacionais', function ($q3) use ($user) {
                            //AQUI
                            $userDomains = $user->dominios()->get();
                            if ($userDomains[0]) {
                                $q3->whereIn('unidade_operacional_id', $userDomains[0]->unidadesOperacionais->pluck('id'));
                                unset($userDomains[0]);
                            }
                            foreach ($userDomains as $userDomain) {
                                $q3->orWhereIn('unidade_operacional_id', $userDomain->unidadesOperacionais->pluck('id'));
                            }
                        });
                    });
                })->orWhere(function ($q) use ($user) {
                    $q->whereHas('roles', function ($q2) use ($user) {
                        $q2->where('name', config('access.users.technician_role'));
                    });

                    $q->where(function ($q2) use ($user) {
                        $q2->whereHas('unidadesOperacionais', function ($q3) use ($user) {
                            //AQUI
                            $userDomains = $user->dominios()->get();
                            if ($userDomains[0]) {
                                $q3->whereIn('unidade_operacional_id', $userDomains[0]->unidadesOperacionais->pluck('id'));
                                unset($userDomains[0]);
                            }
                            foreach ($userDomains as $userDomain) {
                                $q3->orWhereIn('unidade_operacional_id', $userDomain->unidadesOperacionais->pluck('id'));
                            }
                        });
                    });
                });
            } elseif ($auth_user_role === config('access.users.operational_unit_role')) {
                $builder->where(function ($q) use ($user) {
                    $q->whereHas('roles', function ($q2) use ($user) {
                        $q2->where('name', config('access.users.operational_unit_role'))
                            ->orWhere('name', config('access.users.technician_role'));
                    });

                    $q->whereHas('unidadesOperacionais', function ($q2) use ($user) {
                        $q2->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
                    });
                });
            } elseif ($auth_user_role === config('access.users.technician_role')) {
                $builder->where(function ($q) use ($user) {
                    $q->whereHas('roles', function ($q2) use ($user) {
                        $q2->where('name', config('access.users.technician_role'));
                    });

                    $q->whereHas('unidadesOperacionais', function ($q2) use ($user) {
                        $q2->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
                    });
                });
            }
        }
    }
}
