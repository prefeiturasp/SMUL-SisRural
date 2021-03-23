<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\DatatablesHelper;
use App\Http\Controllers\Backend\Forms\UnidadeProdutivaArquivoForm;
use App\Models\Core\UnidadeProdutivaArquivoModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait UnidadeProdutivaArquivosTrait
{
    /**
     * Listagem de arquivos de uma unidade produtiva
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @return void
     */
    public function arquivosIndex(UnidadeProdutivaModel $unidadeProdutiva, Request $request)
    {
        $title = 'Fotos e Anexos';
        $urlAdd = route('admin.core.unidade_produtiva.arquivos.create', ["unidadeProdutiva" => $unidadeProdutiva]);
        $urlDatatable = route('admin.core.unidade_produtiva.arquivos.datatable', ["unidadeProdutiva" => $unidadeProdutiva]);

        return view('backend.core.unidade_produtiva.arquivos.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "arquivosIndex()"
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function arquivosDatatable(UnidadeProdutivaModel $unidadeProdutiva)
    {
        return DataTables::of($unidadeProdutiva->arquivos()->getQuery())
            ->addColumn('arquivo', function ($data) {
                $arquivo = \Storage::url($data['arquivo']);
                return DatatablesHelper::renderColumnFile($data['nome'], $arquivo);
            })
            ->addColumn('actions', function ($row) use ($unidadeProdutiva) {
                $params = ['unidadeProdutiva' => $unidadeProdutiva, 'arquivo' => $row];

                $editUrl = route('admin.core.unidade_produtiva.arquivos.edit', $params);
                $deleteUrl = route('admin.core.unidade_produtiva.arquivos.destroy', $params);

                return view('backend.components.form-actions.index', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $unidadeProdutiva]);
            })
            ->rawColumns(['arquivo'])
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosCreate(UnidadeProdutivaModel $unidadeProdutiva, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(UnidadeProdutivaArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.arquivos.store', ['unidadeProdutiva' => $unidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Cadastrar Arquivo';

        $back = route('admin.core.unidade_produtiva.arquivos.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function arquivosStore(Request $request, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $form = $this->form(UnidadeProdutivaArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['unidade_produtiva_id'] = $unidadeProdutiva->id;

        $model = $this->repositoryArquivo->create($data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.unidade_produtiva.arquivos.index', compact('unidadeProdutiva'))->withFlashSuccess('Arquivo criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  UnidadeProdutivaArquivoModel $arquivo
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function arquivosEdit(UnidadeProdutivaModel $unidadeProdutiva, UnidadeProdutivaArquivoModel $arquivo, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(UnidadeProdutivaArquivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.arquivos.update', ['unidadeProdutiva' => $unidadeProdutiva, 'arquivo' => $arquivo]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $arquivo,
        ]);

        $title = 'Editar arquivo';

        $back = route('admin.core.unidade_produtiva.arquivos.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.arquivos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  UnidadeProdutivaArquivoModel $arquivo
     * @param  Request $request
     * @return void
     */
    public function arquivosUpdate(UnidadeProdutivaModel $unidadeProdutiva, UnidadeProdutivaArquivoModel $arquivo, Request $request)
    {
        $form = $this->form(UnidadeProdutivaArquivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['descricao']);
        $data['unidade_produtiva_id'] = $unidadeProdutiva->id;

        $model = $this->repositoryArquivo->update($arquivo, $data);
        if ($request->hasFile('arquivo')) {
            $this->repositoryArquivo->upload($request->file('arquivo'), $model);
        }

        return redirect()->route('admin.core.unidade_produtiva.arquivos.index', compact('unidadeProdutiva'))->withFlashSuccess('Arquivo atualizado com sucesso!');
    }

    /**
     * Remover
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  UnidadeProdutivaArquivoModel $arquivo
     * @return void
     */
    public function arquivosDestroy(UnidadeProdutivaModel $unidadeProdutiva, UnidadeProdutivaArquivoModel $arquivo)
    {
        $arquivo->delete();

        return redirect()->route('admin.core.unidade_produtiva.arquivos.index', compact('unidadeProdutiva'))->withFlashSuccess('Arquivo deletado com sucesso');
    }
}
