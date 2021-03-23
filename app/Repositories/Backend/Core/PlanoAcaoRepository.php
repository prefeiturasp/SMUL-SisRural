<?php

namespace App\Repositories\Backend\Core;

use App\Enums\ChecklistStatusEnum;
use App\Enums\CorEnum;
use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoRepository extends BaseRepository
{
    public function __construct(PlanoAcaoModel $model, PlanoAcaoItemRepository $repositoryItem)
    {
        $this->model = $model;
        $this->repositoryItem = $repositoryItem;
    }

    /**
     * Cadastro do PDA Individual/Formulário
     *
     * @param  mixed $data
     * @return PlanoAcaoModel
     */
    public function create(array $data): PlanoAcaoModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            //Cria as ações individuais (plano_acao_item) caso seja PDA originado de um Formulário (Checklist)
            if (@$data['checklist_unidade_produtiva_id']) {

                //Verifica se existem perguntas p/ gerar o plano de ação (caso seja do tipo PDA Formulário)
                $checklistPerguntas = ChecklistPerguntaModel::with('pergunta')->where("fl_plano_acao", 1)->whereIn('checklist_categoria_id', $model->checklist_unidade_produtiva->checklist->categorias->pluck('id'))->orderBy('plano_acao_prioridade', 'ASC')->get();
                if (count($checklistPerguntas) == 0) {
                    throw new GeneralException('Formulário inválido p/ gerar plano de ação');
                }

                $respostas = ChecklistSnapshotRespostaModel::where('checklist_unidade_produtiva_id', $model->checklist_unidade_produtiva_id)->pluck('id', 'pergunta_id');

                //Cria os itens do PDA através das "perguntas/respostas" do formulário aplicado
                foreach ($checklistPerguntas as $k => $v) {
                    $checklist_snapshot_resposta_id = @$respostas[$v->pergunta_id];

                    $prioridade = $v->plano_acao_prioridade ? $v->plano_acao_prioridade : PlanoAcaoPrioridadeEnum::AcaoRecomendada;

                    $this->repositoryItem->create(['plano_acao_id' => $model->id, 'checklist_pergunta_id' => $v->id, 'checklist_snapshot_resposta_id' => $checklist_snapshot_resposta_id, 'descricao' => $v->pergunta->plano_acao_default, 'status' => PlanoAcaoItemStatusEnum::NaoIniciado, 'prioridade' => $prioridade, 'prazo' => $model->prazo]);
                }

                $this->updatePrioridades($model);
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza as prioridades
     *
     * Se for um plano de ação com formulário e estiver em modo rascunho
     *
     * @param  PlanoAcaoModel $model
     * @return void
     */
    public function updatePrioridades(PlanoAcaoModel $model)
    {
        if ($model->checklist_unidade_produtiva_id && $model->status == PlanoAcaoStatusEnum::Rascunho) {
            $respostas = $model->checklist_unidade_produtiva->getRespostas();

            foreach ($model->itens as $k => $v) {
                $pergunta_id = $v->checklist_pergunta->pergunta_id;
                $resposta = $respostas[$pergunta_id];
                $cor = @$resposta['resposta_cor'];

                if (@$cor && ($cor == CorEnum::Verde || $cor == CorEnum::Cinza) && $v->prioridade != PlanoAcaoPrioridadeEnum::Atendida) {
                    $v->prioridade = PlanoAcaoPrioridadeEnum::Atendida;
                    $v->status = PlanoAcaoItemStatusEnum::Concluido;
                    $v->save();
                } else if (@$cor && $cor != CorEnum::Verde && $cor != CorEnum::Cinza && $v->prioridade == PlanoAcaoPrioridadeEnum::Atendida) {
                    $v->prioridade = $v->checklist_pergunta->plano_acao_prioridade;
                    $v->status = PlanoAcaoItemStatusEnum::NaoIniciado;
                    $v->save();
                }
            }
        }
    }

    /**
     * Atualização do PDA Individual/Formulário
     *
     * @param  PlanoAcaoModel $model
     * @param  mixed $data
     * @return PlanoAcaoModel
     */
    public function update(PlanoAcaoModel $model, array $data): PlanoAcaoModel
    {
        return DB::transaction(function () use ($model, $data) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            //Não permite "iniciar" o plano de ação caso o formulário ainda esteja em aberto
            if (
                $model->checklist_unidade_produtiva_id
                && $model->checklist_unidade_produtiva->status !== ChecklistStatusEnum::Finalizado
                && $model->checklist_unidade_produtiva->status !== ChecklistStatusEnum::AguardandoPda //Se for esse status, ele permite salvar.
                && $data['status'] == 'nao_iniciado'
            ) {
                throw new GeneralException('Não é possível iniciar o plano de ação com o status atual do formulário aplicado (' . @ChecklistStatusEnum::toSelectArray()[$model->checklist_unidade_produtiva->status] . ').');
            }

            $this->updatePrioridades($model);

            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remover PDA Individual/Formulário
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     */
    public function delete(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Força a remoção de um PDA
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     *
     * @deprecated Não permite essa ação por causa do Sync APP
     */
    public function forceDelete(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            //Remover Acompanhamentos do PDA
            foreach ($model->historicos()->withTrashed()->get() as $vHistorico) {
                $vHistorico->forceDelete();
            }

            foreach ($model->itens()->withTrashed()->get() as $vItem) {
                //Remover Acompanhamentos das Ações
                foreach ($vItem->historicos()->withTrashed()->get() as $vHistoricoItem) {
                    $vHistoricoItem->forceDelete();
                }

                //Remover Ações
                $vItem->forceDelete();
            }

            //Remover PDA
            if ($model->forceDelete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Restaura um PDA removido
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     */
    public function restore(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do coletivo no plano de ação individual/formulário');
            }

            if ($model->restore()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Reabrir um PDA que já foi concluído
     *
     * @param  PlanoAcaoModel $model
     * @return PlanoAcaoModel
     */
    public function reopen(PlanoAcaoModel $model): PlanoAcaoModel
    {
        return DB::transaction(function () use ($model) {
            $model->status = PlanoAcaoStatusEnum::EmAndamento;
            $model->save();

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
