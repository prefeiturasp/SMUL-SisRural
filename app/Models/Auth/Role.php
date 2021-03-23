<?php

namespace App\Models\Auth;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use App\Enums\RolesEnum;
use App\Models\Auth\Traits\Method\RoleMethod;
use App\Models\Auth\Traits\Scope\RolePermissionScope;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role.
 */
class Role extends SpatieRole implements Recordable
{
    use RecordableTrait,
        RoleMethod;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RolePermissionScope);
    }

    /**
     *  Atributo a partir do model "App\Models\Auth\Role".
     *
     *  Ao retornar $user->roles, o model retornado é o "Spatie\Permission\Models\Role". Logo o método não estará presente.
     *
     * @return string
     */
    public function getFormattedNameAttribute()
    {
        return @RolesEnum::toSelectArray()[$this->name];
    }
}
