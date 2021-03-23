<?php

namespace App\Models\Auth\Traits\Attribute;

use App\Enums\RolesEnum;
use Illuminate\Support\Facades\Hash;

/**
 * Trait UserAttribute.
 */
trait UserAttribute
{
    /**
     * @param $password
     */
    public function setPasswordAttribute($password): void
    {
        // If password was accidentally passed in already hashed, try not to double hash it
        if (
            (\strlen($password) === 60 && preg_match('/^\$2y\$/', $password)) ||
            (\strlen($password) === 95 && preg_match('/^\$argon2i\$/', $password))
        ) {
            $hash = $password;
        } else {
            $hash = Hash::make($password);
        }

        // Note: Password Histories are logged from the \App\Observer\User\UserObserver class
        $this->attributes['password'] = $hash;
    }

    /**
     * Retorna o nome completo do usuário
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->last_name
            ? $this->first_name . ' ' . $this->last_name
            : $this->first_name;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    /**
     * @return mixed
     */
    public function getPictureAttribute()
    {
        return $this->getPicture();
    }

    /**
     * Retorna as roles do usuário formatadas com um nome bacana.
     *
     * @return string
     */
    public function getRolesLabelAttribute()
    {
        $roles = $this->getRoleNames()->toArray();

        if (\count($roles)) {
            return implode(', ', array_map(function ($item) {
                //Supress p/ causa do "Super Admin"
                return @RolesEnum::toSelectArray()[$item];
            }, $roles));
        }

        return 'N/A';
    }

    /**
     * @return string
     *
     * @deprecated Bem provável que esse método não é utilizado em nenhuma parte do sistema.
     *
     */
    public function getPermissionsLabelAttribute()
    {
        $permissions = $this->getDirectPermissions()->toArray();

        if (\count($permissions)) {
            return implode(', ', array_map(function ($item) {
                return ucwords($item['name']);
            }, $permissions));
        }

        return 'N/A';
    }

    public function getPermissionMobileAttribute()
    {
        if ($this->isUnidOperacional()) {
            return config('access.users.operational_unit_role');
        } else if ($this->isTecnico()) {
            return config('access.users.technician_role');
        } else if ($this->isTecnicoExterno()) {
            return config('access.users.admin_role');
        }

        return null;
    }
}
