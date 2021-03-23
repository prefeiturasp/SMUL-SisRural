<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\CadernoStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Models\Core\TemplateModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class CadernoPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     *
     * Otimização das queries p/ o sync mobile
     *
     * @param mixed $caderno
     * @param mixed $user
     *
     * @return [type]
     */
    private function checkTemplateDomain($caderno, $user)
    {
        return $this->remember("CadernoPolicy-checkTemplateDomain-{$user->id}-{$caderno->template_id}", function () use ($user, $caderno) {
            return $caderno->template()->first()->dominio_id == $user->singleDominio()->id;
        });
    }

    /**
     * Determina se o usuário tem permissão para visualizar o caderno de campo
     *
     * A listagem é por abrangência, controlado pelo CadernoPermissionScope
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\CadernoModel $caderno
     * @return mixed
     */
    public function view(?User $user, CadernoModel $caderno)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        if ($user->can('view menu caderno')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar um caderno de campo
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('create caderno')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode editar/atualizar o caderno de campo.
     *
     * É verificado se o usuário tem permissão para acessar o template do caderno.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\CadernoModel $caderno
     * @return mixed
     */
    public function update(?User $user, CadernoModel $caderno)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($caderno->status == CadernoStatusEnum::Finalizado) {
            return false;
        }

        //Verifica se o usuário tem permissão para acessar o template do caderno
        //A chamada é feita através do template()->first() p/ não retornar o template na listagem, piora performance.

        if ($user->can('edit caderno') && $this->checkTemplateDomain($caderno, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine se o usuário pode remover o caderno de campo
     *
     * É verificado se o usuário tem permissão para acessar o template do caderno.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\CadernoModel $caderno
     * @return mixed
     */
    public function delete(?User $user, CadernoModel $caderno)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //$user->can('delete caderno') &&
        if ($this->checkTemplateDomain($caderno, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine se o usuário pode restaurar o caderno de campo
     *
     * É verificado se o usuário tem permissão para acessar o template do caderno.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\CadernoModel $caderno
     * @return mixed
     */
    public function restore(?User $user, CadernoModel $caderno)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $list = CadernoModel::where(['produtor_id' => $caderno->produtor_id, 'unidade_produtiva_id' => $caderno->unidade_produtiva_id])->whereIn('status', [CadernoStatusEnum::Rascunho])->where('id', '!=', $caderno->id);
        if ($caderno->status == CadernoStatusEnum::Rascunho && $list->exists()) {
            return false;
        }

        //$user->can('delete caderno') &&
        if ($this->checkTemplateDomain($caderno, $user)) {
            return true;
        }

        return false;
    }

    public function forceDelete(?User $user, CadernoModel $caderno)
    {
        return false;
    }

    /**
     * Determina se o usuário pode enviar ou não email para o produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\CadernoModel $caderno
     * @return mixed
     */
    public function sendEmail(?User $user, CadernoModel $caderno)
    {
        return false; //cliente pediu para retirar

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('edit caderno')) {
            return false;
        }

        if (!$caderno->templateScoped()->exists()) {
            return false;
        }

        //Se tem permissão para editar, pode "aplicar" e o status for finalizado ... então é permitido enviar o email p/ o produtor
        if ($caderno->status == CadernoStatusEnum::Finalizado) {
            return true;
        }

        return false;
    }
}
