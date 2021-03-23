<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\DadoModel;
use App\Models\Core\UnidadeOperacionalModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DadoRepository extends BaseRepository
{

    public function __construct(DadoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criar
     *
     * @param  mixed $data
     * @return DadoModel
     */
    public function create(array $data): DadoModel
    {
        return DB::transaction(function () use ($data) {
            $data['api_token'] = hash('sha256', $data['api_token']);

            $model = $this->model::create($data);
            $model = $this->regioesSync($model, @$data['regioes']);
            $model = $this->estadosSync($model, @$data['abrangenciaEstadual']);
            $model = $this->cidadesSync($model, @$data['abrangenciaMunicipal']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualizar
     *
     * É rodado novamente o sync (abrangência)
     *
     * @param  DadoModel $model
     * @param  mixed $data
     * @return DadoModel
     */
    public function update(DadoModel $model, array $data): DadoModel
    {
        return DB::transaction(function () use ($model, $data) {
            if ($model->api_token != $data['api_token']) {
                $data['api_token'] = hash('sha256', $data['api_token']);
            }

            $model->update($data);
            $model = $this->regioesSync($model, @$data['regioes']);
            $model = $this->estadosSync($model, @$data['abrangenciaEstadual']);
            $model = $this->cidadesSync($model, @$data['abrangenciaMunicipal']);
            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remover
     *
     * @param  DadoModel $model
     * @return bool
     */
    public function delete(DadoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync das regioes (dado_abrangencia_regioes)
     *
     * @param  DadoModel $model
     * @param  mixed $data
     * @return DadoModel
     */
    public function regioesSync(DadoModel $model, $data): DadoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->regioes()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync dos estados (dado_abrangencia_estados)
     *
     * @param  DadoModel $model
     * @param  mixed $data
     * @return DadoModel
     */
    public function estadosSync(DadoModel $model, $data): DadoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->abrangenciaEstadual()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync das cidades (dado_abrangencia_cidades)
     *
     * @param  DadoModel $model
     * @param  mixed $data
     * @return DadoModel
     */
    public function cidadesSync(DadoModel $model, $data): DadoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->abrangenciaMunicipal()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
