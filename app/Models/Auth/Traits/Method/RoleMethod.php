<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait RoleMethod.
 */
trait RoleMethod
{
    /**
     * admin_role = Super Administrator
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->name === config('access.users.admin_role');
    }
}
