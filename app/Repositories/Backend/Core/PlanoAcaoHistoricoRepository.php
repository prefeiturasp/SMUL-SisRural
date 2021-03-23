<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoHistoricoRepository extends BaseRepository
{
    public function __construct(PlanoAcaoHistoricoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criar um histórico no PDA
     *
     * @param  array $data
     * @return PlanoAcaoHistoricoModel
     */
    public function create(array $data): PlanoAcaoHistoricoModel
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
     * Atualizar um histórico no PDA
     *
     * @param  PlanoAcaoHistoricoModel $model
     * @param  array $data
     * @return PlanoAcaoHistoricoModel
     */
    public function update(PlanoAcaoHistoricoModel $model, array $data): PlanoAcaoHistoricoModel
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
     * Remover um histórico no PDA
     *
     * @param  PlanoAcaoHistoricoModel $model
     * @return bool
     */
    public function delete(PlanoAcaoHistoricoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
