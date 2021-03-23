<?php

namespace App\Models\Core\Traits\Policy;

use App\Models\Auth\User;
use App\Models\Core\UnidadeProdutivaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnidadeProdutivaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário tem permissão para visualizar a unidade produtiva
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeProdutivaModel $productive_unit
     * @return mixed
     */
    public function view(?User $user, UnidadeProdutivaModel $productive_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Só pode visualizar unidades produtivas que estão contidas nas unidades operacionais que o usuário enxerga.
        if ($user->can('view same operational units productive units')) {
            return (!is_null($productive_unit->whereHas('unidadesOperacionais', function ($q) use ($user) {
                $q->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
            })->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar uma nova unidade produtiva
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create same operational units productive units')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode atualizar a unidade produtiva
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeProdutivaModel $productive_unit
     * @return mixed
     */
    public function update(?User $user, UnidadeProdutivaModel $productive_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('edit same operational units productive units')) {
            return (!is_null($productive_unit->whereHas('unidadesOperacionais', function ($q) use ($user) {
                $q->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
            })->first())) ? true : false;
        }

        return false;
    }

    /**
     * Determina se o usuário pode remover uma unidade produtiva
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\UnidadeProdutivaModel $productive_unit
     * @return mixed
     */
    public function delete(?User $user, UnidadeProdutivaModel $productive_unit)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('delete same operational units productive units')) {
            return (!is_null($productive_unit->whereHas('unidadesOperacionais', function ($q) use ($user) {
                $q->whereIn('unidade_operacional_id', $user->unidadesOperacionais->pluck('id'));
            })->first())) ? true : false;
        }

        return false;
    }
}
