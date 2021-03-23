<?php

namespace App\Observers;

use App\Enums\ChecklistStatusEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Models\Core\PlanoAcaoModel;

class PlanoAcaoObserver
{
    /**
     * No momento que é salvo o plano de ação é verificado se ele tem formulário.
     *
     * Caso tenha formulário:
     *
     * a) Se tiver fluxo de aprovação e o template do checklist é "plano_acao" = "obrigatorio"
     * b) Se o status do formulário aplicado é "aguardando_pda" e "status do PDA" é "nao_iniciado" e o PDA não foi removido
     * c) Move o PDA e o Formulário Aplicado p/ o fluxo de aprovação/aguardando aprovação
     *
     * @param  PlanoAcaoModel $model
     * @return void
     */
    public function saving(PlanoAcaoModel $model)
    {
        //PDA com Formulário, Formulário com PDA Obrigatório, Formulário com Fluxo de Aprovação
        if ($model->checklist_unidade_produtiva_id && $model->checklist_unidade_produtiva->checklist->fl_fluxo_aprovacao && $model->checklist_unidade_produtiva->checklist->plano_acao == PlanoAcaoEnum::Obrigatorio && $model->isDirty('status')) {
            if ($model->checklist_unidade_produtiva->status == ChecklistStatusEnum::AguardandoPda && $model->status == PlanoAcaoStatusEnum::NaoIniciado && !$model->deleted_at) {
                //Atualiza o status do PDA p/ Aguardando Aprovação
                $model->status = PlanoAcaoStatusEnum::AguardandoAprovacao;

                //Atualiza o status do Formulário p/ Aguardando Aprovação
                $model->checklist_unidade_produtiva->status = ChecklistStatusEnum::AguardandoAprovacao;
                $model->checklist_unidade_produtiva->status_flow = null;
                $model->checklist_unidade_produtiva->save();
            }
        }
    }
}
