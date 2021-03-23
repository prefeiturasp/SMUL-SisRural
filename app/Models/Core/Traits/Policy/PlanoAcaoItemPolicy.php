<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\ChecklistStatusFlowEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoItemStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\PlanoAcaoItemModel;
use Illuminate\Auth\Access\HandlesAuthorization;


/**
 * Este Policy corresponde aos seguintes escopos
 *
 * - Ação - Plano de Ação Individual
 * - Ação - Plano de Ação Coletivo
 * - Ação - Plano de Ação derivado de um Formulário
 */

class PlanoAcaoItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário tem permissão para visualizar a ação do plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemModel $item
     * @return mixed
     */
    public function view(?User $user, PlanoAcaoItemModel $item)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu plano_acao_item')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar uma ação no plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create plano_acao_item')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar a ação do plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemModel $item
     * @return mixed
     */
    public function update(?User $user, PlanoAcaoItemModel $item)
    {
        if ($user === null) {
            return false;
        }

        if ($item->status == PlanoAcaoItemStatusEnum::Cancelado || $item->status == PlanoAcaoItemStatusEnum::Concluido) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit plano_acao_item')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover uma ação do plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoItemModel $item
     * @return mixed
     */
    public function delete(?User $user, PlanoAcaoItemModel $item)
    {
        if ($user === null) {
            return false;
        }

        //Não permite exclusão quando Derivado de formulário
        if (@$item->plano_acao->checklist_unidade_produtiva_id) {
            return false;
        }

        //Não permite exclusão quando a ação for originada do Coletivo
        if (@$item->plano_acao_item_coletivo_id) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete plano_acao_item')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para reabrir o item do plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function reopen(?User $user, PlanoAcaoItemModel $item)
    {
        if ($user === null) {
            return false;
        }

        //Só pode reabrir se o status for Concluído ou Cancelado
        if ($item->status == PlanoAcaoItemStatusEnum::Concluido || $item->status == PlanoAcaoItemStatusEnum::Cancelado) {
            return true;
        }

        return false;
    }

    /**
     * Determina se ainda é possível editar a descrição de um item do plano de ação
     *
     * @param  User $user
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function editDescricao(?User $user, PlanoAcaoItemModel $item)
    {
        if ($user === null) {
            return false;
        }

        if ($item->plano_acao->checklist_unidade_produtiva_id) {
            $checklist_unidade_produtiva = $item->plano_acao->checklist_unidade_produtiva;
            $checklist = $checklist_unidade_produtiva->checklist;
            if ($checklist->plano_acao == PlanoAcaoEnum::Obrigatorio && in_array($checklist_unidade_produtiva->status_flow, [ChecklistStatusFlowEnum::Aprovado, ChecklistStatusFlowEnum::Reprovado])) {
                return false;
            }

            return true;
        }

        return true;
    }
}
