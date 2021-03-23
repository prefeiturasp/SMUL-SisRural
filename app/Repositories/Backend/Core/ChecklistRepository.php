<?php

namespace App\Repositories\Backend\Core;

use App\Enums\TemplateChecklistStatusEnum;
use App\Exceptions\GeneralException;
use App\Helpers\General\SoftDeleteHelper;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistPerguntaRespostaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ChecklistRepository extends BaseRepository
{
    public function __construct(ChecklistModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria um template de formulário
     *
     * - caso seja enviados "usuários" p/ aprovação, descarta "dominios" e "unidades operacionais" da liberação de aplicação.
     * - usuariosAprovação são usuários que podem fazer a análise do formulário
     *
     * @param  mixed $data
     * @return ChecklistModel
     */
    public function create(array $data): ChecklistModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            //Permissão de aplicação
            $model->usuarios()->sync(@$data['usuarios']);
            $model->dominios()->sync(@$data['dominios']);
            $model->unidadesOperacionais()->sync(@$data['unidadesOperacionais']);

            //Permissão de Análise
            $model->usuariosAprovacao()->sync(@$data['usuariosAprovacao']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Verifica se a fórmula cadastrada no template é válida ou não
     *
     * Retorna uma mensagem de erro, caso não é válida.
     *
     * @param  ChecklistModel $model
     * @param  string $formula
     * @return string
     */
    private function isInvalidFormula($model, $formula)
    {
        //Se não tem fórmula informada, passa reto
        if (!$formula) {
            return null;
        }

        //Valida através da aplicação da fórmula com a lista de categorias válidas do Checklist
        try {
            $parser = new \Mathepa\Expression($formula);

            foreach ($model->categorias as $k => $categoria) {
                $parser->setVariable('C' . $categoria['id'], 1);
            }

            $parser->evaluate();

            return null;
        } catch (\Mathepa\Exception\InvalidVariableException $e) {
            //Variable "C10" not set -> Categoria "C10" não existe
            $message = str_replace("Variable", "Categoria", $e->getMessage());
            $message = str_replace("not set", "não existe", $message);
            return $message;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Normaliza a fórmula
     *
     * @param  mixed $formula
     * @return string
     */
    public function normalizeFormula($formula)
    {
        $formula = str_replace(array("{", "["), "(", $formula);
        $formula = str_replace(array("}", "]"), ")", $formula);
        $formula = str_replace(array(","), ".", $formula);
        $formula = str_replace("c", "C", $formula);

        return $formula;
    }

    /**
     * Atualização do template do formulário.
     *
     * @param  ChecklistModel $model
     * @param  mixed $data
     * @return ChecklistModel
     */
    public function update(ChecklistModel $model, array $data): ChecklistModel
    {
        return DB::transaction(function () use ($model, $data) {
            //Verifica se a fórmula informado é válida
            $invalidFormula =  $this->isInvalidFormula($model, $data['formula']);
            if ($invalidFormula) {
                throw new GeneralException($invalidFormula);
            }

            $model->update($data);

            //Permissão de aplicação
            SoftDeleteHelper::syncSoftDelete($model->usuariosWithTrashed(), $model->id, @$data['usuarios']);
            SoftDeleteHelper::syncSoftDelete($model->dominiosWithTrashed(), $model->id, @$data['dominios']);
            SoftDeleteHelper::syncSoftDelete($model->unidadesOperacionaisWithTrashed(), $model->id, @$data['unidadesOperacionais']);

            //Permissão de Análise
            $model->usuariosAprovacao()->sync(@$data['usuariosAprovacao']);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove o formulário
     *
     * Se o template do formulário já foi utilizado, não permite remover. (Apenas inativar)
     *
     * @param  ChecklistModel $model
     * @return bool
     */
    public function delete(ChecklistModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            //double-check p/ não permitir remoção do checklist
            if (ChecklistUnidadeProdutivaModel::where("checklist_id", $model->id)->exists()) {
                throw new GeneralException('Não é possível remover um Checklist utilizado por uma Unidade Produtiva. Você pode alterar o status p/ "Inativo"');
            }

            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Duplica um template de formulário.
     *
     * A duplicação possuí várias etapas
     *
     * - Dados básicos do template
     * - O status é iniciado como "RASCUNHO"
     * - No campo versão é incrementado caso existam cópias do mesmo "formulário base"
     * - Na duplicação o nome do formulário muda, adicionando [NOME] - versão [VERSAO]
     * - É persistido no campo "copia_checklist_id" quem foi o "formulário base" p/ fazer a cópia
     *
     * - Usuários que podem aprovar
     * - Domínios que podem aprovar
     * - Unidades operacionais que podem aprovar
     * - Usuários que podem analisar
     * - Categorias são duplicadas
     * - Perguntas vinculadas as categorias são duplicadas
     * - Respostas das Perguntas são duplicadas
     *
     * @param  mixed $model
     * @return ChecklistModel
     */
    public function duplicate(ChecklistModel $model): ChecklistModel
    {
        return DB::transaction(function () use ($model) {
            //checklist

            //AQUI
            $dominios = auth()->user()->dominios;
            $dominio_id = count($dominios) > 0 ? $dominios->first()->id : $model->dominio_id;

            $checklist = $model->replicate();
            $checklist->dominio_id = $dominio_id;
            $checklist->copia_checklist_id = $model->id;
            $checklist->versao = ChecklistModel::withoutGlobalScopes()->where("copia_checklist_id", $model->id)->count() + 1;
            $checklist->nome = $checklist->nome . ' - versão ' . $checklist->versao;
            $checklist->status = TemplateChecklistStatusEnum::Rascunho;
            $checklist->save();

            //checklist_users
            //checklist_dominios
            //checklist_unidade_operacionais
            //checklist_aprovacao_users
            $checklist->usuarios()->sync(@$model->usuarios->pluck('id')->toArray());
            $checklist->dominios()->sync(@$model->dominios->pluck('id')->toArray());
            $checklist->unidadesOperacionais()->sync($model->unidadesOperacionais->pluck('id')->toArray());
            $checklist->usuariosAprovacao()->sync($model->usuariosAprovacao->pluck('id')->toArray());

            //checklist_categorias
            foreach ($model->categorias as $kCat => $categoria) {
                $novaCategoria = $categoria->replicate();
                $novaCategoria->checklist_id = $checklist->id;
                $novaCategoria->save();

                //checklist_perguntas
                $checklistPerguntas = ChecklistPerguntaModel::where("checklist_categoria_id", $categoria->id)->get();
                foreach ($checklistPerguntas as $kPergunta => $pergunta) {
                    $checklistPerguntaData = array("checklist_categoria_id" => $novaCategoria->id,  "pergunta_id" => $pergunta->pergunta_id, "peso_pergunta" => $pergunta->peso_pergunta, "fl_obrigatorio" => $pergunta->fl_obrigatorio, "fl_plano_acao" => $pergunta->fl_plano_acao, "plano_acao_prioridade" => $pergunta->plano_acao_prioridade, "ordem" => $pergunta->ordem);
                    $novoChecklistPergunta = ChecklistPerguntaModel::create($checklistPerguntaData);

                    //checklist_pergunta_respostas
                    $checklistPerguntaRespostas = ChecklistPerguntaRespostaModel::where("checklist_pergunta_id", $pergunta->id)->get();
                    foreach ($checklistPerguntaRespostas as $kResposta => $vResposta) {
                        $checklistPerguntaRespostaData = array("checklist_pergunta_id" => $novoChecklistPergunta->id, "resposta_id" => $vResposta->resposta_id, "peso" => $vResposta->peso);
                        ChecklistPerguntaRespostaModel::create($checklistPerguntaRespostaData);
                    }
                }
            }

            return $checklist;

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
