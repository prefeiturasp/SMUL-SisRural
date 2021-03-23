<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\ChecklistCategoriaForm;
use App\Models\Core\ChecklistCategoriaModel;
use App\Models\Core\ChecklistModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait ChecklistCategoriaTrait
{
    /**
     * Listagem das categorias criadas no template do formulário (Checklist)
     *
     * @param  ChecklistModel $checklist
     * @param  Request $request
     * @return void
     */
    public function categoriasIndex(ChecklistModel $checklist, Request $request)
    {
        $title = 'Definição de categorias e perguntas';
        $urlAdd = route('admin.core.checklist.categorias.create', ["checklist" => $checklist]);
        $urlDatatable = route('admin.core.checklist.categorias.datatable', ["checklist" => $checklist]);

        return view('backend.core.checklist.categorias.index', compact('urlAdd', 'urlDatatable', 'title', 'checklist'));
    }

    /**
     * API Datatable "categoriasIndex()"
     *
     * @param  ChecklistModel $checklist
     * @return void
     */
    public function categoriasDatatable(ChecklistModel $checklist)
    {
        return DataTables::of($checklist->categorias()->getQuery())
            ->addColumn('tipo_perguntas', function ($row) {
                return AppHelper::tableArrayToList($row->perguntas->toArray(), 'tipo_pergunta');
            })
            ->addColumn('perguntas', function ($row) {
                return @AppHelper::tableArrayToList($row->perguntas->makeVisible('pergunta_sinalizada')->toArray(), 'pergunta_sinalizada');
            })
            ->addColumn('actions', function ($row) {
                $params = ['checklist' => $row->checklist_id, 'checklistCategoria' => $row->id];

                $editUrl = route('admin.core.checklist.categorias.edit', $params);
                $deleteUrl = route('admin.core.checklist.categorias.destroy', $params);

                $moveOrderUp = route('admin.core.checklist.categorias.moveOrderUp', $params);
                $moveOrderDown = route('admin.core.checklist.categorias.moveOrderDown', $params);
                $moveOrderTop = route('admin.core.checklist.categorias.moveOrderTop', $params);
                $moveOrderBack = route('admin.core.checklist.categorias.moveOrderBack', $params);

                $addPerguntaUrl = route('admin.core.checklist.categorias.perguntas.todasPerguntas', $params);

                return view('backend.core.checklist.categorias.form_actions', compact('addPerguntaUrl', 'editUrl', 'deleteUrl', 'moveOrderUp', 'moveOrderDown', 'moveOrderTop', 'moveOrderBack', 'row'));
            })
            ->rawColumns(['pergunta', 'tipo_perguntas', 'perguntas'])
            ->make(true);
    }

    /**
     * Cadastro categoria
     *
     * @param  ChecklistModel $checklist
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function categoriasCreate(ChecklistModel $checklist, FormBuilder $formBuilder)
    {
        $last = ChecklistCategoriaModel::orderBy('ordem', 'desc')->where('checklist_id', $checklist->id)->first();

        $form = $formBuilder->create(ChecklistCategoriaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist.categorias.store', ['checklist' => $checklist]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => $checklist->toArray(),
            'model' => ['ordem' => @$last->ordem + 1]
        ]);

        $title = 'Adicionar categoria';

        $back = route('admin.core.checklist.categorias.index', compact('checklist'));

        return view('backend.core.checklist.categorias.create_update', compact('form', 'title', 'back', 'checklist'));
    }

    /**
     * Cadastro categoria - POST
     *
     * @param  Request $request
     * @param  ChecklistModel $checklist
     * @return void
     */
    public function categoriasStore(Request $request, ChecklistModel $checklist)
    {
        $data = $request->only(['nome']);
        $data['checklist_id'] = $checklist->id;

        $this->repositoryChecklistCategoria->create($data);

        return redirect()->route('admin.core.checklist.categorias.index', compact('checklist'))->withFlashSuccess('Categoria criada com sucesso!');
    }

    /**
     * Edição categoria
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function categoriasEdit(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistCategoriaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.checklist.categorias.update', ['checklist' => $checklist, 'checklistCategoria' => $checklistCategoria]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => $checklist->toArray(),
            'model' => $checklistCategoria,
        ]);

        $title = 'Editar categoria';

        $back = route('admin.core.checklist.categorias.index', compact('checklist'));

        //Iframe categorias vs perguntas (ver ChecklistCategoriaPerguntaTrait.php, método "perguntasDatatable")
        $urlDatatable = route('admin.core.checklist.categorias.perguntas.datatable', ["checklistCategoria" => $checklistCategoria]);
        $urlAdd = route('admin.core.checklist.categorias.perguntas.todasPerguntas', ["checklistCategoria" => $checklistCategoria]);

        return view('backend.core.checklist.categorias.create_update', compact('form', 'title', 'back', 'urlDatatable', 'urlAdd', 'checklist'));
    }

    /**
     * Atualização - POST
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @param  Request $request
     * @return void
     */
    public function categoriasUpdate(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria, Request $request)
    {
        $data = $request->only(['nome']);
        $data['checklist_id'] = $checklist->id;

        $this->repositoryChecklistCategoria->update($checklistCategoria, $data);

        return redirect()->route('admin.core.checklist.categorias.index', compact('checklist'))->withFlashSuccess('Categoria atualizada com sucesso!');
    }

    /**
     * Remover categoria
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function categoriasDestroy(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria)
    {
        $this->repositoryChecklistCategoria->delete($checklistCategoria);

        return redirect()->route('admin.core.checklist.categorias.index', compact('checklist'))->withFlashSuccess('Categoria deletada com sucesso');
    }

    /**
     * Order - move up
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function categoriasMoveOrderUp(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria)
    {
        $checklistCategoria->moveOrderUp();
        return null;
    }

    /**
     * Order - move down
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function categoriasMoveOrderDown(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria)
    {
        $checklistCategoria->moveOrderDown();
        return null;
    }

    /**
     * Order - move top
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function categoriasMoveOrderTop(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria)
    {
        $checklistCategoria->moveToStart();
        return null;
    }

    /**
     * Order - move end
     *
     * @param  ChecklistModel $checklist
     * @param  ChecklistCategoriaModel $checklistCategoria
     * @return void
     */
    public function categoriasMoveOrderBack(ChecklistModel $checklist, ChecklistCategoriaModel $checklistCategoria)
    {
        $checklistCategoria->moveToEnd();
        return null;
    }
}
