<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\ChecklistCategoriaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ChecklistCategoriaRepository extends BaseRepository
{
    public function __construct(ChecklistCategoriaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria uma categoria no template do formulário (ChecklistModel)
     *
     * @param  mixed $data
     * @return ChecklistCategoriaModel
     */
    public function create(array $data): ChecklistCategoriaModel
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
     * Atualiza uma categoria do formulário (checklist)
     *
     * @param  ChecklistCategoriaModel $model
     * @param  mixed $data
     * @return ChecklistCategoriaModel
     */
    public function update(ChecklistCategoriaModel $model, array $data): ChecklistCategoriaModel
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
     * Remove uma categoria do formulário (checklist)
     *
     * @param  ChecklistCategoriaModel $model
     * @return bool
     */
    public function delete(ChecklistCategoriaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
