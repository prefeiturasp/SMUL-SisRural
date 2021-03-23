<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoItemHistoricoRepository extends BaseRepository
{
    public function __construct(PlanoAcaoItemHistoricoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criar um histórico no item do PDA
     *
     * @param  mixed $data
     * @return PlanoAcaoItemHistoricoModel
     */
    public function create(array $data): PlanoAcaoItemHistoricoModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Editar um histórico no item do PDA
     *
     * @param  PlanoAcaoItemHistoricoModel $model
     * @param  array $data
     * @return PlanoAcaoItemHistoricoModel
     */
    public function update(PlanoAcaoItemHistoricoModel $model, array $data)
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
     * Remover um histórico no item do PDA
     *
     * @param  PlanoAcaoItemHistoricoModel $model
     * @return bool
     */
    public function delete(PlanoAcaoItemHistoricoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
