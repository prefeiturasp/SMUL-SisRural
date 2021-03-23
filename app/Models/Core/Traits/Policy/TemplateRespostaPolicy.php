<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\TemplateRespostaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplateRespostaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar a resposta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateRespostaModel $template
     * @return mixed
     */
    public function view(?User $user, TemplateRespostaModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu resposta caderno')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar uma resposta no caderno de campo
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create resposta caderno')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode atualizar uma resposta do caderno de campo
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateRespostaModel $template
     * @return mixed
     */
    public function update(?User $user, TemplateRespostaModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit resposta caderno')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode remover uma resposta do caderno de campo
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\TemplateRespostaModel $template
     * @return mixed
     */
    public function delete(?User $user, TemplateRespostaModel $template)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete resposta caderno')) {
            return true;
        }

        return false;
    }
}
