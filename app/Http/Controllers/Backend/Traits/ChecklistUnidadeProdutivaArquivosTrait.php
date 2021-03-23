<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\DatatablesHelper;
use App\Http\Controllers\Backend\Forms\ChecklistUnidadeProdutivaArquivoForm;
use App\Models\Core\ChecklistUnidadeProdutivaArquivoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait ChecklistUnidadeProdutivaArquivosTrait
{
    /**
     * Listagem dos arquivos
     *
     * @param  mixed $checklistUnidadeProdutiva
     * @param  mixed $request
     * @return void
     */
    public function arquivosIndex(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, Request $request)
    {
        $title = 'Fotos e Anexos';
        $urlAdd = route('admin.core.checklist_unidade_produtiva.arquivos.create', ["checklistUnidadeProdutiva" => $checklistUnidadeProdutiva]);
        $urlDatatable = route('admin.core.checklist_unidade_produtiva.arquivos.datatable', ["checklistUnidadeProdutiva" => $checklistUnidadeProdutiva]);

        return view('backend.core.checklist_unidade_produtiva.arquivos.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "arquivosIndex()"
     *
     * @param  mixed $checklistUnidadeProdutiva
     * @return void
     */
    public function arquivosDatatable(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        return DataTables::of($checklistUnidadeProdutiva->arquivos()->getQuery())
            ->addColumn('arquivo', function ($data) {
                $arquivo = \Storage::url($data['arquivo']);
                return DatatablesHelper::renderColumnFile($data['nome'], $arquivo);
            })
            ->addColumn('actions', function ($row) use ($checklistUnidadeProdutiva) {
                $params = ['checklistUnidadeProdutiva' => $checklistUnidadeProdutiva, 'arquivo' => $row];

                $editUrl = route('admin.core.checklist_unidade_produtiva.arquivos.edit', $params);
                $deleteUrl = route('admin.core.checklist_unidade_produtiva.arquivos.destroy', $params);

                return view('backend.components.form-actions.index', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $checklistUnidadeProdutiva]);
            })
            ->rawColumns(['arquivo'])
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosCreate(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistUnidadeProdutivaArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist_unidade_produtiva.arquivos.store', ['checklistUnidadeProdutiva' => $checklistUnidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Cadastrar Arquivo';

        $back = route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'));

        return view('backend.core.checklist_unidade_produtiva.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function arquivosStore(Request $request, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $form = $this->form(ChecklistUnidadeProdutivaArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['checklist_unidade_produtiva_id'] = $checklistUnidadeProdutiva->id;

        $model = $this->repositoryArquivo->create($data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'))->withFlashSuccess('Arquivo criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  ChecklistUnidadeProdutivaArquivoModel $arquivo
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosEdit(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, ChecklistUnidadeProdutivaArquivoModel $arquivo, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistUnidadeProdutivaArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist_unidade_produtiva.arquivos.update', ['checklistUnidadeProdutiva' => $checklistUnidadeProdutiva, 'arquivo' => $arquivo]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $arquivo,
        ]);

        $title = 'Editar arquivo';

        $back = route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'));

        return view('backend.core.checklist_unidade_produtiva.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  ChecklistUnidadeProdutivaArquivoModel $arquivo
     * @param  Request $request
     * @return void
     */
    public function arquivosUpdate(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, ChecklistUnidadeProdutivaArquivoModel $arquivo, Request $request)
    {
        $form = $this->form(ChecklistUnidadeProdutivaArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['checklist_unidade_produtiva_id'] = $checklistUnidadeProdutiva->id;

        $model = $this->repositoryArquivo->update($arquivo, $data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'))->withFlashSuccess('Arquivo atualizado com sucesso!');
    }

    /**
     * Remover arquivo
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  ChecklistUnidadeProdutivaArquivoModel $arquivo
     * @return void
     */
    public function arquivosDestroy(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, ChecklistUnidadeProdutivaArquivoModel $arquivo)
    {
        $arquivo->delete();

        return redirect()->route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'))->withFlashSuccess('Arquivo deletado com sucesso');
    }
}
