<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\ChecklistPerguntaRespostaModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\RespostaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class RespostaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar as respostas
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\RespostaModel $resposta
     * @return mixed
     */
    public function view(?User $user, RespostaModel $resposta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu resposta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar respostas para uma pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create resposta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar a resposta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\RespostaModel $resposta
     * @return mixed
     */
    public function update(?User $user, RespostaModel $resposta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit resposta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover uma resposta.
     *
     * Tabelas fk
     *   unidade_produtiva_respostas
     *   checklist_pergunta_respostas
     *   checklist_snapshot_respostas
     *
     * - Respostas de Perguntas vinculadas no Checklist, não podem ser removidas, mesmo sem resposta, porque existe um PESO na questão.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\RespostaModel $resposta
     * @return mixed
     */
    public function delete(?User $user, RespostaModel $resposta)
    {
        if ($user === null) {
            return false;
        }

        //Se existe Respostas vinculadas na Unidade Produtiva
        if (UnidadeProdutivaRespostaModel::where("resposta_id", $resposta->id)->exists()) {
            return false;
        }

        //Se existe Respostas vinculadas ao Checklist (Onde é informado os pesos das respostas)
        if (ChecklistPerguntaRespostaModel::where("resposta_id", $resposta->id)->exists()) {
            return false;
        }

        //Se existe Respostas vinculadas ao Checklist Finalizado
        if (ChecklistSnapshotRespostaModel::where("resposta_id", $resposta->id)->exists()) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete resposta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se a cor da resposta pode ser editada
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\RespostaModel $resposta
     * @return mixed
     */
    public function editForm(?User $user, RespostaModel $resposta)
    {
        if ($user === null) {
            return false;
        }

        //Se a resposta foi utilizada em alguma resposta vinculada a Unidade Produtiva, não permite alteração
        if (UnidadeProdutivaRespostaModel::where("resposta_id", $resposta->id)->exists()) {
            return false;
        }

        //Se a resposta foi utilizada em alguma resposta vinculada ao Checklist (Checklist Finalizados), não permite alteração
        if (ChecklistSnapshotRespostaModel::where("resposta_id", $resposta->id)->exists()) {
            return false;
        }


        //Se a pergunta da respota já foi utilizada em algum momento, também não permite mais a edição (Regra solicitada pelo cliente)
        $pergunta_id = $resposta->pergunta_id;

        //Se a pergunta foi utilizada em alguma resposta vinculada a Unidade Produtiva, não permite alteração
        if (UnidadeProdutivaRespostaModel::where("pergunta_id", $pergunta_id)->exists()) {
            return false;
        }

        //Se a pergunta foi utilizada em alguma resposta vinculada ao Checklist (Checklist Finalizados), não permite alteração
        if (ChecklistSnapshotRespostaModel::where("pergunta_id", $pergunta_id)->exists()) {
            return false;
        }

        return true;
    }
}
