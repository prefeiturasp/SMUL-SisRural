<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\ColaboradorForm;
use App\Models\Core\ColaboradorModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait UnidadeProdutivaColaboradoresTrait
{
    /**
     * Listagem de pessoas (colaborador) na unidade produtiva
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @return void
     */
    public function colaboradoresIndex(UnidadeProdutivaModel $unidadeProdutiva, Request $request)
    {
        $title = 'Pessoas';
        $addUrl = route('admin.core.unidade_produtiva.colaboradores.create', ["unidadeProdutiva" => $unidadeProdutiva]);
        $urlDatatable = route('admin.core.unidade_produtiva.colaboradores.datatable', ["unidadeProdutiva" => $unidadeProdutiva]);
        $labelAdd = 'Adicionar Pessoas';

        return view('backend.core.unidade_produtiva.colaboradores.index', compact('addUrl', 'urlDatatable', 'title', 'labelAdd'));
    }

    /**
     * API Datatable "colaboradoresIndex()"
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function colaboradoresDatatable(UnidadeProdutivaModel $unidadeProdutiva)
    {
        return DataTables::of($unidadeProdutiva->colaboradores()->get())
            ->addColumn('relacao', function ($row) {
                return @$row->relacao->nome;
            })->addColumn('dedicacao', function ($row) {
                return @$row->dedicacao->nome;
            })->addColumn('actions', function ($row) use ($unidadeProdutiva) {
                $params = ['unidadeProdutiva' => $row->unidade_produtiva_id, 'colaborador' => $row->id];

                $editUrl = route('admin.core.unidade_produtiva.colaboradores.edit', $params);
                $deleteUrl = route('admin.core.unidade_produtiva.colaboradores.destroy', $params);

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
    public function colaboradoresCreate(UnidadeProdutivaModel $unidadeProdutiva, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ColaboradorForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.colaboradores.store', ['unidadeProdutiva' => $unidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Cadastrar Nova Pessoa';

        $back = route('admin.core.unidade_produtiva.colaboradores.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.colaboradores.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function colaboradoresStore(Request $request, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $data = $request->only(['nome', 'cpf', 'funcao', 'alocacao', 'dedicacao_id', 'relacao_id']);
        $data['unidade_produtiva_id'] = $unidadeProdutiva->id;

        ColaboradorModel::create($data);

        return redirect()->route('admin.core.unidade_produtiva.colaboradores.index', compact('unidadeProdutiva'))->withFlashSuccess('Colaborador criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  ColaboradorModel $colaborador
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function colaboradoresEdit(UnidadeProdutivaModel $unidadeProdutiva, ColaboradorModel $colaborador, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ColaboradorForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_produtiva.colaboradores.update', ['unidadeProdutiva' => $unidadeProdutiva, 'colaborador' => $colaborador]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $colaborador,
        ]);

        $title = 'Editar pessoa';

        $back = route('admin.core.unidade_produtiva.colaboradores.index', compact('unidadeProdutiva'));

        return view('backend.core.unidade_produtiva.colaboradores.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  Request $request
     * @param  ColaboradorModel $colaborador
     * @return void
     */
    public function colaboradoresUpdate(UnidadeProdutivaModel $unidadeProdutiva, Request $request, ColaboradorModel $colaborador)
    {
        $data = $request->only(['nome', 'cpf', 'funcao', 'alocacao', 'dedicacao_id', 'relacao_id']);

        $colaborador->update($data);

        return redirect()->route('admin.core.unidade_produtiva.colaboradores.index', compact('unidadeProdutiva'))->withFlashSuccess('Colaborador atualizado com sucesso!');
    }

    /**
     * Remoção
     *
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  ColaboradorModel $colaborador
     * @return void
     */
    public function colaboradoresDestroy(UnidadeProdutivaModel $unidadeProdutiva, ColaboradorModel $colaborador)
    {
        $colaborador->delete();

        return redirect()->route('admin.core.unidade_produtiva.colaboradores.index', compact('unidadeProdutiva'))->withFlashSuccess('Colaborador deletado com sucesso');
    }
}
