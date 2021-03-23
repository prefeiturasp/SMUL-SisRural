<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\ProdutorModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProdutorPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usário tem permissão para visualizar o produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ProdutorModel $farmer
     * @return mixed
     */
    public function view(?User $user, ProdutorModel $farmer)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Se usuário for do tipo "Unidade Operacional" ou "Técnico
        if ($user->can('view same operational units farmers')) {
            return (!is_null($farmer->has('unidadesProdutivas')
                ->orDoesntHave('unidadesProdutivasNS')
                ->first())) ? true : false;
        }


        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create same operational units farmers')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar o produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ProdutorModel $farmer
     * @return mixed
     */
    public function update(?User $user, ProdutorModel $farmer)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Se usuário for do tipo "Unidade Operacional" ou "Técnico
        if ($user->can('edit same operational units farmers')) {
            return (!is_null($farmer->has('unidadesProdutivas')
                ->orDoesntHave('unidadesProdutivasNS')
                ->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para deletar o produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ProdutorModel $farmer
     * @return mixed
     */
    public function delete(?User $user, ProdutorModel $farmer)
    {
        return false;

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('delete same operational units farmers')) {
            return (!is_null($farmer->whereHas('unidadesProdutivas', function ($q) use ($user) {
                $q->whereHas('unidadesOperacionais', function ($q2) use ($user) {
                    $q2->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
                });
            })->first())) ? true : false;
        }

        return false;
    }
}
