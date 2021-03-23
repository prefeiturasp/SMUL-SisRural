<?php

namespace App\Models\Auth\Traits\Policy;

use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar o outro usuário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Auth\User $target_user
     * @return bool
     */
    public function view(?User $user, User $target_user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        } elseif ($target_user->isAdmin()) { //Restringe a visualização de super usuários
            return false;
        }

        if ($user->can('view all users')) {
            return true;
        }

        if ($user->can('view admin users') && $target_user->isAdminLOP()) {
            return true;
        }

        /*
            Usuários do tipo "Admin" e target do tipo "Dominio"
        */
        if ($user->can('view all domain users') && $target_user->isDominio()) {
            return true;
        }

        /*
           Usuários do tipo "Domínio" e target do tipo "Dominio"

           Regra: Domínio do usuário logado é o mesmo do Domínio do usuário "target"
        */
        if ($user->can('view same domain users') && $target_user->isDominio()) {
            if (!is_null($user->dominios()
                ->whereIn('dominio_id', $target_user->dominios->pluck('id'))
                ->first())) {
                return true;
            }
        }

        /*
           Usuários do tipo "Domínio" e target do tipo "Unidade Operacional" ou "Técnico"

           Regra: Se o usuário estiver dentro de unidades operacionais que eu tenho acesso.
        */
        if (($user->can('view same domain operational unit users') && $target_user->isUnidOperacional()) ||
            ($user->can('view same domain technical users') && $target_user->isTecnico())
        ) {
            //AQUI
            $userDomains = $user->dominios()->get();
            foreach ($userDomains as $userDomain) {
                if (!is_null($userDomain->unidadesOperacionais()
                    ->findMany($target_user->unidadesOperacionais->pluck('id'))->first())) {
                    return true;
                }
            }
        }

        /**
         * Usuários do tipo "Unidade Operacional" e target do tipo "Unidade Operacional" ou "Técnico"
         *
         * Regra: Se o usuário estiver dentro de unidades operacionais que eu tenho acesso.
         */
        if (($user->can('view same operational unit users') && $target_user->isUnidOperacional()) ||
            ($user->can('view same operational unit technical users') && $target_user->isTecnico())
        ) {
            if (!is_null($user->unidadesOperacionais()
                ->whereIn(
                    'unidade_operacional_id',
                    $target_user->unidadesOperacionais->pluck('id')
                )
                ->first())) {
                return true;
            }
        }

        if (($user->can('view own user') && $user->id === $target_user->id)) {
            return true;
        }

        return false;
    }

    /**
     * Determina se é possível criar um novo usuário no sistema.
     *
     * "Admin", "Dominio" e "Unidade Operacional" possuem permissão para criar.
     *
     * "Técnico" não possuí permissão para criar
     *
     * @param \App\Models\Auth\User|null $user
     * @return bool
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if (
            $user->isAdmin() ||
            $user->can('create admin users') ||
            $user->can('create all domain users') ||
            $user->can('create same domain users') ||
            $user->can('create same domain operational unit users') ||
            $user->can('create same operational unit users') ||
            $user->can('create same operational unit technical users')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar dados do usuário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Auth\User $target_user
     * @return bool
     */
    public function update(?User $user, User $target_user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('edit admin users') && $target_user->isAdminLOP()) {
            return true;
        }

        if ($user->can('edit all domain users') && $target_user->isDominio()) {
            return true;
        }

        /**
         * Usuário do tipo "Domínio" e o targetUser do tipo "Dominio"
         */
        if ($user->can('edit same domain users') && $target_user->isDominio()) {
            //AQUI
            if (!is_null($user->dominios()
                ->whereIn('dominio_id', $target_user->dominios->pluck('id'))
                ->first())) {
                return true;
            }
        }

        /**
         * Usuário do tipo "Domínio" e o targetUser do tipo "Unidade Operacional" ou "Técnico"
         */
        if (
            $user->can('edit same domain operational unit users') && $target_user->isUnidOperacional()
            || $user->can('edit same domain operational unit users') && $target_user->isTecnico()
        ) {
            //AQUI
            $userDomains = $user->dominios()->get();
            foreach ($userDomains as $userDomain) {
                if (!is_null($userDomain->unidadesOperacionais()
                    ->findMany($target_user->unidadesOperacionais->pluck('id'))->first())) {
                    return true;
                }
            }
        }

        /**
         * Usuário do tipo "Unidade Operacional" e o targetUser "Unidade Operacional" ou "Técnico"
         */
        if (($user->can('edit same operational unit users') && $target_user->isUnidOperacional()) ||
            ($user->can('edit same operational unit technical users') && $target_user->isTecnico())
        ) {
            if (!is_null($user->unidadesOperacionais()
                ->whereIn(
                    'unidade_operacional_id',
                    $target_user->unidadesOperacionais->pluck('id')
                )
                ->first())) {
                return true;
            }
        }

        if (($user->can('edit own user') && $user->id === $target_user->id)) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover o outro usuário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Auth\User $target_user
     * @return bool
     */
    public function delete(?User $user, User $target_user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->id == $target_user->id) {
            return false;
        }

        /* Usuários do tipo Super admin ou Admin não podem ser removidos */
        if ($target_user->isAdmin() || $target_user->isAdminLOP()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        /** Usuário do tipo Admin pode remover outros usuários do tipo Admin */
        if ($user->can('delete admin users') && $target_user->isAdminLOP()) {
            return true;
        }

        /** Usuário do tipo Admin pode remover outros usuários do tipo Domínio */
        if ($user->can('delete all domain users') && $target_user->isDominio()) {
            return true;
        }

        /** Usuário do tipo Domínio pode remover outros usuários do tipo Domínio */
        if ($user->can('delete same domain users') && $target_user->isDominio()) {
            //AQUI
            if (!is_null($user->dominios()
                ->whereIn('dominio_id', $target_user->dominios->pluck('id'))
                ->first())) {
                return true;
            }
        }

        /** Usuário do tipo Domínio pode remover outros usuários do tipo Unidade Operacional ou Técnico */
        if ($user->can('delete same domain operational unit users') && ($target_user->isUnidOperacional() || $target_user->isTecnico())) {
            //AQUI
            $userDomains = $user->dominios()->get();
            foreach ($userDomains as $userDomain) {
                if (!is_null($userDomain->unidadesOperacionais()
                    ->findMany($target_user->unidadesOperacionais->pluck('id'))->first())) {
                    return true;
                }
            }
        }

        /** Usuário do tipo Unidade Operacional pode remover outros usuários do tipo Unidade Operacional ou Técnico */
        if (($user->can('delete same operational unit users') && $target_user->isUnidOperacional()) ||
            ($user->can('delete same operational unit technical users') && $target_user->isTecnico())
        ) {
            if (!is_null($user->unidadesOperacionais()
                ->whereIn(
                    'unidade_operacional_id',
                    $target_user->unidadesOperacionais->pluck('id')
                )
                ->first())) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param  mixed $user
     * @return bool
     *
     * @deprecated Será removido futuramente, não parece ser utilizado no projeto.
     */
    public function createRole(?User $user)
    {
        return \Auth::user()->can('create', $user);
    }

    /**
     * @param  mixed $user
     * @return bool
     *
     * @deprecated Será removido futuramente, não parece ser utilizado no projeto.
     */
    public function updateRole(?User $user, User $target_user)
    {
        if ($user->isTecnico()) {
            return false;
        }

        return \Auth::user()->can('update', $target_user);
    }

    /**
     * @param  mixed $user
     * @return bool
     *
     * @deprecated Será removido futuramente, não parece ser utilizado no projeto.
     */
    public function deleteRole(?User $user, User $target_user)
    {
        if ($user->isTecnico()) {
            return false;
        }

        return \Auth::user()->can('delete', $target_user);
    }
}
