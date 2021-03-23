<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Http\Controllers\Backend\Forms\PlanoAcaoItemForm;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoItemTrait
{
    /**
     * Listagem dos itens de um Plano de ação Individual/Formulário
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function itemIndex(PlanoAcaoModel $planoAcao, Request $request)
    {
        $title = 'Ações cadastradas';
        $urlAdd = route('admin.core.plano_acao.item.create', ["planoAcao" => $planoAcao]);

        $urlDatatable = route('admin.core.plano_acao.item.datatable', ["planoAcao" => $planoAcao]);

        return view('backend.core.plano_acao.item.index', compact('urlAdd', 'urlDatatable', 'title', 'planoAcao'));
    }

    /**
     * API Database "itemIndex()"
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function itemDatatable(PlanoAcaoModel $planoAcao, Request $request)
    {
        $datatables = DataTables::of($planoAcao->itens()->with('historicos')->getQuery())
            ->addColumn('historicos', function ($row) {
                $table = '<table class="table table-ater"><tr><th>#</th><th>Acompanhamento</th><th>Usuário</th><th>Adicionado em</th></tr>';
                foreach ($row->historicos as $k => $v) {
                    $table .= '<tr><td>' . $v['uid'] . '</td><td>' . $v['texto'] . '</td><td>' . $v->usuario->first_name . '</td><td>' . $v['created_at_formatted'] . '</td></tr>';
                }

                if (count($row->historicos) == 0) {
                    $table .= '<tr><td colspan="4">Não foi cadastrado nenhum acompanhamento para essa ação.</td></tr>';
                }

                $table .= '</table>';
                return $table;
            })->editColumn('prioridade', function ($row) {
                return '<img src="img/backend/select/' . $row->prioridade . '.png" alt="' . PlanoAcaoPrioridadeEnum::toSelectArray()[$row->prioridade] . '"  style="display:block; margin:auto; padding-right:30px;"/>';
            })->editColumn('status', function ($row) {
                return '<img style="width:14px; height:auto;" src="img/backend/select/' . $row->status . '.png" alt="' . PlanoAcaoItemStatusEnum::toSelectArray()[$row->status] . '"/> <span class="ml-2">' . PlanoAcaoItemStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('action_custom', function ($row) {
                return 'Expandir';
            })
            ->addColumn('prazo_formatted', function ($row) {
                return $row->prazo_formatted;
            })
            ->addColumn('ultima_observacao_data_formatted', function ($row) {
                return $row->ultima_observacao_data_formatted;
            })
            ->addColumn('actions', function ($row) {
                $params = ['planoAcao' => $row->plano_acao_id, 'item' => $row->id];

                $editUrl = route('admin.core.plano_acao.item.edit', $params);
                $deleteUrl = route('admin.core.plano_acao.item.destroy', $params);
                $createHistoricoUrl = route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $row]);
                $reopenUrl = route('admin.core.plano_acao.item.reopen', $params);

                return view('backend.core.plano_acao.item.form_actions', compact('createHistoricoUrl', 'editUrl', 'deleteUrl', 'reopenUrl', 'row'));
            })->addColumn('actionsView', function ($row) {
                $createHistoricoUrl = route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $row]);
                return view('backend.core.plano_acao.item.form_actions', compact('createHistoricoUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['prioridade', 'status', 'historicos']);


        //Só adiciona a regra de filtro custom caso a tabela não tenha uma busca geral, apenas por status e prioridade
        if ($request->has('filter_prioridade') && $request->get('filter_prioridade') || $request->has('filter_status') && $request->get('filter_status')) {
            $datatables->filter(function ($query) use ($request) {
                //Ao adicionar um filter ... o filtro geral não funciona mais, cuidado.

                // FILTRO OR
                // if (@$request->get('filter_prioridade') || @$request->get('filter_status')) {
                //     $query->where(function ($q) use ($request) {
                //         if ($request->has('filter_prioridade') && $request->get('filter_prioridade')) {
                //             $q->where('prioridade', $request->get('filter_prioridade'));
                //         }

                //         if ($request->has('filter_status') && $request->get('filter_status')) {
                //             $q->orWhere('status', $request->get('filter_status'));
                //         }
                //     });
                // }

                if ($request->has('filter_prioridade') && $request->get('filter_prioridade')) {
                    $query->where('prioridade', $request->get('filter_prioridade'));
                }

                if ($request->has('filter_status') && $request->get('filter_status')) {
                    $query->where('status', $request->get('filter_status'));
                }
            });
        }

        return $datatables->make(true);;
    }

    /**
     * Cadastro de um item no PDA individual/formulário
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function itemCreate(PlanoAcaoModel $planoAcao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.item.store', ['planoAcao' => $planoAcao]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
        ]);

        $title = 'Nova ação';

        $back = route('admin.core.plano_acao.item.index', compact('planoAcao'));

        $historicoId = null;
        $historicoSrc = null;

        return view('backend.core.plano_acao.item.create_update', compact('form', 'historicoId', 'historicoSrc', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function itemStore(Request $request, PlanoAcaoModel $planoAcao)
    {
        $data = $request->only(['descricao', 'status', 'prazo', 'prioridade', 'observacao']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_id'] = $planoAcao->id;

        //Tratamento só no create
        $model = $this->repositoryItem->create($data);
        if ($data['observacao']) {
            $this->repositoryItemHistorico->create(array('texto' => $data['observacao'], 'plano_acao_item_id' => $model->id, 'user_id' => auth()->user()->id));
        }

        return redirect()->route('admin.core.plano_acao.item.index', compact('planoAcao'))->withFlashSuccess('Ação criada com sucesso!');
    }

    /**
     * Edição do item
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function itemEdit(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.item.update', ['planoAcao' => $planoAcao, 'item' => $item]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
            'model' => $item,
        ]);

        $title = 'Editar ação';

        $back = route('admin.core.plano_acao.item.index', compact('planoAcao'));

        $planoAcaoItem = $item;
        $historicoId = 'iframeHistorico';
        $historicoSrc = route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'));

        return view('backend.core.plano_acao.item.create_update', compact('form', 'historicoId', 'historicoSrc', 'title', 'back', 'item'));
    }

    /**
     * Edição - POST
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @param  Request $request
     * @return void
     */
    public function itemUpdate(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item, Request $request)
    {
        $data = $request->only(['descricao', 'status', 'prazo', 'prioridade', 'observacao']);
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryItem->update($item, $data);

        return redirect()->route('admin.core.plano_acao.item.index', compact('planoAcao'))->withFlashSuccess('Ação atualizada com sucesso!');
    }

    /**
     * Remover item
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemDestroy(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item)
    {
        $this->repositoryItem->delete($item);

        return redirect()->route('admin.core.plano_acao.item.index', compact('planoAcao'))->withFlashSuccess('Ação deletada com sucesso');
    }

    /**
     * Reabrir item que foi concluído/finalizado/cancelado
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemReopen(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item)
    {
        $versaoSimples = 0;

        $this->repositoryItem->reopen($item);

        return redirect()->route('admin.core.plano_acao.item.index', compact('planoAcao'))->withFlashSuccess('Ação reaberta com sucesso!');
    }
}
