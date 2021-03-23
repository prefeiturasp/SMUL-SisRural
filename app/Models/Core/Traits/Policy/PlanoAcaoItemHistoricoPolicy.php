<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanoAcaoItemHistoricoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar o histórico do item do PDA
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemHistoricoModel $historico
     * @return mixed
     */
    public function view(?User $user, PlanoAcaoItemHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu plano_acao_item_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar um "histórico" no item do plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create plano_acao_item_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the user.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemHistoricoModel $historico
     * @return mixed
     */
    public function update(?User $user, PlanoAcaoItemHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit plano_acao_item_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode remover um histórico do item do PDA
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemHistoricoModel $historico
     * @return mixed
     */
    public function delete(?User $user, PlanoAcaoItemHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete plano_acao_item_historico')) {
            return true;
        }

        return false;
    }
}
