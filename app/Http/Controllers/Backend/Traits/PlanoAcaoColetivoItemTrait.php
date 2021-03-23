<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\PlanoAcaoItemForm;
use App\Http\Controllers\Backend\Forms\PlanoAcaoItemIndividuaisForm;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoColetivoItemTrait
{
    /**
     * Listagem dos itens dos planos de ação coletivo
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function itemIndex(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0, UnidadeProdutivaModel $unidadeProdutiva = null)
    {
        $title = 'Ações coletivas cadastradas';
        $urlAdd = route('admin.core.plano_acao_coletivo.item.create', ["planoAcao" => $planoAcao, "versaoSimples" => $versaoSimples]);

        $urlDatatable = route('admin.core.plano_acao_coletivo.item.datatable', ["planoAcao" => $planoAcao, "versaoSimples" => $versaoSimples]);

        $unidadesProdutivas = $planoAcao->plano_acao_filhos->pluck('unidade_produtiva.nome', 'unidade_produtiva.id')->toArray();

        return view('backend.core.plano_acao_coletivo.item.index', compact('urlAdd', 'urlDatatable', 'title', 'unidadesProdutivas', 'planoAcao', 'versaoSimples', 'unidadeProdutiva'));
    }

    /**
     * API Database "itemDatatable()"
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @return void
     */
    public function itemDatatable(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0)
    {
        //     $data = $planoAcao->with(['itens' => function ($q) {
        //    $q->with('plano_acao_item_filhos_with_count_status');
        //}])->getQuery();

        $data = $planoAcao->itens_with_count_status()->getQuery();

        $datatables = DataTables::of($data)
            ->editColumn('prioridade', function ($row) {
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
                return $row->prazo_formatted;
            })
            ->editColumn('nao_iniciado', function ($row) {
                return $row->nao_iniciado . ' ' . AppHelper::toPerc($row->nao_iniciado, $row->total);
            })->editColumn('em_andamento', function ($row) {
                return $row->em_andamento . ' ' . AppHelper::toPerc($row->em_andamento, $row->total);
            })->editColumn('cancelado', function ($row) {
                return $row->cancelado . ' ' . AppHelper::toPerc($row->cancelado, $row->total);
            })->editColumn('concluido', function ($row) {
                return $row->concluido . ' ' . AppHelper::toPerc($row->concluido, $row->total);
            })
            ->addColumn('actions', function ($row) use ($planoAcao, $versaoSimples) {
                $params = ['planoAcao' => $row->plano_acao_id, 'item' => $row, 'versaoSimples' => $versaoSimples];

                $editUrl = route('admin.core.plano_acao_coletivo.item.edit', $params);
                $deleteUrl = route('admin.core.plano_acao_coletivo.item.destroy', $params);
                $reopenUrl = route('admin.core.plano_acao_coletivo.item.reopen', $params);
                $createHistoricoUrl = route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $row]);

                if ($versaoSimples) {
                    $createHistoricoUrl = null;
                }

                return view('backend.core.plano_acao_coletivo.item.form_actions', compact('createHistoricoUrl', 'editUrl', 'deleteUrl', 'reopenUrl', 'planoAcao', 'row'));
            })
            ->addColumn('actionsView', function ($row) use ($planoAcao, $versaoSimples) {
                $createHistoricoUrl = route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $row]);
                return view('backend.core.plano_acao_coletivo.item.form_actions', compact('createHistoricoUrl', 'planoAcao', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['prioridade', 'status']);

        return $datatables->make(true);;
    }

    /**
     * Cadastro de um item do PDA Coletivo
     *
     * @param  FormBuilder $formBuilder
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @return void
     */
    public function itemCreate(FormBuilder $formBuilder, PlanoAcaoModel $planoAcao, $versaoSimples = 0)
    {
        if (!$planoAcao->fl_coletivo || $planoAcao->plano_acao_coletivo_id) {
            return redirect()->route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'))->withFlashDanger('Ação não permitida!');
        }

        $form = $formBuilder->create(PlanoAcaoItemForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_coletivo.item.store', ['planoAcao' => $planoAcao, 'versaoSimples' => $versaoSimples]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
        ]);

        $title = 'Nova ação coletiva';

        $back = route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'));

        $historicoId = null;
        $historicoSrc = null;
        $flIndividual = false;

        return view('backend.core.plano_acao_coletivo.item.create_update', compact('form', 'historicoId', 'historicoSrc', 'title', 'back', 'flIndividual'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @return void
     */
    public function itemStore(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0)
    {
        $data = $request->only(['descricao', 'status', 'prazo', 'prioridade', 'observacao']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_id'] = $planoAcao->id;

        //Tratamento só no create
        $model = $this->repositoryItem->create($data);
        if ($data['observacao']) {
            $this->repositoryItemHistorico->create(array('texto' => $data['observacao'], 'plano_acao_item_id' => $model->id, 'user_id' => auth()->user()->id));
        }

        return redirect()->route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'))->withFlashSuccess('Ação criada com sucesso!');
    }

    /**
     * Edição de um item do PDA coletivo
     *
     * @param  FormBuilder $formBuilder
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemEdit(FormBuilder $formBuilder, PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $form = $formBuilder->create(PlanoAcaoItemForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_coletivo.item.update', ['planoAcao' => $planoAcao, 'item' => $item, 'versaoSimples' => $versaoSimples]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
            'model' => $item,
        ]);

        $title = 'Editar ação coletiva';

        $back = route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'));

        $planoAcaoItem = $item;
        $historicoId = 'iframeHistorico';
        $historicoSrc = route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'));
        $flIndividual = false;

        return view('backend.core.plano_acao_coletivo.item.create_update', compact('form', 'historicoId', 'historicoSrc', 'title', 'back', 'item', 'flIndividual'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemUpdate(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $data = $request->only(['descricao', 'status', 'prazo', 'prioridade', 'observacao']);
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryItem->update($item, $data);

        //Não é o coletivo, é o item individual, muda o redirect
        $unidadeProdutiva = null;
        if ($planoAcao->plano_acao_coletivo_id) {
            $unidadeProdutiva = UnidadeProdutivaModel::where("id", $planoAcao->unidade_produtiva_id)->first();
            $planoAcao = PlanoAcaoModel::where('id', $planoAcao->plano_acao_coletivo_id)->first();
        }

        return redirect()->route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples', 'unidadeProdutiva'))->withFlashSuccess('Ação atualizada com sucesso!');
    }

    /**
     * Remover um item do PDA coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemDestroy(PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $this->repositoryItem->delete($item);

        //Não é o coletivo, é o item individual, muda o redirect
        $unidadeProdutiva = null;
        if ($planoAcao->plano_acao_coletivo_id) {
            $unidadeProdutiva = UnidadeProdutivaModel::where("id", $planoAcao->unidade_produtiva_id)->first();
            $planoAcao = PlanoAcaoModel::where('id', $planoAcao->plano_acao_coletivo_id)->first();
        }

        return redirect()->route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples', 'unidadeProdutiva'))->withFlashSuccess('Ação deletada com sucesso');
    }

    /**
     * Listagem dos itens individuais (itens das unidades produtivas) do plano de ação coletivo (os filhos)
     *
     * Existe uma filtragem que é passada por parametro na url (unidade produtiva, plano de acao item pai)
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  mixed $unidadeProdutiva
     * @param  mixed $planoAcaoItem
     * @return void
     */
    public function itemIndividuaisIndex(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0, $unidadeProdutiva = null, $planoAcaoItem = null)
    {
        $title = 'Ações individuais';

        $urlDatatable = route('admin.core.plano_acao_coletivo.item.datatable_individuais', ["planoAcao" => $planoAcao, "versaoSimples" => $versaoSimples]);

        $unidadesProdutivas = $planoAcao->plano_acao_filhos->pluck('unidade_produtiva.nome', 'unidade_produtiva.id')->toArray();
        $itens = $planoAcao->itens->pluck('descricao', 'id')->toArray();

        //Controla a seleção do filtro por sessão p/ não precisar propagar a variavel para as rotas
        $selectUnidadeProdutiva = null;
        $selectItem = null;
        $selectPrioridade = null;
        $selectStatus = null;

        $filterPlanoAcaoSession = $request->session()->get('filter_plano_acao');

        //Se a unidade produtiva vem pela url, ela tem prioridade no filtro (não importa o resto)
        if ($unidadeProdutiva) {
            $selectUnidadeProdutiva = $unidadeProdutiva;
        } else if ($planoAcaoItem) {
            $selectItem = $planoAcaoItem;
        } else if (@$filterPlanoAcaoSession && $planoAcao->id == $filterPlanoAcaoSession) {
            $selectUnidadeProdutiva = @$request->session()->get('filter_unidade_produtiva');
            $selectItem = @$request->session()->get('filter_item');
            $selectPrioridade = @$request->session()->get('filter_prioridade');
            $selectStatus = @$request->session()->get('filter_status');
        }

        //Se vier pela url, força por cima da sessão
        if ($unidadeProdutiva && @$unidadeProdutiva->id) {
            $selectUnidadeProdutiva = $unidadeProdutiva->id;
        }

        return view('backend.core.plano_acao_coletivo.item.index_individuais', compact('urlDatatable', 'title', 'itens', 'unidadesProdutivas', 'planoAcao', 'versaoSimples', 'selectUnidadeProdutiva', 'selectItem', 'selectPrioridade', 'selectStatus'));
    }

    /**
     * API Datatable "itemIndividuaisIndex()"
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @return void
     */
    public function itemIndividuaisDatatable(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0)
    {
        $data = PlanoAcaoItemModel::whereIn("plano_acao_id", $planoAcao->plano_acao_filhos->pluck('id'))->with(['historicos', 'plano_acao:id,nome,unidade_produtiva_id']);

        //Aplicação do filtro (unidade produtiva, item, prioridade, status)
        if (@$request->get('filter_unidade_produtiva') || @$request->get('filter_item') || @$request->get('filter_prioridade') || @$request->get('filter_status')) {
            $unidade_produtiva_id = @$request->get('filter_unidade_produtiva');
            $item_id = @$request->get('filter_item');
            $prioridade = @$request->get('filter_prioridade');
            $status = @$request->get('filter_status');

            $request->session()->put('filter_plano_acao', $planoAcao->id);
            $request->session()->put('filter_unidade_produtiva', $unidade_produtiva_id);
            $request->session()->put('filter_item', $item_id);
            $request->session()->put('filter_prioridade', $prioridade);
            $request->session()->put('filter_status', $status);

            if ($unidade_produtiva_id) {
                $data->whereHas('plano_acao', function ($q) use ($unidade_produtiva_id) {
                    $q->where("unidade_produtiva_id", $unidade_produtiva_id);
                });
            }

            if ($item_id) {
                $data->where("plano_acao_item_coletivo_id", $item_id);
            }

            if ($prioridade) {
                $data->where('prioridade', $prioridade);
            }

            if ($status) {
                $data->where('status', $status);
            }
        } else {
            //Caso não tenha filtro, força a limpeza da sessão
            $request->session()->put('filter_plano_acao', null);
            $request->session()->put('filter_unidade_produtiva', null);
            $request->session()->put('filter_item', null);
            $request->session()->put('filter_prioridade', null);
            $request->session()->put('filter_status', null);
        }

        //Dados
        $datatables = DataTables::of($data)
            ->addColumn('unidade_produtiva', function ($row) {
                return $row->plano_acao->unidade_produtiva->nome;
            })
            ->addColumn('historicos', function ($row) {
                $table = '<table class="table table-ater"><tr><th>#</th><th>Acompanhamento</th><th>Usuário</th><th>Adicionado em</th></tr>';
                foreach ($row->historicos as $k => $v) {
                    $table .= '<tr><td>' . $v['uid'] . '</td><td>' . $v['texto'] . '</td><td>' . $v->usuario->first_name . '</td><td>' . $v['created_at_formatted'] . '</td></tr>';
                }
                $table .= '</table>';
                return $table;
            })
            ->editColumn('prioridade', function ($row) {
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
            ->addColumn('actions', function ($row) use ($planoAcao, $versaoSimples) {
                $params = ['planoAcao' => $row->plano_acao_id, 'item' => $row, 'versaoSimples' => $versaoSimples];

                $editUrl = route('admin.core.plano_acao_coletivo.item.edit_individuais', $params);
                $createHistoricoUrl = route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $row]);
                $reopenUrl = route('admin.core.plano_acao_coletivo.item.reopenIndividual', $params);

                if ($versaoSimples) {
                    $createHistoricoUrl = null;
                }

                return view('backend.core.plano_acao_coletivo.item.form_actions', compact('createHistoricoUrl', 'editUrl', 'reopenUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->orderColumn('unidade_produtiva', function ($query, $order) {
                // $query->orderBy('plano_acao_coletivo_id', $order);
                $query->whereHas('plano_acao', function ($q) use ($order) {
                    $q->whereHas('unidade_produtiva', function ($qq) use ($order) {
                        $qq->orderBy('nome', $order);
                    });
                });
            })
            ->rawColumns(['prioridade', 'status', 'historicos']);

        return $datatables->make(true);;
    }

    /**
     * Edição um item individual de um PDA coletivo
     *
     * @param  FormBuilder $formBuilder
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemIndividuaisEdit(FormBuilder $formBuilder, PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $form = $formBuilder->create(PlanoAcaoItemIndividuaisForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_coletivo.item.update_individuais', ['planoAcao' => $planoAcao, 'item' => $item, 'versaoSimples' => $versaoSimples]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
            'model' => $item,
        ]);

        $title = 'Editar ação';

        $planoAcaoItem = $item;
        $historicoId = 'iframeHistorico';
        $historicoSrc = route('admin.core.plano_acao_item.historico_item.index', compact('planoAcaoItem'));

        $planoAcao = PlanoAcaoModel::where('id', $planoAcao->plano_acao_coletivo_id)->first();
        $back = route('admin.core.plano_acao_coletivo.item.index_individuais', compact('planoAcao', 'versaoSimples'));

        $flIndividual = true;

        return view('backend.core.plano_acao_coletivo.item.create_update', compact('form', 'historicoId', 'historicoSrc', 'title', 'back', 'item', 'flIndividual'));
    }

    /**
     * Edição - POST
     */
    public function itemIndividuaisUpdate(Request $request, PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $data = $request->only(['status']);
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryItem->update($item, $data);

        //Volta para o coletivo com a Unidade Produtiva Selecionada
        $unidadeProdutiva = null; // UnidadeProdutivaModel::where("id", $planoAcao->unidade_produtiva_id)->first();
        $planoAcao = PlanoAcaoModel::where('id', $planoAcao->plano_acao_coletivo_id)->first();

        return redirect()->route('admin.core.plano_acao_coletivo.item.index_individuais', compact('planoAcao', 'versaoSimples', 'unidadeProdutiva'))->withFlashSuccess('Ação atualizada com sucesso!');
    }

    /**
     * Reabrir um item individual do PDA coletivo, na listagem dos itens (pai) do coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemReopen(PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $versaoSimples = 0;

        $this->repositoryItem->reopen($item);

        return redirect()->route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'))->withFlashSuccess('Ação reaberta com sucesso!');
    }

    /**
     * Reabrir um item individual do PDA coletivo, na listagem dos itens individuais
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixed $versaoSimples
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemIndividualReopen(PlanoAcaoModel $planoAcao, $versaoSimples = 0, PlanoAcaoItemModel $item)
    {
        $versaoSimples = 0;

        $this->repositoryItem->reopen($item);

        $planoAcao = PlanoAcaoModel::where('id', $planoAcao->plano_acao_coletivo_id)->first();
        return redirect()->route('admin.core.plano_acao_coletivo.item.index_individuais', compact('planoAcao', 'versaoSimples'))->withFlashSuccess('Ação atualizada com sucesso!');
    }
}
