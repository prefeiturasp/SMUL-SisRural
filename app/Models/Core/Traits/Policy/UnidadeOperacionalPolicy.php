<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\UnidadeOperacionalModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnidadeOperacionalPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar uma unidade operacional
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeOperacionalModel $operational_unit
     * @return mixed
     */
    public function view(?User $user, UnidadeOperacionalModel $operational_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //AQUI
        if ($user->can('view same domain operational units')) {
            return (!is_null($operational_unit->whereIn('dominio_id', $user->dominios->pluck('id'))->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar uma unidade operacional.
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        //Usuários do tipo "Domínio" podem fazer essa ação
        if ($user->isAdmin() || $user->can('create same domain operational units')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar uma unidade operacional
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeOperacionalModel $operational_unit
     * @return mixed
     */
    public function update(?User $user, UnidadeOperacionalModel $operational_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //AQUI
        //Usuários do tipo "Domínio" tem permissão para fazer essa ação
        if ($user->can('edit same domain operational units')) {
            return (!is_null($operational_unit->whereIn('dominio_id', $user->dominios->pluck('id'))->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover a unidade operacional
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeOperacionalModel $operational_unit
     * @return mixed
     */
    public function delete(?User $user, UnidadeOperacionalModel $operational_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //AQUI
        if ($user->can('delete same domain operational units')) {
            return (!is_null($operational_unit->whereIn('dominio_id', $user->dominios->pluck('id'))->first())) ? true : false;
        }

        return false;
    }
}
