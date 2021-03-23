<?php

namespace App\Repositories\Backend\Core;

use App\Enums\TipoPerguntaEnum;
use App\Enums\TipoPontuacaoEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistPerguntaRespostaModel;
use App\Models\Core\RespostaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ChecklistPerguntaRepository extends BaseRepository
{
    public function __construct(ChecklistPerguntaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Vincula uma "Pergunta" com um "Checklist" (template formulário)
     *
     * No momento da vinculação existem alguns campos chaves que compoe a lógica do Formulário.
     *
     * - Pesos de cada resposta (Caso a pergunta seja de pontuação / do tipo semafórica, binária, multipla escolha ou única escolha)
     * - Se é obrigatória ou não, p/ ser validado no momento de finalizar a aplicação
     * - Se entra no plano de ação ou não (caso o template do formulário tenha plano de ação)
     *
     * @param  mixed $data
     * @return ChecklistPerguntaModel
     */
    public function create(array $data): ChecklistPerguntaModel
    {
        return DB::transaction(function () use ($data) {
            //Restaura caso seja softDelete
            $checklistPergunta = ChecklistPerguntaModel::withTrashed()->where(['pergunta_id' => $data['pergunta_id'], 'checklist_categoria_id' => $data['checklist_categoria_id']])->first();
            if ($checklistPergunta) {
                $checklistPergunta->restore();
                $checklistPergunta->update($data);
            } else {
                $checklistPergunta = ChecklistPerguntaModel::create($data);
            }

            //Salvar os pesos das respostas (Caso a questão seja do tipo Semafórica/Binária/Escolha Simples/Escolha Simples com Pontuacao)
            $tipo_pergunta = $checklistPergunta->pergunta->tipo_pergunta;
            if ($tipo_pergunta == TipoPerguntaEnum::Semaforica || $tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $tipo_pergunta == TipoPerguntaEnum::Binaria || $tipo_pergunta == TipoPerguntaEnum::BinariaCinza || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                $respostas = array_filter($data, function ($k) {
                    return \is_numeric($k);
                }, ARRAY_FILTER_USE_KEY);

                foreach ($respostas as $k => $v) {
                    $checklistPerguntaResposta = ChecklistPerguntaRespostaModel::where(['checklist_pergunta_id' => $checklistPergunta->id, 'resposta_id' => $k])->first();

                    if ($checklistPerguntaResposta) {
                        $checklistPerguntaResposta->restore();
                        //* 1 para fixar valor em 0
                        $checklistPerguntaResposta->update(['peso' => $v * 1]);
                    } else {
                        ChecklistPerguntaRespostaModel::create(['checklist_pergunta_id' => $checklistPergunta->id, 'resposta_id' => $k, 'peso' => $v * 1]);
                    }
                }

                //Se for questão de COR, valida se o peso mínimo esta na cor VERMELHA ... o "vermelho" é utilizado para o calculo do valor MÍNIMO possível dentro de uma categoria
                if (
                    in_array($tipo_pergunta, [TipoPerguntaEnum::Semaforica, TipoPerguntaEnum::SemaforicaCinza, TipoPerguntaEnum::Binaria, TipoPerguntaEnum::BinariaCinza])
                    && RespostaModel::find(ChecklistPerguntaRespostaModel::where('checklist_pergunta_id', $checklistPergunta->id)->orderBy('peso', 'asc')->first()->resposta_id)->cor != 'vermelho'
                    && $checklistPergunta->categoria->checklist->tipo_pontuacao != TipoPontuacaoEnum::SemPontuacao
                ) {
                    throw new GeneralException('O PESO mínimo precisa estar na cor "VERMELHA".');
                }
            }


            return $checklistPergunta;

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de uma vinculação de pergunta com o template do formulário.
     *
     * Executa a mesma regra de criação. (porque de certo modo, ele resgata o que foi descartado p/ recriar em cima do registro (por causa do APP/sync mobile))
     *
     * @param  ChecklistPerguntaModel $model
     * @param  mixed $data
     * @return ChecklistPerguntaModel
     */
    public function update(ChecklistPerguntaModel $model, array $data): ChecklistPerguntaModel
    {
        return $this->create($data);
    }

    /**
     * Remove a vinculação de uma pergunta com um template de formulário
     *
     * É importante ressaltar que é feito um "SoftDelete"
     *
     * @param  mixed $model
     * @return bool
     *
     */
    public function delete(ChecklistPerguntaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
