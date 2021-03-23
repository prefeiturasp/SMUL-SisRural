<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Models\Core\PlanoAcaoModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanoAcaoHistoricoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode ver o histórico do PDA
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoHistoricoModel $historico
     * @return mixed
     */
    public function view(?User $user, PlanoAcaoHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu plano_acao_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar um histórico no PDA
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create plano_acao_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode atualizar o histórico
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoHistoricoModel $historico
     * @return mixed
     */
    public function update(?User $user, PlanoAcaoHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit plano_acao_historico')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode remover
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoHistoricoModel $historico
     * @return mixed
     */
    public function delete(?User $user, PlanoAcaoHistoricoModel $historico)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete plano_acao_historico')) {
            return true;
        }

        return false;
    }
}
