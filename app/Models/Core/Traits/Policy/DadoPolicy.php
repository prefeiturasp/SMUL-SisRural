<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\DadoModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class DadoPolicy
{
    use HandlesAuthorization;

    /**
     * Visualizar dado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DadoModel $dado
     * @return mixed
     */
    public function view(?User $user, DadoModel $dado)
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
     * Listar dados
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function list(?User $user)
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
     * Criar dado
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        return false;

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }

    /**
     * Atualizar dado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DadoModel $dado
     * @return mixed
     */
    public function update(?User $user, DadoModel $dado)
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
     * Determina se o usuário tem permissão para remover um dado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DadoModel $dado
     * @return mixed
     */
    public function delete(?User $user, DadoModel $dado)
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
