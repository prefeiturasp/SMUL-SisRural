<?php

namespace App\Observers;

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Events\ChecklistUnidadeProdutivaFinished;
use App\Models\Core\ChecklistAprovacaoLogsModel;
use App\Models\Core\PlanoAcaoModel;
use App\Services\ChecklistNotificationService;

class ChecklistUnidadeProdutivaObserver
{
    //Status = Rascunho
    ////Não faz nada

    //Tem Fluxo
    ////Status = Finalizado & Formulário tem PDA Obrigatorio & (StatusFlow = "null" || StatusFlow = "Aguardando Revisão")
    //////Status vira "Aguardando PDA"

    ////Status = Finalizado & (StatusFlow = "null" || StatusFlow = "Aguardando Revisão")
    //////Status vira "Aguardando Aprovação"
    //////Dispara emails para os Responsáveis

    //Status = "Aguardando Aprovação" & StatusFlow = "Aguardando Revisão"
    ////Status vira "Rascunho"
    //////StatusFlow vira "Aguardando Revisão"
    //////Dispara email p/ o Técnico que solicitou?

    //Status = "Aguardando Aprovação" & StatusFlow = "Aprovado"
    ////Status vira "Finalizado"
    //////StatusFlow vira "Aprovado"
    //////Dispara email p/ o Técnico que solicitou?

    //Status = "Aguardando Aprovação" & StatusFlow = "Reprovado"
    ////Status vira "Finalizado"
    //////StatusFlow vira "Reprovado"
    //////Dispara email p/ o Técnico que solicitou?

    public function saving(ChecklistUnidadeProdutivaModel $model)
    {
        $status = $model->status;

        //Rascunho não faz nada
        if ($status == ChecklistStatusEnum::Rascunho) {
            return;
        }

        //Só dispara caso o Status ou o StatusFlow tenha sido alterado

        //Ver se o isDirty Dispara no create?
        if ($model->isDirty('status') || $model->isDirty('status_flow')) {
            //Possui Fluxo
            if ($model->checklist->fl_fluxo_aprovacao) {
                $isFormularioObrigatorio = $model->checklist->plano_acao == PlanoAcaoEnum::Obrigatorio;

                if ($model->status == ChecklistStatusEnum::Finalizado && $isFormularioObrigatorio && (!$model->status_flow || $model->status_flow == ChecklistStatusFlowEnum::AguardandoRevisao)) {
                    //PDA só pode ser concluído quando o formulário estiver finalizado, por isso não vai nenhum teste aqui.
                    $model->status = ChecklistStatusEnum::AguardandoPda;
                    $model->status_flow = null;
                } else if ($model->status == ChecklistStatusEnum::Finalizado && (!$model->status_flow || $model->status_flow == ChecklistStatusFlowEnum::AguardandoRevisao)) {
                    $model->status = ChecklistStatusEnum::AguardandoAprovacao;
                    $model->status_flow = null;
                } else if ($model->status == ChecklistStatusEnum::AguardandoAprovacao && $model->status_flow == ChecklistStatusFlowEnum::AguardandoRevisao) {
                    $model->status = ChecklistStatusEnum::Rascunho;
                    $model->status_flow = ChecklistStatusFlowEnum::AguardandoRevisao;
                } else if ($model->status == ChecklistStatusEnum::AguardandoAprovacao && $model->status_flow == ChecklistStatusFlowEnum::Aprovado) {
                    $model->status = ChecklistStatusEnum::Finalizado;
                    $model->status_flow = ChecklistStatusFlowEnum::Aprovado;
                } else if ($model->status == ChecklistStatusEnum::AguardandoAprovacao && $model->status_flow == ChecklistStatusFlowEnum::Reprovado) {
                    $model->status = ChecklistStatusEnum::Finalizado;
                    $model->status_flow = ChecklistStatusFlowEnum::Reprovado;
                }

                if ($isFormularioObrigatorio && $model->status_flow == ChecklistStatusFlowEnum::Aprovado) {
                    foreach ($model->plano_acao as $k => $v) {
                        $v->status = PlanoAcaoStatusEnum::EmAndamento;
                        $v->save();
                    }
                } else if ($isFormularioObrigatorio && $model->status_flow == ChecklistStatusFlowEnum::Reprovado) {
                    foreach ($model->plano_acao as $k => $v) {
                        $v->status = PlanoAcaoStatusEnum::Cancelado;
                        $v->save();
                    }
                } else if ($isFormularioObrigatorio && $model->status_flow == ChecklistStatusFlowEnum::AguardandoRevisao  && $model->status == ChecklistStatusEnum::Rascunho) {
                    foreach ($model->plano_acao as $k => $v) {
                        $v->status = PlanoAcaoStatusEnum::Rascunho;
                        $v->save();
                    }
                }
            } else {
                //Não Possui Fluxo && se status finalizado, dispara email
                if ($model->status == ChecklistStatusEnum::Finalizado) {
                    $this->dispatchStatusFinished($model);
                }
            }
        }
    }

    public function saved(ChecklistUnidadeProdutivaModel $model)
    {
        if ($model->isDirty('status') || $model->isDirty('status_flow')) {
            if ($model->checklist->fl_fluxo_aprovacao) {
                ChecklistNotificationService::sendAnalisisFlowMail($model);
            }
        }

        if ($model->isDirty('status') && $model->checklist->fl_fluxo_aprovacao && $model->checklist->fl_fluxo_aprovacao && $model->status_flow == null && $model->status == ChecklistStatusEnum::AguardandoAprovacao) {
            ChecklistAprovacaoLogsModel::create(['checklist_unidade_produtiva_id' => $model->id, 'user_id' => auth()->user()->id, 'message' => 'Enviou para análise', 'status' => ""]);
        }
    }

    private function dispatchStatusFinished(ChecklistUnidadeProdutivaModel $model)
    {
        event(new ChecklistUnidadeProdutivaFinished($model));
    }
}
