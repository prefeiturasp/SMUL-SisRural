<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\TipoPerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\ChecklistPerguntaForm;
use App\Models\Core\ChecklistCategoriaModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\PerguntaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait ChecklistCategoriaPerguntaTrait
{

    public function perguntasIndex(ChecklistCategoriaModel $checklistCategoria, Request $request)
    {
        die();
    }

    /**
     * Listagem de todas perguntas que ainda não foram utilizadas no Checklist (template do formulário)
     *
     * @param  mixed $checklistCategoria
     * @param  mixed $request
     * @return void
     */
    public function perguntasTodasIndex(ChecklistCategoriaModel $checklistCategoria, Request $request)
    {
        $title = $checklistCategoria->nome . ' / Perguntas Vinculadas';

        $urlDatatable = route('admin.core.checklist.categorias.perguntas.todasPerguntasDatatable', ["checklistCategoria" => $checklistCategoria]);

        $urlBack = route('admin.core.checklist.categorias.edit', ["checklist" => $checklistCategoria->checklist_id, "checklistCategoria" => $checklistCategoria]);
        return view('backend.core.checklist.categorias.perguntas.create_update_todas_perguntas', compact('urlDatatable', 'urlBack', 'title'));
    }

    /**
     * API Datatable "perguntasTodasIndex()"
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function todasPerguntasDatatable(ChecklistCategoriaModel $checklistCategoria)
    {
        $checklist_id = $checklistCategoria->checklist_id;

        $perguntas_id = array();
        $checklist = ChecklistModel::where("id", $checklist_id)->with('categorias.perguntas')->first();
        foreach ($checklist->categorias as $k => $v) {
            $perguntas_id = array_merge($perguntas_id, $v->perguntas->pluck('id')->toArray());
        }

        return DataTables::of(PerguntaModel::where('fl_arquivada', false)->whereNotIn('id', $perguntas_id))
            ->editColumn('pergunta', function ($row) {
                return $row->pergunta_sinalizada;
            })->addColumn('respostas', function ($row) {
                return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->editColumn('tipo_pergunta', function ($row) {
                return TipoPerguntaEnum::toSelectArray()[$row->tipo_pergunta];
            })->editColumn('tags', function ($row) {
                return AppHelper::tableTags($row->tags);
            })->addColumn('actions', function ($row) use ($checklistCategoria) {
                $params = ['checklistCategoria' => $checklistCategoria->id, 'pergunta' => $row->id];
                $title = 'Vincular Pergunta';

                $addUrl = route('admin.core.checklist.categorias.perguntas.create', $params);
                return view('backend.core.checklist.categorias.perguntas.form_actions', compact('addUrl'));
            })
            ->rawColumns(['pergunta', 'respostas', 'tags'])
            ->make(true);
    }

    /**
     * API Datatable "perguntasDatatable()"
     *
     * Utilizado no método "categoriasEdit()" do "ChecklistCategoriaTrait.php"
     *
     * Retorna as perguntas vinculadas na categoria
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function perguntasDatatable(ChecklistCategoriaModel $checklistCategoria)
    {
        return DataTables::of($checklistCategoria->perguntas)
            ->addColumn('pesos', function ($row) {
                if ($row->tipo_pergunta == TipoPerguntaEnum::NumericaPontuacao) {
                    return $row->pivot->peso_pergunta;
                } else if ($row->tipo_pergunta == TipoPerguntaEnum::Semaforica || $row->tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $row->tipo_pergunta == TipoPerguntaEnum::Binaria || $row->tipo_pergunta == TipoPerguntaEnum::BinariaCinza || $row->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $row->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                    return AppHelper::tableArrayToList($row->pivot->perguntaRespostasPesos->toArray(), 'peso');
                }

                return '-';
            })->addColumn('respostas', function ($row) {
                $dataPesos = $row->pivot->perguntaRespostasPesos->pluck('peso', 'resposta_id');

                $tableRespostasPeso = '<table>';
                foreach ($row->respostas->toArray() as $k => $v) {
                    $tableRespostasPeso .= '<tr><td>' . $v['descricao'] . '</td><td>' . (@!is_null($dataPesos[$v['id']]) ? $dataPesos[$v['id']] : 'Não há peso') . '</td></tr>';
                }
                $tableRespostasPeso .= '</table>';


                $tableRespostasPeso = '';
                foreach ($row->respostas->toArray() as $k => $v) {
                    $tableRespostasPeso .= '<div class="d-flex flex-row"><span style="width:50%;">' . $v['descricao'] . '</span><span style="width:50%;">' . (@!is_null($dataPesos[$v['id']]) ? $dataPesos[$v['id']] : 'Não há peso') . '</span></div><hr class="mt-2 mb-2"/>';
                }
                $tableRespostasPeso .= '';


                return $tableRespostasPeso;
                // return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->editColumn('pergunta', function ($row) {
                // return '<table class="table table-borderless"><tr><td style="border-right: 1px solid #d8dbe0;">' . $row->id . '</td><td style="width:90%;">' . $row->pergunta . '</td></tr>';
                return $row->pergunta_sinalizada;
            })->addColumn('tipoPergunta', function ($row) {
                return TipoPerguntaEnum::toSelectArray()[$row->tipo_pergunta];
            })->addColumn('ordem', function ($row) {
                return $row->pivot->ordem;
            })->addColumn('fl_plano_acao', function ($row) {
                return boolean_sim_nao($row->pivot->fl_plano_acao);
            })->addColumn('plano_acao_prioridade', function ($row) {
                return @PlanoAcaoPrioridadeEnum::toSelectArray()[$row->pivot->plano_acao_prioridade];
            })->addColumn('actions', function ($rs) {
                $params = ['checklistCategoria' => $rs->pivot->checklist_categoria_id, 'checklistPergunta' => $rs->pivot->id];
                $editUrl = route('admin.core.checklist.categorias.perguntas.edit', $params);

                $deleteUrl = route('admin.core.checklist.categorias.perguntas.destroy', $params);

                $moveOrderUp = route('admin.core.checklist.categorias.perguntas.moveOrderUp', $params);
                $moveOrderDown = route('admin.core.checklist.categorias.perguntas.moveOrderDown', $params);
                $moveOrderTop = route('admin.core.checklist.categorias.perguntas.moveOrderTop', $params);
                $moveOrderBack = route('admin.core.checklist.categorias.perguntas.moveOrderBack', $params);

                $row = $rs->pivot; //ChecklistPerguntaModel
                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'moveOrderUp', 'moveOrderDown', 'moveOrderTop', 'moveOrderBack', 'row'));
            })
            ->rawColumns(['pergunta', 'respostas', 'pesos'])
            ->make(true);
    }


    /**
     * Cadastro/vinculação de perguntas dentro de uma categoria de um template de formulário
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  PerguntaModel $pergunta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function perguntasCreate(ChecklistCategoriaModel $checklistCategoria, PerguntaModel $pergunta, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistPerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist.categorias.perguntas.store', ['checklistCategoria' => $checklistCategoria]),
            'class' => 'needs-validation',
            'data' => ['pergunta' => $pergunta, 'checklistCategoria' => $checklistCategoria],
            'model' => ['pergunta_id' => $pergunta->id],
            'novalidate' => true
        ]);

        $title = 'Vincular Pergunta';

        $checklist = $checklistCategoria->checklist_id;
        $back = route('admin.core.checklist.categorias.edit', compact('checklist', 'checklistCategoria'));

        return view('backend.core.checklist.categorias.perguntas.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function perguntasStore(Request $request, ChecklistCategoriaModel $checklistCategoria)
    {
        //Wrap para carregar as perguntas no Formulário (formbuilder) (para ter validação)
        $pergunta = PerguntaModel::where("id", $request->pergunta_id)->first();
        $form = $this->form(ChecklistPerguntaForm::class, [], ['checklistCategoria' => $checklistCategoria, 'pergunta' => $pergunta]);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();
        $data['checklist_categoria_id'] = $checklistCategoria->id;
        $data['fl_plano_acao'] = @!$data['fl_plano_acao'] ? 0 : 1;

        $this->repositoryChecklistPergunta->create($data);

        $checklist = $checklistCategoria->checklist_id;
        return redirect()->route('admin.core.checklist.categorias.edit', compact('checklist', 'checklistCategoria'))->withFlashSuccess('Pergunta vinculada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  FormBuilder $formBuilder
     * @param  Request $request
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasEdit(FormBuilder $formBuilder, Request $request, ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $form = $formBuilder->create(ChecklistPerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.checklist.categorias.perguntas.update', ['checklistCategoria' => $checklistCategoria, 'checklistPergunta' => $checklistPergunta]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['pergunta' => $checklistPergunta->pergunta, 'checklistCategoria' => $checklistCategoria, 'checklistPergunta' => $checklistPergunta],
            'model' => $checklistPergunta->toArray() + @$checklistPergunta->perguntaRespostasPesos->pluck('peso', 'resposta_id')->toArray(),
        ]);

        $title = 'Editar pergunta';

        $checklist = $checklistCategoria->checklist_id;
        $back = route('admin.core.checklist.categorias.edit', compact('checklist', 'checklistCategoria'));

        return view('backend.core.checklist.categorias.perguntas.create_update', compact('form', 'title', 'back', 'checklistPergunta'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasUpdate(Request $request, ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $form = $this->form(ChecklistPerguntaForm::class, [], ['pergunta' => $checklistPergunta->pergunta, 'checklistCategoria' => $checklistCategoria, 'checklistPergunta' => $checklistPergunta]);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();
        $data['checklist_categoria_id'] = $checklistCategoria->id;
        $data['fl_plano_acao'] = @!$data['fl_plano_acao'] ? 0 : 1;

        $this->repositoryChecklistPergunta->update($checklistPergunta, $data);

        $checklist = $checklistCategoria->checklist_id;
        return redirect()->route('admin.core.checklist.categorias.edit', compact('checklist', 'checklistCategoria'))->withFlashSuccess('Pergunta vinculada com sucesso!');
    }

    /**
     * Desvincular pergunta utilizada em uma categoria do template de um formulário
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasDestroy(ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $this->repositoryChecklistPergunta->delete($checklistPergunta);

        $checklist = $checklistCategoria->checklist_id;
        return redirect()->route('admin.core.checklist.categorias.edit', compact('checklist', 'checklistCategoria'))->withFlashSuccess('Pergunta desvinculada com sucesso');
    }

    /**
     * Order - move up
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasMoveOrderUp(ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $checklistPergunta->moveOrderUp();
        return null;
    }

    /**
     * Order - move down
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasMoveOrderDown(ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $checklistPergunta->moveOrderDown();
        return null;
    }

    /**
     * Order - move top
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasMoveOrderTop(ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $checklistPergunta->moveToStart();
        return null;
    }

    /**
     * Order - move end
     *
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  ChecklistPerguntaModel $checklistPergunta
     * @return void
     */
    public function perguntasMoveOrderBack(ChecklistCategoriaModel $checklistCategoria, ChecklistPerguntaModel $checklistPergunta)
    {
        $checklistPergunta->moveToEnd();
        return null;
    }
}
