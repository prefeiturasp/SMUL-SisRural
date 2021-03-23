<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Http\Controllers\Backend\Forms\PlanoAcaoItemModalForm;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoItemComChecklistTrait
{

    /**
     * Listagem dos itens do plano de ação que possuem formulário (checklist_unidade_produtiva_id)
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function itemIndexComChecklist(PlanoAcaoModel $planoAcao, Request $request)
    {
        $title = 'Ações cadastradas';
        $urlAdd = route('admin.core.plano_acao.item.create', ["planoAcao" => $planoAcao]);

        $urlDatatable = route('admin.core.plano_acao.item.datatableComChecklist', ["planoAcao" => $planoAcao]);

        return view('backend.core.plano_acao.item.index_com_checklist', compact('urlAdd', 'urlDatatable', 'title', 'planoAcao'));
    }

    /**
     * API Datatable "itemIndexComChecklist()"
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function itemDatatableComChecklist(PlanoAcaoModel $planoAcao, Request $request)
    {
        $respostas = $planoAcao->checklist_unidade_produtiva->getRespostas();

        return DataTables::of($planoAcao->itens()->getQuery())
            ->editColumn('prioridade', function ($row) {
                return '<img src="img/backend/select/' . $row->prioridade . '.png" alt="' . PlanoAcaoPrioridadeEnum::toSelectArray()[$row->prioridade] . '"  style="display:block; margin:auto; padding-right:30px;"/>';
            })->addColumn('pergunta', function ($row) {
                return $row->checklist_pergunta->pergunta->pergunta;
            })->addColumn('resposta', function ($row) use ($respostas) {
                $resposta = $row->checklist_snapshot_resposta;
                if (@$resposta) { //Usuário pode não ter respondido
                    return @$resposta->resposta_id ? $resposta->respostas()->first()->descricao : $resposta->resposta;
                } else {
                    return @$respostas[$row->checklist_pergunta->pergunta_id]['resposta'];
                }
            })->addColumn('plano_acao_default', function ($row) {
                return $row->checklist_pergunta->pergunta->plano_acao_default;
            })->editColumn('descricao', function ($row) {
                $descricao = $row->descricao;
                $plano_acao_default = $row->checklist_pergunta->pergunta->plano_acao_default;

                //Some só para ter impressão que ele precisa digitar o detalhamento do plano de ação
                if ($descricao == $plano_acao_default) {
                    return "";
                } else {
                    return $descricao;
                }
            })
            ->addColumn('actions', function ($row) {
                $params = ['planoAcao' => $row->plano_acao_id, 'item' => $row->id];

                $detalharAcaoUrl = route('admin.core.plano_acao.item.modal_edit_com_checklist', $params);
                $prioridadeUpUrl =  route('admin.core.plano_acao.item.prioridadeUp', $params);
                $prioridadeDownUrl =  route('admin.core.plano_acao.item.prioridadeDown', $params);

                return view('backend.core.plano_acao.item.form_actions_com_checklist', compact('detalharAcaoUrl', 'prioridadeUpUrl', 'prioridadeDownUrl', 'row'));
            })->filter(function ($query) use ($request) {
                if ($request->has('filter_prioridade') && $request->get('filter_prioridade')) {
                    $query->where('prioridade', $request->get('filter_prioridade'));
                }
            })
            ->rawColumns(['prioridade'])
            ->make(true);
    }

    /**
     * Move a prioridade do item "para cima"
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemPrioridadeUp(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item)
    {
        $this->repositoryItem->prioridadeUp($item);
        return null;
    }

    /**
     * Move a prioridade do item "para baixo"
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @return void
     */
    public function itemPrioridadeDown(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item)
    {
        $this->repositoryItem->prioridadeDown($item);
        return null;
    }

    /**
     * Edição
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoItemModel $item
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function modalEditComChecklist(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item, FormBuilder $formBuilder)
    {
        if ($item->checklist_pergunta->pergunta->plano_acao_default == $item->descricao) {
            $item->descricao = '';
        }

        $form = $formBuilder->create(PlanoAcaoItemModalForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.item.modal_update_com_checklist', ['planoAcao' => $planoAcao, 'item' => $item]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
            'model' => $item,
        ]);

        return view('backend.core.plano_acao.item.modal_edit_com_checklist', compact('form', 'item'));
    }

    /**
     * Edição - POST
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  mixPlanoAcaoItemModeled $item
     * @param  Request $request
     * @return void
     */
    public function modalUpdateComChecklist(PlanoAcaoModel $planoAcao, PlanoAcaoItemModel $item, Request $request)
    {
        $data = $request->only(['descricao', 'prazo']);
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryItem->update($item, $data);

        return redirect()->route('admin.core.plano_acao.item.modal_edit_com_checklist', compact('planoAcao', 'item'))->withFlashSuccess('Ação atualizada com sucesso!');
    }
}
