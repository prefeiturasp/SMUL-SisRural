<?php

namespace App\Models\Core\Traits\Policy;

use App\Helpers\General\CacheHelper;
use App\Models\Auth\User;
use App\Models\Core\TemplateModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário tem permissão para visualizar o template
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateModel $template
     * @return mixed
     */
    public function view(?User $user, TemplateModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu caderno base')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar o template
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create caderno base')) {
            $dominio_id = CacheHelper::singleDominio($user);

            $existCaderno = TemplateModel::where('dominio_id', $dominio_id)->where('tipo', 'caderno')->whereNull('deleted_at')->first();
            if ($existCaderno) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar o template
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateModel $template
     * @return mixed
     */
    public function update(?User $user, TemplateModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit caderno base')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para remover o template
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateModel $template
     * @return mixed
     */
    public function delete(?User $user, TemplateModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete caderno base')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão de aplicar o template selecionado.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateModel $template
     * @return mixed
     */
    public function apply(?User $user, TemplateModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($template->dominio_id == $user->singleDominio()->id) {
            return true;
        }

        return false;
    }
}
