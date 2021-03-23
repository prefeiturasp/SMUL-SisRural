<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\PlanoAcaoItemHistoricoForm;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoColetivoUnidadeProdutivaTrait
{
    /**
     * Listagem dos planos de ações filhos do coletivo (é os PDAS coletivos que tem uma unidade produtiva atrelada)
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function unidadeProdutivaIndex(PlanoAcaoModel $planoAcao, Request $request)
    {
        $title = 'Unidades Produtivas';
        $urlAdd = route('admin.core.plano_acao_coletivo.unidade_produtiva.create', ["planoAcao" => $planoAcao]);

        $urlDatatable = route('admin.core.plano_acao_coletivo.unidade_produtiva.datatable', ["planoAcao" => $planoAcao]);

        return view('backend.core.plano_acao_coletivo.unidade_produtiva.index', compact('urlAdd', 'urlDatatable', 'title', 'planoAcao'));
    }

    /**
     * API Database "unidadeProdutivaIndex()"
     *
     * @param  mixed $planoAcao
     * @param  mixed $request
     * @return void
     */
    public function unidadeProdutivaDatatable(PlanoAcaoModel $planoAcao, Request $request)
    {
        //, 'unidade_produtiva.cidade:id,nome', 'unidade_produtiva.estado:id,nome'
        //'itens:id,plano_acao_id,status'
        $data = $planoAcao
            ->plano_acao_filhos_with_count_status()
            ->with(['unidade_produtiva:id,uid,nome', 'produtor:id,uid,nome'])
            ->getQuery();

        return DataTables::of($data)
            ->editColumn('produtor.nome', function ($row) use ($planoAcao) {
                return $row->produtor->nome . '<br>' . $row->unidade_produtiva['socios'];
            })->editColumn('nao_iniciado', function ($row) {
                return $row->nao_iniciado . ' ' . AppHelper::toPerc($row->nao_iniciado, $row->total);
            })->editColumn('em_andamento', function ($row) {
                return $row->em_andamento . ' ' . AppHelper::toPerc($row->em_andamento, $row->total);
            })->editColumn('cancelado', function ($row) {
                return $row->cancelado . ' ' . AppHelper::toPerc($row->cancelado, $row->total);
            })->editColumn('concluido', function ($row) {
                return $row->concluido . ' ' . AppHelper::toPerc($row->concluido, $row->total);
            })->addColumn('actions', function ($row) use ($planoAcao) {
                $deleteUrl = route('admin.core.plano_acao_coletivo.unidade_produtiva.destroy', ['planoAcao' => $planoAcao, 'item' => $row]);
                $createHistoricoUrl = route('admin.core.plano_acao_coletivo.unidade_produtiva.create_and_list', ['planoAcao' => $planoAcao, 'planoAcaoFilhoId' => $row->id]);

                return view('backend.core.plano_acao_coletivo.unidade_produtiva.form_actions', compact('deleteUrl', 'createHistoricoUrl', 'row'));
            })->addColumn('actionsView', function ($row) use ($planoAcao) {
                $createHistoricoUrl = route('admin.core.plano_acao_coletivo.unidade_produtiva.create_and_list', ['planoAcao' => $planoAcao, 'planoAcaoFilhoId' => $row->id]);

                return view('backend.core.plano_acao_coletivo.item.form_actions', compact('createHistoricoUrl', 'planoAcao', 'row'));
            })->filterColumn('unidade_produtiva', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('unidade_produtivas.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('produtor.nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('produtor', function ($q) use ($keyword) {
                        $q->where('nome', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['produtor.nome'])
            ->make(true);
    }


    /**
     * Criar um PDA coletivo filho (pda coletivo de uma unidade produtiva)
     *
     * Mostra uma lista de unidades produtivas para o usuário selecionar qual ele quer adicionar no PDA Coletivo PAI
     *
     * @param  mixed $planoAcao
     * @return void
     */
    public function unidadeProdutivaCreate(PlanoAcaoModel $planoAcao)
    {
        $datatableUrl = route('admin.core.plano_acao_coletivo.unidade_produtiva.unidadeProdutivaCreateDatatable', ['planoAcao' => $planoAcao]);

        $urlBack = route('admin.core.plano_acao_coletivo.unidade_produtiva.index', ['planoAcao' => $planoAcao]);

        return view('backend.core.plano_acao_coletivo.unidade_produtiva.create', compact('datatableUrl', 'planoAcao', 'urlBack'));
    }

    /**
     * API Database "unidadeProdutivaCreate()"
     *
     * @param  mixed $planoAcao
     * @return void
     */
    public function unidadeProdutivaCreateDatatable(PlanoAcaoModel $planoAcao)
    {
        $sql = UnidadeProdutivaModel::with(['estado', 'cidade'])
            ->select('produtores.uid', 'produtores.nome', 'produtores.cpf', 'produtores.cnpj', 'produtores.id as produtor_id', 'unidade_produtivas.uid as unidade_produtiva_uid', 'unidade_produtivas.id as unidade_produtiva_id', 'unidade_produtivas.nome as unidade_produtiva', 'unidade_produtivas.cidade_id', 'unidade_produtivas.estado_id', 'unidade_produtivas.socios')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');

        $sql->whereNotIn('unidade_produtiva_id', $planoAcao->plano_acao_filhos->pluck("unidade_produtiva_id"));

        return DataTables::of($sql)
            ->editColumn('uid', function ($row) {
                return $row->uid . ' - ' . $row->unidade_produtiva_uid;
            })->addColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) use ($planoAcao) {
                $addUrl = route('admin.core.plano_acao_coletivo.unidade_produtiva.store', ['planoAcao' => $planoAcao, 'produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]);

                return view('backend.components.form-actions.index', compact('addUrl'));
            })->filterColumn('unidade_produtiva', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('unidade_produtivas.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('cpf', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.cpf', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.nome', 'like', '%' . $keyword . '%');
                }
            })
            ->make(true);
    }

    /**
     *
     * "unidadeProdutivaCreate()" - POST
     *
     * Gera um PDA coletivo para a unidade produtiva selecionada
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function unidadeProdutivaStore(PlanoAcaoModel $planoAcao, ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $this->repository->createUnidadeProdutiva($planoAcao, $produtor, $unidadeProdutiva);

        return redirect()->route('admin.core.plano_acao_coletivo.unidade_produtiva.create', compact('planoAcao'))->withFlashSuccess('Unidade produtiva vinculada com sucesso!');
    }

    /**
     * Remove a unidade produtiva do PDA Coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoModel $item
     * @return void
     */
    public function unidadeProdutivaDestroy(PlanoAcaoModel $planoAcao, PlanoAcaoModel $item)
    {
        $this->repository->delete($item);

        return redirect()->route('admin.core.plano_acao_coletivo.unidade_produtiva.index', compact('planoAcao'))->withFlashSuccess('Unidade produtiva removida com sucesso');
    }

    /**
     * Lista todos os acompanhamentos e permite adicionar um novo acompanhamento no Plano de ação coletivo da "Unidade Produtiva"
     *
     * Importante: PDA coletivo da "UNIDADE PRODUTIVA"
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function unidadeProdutivaCreateAndList(PlanoAcaoModel $planoAcao, $planoAcaoFilhoId, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoItemHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_coletivo.unidade_produtiva.store_create_and_list', ['planoAcao' => $planoAcao, 'planoAcaoFilhoId' => $planoAcaoFilhoId]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
        ]);

        //, 'planoAcaoFilho' => $planoAcaoFilho
        $urlDatatable = route('admin.core.plano_acao.historico.datatable', ["planoAcaoId" => $planoAcaoFilhoId]);

        return view('backend.core.plano_acao_coletivo.unidade_produtiva.modal_create_list', compact('form', 'planoAcao', 'urlDatatable'));
    }

    /**
     * POST p/ inserir um acompanhamento no PDA coletivo da unidade produtiva
     *
     * @param  Request $request
     * @param  mixPlanoAcaoModeled $planoAcao
     * @return void
     */
    public function unidadeProdutivaStoreCreateAndList(Request $request, PlanoAcaoModel $planoAcao, $planoAcaoFilhoId)
    {
        $data = $request->only(['texto']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_id'] = $planoAcaoFilhoId;

        $this->repositoryHistorico->create($data);

        return redirect()->route('admin.core.plano_acao_coletivo.unidade_produtiva.create_and_list', compact('planoAcao', 'planoAcaoFilhoId'))->withFlashSuccess('Acompanhamento criado com sucesso!');
    }
}
