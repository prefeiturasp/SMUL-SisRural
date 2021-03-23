<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\UnidadeOperacionalModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UnidadeOperacionalRepository extends BaseRepository
{

    public function __construct(UnidadeOperacionalModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria uma unidade operacional (vinculada a um domínio)
     *
     * @param  mixed $data
     * @return UnidadeOperacionalModel
     */
    public function create(array $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);
            $model = $this->regioesSync($model, @$data['regioes']);
            $model = $this->estadosSync($model, @$data['abrangenciaEstadual']);
            $model = $this->cidadesSync($model, @$data['abrangenciaMunicipal']);
            $model = $this->unidadesProdutivasManuaisSync($model, @$data['unidadesProdutivasManuais']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza a unidade operacional
     *
     * É rodado novamente o sync (abrangência)
     *
     * @param  UnidadeOperacionalModel $model
     * @param  mixed $data
     * @return UnidadeOperacionalModel
     */
    public function update(UnidadeOperacionalModel $model, array $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            $model = $this->regioesSync($model, @$data['regioes']);
            $model = $this->estadosSync($model, @$data['abrangenciaEstadual']);
            $model = $this->cidadesSync($model, @$data['abrangenciaMunicipal']);
            $model = $this->unidadesProdutivasManuaisSync($model, @$data['unidadesProdutivasManuais']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remover uma unidade operacional
     *
     * @param  UnidadeOperacionalModel $model
     * @return bool
     */
    public function delete(UnidadeOperacionalModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync das regioes (unidade_operacional_regioes)
     *
     * @param  UnidadeOperacionalModel $model
     * @param  mixed $data
     * @return UnidadeOperacionalModel
     */
    public function regioesSync(UnidadeOperacionalModel $model, $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($model, $data) {
            //Atualiza o updated_at da Unidade Operacional caso tenha alguma alteração na abrangência
            $regioes = [];
            if ($data) {
                $regioes = $model->regioes()->whereIn('regioes.id', $data)->pluck('regioes.id')->toArray();
            }
            if ($data && count(array_diff($data, $regioes)) > 0 || !$data && count($regioes) > 0) {
                $model->touchAbrangenciaAt();
            }
            //Fim

            $model->regioes()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync dos estados (unidade_operacional_abrangencia_estados)
     *
     * @param  UnidadeOperacionalModel $model
     * @param  mixed $data
     * @return UnidadeOperacionalModel
     */
    public function estadosSync(UnidadeOperacionalModel $model, $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($model, $data) {
            //Atualiza o updated_at da Unidade Operacional caso tenha alguma alteração na abrangência
            $estados = [];
            if ($data) {
                $estados = $model->abrangenciaEstadual()->whereIn('estados.id', $data)->pluck('estados.id')->toArray();
            }
            if ($data && count(array_diff($data, $estados)) > 0 || !$data && count($estados) > 0) {
                $model->touchAbrangenciaAt();
            }
            //Fim

            $model->abrangenciaEstadual()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync das cidades (unidade_operacional_abrangencia_cidades)
     *
     * @param  UnidadeOperacionalModel $model
     * @param  mixed $data
     * @return UnidadeOperacionalModel
     */
    public function cidadesSync(UnidadeOperacionalModel $model, $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($model, $data) {
            //Atualiza o updated_at da Unidade Operacional caso tenha alguma alteração na abrangência
            $cidades = [];
            if ($data) {
                $cidades = $model->abrangenciaMunicipal()->whereIn('cidades.id', $data)->pluck('cidades.id')->toArray();
            }
            if ($data && count(array_diff($data, $cidades)) > 0 || !$data && count($cidades) > 0) {
                $model->touchAbrangenciaAt();
            }
            //Fim

            $model->abrangenciaMunicipal()->sync($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Sync das cidades (unidade_operacional_abrangencia_cidades)
     *
     * @param  UnidadeOperacionalModel $model
     * @param  mixed $data ID da Unidade Produtiva
     * @return UnidadeOperacionalModel
     */
    public function unidadesProdutivasManuaisSync(UnidadeOperacionalModel $model, $data): UnidadeOperacionalModel
    {
        return DB::transaction(function () use ($model, $data) {
            if (!$data) {
                $data = [];
            }

            //Retira as Unidades Produtivas que foram "retiradas" do "select"
            $excludeIds = $model->unidadesProdutivasManuais()->whereNotIn('unidade_produtivas.id', $data)->pluck('unidade_produtivas.id')->toArray();
            if (count($excludeIds) > 0) {
                $model->unidadesProdutivasManuais()->detach($excludeIds);
            }

            $newIds = [];
            //Pega os IDS das unidades produtivas que já estão na Unidade Operacional
            if (count($data) > 0) {
                $attachedIds = $model->unidadesProdutivas()->whereIn('unidade_produtivas.id', $data)->pluck('unidade_produtivas.id')->toArray();
                $newIds = array_diff($data, $attachedIds);

                //Insere os novos ids com "add_manual" = 1
                $model->unidadesProdutivasManuais()->attach($newIds, ['add_manual'=>1, 'unidade_operacional_id'=>$model->id]);
            }

            //Atualiza o updated_at da Unidade Operacional caso tenha alguma alteração na abrangência
            if (count($excludeIds) > 0 || count($newIds) > 0) {
                $model->touchAbrangenciaAt();
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
