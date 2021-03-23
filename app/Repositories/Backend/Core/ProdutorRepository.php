<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\ProdutorModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProdutorRepository extends BaseRepository
{

    public function __construct(ProdutorModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cadastro de um produtor
     *
     * @param  mixed $data
     * @return ProdutorModel
     */
    public function create(array $data): ProdutorModel
    {
        return DB::transaction(function () use ($data) {
            if (@$data['cpf']) {
                $data['cpf'] =  preg_replace('/[^0-9]/', '', @$data['cpf']);
            }

            $model = @$this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de um produtor
     *
     * @param  ProdutorModel $model
     * @param  mixed $data
     * @return ProdutorModel
     */
    public function update(ProdutorModel $model, array $data)
    {
        return DB::transaction(function () use ($model, $data) {
            //normaliza o cpf
            if (@$data['cpf']) {
                $data['cpf'] =  preg_replace('/[^0-9]/', '', @$data['cpf']);
            }

            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove um produtor
     *
     * @param  ProdutorModel $model
     * @return bool
     */
    public function delete(ProdutorModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
