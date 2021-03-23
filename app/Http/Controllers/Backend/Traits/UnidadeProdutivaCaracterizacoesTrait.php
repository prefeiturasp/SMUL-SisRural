<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\SoftDeleteHelper;
use App\Http\Controllers\Backend\Forms\UnidadeProdutivaCaracterizacaoForm;
use App\Models\Core\UnidadeProdutivaCaracterizacaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait UnidadeProdutivaCaracterizacoesTrait
{
    /**
     * Listagem de uso de solo de uma unidade produtiva
     *
     * @param  mixed $unidadeProdutiva
     * @param  mixed $request
     * @return void
     */
    public function caracterizacoesIndex(UnidadeProdutivaModel $unidadeProdutiva, Request $request)
    {
        $title = 'Uso do Solo';
        $addUrl = route('admin.core.unidade_produtiva.caracterizacoes.create', ["unidadeProdutiva" => $unidadeProdutiva]);
        $urlDatatable = route('admin.core.unidade_produtiva.caracterizacoes.datatable', ["unidadeProdutiva" => $unidadeProdutiva]);
        $labelAdd = 'Adicionar Uso do Solo';

        return view('backend.core.unidade_produtiva.caracterizacoes.index', compact('addUrl', 'urlDatatable', 'title', 'labelAdd'));
    }

    /**
     * API Datatable "caracterizacoesIndex()"
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function caracterizacoesDatatable(UnidadeProdutivaModel $unidadeProdutiva)
    {
        return DataTables::of($unidadeProdutiva->caracterizacoes()->get())
            ->addColumn('categoria', function ($row) {
                return $row->categoria->nome;
            })->addColumn('agrobiodiversidade', function ($row) {
                return $row->categoria->agrobiodiversidade($row->quantidade);
            })->addColumn('actions', function ($row) use ($unidadeProdutiva) {
                $params = ['unidadeProdutiva' => $row->unidade_produtiva_id, 'unidadeProdutivaCaracterizacao' => $row->id];
                $editUrl = route('admin.core.unidade_produtiva.caracterizacoes.edit', $params);
                $deleteUrl = route('admin.core.unidade_produtiva.caracterizacoes.destroy', $params);

                return view('backend.components.form-actions.index', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $unidadeProdutiva]);
            })
            ->make(true);
    }

    /**
     * Cadastro uso do solo
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function caracterizacoesCreate(UnidadeProdutivaModel $unidadeProdutiva, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(UnidadeProdutivaCaracterizacaoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.caracterizacoes.store', ['unidadeProdutiva' => $unidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Cadastrar Uso do Solo';

        $back = route('admin.core.unidade_produtiva.caracterizacoes.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.caracterizacoes.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro uso do solo - POST
     *
     * @param  Request $request
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function caracterizacoesStore(Request $request, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $data = $request->only(['area', 'quantidade', 'descricao', 'solo_categoria_id']);
        $data['unidade_produtiva_id'] = $unidadeProdutiva->id;

        $unidadeProdutivaCaracterizacao = UnidadeProdutivaCaracterizacaoModel::create($data);

        return redirect()->route('admin.core.unidade_produtiva.caracterizacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Uso do Solo criado com sucesso!');
    }

    /**
     * Edição uso do solo
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  mixeUnidadeProdutivaCaracterizacaoModeld $unidadeProdutivaCaracterizacao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function caracterizacoesEdit(UnidadeProdutivaModel $unidadeProdutiva, UnidadeProdutivaCaracterizacaoModel $unidadeProdutivaCaracterizacao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(UnidadeProdutivaCaracterizacaoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.caracterizacoes.update', ['unidadeProdutiva' => $unidadeProdutiva, 'unidadeProdutivaCaracterizacao' => $unidadeProdutivaCaracterizacao]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $unidadeProdutivaCaracterizacao,
        ]);

        $title = 'Editar uso do solo';

        $back = route('admin.core.unidade_produtiva.caracterizacoes.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.caracterizacoes.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Atualização uso do solo - POST
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @param  UnidadeProdutivaCaracterizacaoModel $unidadeProdutivaCaracterizacao
     * @return void
     */
    public function caracterizacoesUpdate(UnidadeProdutivaModel $unidadeProdutiva, Request $request, UnidadeProdutivaCaracterizacaoModel $unidadeProdutivaCaracterizacao)
    {
        $data = $request->only(['area', 'quantidade', 'descricao', 'unidade_produtiva_id', 'solo_categoria_id']);

        $unidadeProdutivaCaracterizacao->update($data);

        return redirect()->route('admin.core.unidade_produtiva.caracterizacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Uso do Solo atualizado com sucesso!');
    }

    /**
     * Remover uso do solo
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  UnidadeProdutivaCaracterizacaoModel $unidadeProdutivaCaracterizacao
     * @return void
     */
    public function caracterizacoesDestroy(UnidadeProdutivaModel $unidadeProdutiva, UnidadeProdutivaCaracterizacaoModel $unidadeProdutivaCaracterizacao)
    {
        $unidadeProdutivaCaracterizacao->delete();

        return redirect()->route('admin.core.unidade_produtiva.caracterizacoes.index', compact('unidadeProdutiva'))->withFlashSuccess('Uso do Solo deletado com sucesso');
    }
}
