<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\DominioModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class DominioPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário tem permissão para visualizar o domínio
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DominioModel $domain
     * @return mixed
     */
    public function view(?User $user, DominioModel $domain)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('view all domains')) {
            return true;
        }

        //AQUI
        if ($user->can('view same domains')) {
            return (!is_null($domain->whereIn('id', $user->dominios->pluck('id'))->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar um domínio
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create domains')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para editar um domínio
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DominioModel $domain
     * @return mixed
     */
    public function update(?User $user, DominioModel $domain)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit domains')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover um domínio
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\DominioModel $domain
     * @return mixed
     */
    public function delete(?User $user, DominioModel $domain)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete domains')) {
            return true;
        }

        return false;
    }
}
