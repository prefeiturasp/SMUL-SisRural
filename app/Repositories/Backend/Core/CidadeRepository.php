<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Core\CidadeModel;

class CidadeRepository extends BaseRepository
{

    public function __construct(CidadeModel $model)
    {
        $this->model = $model;
    }

    public function create(array $data): CidadeModel
    {

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

    public function update(CidadeModel $model, array $data)
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function delete(CidadeModel $model)
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
