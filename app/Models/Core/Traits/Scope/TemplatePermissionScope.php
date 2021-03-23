<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Helpers\General\CacheHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TemplatePermissionScope implements Scope
{
    /**
     * Libera o "template" do caderno de campo do meu domínio
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

            $builder->where('dominio_id', CacheHelper::singleDominio($user));
        }
    }
}
