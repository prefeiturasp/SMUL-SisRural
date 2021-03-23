<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\InstalacoesForm;
use App\Models\Core\InstalacaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait UnidadeProdutivaInstalacoesTrait
{
    /**
     * Listagem de instalações (infra-estrutura)
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @return void
     */
    public function instalacoesIndex(UnidadeProdutivaModel $unidadeProdutiva, Request $request)
    {
        $title = 'Infra-estrutura';
        $addUrl = route('admin.core.unidade_produtiva.instalacoes.create', ["unidadeProdutiva" => $unidadeProdutiva]);
        $urlDatatable = route('admin.core.unidade_produtiva.instalacoes.datatable', ["unidadeProdutiva" => $unidadeProdutiva]);
        $labelAdd = 'Adicionar Infra-estrutura';

        return view('backend.core.unidade_produtiva.instalacoes.index', compact('addUrl', 'urlDatatable', 'title', 'labelAdd'));
    }

    /**
     * API Datatable "instalacoesIndex()"
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function instalacoesDatatable(UnidadeProdutivaModel $unidadeProdutiva)
    {
        return DataTables::of($unidadeProdutiva->instalacoes()->get())
            ->addColumn('instalacao_tipo', function ($row) {
                return $row->instalacaoTipo->nome;
            })
            ->addColumn('actions', function ($row) use ($unidadeProdutiva) {
                $params = ['unidadeProdutiva' => $row->unidade_produtiva_id, 'instalacao' => $row->id];

                $editUrl = route('admin.core.unidade_produtiva.instalacoes.edit', $params);
                $deleteUrl = route('admin.core.unidade_produtiva.instalacoes.destroy', $params);

                return view('backend.components.form-actions.index', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $unidadeProdutiva]);
            })
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function instalacoesCreate(UnidadeProdutivaModel $unidadeProdutiva, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(InstalacoesForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.instalacoes.store', ['unidadeProdutiva' => $unidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Cadastrar Infra-estrutura';

        $back = route('admin.core.unidade_produtiva.instalacoes.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.instalacoes.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function instalacoesStore(Request $request, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $data = $request->only(['descricao', 'quantidade', 'area', 'observacao', 'localizacao', 'instalacao_tipo_id']);
        $data['unidade_produtiva_id'] = $unidadeProdutiva->id;

        InstalacaoModel::create($data);

        return redirect()->route('admin.core.unidade_produtiva.instalacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Infra-estrutura criada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  InstalacaoModel $instalacao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function instalacoesEdit(UnidadeProdutivaModel $unidadeProdutiva, InstalacaoModel $instalacao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(InstalacoesForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.instalacoes.update', ['unidadeProdutiva' => $unidadeProdutiva, 'instalacao' => $instalacao]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $instalacao,
        ]);

        $title = 'Editar infra-estrutura';

        $back = route('admin.core.unidade_produtiva.instalacoes.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.instalacoes.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @param  InstalacaoModel $instalacao
     * @return void
     */
    public function instalacoesUpdate(UnidadeProdutivaModel $unidadeProdutiva, Request $request, InstalacaoModel $instalacao)
    {
        $data = $request->only(['descricao', 'quantidade', 'area', 'observacao', 'localizacao', 'unidade_produtiva_id', 'instalacao_tipo_id']);

        $instalacao->update($data);

        return redirect()->route('admin.core.unidade_produtiva.instalacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Infra-estrutura atualizada com sucesso!');
    }

    /**
     * Remoção
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  InstalacaoModel $instalacao
     * @return void
     */
    public function instalacoesDestroy(UnidadeProdutivaModel $unidadeProdutiva, InstalacaoModel $instalacao)
    {
        $instalacao->delete();

        return redirect()->route('admin.core.unidade_produtiva.instalacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Infra-estrutura deletada com sucesso');
    }
}
