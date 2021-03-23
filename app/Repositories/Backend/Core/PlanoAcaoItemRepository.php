<?php

namespace App\Repositories\Backend\Core;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\PlanoAcaoItemModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoItemRepository extends BaseRepository
{
    public function __construct(PlanoAcaoItemModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criar um Item no Plano de ação
     *
     * @param  mixed $data
     * @return PlanoAcaoItemModel
     */
    public function create(array $data): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($data) {
            if (@$data['prioridade'] == PlanoAcaoPrioridadeEnum::Atendida) {
                $data['status'] = PlanoAcaoItemStatusEnum::Concluido;
            }

            $model = $this->model::create($data);

            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualizar um Item no Plano de ação
     *
     * @param  PlanoAcaoItemModel $model
     * @param  mixed $data
     * @return PlanoAcaoItemModel
     */
    public function update(PlanoAcaoItemModel $model, array $data): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($model, $data) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            if (@$data['prioridade'] == PlanoAcaoPrioridadeEnum::Atendida) {
                $data['status'] = PlanoAcaoItemStatusEnum::Concluido;
            }

            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remover um Item do PDA
     *
     * @param  PlanoAcaoItemModel $model
     * @return bool
     */
    public function delete(PlanoAcaoItemModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            // TODO Caso seja um excluir para sempre
            // foreach ($model->historicos()->withTrashed()->get() as $vHistorico) {
            //     $vHistorico->forceDelete();
            // }

            // if ($model->forceDelete()) {
            //     return true;
            // }

            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Avança a "prioridade"
     *
     * @param  PlanoAcaoItemModel $model
     * @return void
     */
    public function prioridadeUp(PlanoAcaoItemModel $model): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($model) {
            $prioridade = $model->prioridade;

            if ($prioridade == PlanoAcaoPrioridadeEnum::AcaoRecomendada) {
                $prioridade = PlanoAcaoPrioridadeEnum::PriorizacaoTecnica;
            } else if ($prioridade == PlanoAcaoPrioridadeEnum::Atendida) {
                $prioridade = PlanoAcaoPrioridadeEnum::AcaoRecomendada;
            }

            $model->update(['prioridade' => $prioridade]);

            return $model;

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Volta a "prioridade"
     *
     * @param  PlanoAcaoItemModel $model
     * @return PlanoAcaoItemModel
     */
    public function prioridadeDown(PlanoAcaoItemModel $model): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($model) {
            $prioridade = $model->prioridade;

            if ($prioridade == PlanoAcaoPrioridadeEnum::PriorizacaoTecnica) {
                $prioridade = PlanoAcaoPrioridadeEnum::AcaoRecomendada;
            } else if ($prioridade  == PlanoAcaoPrioridadeEnum::AcaoRecomendada) {
                $prioridade = PlanoAcaoPrioridadeEnum::Atendida;
            }

            $model->update(['prioridade' => $prioridade]);

            return $model;

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Reabre um item do PDA que foi concluído
     *
     * @param  PlanoAcaoItemModel $model
     * @return PlanoAcaoItemModel
     */
    public function reopen(PlanoAcaoItemModel $model): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($model) {
            $model->status = PlanoAcaoStatusEnum::EmAndamento;
            $model->save();

            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
