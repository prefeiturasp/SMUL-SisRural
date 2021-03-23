<?php

namespace App\Repositories\Backend\Core;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\PlanoAcaoItemModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoColetivoItemRepository extends BaseRepository
{
    public function __construct(PlanoAcaoItemModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cadastro
     *
     * Caso seja coletivo e não é o "clone" (plano_acao_item_coletivo_id), cria as cópias para cada Unidade Produtiva vinculada ao Plano de Ação Coletivo
     *
     * @param  array $data
     * @return PlanoAcaoItemModel
     */
    public function create(array $data): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($data) {
            $data['fl_coletivo'] = 1;

            if (@$data['prioridade'] == PlanoAcaoPrioridadeEnum::Atendida) {
                $data['status'] = PlanoAcaoItemStatusEnum::Concluido;
            }

            $model = $this->model::create($data);

            if ($model) {
                //Caso o item seja do Plano de Ação Coletivo, deve pegar todos os planos de acoes e adicionar o novo item com as mesmas infos.
                if ($model->fl_coletivo && !$model->plano_acao_item_coletivo_id) {
                    foreach ($model->plano_acao->plano_acao_filhos as $k => $v) {
                        $planoAcaoItemCopia = $model->replicate();
                        $planoAcaoItemCopia->uid = null;

                        $planoAcaoItemCopia->ultima_observacao = null;
                        $planoAcaoItemCopia->ultima_observacao_data = null;

                        $planoAcaoItemCopia->plano_acao_id = $v->id;
                        $planoAcaoItemCopia->plano_acao_item_coletivo_id = $model->id;
                        $planoAcaoItemCopia->fl_coletivo = 1;

                        $planoAcaoItemCopia->save();
                    }
                }

                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização
     *
     * Caso seja coletivo e não é o "clone" (plano_acao_item_coletivo_id), atualiza os itens vinculados a ele
     *
     * @param  PlanoAcaoItemModel $model
     * @param  array $data
     * @return PlanoAcaoItemModel
     */
    public function update(PlanoAcaoItemModel $model, array $data): PlanoAcaoItemModel
    {
        return DB::transaction(function () use ($model, $data) {
            if (@$data['prioridade'] == PlanoAcaoPrioridadeEnum::Atendida) {
                $data['status'] = PlanoAcaoItemStatusEnum::Concluido;
            }

            $model->update($data);

            if ($model) {
                if ($model->fl_coletivo && !$model->plano_acao_item_coletivo_id) {

                    //Atualização dos itens "filhos"
                    foreach ($model->plano_acao_item_filhos as $k => $v) {
                        $v->descricao = $model->descricao;
                        $v->prioridade = $model->prioridade;
                        $v->status = $model->status;
                        $v->prazo = $model->prazo;
                        $v->save();
                    }
                }

                return $model;
            }


            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remoção de um item do plano de ação coletivo
     *
     * Caso seja coletivo e não é o "clone" (plano_acao_item_coletivo_id), remove os itens vinculados a ele
     *
     * @param  PlanoAcaoItemModel $model
     * @return bool
     */
    public function delete(PlanoAcaoItemModel $model): bool
    {
        // TODO Aguardando cliente definir se a exclusão é Física ou Lógica

        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                if ($model->fl_coletivo && !$model->plano_acao_item_coletivo_id) {
                    foreach ($model->plano_acao_item_filhos as $k => $v) {
                        $v->delete();
                    }
                }

                return true;
            }

            //SAMPLES de exclusao física
            // foreach ($model->historicos()->withTrashed()->get() as $vHistorico) {
            //     $vHistorico->forceDelete();
            // }

            // if ($model->forceDelete()) {
            //     return true;
            // }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Reabre uma ação do plano de ação coletivo (ação coletiva e propaga para as individuais // ou só individual)
     *
     * @param  PlanoAcaoItemModel $model
     * @return void
     */
    public function reopen(PlanoAcaoItemModel $model): PlanoAcaoItemModel
    {
        return $this->update($model, ['status' => PlanoAcaoItemStatusEnum::EmAndamento]);
    }
}
