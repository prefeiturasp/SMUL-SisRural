<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Core\SoloCategoriaModel;

class SoloCategoriaRepository extends BaseRepository
{
    public function __construct(SoloCategoriaModel $model)
    {
        $this->model = $model;
    }

    public function create(array $data): SoloCategoriaModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function update(SoloCategoriaModel $model, array $data): SoloCategoriaModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function delete(SoloCategoriaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
