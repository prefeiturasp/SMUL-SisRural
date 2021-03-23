<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistPerguntaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar a pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistPerguntaModel $checklistPergunta
     * @return mixed
     */
    public function view(?User $user, ChecklistPerguntaModel $checklistPergunta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode vincular a pergunta no checklist
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode atualizar a pergunta vinculada no checklist
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function update(?User $user, ChecklistPerguntaModel $checklistPergunta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário remover o vinculo da pergunta com o checklist/formulário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistPerguntaModel $checklistPergunta
     * @return mixed
     */
    public function delete(?User $user, ChecklistPerguntaModel $checklistPergunta)
    {
        if ($user === null) {
            return false;
        }

        // Se a pergunta já foi utilizada em alguma Unidade Produtiva (Checklist rascunho ou futuras aplicações)
        // Analisando o caso, não precisa mais dessa regra, porque mesmo que já tenha respondido, fica no histórico ... a pergunta ainda pode ser desvinculada de um checklist
        // if (UnidadeProdutivaRespostaModel::where('pergunta_id', $checklistPergunta->pergunta->id)->exists()) {
        //     return false;
        // }


        //Se a pergunta já foi utilizada em alguma aplicação de Checklist (finalizado) e que tenha sido do Checklist em questão (whereHas)
        $checklist_id = $checklistPergunta->categoria->checklist_id;

        if (ChecklistSnapshotRespostaModel::where('pergunta_id', $checklistPergunta->pergunta->id)->whereHas('checklistUnidadeProdutivas',  function ($q) use ($checklist_id) {
            $q->where('checklist_id', $checklist_id);
        })->exists()) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para editar algumas informações do formulário
     *
     * - Pontuação
     * - Plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistPerguntaModel $checklistPergunta
     * @return mixed
     */
    public function editForm(?User $user, ChecklistPerguntaModel $checklistPergunta)
    {
        if ($user === null) {
            return false;
        }

        //Mesma regra do "delete"
        $checklist_id = $checklistPergunta->categoria->checklist_id;

        if (ChecklistSnapshotRespostaModel::where('pergunta_id', $checklistPergunta->pergunta->id)->whereHas('checklistUnidadeProdutivas',  function ($q) use ($checklist_id) {
            $q->where('checklist_id', $checklist_id);
        })->exists()) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit pergunta checklist')) {
            return true;
        }

        return false;
    }
}
