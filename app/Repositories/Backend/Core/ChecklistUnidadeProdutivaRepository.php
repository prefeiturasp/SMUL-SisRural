<?php

namespace App\Repositories\Backend\Core;

use App\Enums\ChecklistStatusEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Enums\TipoPerguntaEnum;
use App\Exceptions\GeneralException;
use App\Jobs\ProcessChecklistUnidadeProdutivas;
use App\Models\Core\ChecklistAprovacaoLogsModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChecklistUnidadeProdutivaRepository extends BaseRepository
{
    public function __construct(ChecklistUnidadeProdutivaModel $model, PlanoAcaoRepository $planoAcaoRepository)
    {
        $this->model = $model;
        $this->planoAcaoRepository = $planoAcaoRepository;
    }

    /**
     * Verifica se no momento de finalizar um formulário, todas as perguntas obrigatórias foram respondidas
     *
     * Dispara uma exception.
     *
     * $isApp serve para customizar a mensagem de retorno da exception. Não queriam mostrar a lista de todas que não foram respondidas.
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @param  bool $isApp
     * @return void
     */
    private function throwIfPerguntasSemRespostas(ChecklistUnidadeProdutivaModel $model, $isApp = false)
    {
        $perguntasObrigatorias = ChecklistPerguntaModel::with('pergunta')->where("fl_obrigatorio", 1)->whereIn('checklist_categoria_id', $model->checklist->categorias->pluck('id'))->get();
        $respostasUnidadeProdutiva = UnidadeProdutivaRespostaModel::where("unidade_produtiva_id", $model->unidade_produtiva_id)->get();

        //vai ignorar multiplas respostas, mas não tem problema, queremos saber apenas se existe alguma resposta atrelada ou não
        $respostasUnidadeProdutiva = $respostasUnidadeProdutiva->keyBy('pergunta_id');

        $notfound = array();
        foreach ($perguntasObrigatorias as $k => $v) {
            if (!@$respostasUnidadeProdutiva[$v->pergunta_id]) {
                $notfound[] = $v;
            }
        }

        if (count($notfound) > 0) {
            if ($isApp) {
                throw new GeneralException('Para finalizar o formulário, é necessário responder as perguntas obrigatórias.');
            }

            $messagePerguntas = join("<br/>", collect($notfound)->pluck('pergunta.pergunta')->toArray());
            throw new GeneralException('Para finalizar o formulário, é necessário responder as perguntas obrigatórias: <br/><br/>' . $messagePerguntas);
        }
    }

    /**
     * Aplicar um formulário inicial
     *
     * @param  mixed $data
     * @return ChecklistUnidadeProdutivaModel
     */
    public function create(array $data): ChecklistUnidadeProdutivaModel
    {
        return DB::transaction(function () use ($data) {
            $customData = collect($data)->only(['checklist_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'status_flow'])->toArray();
            $customData['user_id'] = \Auth::user()->id;

            $model = $this->model::create($customData);

            //salva as respostas
            $this->saveRespostas($data, $model);

            //caso o status seja finalizado/ou vai para aprovação, verifica se todas as perguntas foram respondidas
            if ($model->status == ChecklistStatusEnum::Finalizado || $model->status == ChecklistStatusEnum::AguardandoAprovacao || $model->status == ChecklistStatusEnum::AguardandoPda) {
                $this->throwIfPerguntasSemRespostas($model);
            }

            //caso o status seja finalizado (no momento de salvar), gera a "cópia" das respostas da unidade produtiva
            if ($data['status'] == ChecklistStatusEnum::Finalizado) {
                $this->saveRespostasSnapshot($model);
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza o formulário aplicado
     *
     * Faz basicamente as mesmas ações do "create"
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @param  mixed $data
     * @param  bool $isApp
     * @return ChecklistUnidadeProdutivaModel
     */
    public function update(ChecklistUnidadeProdutivaModel $model, array $data, $isApp = false): ChecklistUnidadeProdutivaModel
    {
        return DB::transaction(function () use ($model, $data, $isApp) {
            $customData = collect($data)->only(['checklist_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'status_flow'])->toArray();

            //Se ele ainda não é Finalizado, persite ainda as respostas globais
            if ($model->status != ChecklistStatusEnum::Finalizado) {
                $this->saveRespostas($data, $model);
            }

            $model->update($customData);

            //caso o status seja finalizado/ou vai para aprovação, verifica se todas as perguntas foram respondidas
            if ($model->status == ChecklistStatusEnum::Finalizado || $model->status == ChecklistStatusEnum::AguardandoAprovacao || $model->status == ChecklistStatusEnum::AguardandoPda) {
                $this->throwIfPerguntasSemRespostas($model, $isApp);
            }

            //caso o status seja finalizado (no momento de salvar), gera a "cópia" das respostas da unidade produtiva
            if ($data['status'] == ChecklistStatusEnum::Finalizado) {
                $this->saveRespostasSnapshot($model);
            }

            $checklistSnapshotRespostaId = $model->respostasMany->pluck('id', 'pergunta_id');

            //Atualizar os relacionamentos do PDA com esse formulário
            if ($model->status == ChecklistStatusEnum::AguardandoPda) {
                foreach ($model->plano_acao as $v) {
                    if ($v->status == PlanoAcaoStatusEnum::Rascunho) {
                        foreach ($v->itens as $vv) {
                            //atualiza a referencia do item do plano de ação com a resposta do snapshot_resposta
                            $pergunta_id = $vv->checklist_pergunta->pergunta_id;
                            $vv->checklist_snapshot_resposta_id = @$checklistSnapshotRespostaId[$pergunta_id];
                            $vv->save();
                        }
                    }
                }
            }

            if ($model->status == ChecklistStatusEnum::Rascunho) {
                foreach ($model->plano_acao as $v) {
                    if ($v->status == PlanoAcaoStatusEnum::Rascunho) {
                        $this->planoAcaoRepository->updatePrioridades($v);
                    }
                }
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove um formulário aplicado (softdelete), olhar o ChecklistUnidadeProdutivaPolicy para ver as regras
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @return bool
     */
    public function delete(ChecklistUnidadeProdutivaModel $model): bool
    {
        //Não deleta os PDA`s vinculados ao formulário aplicado removido.

        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove físicamente o registro
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @return bool
     *
     * @deprecated Não é possível remover fisicamente por causa do sync dos dados com o APP. Ainda não foi visto uma solução para isso.
     */
    public function forceDelete(ChecklistUnidadeProdutivaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->forceDelete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Restaura um formulário aplicado que foi removido (softdelete), olhar o ChecklistUnidadeProdutivaPolicy para ver as regras
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @return bool
     */
    public function restore(ChecklistUnidadeProdutivaModel $model): bool
    {
        //Não restaura os PDA`s vinculados ao formulário aplicado removido.

        return DB::transaction(function () use ($model) {
            if ($model->restore()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Salva as respostas da unidade produtiva (unidade_produtiva_respostas) no checklist aplicado (checklist_snapshot_respostas)
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @return bool
     */
    public function saveRespostasSnapshot(ChecklistUnidadeProdutivaModel $model): bool
    {
        //Força remoção, ex: Rascunho -> Finalizado -> Aguardando Aprovação -> Rascunho -> Aprovação -> Finalizado
        ChecklistSnapshotRespostaModel::where('checklist_unidade_produtiva_id', $model->id)->delete();

        //Propaga as respostas de todas as perguntas (normais e arquivadas)
        $perguntasChecklist = $model->checklist->categorias()->with('perguntas')->get()->pluck('perguntas')->collapse()->pluck('id')->all();

        $respostas = $model->unidade_produtiva->respostasMany->whereIn('pergunta_id', $perguntasChecklist);

        foreach ($respostas as $k => $v) {
            $v->touch(); //Atualiza o "updated_at"

            $data = ['checklist_unidade_produtiva_id' => $model->id, 'pergunta_id' => $v['pergunta_id'], 'resposta_id' => $v['resposta_id'], 'resposta' => $v['resposta']];
            ChecklistSnapshotRespostaModel::create($data);
        }

        ProcessChecklistUnidadeProdutivas::dispatch($model->id);

        return true;
    }

    /**
     * Salva as respostas do formulário aplicado na "Unidade Produtiva" (tabela unidade_produtiva_respostas)
     *
     * @param  mixed $data
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    private function saveRespostas($data, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        //Tratamento especifico para o OfflineApi -> checklist_finalizar
        if (@!$data['checklist_id']) {
            return;
        }

        //Separa todas as perguntas do formulário aplicado
        $perguntas = ChecklistModel::where('id', $data['checklist_id'])->first()->categorias()->with('perguntas')->get()->pluck("perguntas")->collapse()->all();

        //Separa os tipos de pergunta por "id", isso é utilizado para saber onde deve salvar a resposta, coluna "resposta_id" (se a resposta for texto, ex: resposta de uma semafórica) ou "resposta" (se for texto)
        $perguntasTipo = array();
        foreach ($perguntas as $k => $v) {
            $perguntasTipo[$v['id']] = $v['tipo_pergunta'];
        }

        //Separa as questões respondidas no form
        $questions = array_filter($data, function ($k) {
            return \is_numeric($k);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($questions as $k => $v) {
            $tipo = $perguntasTipo[$k];
            $isTexto = ($tipo == TipoPerguntaEnum::Anexo || $tipo == TipoPerguntaEnum::NumericaPontuacao  || $tipo == TipoPerguntaEnum::Numerica ||  $tipo == TipoPerguntaEnum::Texto || $perguntasTipo[$k] == TipoPerguntaEnum::Tabela);

            $template_resposta_id = $isTexto ? null : $v;
            $resposta = $isTexto ? $v : null;

            if ($v) {
                $where = ['unidade_produtiva_id' => $checklistUnidadeProdutiva->unidade_produtiva_id, 'pergunta_id' => $k];

                //Se for um array (multipla-escolha), salva todas as respostas
                if (is_array($v)) {
                    //Primeiro remove todas as respostas para aquela pergunta
                    UnidadeProdutivaRespostaModel::where($where)->withTrashed()->update(['deleted_at' => Carbon::now()]);

                    //Agora insere/restaura todas as respostas para aquela pergunta
                    foreach ($v as $kk => $vv) {
                        UnidadeProdutivaRespostaModel::withTrashed()->updateOrCreate(array_merge($where, ['resposta_id' => $vv]), ['resposta' => null])->restore();
                    }
                } else {
                    //Se for uma escolha, salva o "resposta" e "resposta_id", dependendo o tipo de pergunta é um campo.
                    $model = UnidadeProdutivaRespostaModel::updateOrCreate(['unidade_produtiva_id' => $checklistUnidadeProdutiva->unidade_produtiva_id, 'pergunta_id' => $k], ['resposta_id' => $template_resposta_id, 'resposta' => @$resposta]);

                    //Faz o upload caso seja um "anexo"
                    if ($tipo == TipoPerguntaEnum::Anexo) {
                        if ($v->isValid()) {
                            $resposta = 'unidade_produtiva_respostas/' . $model->id . '.' . $v->getClientOriginalExtension();
                            \Storage::put($resposta, file_get_contents($v->getRealPath()));
                            //$resposta = $v->storeAs('unidade_produtiva_respostas', $model->id . '.' . $v->getClientOriginalExtension(), ['disk' => 'public']);

                            if ($resposta) {
                                $model->resposta = $resposta;
                                $model->save();
                            }
                        }
                    }
                }
            }
        }

        //Atualiza data de atualização do formulário aplicado
        if (count($questions) > 0) {
            $checklistUnidadeProdutiva->touch();
        }
    }

    /**
     * Se o formulário possuí fluxo de aprovação e ele possuí o "status_flow"= "aguardando_revisão".
     *
     * Permite que o usuário reanalise (formulário volta p/ o status "aguardando_aprovacao")
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @return ChecklistUnidadeProdutivaModel
     */
    public function reanalyse(ChecklistUnidadeProdutivaModel $model): ChecklistUnidadeProdutivaModel
    {
        return DB::transaction(function () use ($model) {
            $model->status = ChecklistStatusEnum::Finalizado;

            if ($model->save()) {
                if ($model->checklist->fl_fluxo_aprovacao) {
                    ChecklistAprovacaoLogsModel::create(['checklist_unidade_produtiva_id' => $model->id, 'user_id' => auth()->user()->id, 'message' => 'Analista alterou status para "Aguardando Aprovação"', 'status' => ""]);
                }

                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
