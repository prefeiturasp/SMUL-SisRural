<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\ChecklistStatusEnum;
use App\Http\Controllers\Backend\Forms\ChecklistUnidadeProdutivaForm;
use App\Models\Auth\User;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar o registro
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistModel $checklist
     * @return mixed
     */
    public function view(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //AQUI
        $found = in_array($checklist->dominio_id, \Auth::user()->dominios->pluck('id')->toArray());
        if (!$found) {
            return false;
        }

        if ($user->can('view menu checklist base')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar um formulário (template)
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create checklist base')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode atualizar um formulário (template)
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistModel $checklist
     * @return mixed
     */
    public function update(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        //Se o usuário não for dono do "checklist/formulário", não permite que ele acesse a tela de edição (botão de editar na tabela)
        //AQUI
        if (!in_array($checklist->dominio_id, \Auth::user()->dominios->pluck('id')->toArray())) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit checklist base')) {
            return true;
        }

        return false;
    }


    /**
     * Determina se o usuário pode editar algumas informações do Checklist
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistModel $checklist
     * @return mixed
     */
    public function editStatus(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('edit checklist base')) {
            $checklistAplicadosFinalizados = ChecklistUnidadeProdutivaModel::withTrashed()->where('checklist_id', $checklist->id)->whereIn('status', [ChecklistStatusEnum::Finalizado]);
            if (!$checklistAplicadosFinalizados->exists()) {
                return true;
            }
        }

        if ($checklist->id) {
            return false;
        }

        return false;
    }

    /**
     * Determina se o usuário pode remover o formulário (template).
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistModel $checklist
     * @return mixed
     */
    public function delete(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        //Se tiver algum checklist (formulário) aplicado em alguma unidade produtiva, não permite a exclusão do Checklist
        if (ChecklistUnidadeProdutivaModel::withTrashed()->where("checklist_id", $checklist->id)->where('status', ChecklistStatusEnum::Finalizado)->exists()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Domínio e se é "dono" do checklist aplicado
        //AQUI
        if ($user->can('delete checklist base') && in_array($checklist->dominio_id, \Auth::user()->dominios->pluck('id')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode adicionar novas categorias no formulário
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistModel $checklist
     * @return mixed
     */
    public function addCategorias(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        //Se tiver algum checklist finalizado, não permite mais a modificação
        if (ChecklistUnidadeProdutivaModel::withTrashed()->where("checklist_id", $checklist->id)->where('status', ChecklistStatusEnum::Finalizado)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determina se o usuário pode vincular novas perguntas no checklist (Sessão categoria)
     */
    public function addPerguntas(?User $user, ChecklistModel $checklist)
    {
        return $this->addCategorias($user, $checklist);
    }


    /**
     * Determina se o usuário pode duplicar um Checklist
     */
    public function duplicate(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('duplicate checklist base')) {
            return true;
        }

        return false;
    }

    /**
     *
     * Determina se o usuário tem permissão para editar a fórmula presente no template do formulário
     *
     * @param  mixed $user
     * @param  mixed $checklist
     * @return bool
     */
    public function editFormula(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit checklist base')) {
            //Se tem aplicado em algum checklist finalizado
            if (ChecklistUnidadeProdutivaModel::withTrashed()->where("checklist_id", $checklist->id)->where("status", ChecklistStatusEnum::Finalizado)->exists()) {
                return false;
            }

            //Só permite se tiver categorias atreladas
            if ($checklist->categorias()->count() == 0) {
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * Determina se o usuário tem permissão de aplicar o template selecionado.
     *
     * Esse método é utilizado pelo APP
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateModel $template
     * @return mixed
     */
    public function apply(?User $user, ChecklistModel $checklist)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (ChecklistModel::where("id", $checklist->id)->publicado()->exists()) {
            return true;
        }

        return false;
    }
}
