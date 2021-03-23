<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\SobreModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class SobrePolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode editar/atualizar a página Sobre
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\SobreModel $sobre
     * @return mixed
     */
    public function update(?User $user, SobreModel $sobre)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        return false;
    }
}
