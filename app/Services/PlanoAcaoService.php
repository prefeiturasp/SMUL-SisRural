<?php

namespace App\Services;

use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;

class PlanoAcaoService
{

    /**
     * Valida se é possível criar um PDA
     *
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return bool
     */
    public function permiteCriarPda(ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva): bool
    {
        $list = PlanoAcaoModel::where('produtor_id', $produtor->id)->where('unidade_produtiva_id', $unidadeProdutiva->id)->where('fl_coletivo', 0)->whereNull('checklist_unidade_produtiva_id')->whereIn('status', [PlanoAcaoStatusEnum::EmAndamento, PlanoAcaoStatusEnum::NaoIniciado]);

        if ($list->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Valida se é possível criar um PDA com Checklist
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return bool
     */
    public function permiteCriarPdaComChecklist(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva): bool
    {
        //Valida se tem algum PDA iniciado/em andamento
        $list = PlanoAcaoModel::where('checklist_unidade_produtiva_id', $checklistUnidadeProdutiva->id)->whereIn('status', [PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::AguardandoAprovacao, PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento]);

        if ($list->exists()) {
            return false;
        }

        return true;
    }


    /**
     * Valida se permite criar apenas um PDA obrigatório/opcional vinculado a um formulário
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return bool
     */
    public function permiteCriarPdaComChecklistConcluido(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva): bool
    {
        //Se tiver ao menos um concluído, não permite mais criar PDA vinculando com o formulário aplicado (aprovado/reprovado)
        $listConcluido = PlanoAcaoModel::where('checklist_unidade_produtiva_id', $checklistUnidadeProdutiva->id)->whereIn('status', [PlanoAcaoStatusEnum::Concluido]);
        if ($listConcluido->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Valida se é permitido criar um PDA com Formulário / valida o template do formulário (plano_acao e perguntas)
     *
     * @param  ChecklistModel $checklist
     * @return bool
     */
    public function permiteCriarPdaComChecklistPerguntas(ChecklistModel $checklist): bool
    {
        //Verifica se é possível criar um plano de ação com o checklist selecionado
        if ($checklist->plano_acao == PlanoAcaoEnum::NaoCriar) {
            return false;
        }

        //Verifica se tem alguma pergunta que entra no plano de ação
        $checklistPerguntas = ChecklistPerguntaModel::with('pergunta')->where("fl_plano_acao", 1)->whereIn('checklist_categoria_id', $checklist->categorias->pluck('id'))->orderBy('plano_acao_prioridade', 'ASC')->get();
        if (count($checklistPerguntas) == 0) {
            return false;
        }

        return true;
    }
}
