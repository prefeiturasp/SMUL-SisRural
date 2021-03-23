<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Core\EstadoModel;

class EstadoRepository extends BaseRepository
{

    public function __construct(EstadoModel $model)
    {
        $this->model = $model;
    }

    public function create(array $data) : EstadoModel {

        return DB::transaction(function () use ($data) {
            $model = $this->model::create([
                'nome' => $data['nome'],
            ]);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');

        });
    }

    public function update(EstadoModel $model, array $data) {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function delete(EstadoModel $model) {
        return DB::transaction(function () use ($model) {
            if($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
