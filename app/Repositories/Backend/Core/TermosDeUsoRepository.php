<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\TermosDeUsoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class TermosDeUsoRepository extends BaseRepository
{
    public function __construct(TermosDeUsoModel $model)
    {
        $this->model = $model;
    }

    public function create(array $data): TermosDeUsoModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function update(TermosDeUsoModel $model, array $data): TermosDeUsoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function delete(TermosDeUsoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
