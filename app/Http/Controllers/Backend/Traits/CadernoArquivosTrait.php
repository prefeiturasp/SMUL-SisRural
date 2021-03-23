<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\DatatablesHelper;
use App\Http\Controllers\Backend\Forms\CadernoArquivoForm;
use App\Models\Core\CadernoModel;
use App\Models\Core\CadernoArquivoModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait CadernoArquivosTrait
{
    /**
     * Listagem dos arquivos do caderno de campo
     *
     * @param  mixed $caderno
     * @param  mixed $request
     * @return void
     */
    public function arquivosIndex(CadernoModel $caderno, Request $request)
    {
        $title = 'Fotos e Anexos';
        $urlAdd = route('admin.core.cadernos.arquivos.create', ["caderno" => $caderno]);
        $urlDatatable = route('admin.core.cadernos.arquivos.datatable', ["caderno" => $caderno]);

        return view('backend.core.cadernos.arquivos.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "arquivosIndex()"
     *
     * @param  mixed $caderno
     * @return void
     */
    public function arquivosDatatable(CadernoModel $caderno)
    {
        return DataTables::of($caderno->arquivos()->getQuery())
            ->addColumn('arquivo', function ($data) {
                $arquivo = \Storage::url($data['arquivo']);
                return DatatablesHelper::renderColumnFile($data['nome'], $arquivo);
            })
            ->addColumn('actions', function ($row) use ($caderno) {
                $params = ['caderno' => $caderno, 'arquivo' => $row];

                $editUrl = route('admin.core.cadernos.arquivos.edit', $params);
                $deleteUrl = route('admin.core.cadernos.arquivos.destroy', $params);

                return view('backend.components.form-actions.index', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $caderno]);
            })
            ->rawColumns(['arquivo'])
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  CadernoModel $caderno
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosCreate(CadernoModel $caderno, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(CadernoArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.cadernos.arquivos.store', ['caderno' => $caderno]),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Cadastrar Arquivo';

        $back = route('admin.core.cadernos.arquivos.index', compact('caderno'));

        return view('backend.core.cadernos.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  CadernoModel $caderno
     * @return void
     */
    public function arquivosStore(Request $request, CadernoModel $caderno)
    {
        $form = $this->form(CadernoArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['caderno_id'] = $caderno->id;

        //Repositorio foi instanciado no CadastroController.php
        $model = $this->repositoryArquivo->create($data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.cadernos.arquivos.index', compact('caderno'))->withFlashSuccess('Arquivo criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  CadernoModel $caderno
     * @param  CadernoArquivoModel $arquivo
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosEdit(CadernoModel $caderno, CadernoArquivoModel $arquivo, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(CadernoArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.cadernos.arquivos.update', ['caderno' => $caderno, 'arquivo' => $arquivo]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $arquivo,
        ]);

        $title = 'Editar arquivo';

        $back = route('admin.core.cadernos.arquivos.index', compact('caderno'));

        return view('backend.core.cadernos.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  CadernoModel $caderno
     * @param  CadernoArquivoModel $arquivo
     * @param  Request $request
     * @return void
     */
    public function arquivosUpdate(CadernoModel $caderno, CadernoArquivoModel $arquivo, Request $request)
    {
        $form = $this->form(CadernoArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['caderno_id'] = $caderno->id;

        $model = $this->repositoryArquivo->update($arquivo, $data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.cadernos.arquivos.index', compact('caderno'))->withFlashSuccess('Arquivo atualizado com sucesso!');
    }

    /**
     * Remover arquivo
     *
     * @param  CadernoModel $caderno
     * @param  CadernoArquivoModel $arquivo
     * @return void
     */
    public function arquivosDestroy(CadernoModel $caderno, CadernoArquivoModel $arquivo)
    {
        $arquivo->delete();

        return redirect()->route('admin.core.cadernos.arquivos.index', compact('caderno'))->withFlashSuccess('Arquivo deletado com sucesso');
    }
}
