<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\DominioModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DominioRepository extends BaseRepository
{

    public function __construct(DominioModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criação de um domínio
     *
     * Sync das abrangências (dominio_abrangencia_regioes, dominio_abrangencia_estados, dominio_abrangencia_cidades)
     *
     * @param  mixed $data
     * @return DominioModel
     */
    public function create(array $data): DominioModel
    {

        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);
            $model = $this->abrangenciaRegionalSync($model, @$data['abrangenciaRegional']);
            $model = $this->abrangenciaEstadualSync($model, @$data['abrangenciaEstadual']);
            $model = $this->abrangenciaMunicipalSync($model, @$data['abrangenciaMunicipal']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de um domínio
     *
     * Sync das abrangências (dominio_abrangencia_regioes, dominio_abrangencia_estados, dominio_abrangencia_cidades)
     *
     * @param  DominioModel $model
     * @param  mixed $data
     * @return DominioModel
     */
    public function update(DominioModel $model, array $data): DominioModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model = $this->abrangenciaRegionalSync($model, @$data['abrangenciaRegional']);
            $model = $this->abrangenciaEstadualSync($model, @$data['abrangenciaEstadual']);
            $model = $this->abrangenciaMunicipalSync($model, @$data['abrangenciaMunicipal']);
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remoção de um domínio
     *
     * @param  DominioModel $model
     * @return bool
     */
    public function delete(DominioModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Vinculação das regiões do Domínio (dominio_abrangencia_regioes)
     *
     * @param  DominioModel $model
     * @param  mixed $data
     * @return DominioModel
     */
    public function abrangenciaRegionalSync(DominioModel $model, $data): DominioModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->abrangenciaRegional()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Vinculação dos estados do Domínio (dominio_abrangencia_estados)
     *
     * @param  DominioModel $model
     * @param  mixed $data
     * @return DominioModel
     */
    public function abrangenciaEstadualSync(DominioModel $model, $data): DominioModel
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
     * Vinculação das cidades do Domínio (dominio_abrangencia_cidades)
     *
     * @param  DominioModel $model
     * @param  mixed $data
     * @return DominioModel
     */
    public function abrangenciaMunicipalSync(DominioModel $model, $data): DominioModel
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
