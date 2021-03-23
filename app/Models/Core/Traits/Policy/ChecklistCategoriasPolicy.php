<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\ChecklistStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\ChecklistCategoriaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistCategoriasPolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistCategoriaModel $checklistCategoria
     * @return mixed
     */
    public function view(?User $user, ChecklistCategoriaModel $checklistCategoria)
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
     * Determina se o usuário pode criar a categoria
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
     * Determina se o usuário pode atualizar a categoria
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistCategoriaModel $checklistCategoria
     * @return mixed
     */
    public function update(?User $user, ChecklistCategoriaModel $checklistCategoria)
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
     * Determina se o usuário pode remover a categoria
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistCategoriaModel $checklistCategoria
     * @return mixed
     */
    public function delete(?User $user, ChecklistCategoriaModel $checklistCategoria)
    {
        if ($user === null) {
            return false;
        }

        //Se tiver algum checklist (formulário) aplicado em alguma unidade produtiva, não permite a exclusão da categoria
        if (ChecklistUnidadeProdutivaModel::withTrashed()->where("checklist_id", $checklistCategoria->checklist_id)->where('status', ChecklistStatusEnum::Finalizado)->exists()) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode adicionar novas perguntas no formulário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistCategoriaModel $checklistCategoria
     * @return mixed
     */
    public function addPerguntas(?User $user, ChecklistCategoriaModel $checklistCategoria)
    {
        if ($user === null) {
            return false;
        }

        //Se tiver algum checklist (formulário) aplicado em alguma unidade produtiva, não permite a adição de novas perguntas
        if (ChecklistUnidadeProdutivaModel::withTrashed()->where("checklist_id", $checklistCategoria->checklist_id)->where('status', ChecklistStatusEnum::Finalizado)->exists()) {
            return false;
        }

        return true;
    }
}
