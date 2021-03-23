<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\TermosDeUsoModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermosDeUsoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TermosDeUsoModel $termosDeUso
     * @return mixed
     */
    public function view(?User $user, TermosDeUsoModel $termosDeUso)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create an user.
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the user.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TermosDeUsoModel $termosDeUso
     * @return mixed
     */
    public function update(?User $user, TermosDeUsoModel $termosDeUso)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TermosDeUsoModel $termosDeUso
     * @return mixed
     */
    public function delete(?User $user, TermosDeUsoModel $termosDeUso)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }
}
