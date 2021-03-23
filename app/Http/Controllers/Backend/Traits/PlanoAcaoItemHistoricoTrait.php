<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\PlanoAcaoItemHistoricoForm;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use App\Models\Core\PlanoAcaoItemModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoItemHistoricoTrait
{
    /**
     * Listagem dos históricos dos itens do PDA
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  Request $request
     * @return void
     */
    public function historicoItemIndex(PlanoAcaoItemModel $planoAcaoItem, Request $request)
    {
        $title = 'Observações';
        $urlAdd = route('admin.core.plano_acao_item.historico_item.create', ["planoAcaoItem" => $planoAcaoItem]);
        $urlDatatable = route('admin.core.plano_acao_item.historico_item.datatable', ["planoAcaoItem" => $planoAcaoItem]);

        return view('backend.core.plano_acao.historico_item.index', compact('urlAdd', 'urlDatatable', 'title', 'planoAcaoItem'));
    }

    /**
     * API Database "historicoItemIndex()"
     *
     * @param  mixed $planoAcaoItem
     * @return void
     */
    public function historicoItemDatatable(PlanoAcaoItemModel $planoAcaoItem)
    {
        return DataTables::of($planoAcaoItem->historicos()->getQuery())
            ->addColumn('usuario', function ($row) {
                return $row->usuario->first_name;
            })->addColumn('actions', function ($row) {
                $params = ['planoAcaoItem' => $row->plano_acao_item_id, 'historico' => $row->id];

                $editUrl = route('admin.core.plano_acao_item.historico_item.edit', $params);
                $deleteUrl = route('admin.core.plano_acao_item.historico_item.destroy', $params);

                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->filterColumn('usuario', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('usuario', function ($q) use ($keyword) {
                        $q->where('users.first_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->make(true);
    }

    /**
     * Listagem e formulário do item do plano de ação
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoItemCreateAndList(PlanoAcaoItemModel $planoAcaoItem, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_item.historico_item.store_create_and_list', ['planoAcaoItem' => $planoAcaoItem]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcaoItem' => $planoAcaoItem],
        ]);

        $urlDatatable = route('admin.core.plano_acao_item.historico_item.datatable', ["planoAcaoItem" => $planoAcaoItem]);

        $planoAcao = $planoAcaoItem->plano_acao;

        return view('backend.core.plano_acao.historico_item.modal_create_list', compact('form', 'planoAcao', 'urlDatatable'));
    }

    /**
     * POST - Listagem/formulário - "historicoItemCreateAndList()"
     *
     * @param  Request $request
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @return void
     */
    public function historicoItemStoreCreateAndList(Request $request, PlanoAcaoItemModel $planoAcaoItem)
    {
        $data = $request->only(['texto']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_item_id'] = $planoAcaoItem->id;

        $this->repositoryItemHistorico->create($data);

        return redirect()->route('admin.core.plano_acao_item.historico_item.create_and_list', compact('planoAcaoItem'))->withFlashSuccess('Acompanhamento criado com sucesso!');
    }

    /**
     * Cadastro de um histórico no item
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoItemCreate(PlanoAcaoItemModel $planoAcaoItem, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_item.historico_item.store', ['planoAcaoItem' => $planoAcaoItem]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcaoItem' => $planoAcaoItem],
        ]);

        $title = 'Adicionar Novo Acompanhamento';

        $back = route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'));

        return view('backend.core.plano_acao.historico_item.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @return void
     */
    public function historicoItemStore(Request $request, PlanoAcaoItemModel $planoAcaoItem)
    {
        $data = $request->only(['texto']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_item_id'] = $planoAcaoItem->id;

        $this->repositoryItemHistorico->create($data);

        return redirect()->route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'))->withFlashSuccess('Acompanhamento criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  PlanoAcaoItemHistoricoModel $historico
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoItemEdit(PlanoAcaoItemModel $planoAcaoItem, PlanoAcaoItemHistoricoModel $historico, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_item.historico_item.update', ['planoAcaoItem' => $planoAcaoItem, 'historico' => $historico]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcaoItem' => $planoAcaoItem],
            'model' => $historico,
        ]);

        $title = 'Editar Acompanhamento';

        $back = route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'));

        return view('backend.core.plano_acao.historico_item.create_update', compact('form', 'title', 'back', 'historico'));
    }

    /**
     * Edição - POST
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  PlanoAcaoItemHistoricoModel $historico
     * @param  Request $request
     * @return void
     */
    public function historicoItemUpdate(PlanoAcaoItemModel $planoAcaoItem, PlanoAcaoItemHistoricoModel $historico, Request $request)
    {
        $data = $request->only(['texto']);
        $data['plano_acao_item_id'] = $planoAcaoItem->id;

        $this->repositoryItemHistorico->update($historico, $data);

        return redirect()->route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'))->withFlashSuccess('Acompanhamento atualizado com sucesso!');
    }

    /**
     * Remover
     *
     * @param  PlanoAcaoItemModel $planoAcaoItem
     * @param  PlanoAcaoItemHistoricoModel $historico
     * @return void
     */
    public function historicoItemDestroy(PlanoAcaoItemModel $planoAcaoItem, PlanoAcaoItemHistoricoModel $historico)
    {
        $this->repositoryItemHistorico->delete($historico);

        return redirect()->route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'))->withFlashSuccess('Acompanhamento deletado com sucesso');
    }
}
