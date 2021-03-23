<?php

namespace App\Http\Controllers\Backend;

use App\Enums\PlanoAcaoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\PlanoAcaoColetivoForm;
use App\Http\Controllers\Backend\Traits\PlanoAcaoColetivoItemTrait;
use App\Http\Controllers\Backend\Traits\PlanoAcaoColetivoUnidadeProdutivaTrait;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Repositories\Backend\Core\PlanoAcaoColetivoItemRepository;
use App\Repositories\Backend\Core\PlanoAcaoColetivoRepository;
use App\Repositories\Backend\Core\PlanoAcaoHistoricoRepository;
use App\Repositories\Backend\Core\PlanoAcaoItemHistoricoRepository;
use App\Services\PlanoAcaoNotificationService;

class PlanoAcaoColetivoController extends Controller
{
    use FormBuilderTrait;
    use PlanoAcaoColetivoItemTrait;
    use PlanoAcaoColetivoUnidadeProdutivaTrait;

    protected $repository;
    protected $repositoryItem;

    public function __construct(PlanoAcaoColetivoRepository $repository, PlanoAcaoColetivoItemRepository $repositoryItem, PlanoAcaoHistoricoRepository $repositoryHistorico, PlanoAcaoItemHistoricoRepository $repositoryItemHistorico)
    {
        $this->repository = $repository;
        $this->repositoryItem = $repositoryItem;

        $this->repositoryHistorico = $repositoryHistorico;
        $this->repositoryItemHistorico = $repositoryItemHistorico;
    }

    /**
     * Listagem do plano de ação coletivo
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function index(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.plano_acao_coletivo.datatable', ['produtor' => $produtor]);

        $title = 'Planos de Ação Coletivos Ativos';

        $showLinkExcluidos = true;

        return view('backend.core.plano_acao_coletivo.index', compact('datatableUrl', 'title', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "index()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatable(ProdutorModel $produtor)
    {
        $data = PlanoAcaoModel::with('plano_acao_filhos')->coletivo()->select("plano_acoes.*");
        if ($produtor && $produtor->id) {
            $data->whereHas('plano_acao_filhos', function ($q) use ($produtor) {
                $q->where("produtor_id", $produtor->id);
            });
        }

        return DataTables::of($data)
            ->addColumn('unidade_produtivas', function ($row) {
                return AppHelper::tableArrayToListExpand($row->plano_acao_filhos->pluck('unidade_produtiva.nome')->toArray(), null, 'Ver todas unidades produtivas', '1');
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('prazo_formatted', function ($row) {
                return $row->prazo_formatted;
            })->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if (in_array($row->status, [PlanoAcaoStatusEnum::Cancelado, PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::NaoIniciado])) {
                    $classBadge = 'text-danger';
                }

                return '<span class="' . $classBadge . ' font-weight-normal">' . PlanoAcaoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.plano_acao_coletivo.edit', $row->id);
                $viewUrl = route('admin.core.plano_acao_coletivo.view', $row->id);
                $deleteUrl = route('admin.core.plano_acao_coletivo.destroy', $row->id);
                $downloadUrl = route('admin.core.plano_acao_coletivo.pdf', $row->id);
                $reopenUrl = route('admin.core.plano_acao_coletivo.reopen', $row->id);

                return view('backend.core.plano_acao_coletivo.form_actions', compact('editUrl', 'viewUrl', 'reopenUrl', 'deleteUrl', 'downloadUrl', 'row'));
            })->filterColumn('unidade_produtivas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('plano_acao_filhos', function ($q) use ($keyword) {
                        $q->whereHas('unidade_produtiva', function ($qq) use ($keyword) {
                            $qq->where('nome', 'like', '%' . $keyword . '%');
                        });
                    });
                }
            })
            ->rawColumns(['status', 'unidade_produtivas'])
            ->make(true);
    }

    /**
     * Cadastro de um PDA Coletivo
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoColetivoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao_coletivo.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Criar Plano de Ação coletivo';

        $planoAcao = null;
        $iframeIndividuaisUrl = null; //route('admin.core.plano_acao_coletivo.item.index_individuais', ['planoAcao' => $planoAcao, 'versaoSimples' => 0]);

        return view('backend.core.plano_acao_coletivo.create_update', compact('form', 'title', 'planoAcao', 'iframeIndividuaisUrl'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(PlanoAcaoColetivoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'prazo']);
        $data['user_id'] = auth()->user()->id;

        $model =  $this->repository->create($data);

        $redirect = route('admin.core.plano_acao_coletivo.edit', ['planoAcao' => $model, 'versaoSimples' => true]);
        return redirect($redirect)->withFlashSuccess('Plano de Ação coletivo criado com sucesso!');
    }

    /**
     * Edição de um PDA Coletivo
     *
     * $versãoSimples corresponde a uma tela simplificada (com menos informações) p/ facilitar na hora do cadastro.
     *  a) abre a tela de adicionar unidade produtiva (não a listagem)
     *  b) abre a tela de adicionar uma ação (não a listagem)
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @param  mixed $versaoSimples
     * @return void
     */
    public function edit(PlanoAcaoModel $planoAcao, FormBuilder $formBuilder, $versaoSimples = 0)
    {
        $form = $formBuilder->create(PlanoAcaoColetivoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.plano_acao_coletivo.update', compact('planoAcao')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $planoAcao,
        ]);

        $title = 'Editar plano de ação';

        $unidadeProdutivaId = 'iframeUnidadeProdutiva';

        if ($versaoSimples) {
            $unidadeProdutivaSrc = route('admin.core.plano_acao_coletivo.unidade_produtiva.create', compact('planoAcao'));
        } else {
            $unidadeProdutivaSrc = route('admin.core.plano_acao_coletivo.unidade_produtiva.index', compact('planoAcao'));
        }

        $itemId = 'iframeItem';
        if ($versaoSimples) {
            $itemSrc = route('admin.core.plano_acao_coletivo.item.create', compact('planoAcao', 'versaoSimples'));
        } else {
            $itemSrc = route('admin.core.plano_acao_coletivo.item.index', compact('planoAcao', 'versaoSimples'));
        }

        $individuaisId = 'iframeIndividuais';
        if ($versaoSimples) {
            $individuaisSrc = null;
        } else {
            $individuaisSrc = route('admin.core.plano_acao_coletivo.item.index_individuais', compact('planoAcao', 'versaoSimples'));
        }

        if ($versaoSimples) {
            $historicoId = null;
            $historicoSrc = null;
        } else {
            $historicoId = 'iframeHistorico';
            $historicoSrc = route('admin.core.plano_acao.historico.index', compact('planoAcao'));
        }

        $iframeIndividuaisUrl = route('admin.core.plano_acao_coletivo.item.index_individuais', ['planoAcao' => $planoAcao, 'versaoSimples' => 0]);

        return view('backend.core.plano_acao_coletivo.create_update', compact('form', 'title', 'historicoId', 'historicoSrc', 'itemId', 'itemSrc', 'unidadeProdutivaId', 'unidadeProdutivaSrc', 'individuaisId', 'individuaisSrc', 'planoAcao', 'iframeIndividuaisUrl'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function update(Request $request, PlanoAcaoModel $planoAcao)
    {
        $form = $this->form(PlanoAcaoColetivoForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'status', 'prazo']);
        $this->repository->update($planoAcao, $data);

        return redirect()->route('admin.core.plano_acao_coletivo.index')->withFlashSuccess('Plano de Ação alterado com sucesso!');
    }

    /**
     * Remover um PDA Coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function destroy(PlanoAcaoModel $planoAcao)
    {
        $this->repository->delete($planoAcao);

        return redirect()->route('admin.core.plano_acao_coletivo.index')->withFlashSuccess('Plano de ação removido com sucesso!');
    }

    /**
     * Remover fisicamente um PDA Coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function forceDelete(PlanoAcaoModel $planoAcao)
    {
        $this->repository->forceDelete($planoAcao);

        return redirect()->route('admin.core.plano_acao_coletivo.index')->withFlashSuccess('Plano de ação removido com sucesso!');
    }

    /**
     * Reabrir um PDA coletivo que foi finalizado/concluído
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function reopen(PlanoAcaoModel $planoAcao)
    {
        $this->repository->reopen($planoAcao);

        return redirect()->route('admin.core.plano_acao_coletivo.index')->withFlashSuccess('Plano de ação reaberto com sucesso!');
    }

    /**
     * Visualizar um PDA coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function view(PlanoAcaoModel $planoAcao)
    {
        $title = 'Plano de Ação Coletivo';

        $back = AppHelper::prevUrl(route('admin.core.plano_acao_coletivo.index'));

        // $datatableUnidadesProdutivas = route('admin.core.plano_acao_coletivo.unidade_produtiva.datatable', ["planoAcao" => $planoAcao]);
        // $datatableAcoesIndividuais = route('admin.core.plano_acao_coletivo.item.datatable_individuais', ["planoAcao" => $planoAcao, "versaoSimples" => 0]);

        $datatableAcoesColetivas =  route('admin.core.plano_acao_coletivo.item.datatable', ["planoAcao" => $planoAcao, "versaoSimples" => 0]);
        $datatableUnidadesProdutivas = route('admin.core.plano_acao_coletivo.unidade_produtiva.datatable', ["planoAcao" => $planoAcao]);

        $datatableAcompanhamentoUrl = route('admin.core.plano_acao.historico.datatable', ["planoAcaoId" => $planoAcao->id]);
        $addHistoricoUrl = route('admin.core.plano_acao.historico.create_and_list', ["planoAcao" => $planoAcao]);

        //datatableUnidadesProdutivas, datatableAcoesIndividuais,
        return view('backend.core.plano_acao_coletivo.view', compact('back', 'datatableAcoesColetivas', 'datatableUnidadesProdutivas', 'datatableAcompanhamentoUrl', 'addHistoricoUrl', 'planoAcao'));
    }

    /**
     * PDF de um PDA coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoNotificationService $service
     * @param  bool $debug
     * @return void
     */
    public function pdf(PlanoAcaoModel $planoAcao, PlanoAcaoNotificationService $service, $debug = false)
    {
        //Se debug, renderiza a "view" do PDF
        if ($debug) {
            return view('backend.core.plano_acao_coletivo.pdf', compact('planoAcao'));
        }

        return $service->getPlanoAcaoColetivoPDF($planoAcao)
            ->download($planoAcao->nome . '.pdf');
    }

    /**
     * Lista dos PDAS coletivos removids
     *
     * @return void
     */
    public function indexExcluidos()
    {
        $datatableUrl = route('admin.core.plano_acao_coletivo.datatableExcluidos');

        $title = 'Planos de Ação Coletivos Excluídos';

        $showLinkExcluidos = false;

        return view('backend.core.plano_acao_coletivo.index', compact('datatableUrl', 'title', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "indexExcluidos()"
     *
     * @return void
     */
    public function datatableExcluidos()
    {
        $data = PlanoAcaoModel::onlyTrashed()->with(['plano_acao_filhos' => function ($q) {
            $q->withTrashed();
        }])->coletivo()->select("plano_acoes.*");

        return DataTables::of($data)
            ->addColumn('unidade_produtivas', function ($row) {
                return AppHelper::tableArrayToListExpand($row->plano_acao_filhos->pluck('unidade_produtiva.nome')->toArray(), null, 'Ver todas unidades produtivas', '1');
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('prazo_formatted', function ($row) {
                return $row->prazo_formatted;
            })->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if (in_array($row->status, [PlanoAcaoStatusEnum::Cancelado, PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::NaoIniciado])) {
                    $classBadge = 'text-danger';
                }

                return '<span class="' . $classBadge . ' font-weight-normal">' . PlanoAcaoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('actions', function ($row) {
                $restoreUrl = route('admin.core.plano_acao_coletivo.restore', $row->id);
                $forceDeleteUrl = route('admin.core.plano_acao_coletivo.forceDelete', ['planoAcao' => $row->id]);

                return view('backend.core.plano_acao_coletivo.form_actions', compact('restoreUrl', 'forceDeleteUrl', 'row'));
            })->filterColumn('unidade_produtivas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('plano_acao_filhos', function ($q) use ($keyword) {
                        $q->whereHas('unidade_produtiva', function ($qq) use ($keyword) {
                            $qq->where('nome', 'like', '%' . $keyword . '%');
                        });
                    });
                }
            })
            ->rawColumns(['status', 'unidade_produtivas'])
            ->make(true);
    }

    /**
     * Restaura um PDA coletivo removido
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function restore(PlanoAcaoModel $planoAcao)
    {
        $this->repository->restore($planoAcao);

        return redirect()->route('admin.core.plano_acao_coletivo.index')->withFlashSuccess('Plano de ação restaurado com sucesso!');
    }
}
