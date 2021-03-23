<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\CadernoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CadernoRepository extends BaseRepository
{
    public function __construct(CadernoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria o caderno de campo
     *
     * @param  mixed $data
     * @return CadernoModel
     */
    public function create(array $data): CadernoModel
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = \Auth::user()->id;
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza o caderno de campo
     *
     * @param  CadernoModel $model
     * @param  mixed $data
     * @return CadernoModel
     */
    public function update(CadernoModel $model, array $data): CadernoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove o caderno de campo
     *
     * @param  CadernoModel $model
     * @return bool
     */
    public function delete(CadernoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove físicamente o registro
     *
     * @param  CadernoModel $model
     * @return bool
     *
     * @deprecated Não é possível remover fisicamente por causa do sync dos dados com o APP. Ainda não foi visto uma solução para isso.
     */
    public function forceDelete(CadernoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->forceDelete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }


    /**
     * Restaura um caderno removido (softdelete), olhar o CadernoPolicy para ver as regras
     *
     * @param  CadernoModel $model
     * @return bool
     */
    public function restore(CadernoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->restore()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
