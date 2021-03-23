<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\PlanoAcaoComFormularioForm;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Http\Controllers\Backend\Forms\PlanoAcaoIndividualForm;
use App\Http\Controllers\Backend\Traits\PlanoAcaoHistoricoTrait;
use App\Http\Controllers\Backend\Traits\PlanoAcaoItemComChecklistTrait;
use App\Http\Controllers\Backend\Traits\PlanoAcaoItemHistoricoTrait;
use App\Http\Controllers\Backend\Traits\PlanoAcaoItemTrait;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\Backend\Core\PlanoAcaoHistoricoRepository;
use App\Repositories\Backend\Core\PlanoAcaoItemHistoricoRepository;
use App\Repositories\Backend\Core\PlanoAcaoItemRepository;
use App\Repositories\Backend\Core\PlanoAcaoRepository;
use App\Services\PlanoAcaoNotificationService;
use App\Services\PlanoAcaoService;
use VerumConsilium\Browsershot\Facades\PDF;

class PlanoAcaoController extends Controller
{
    use FormBuilderTrait;
    use PlanoAcaoHistoricoTrait;
    use PlanoAcaoItemTrait;
    use PlanoAcaoItemComChecklistTrait;
    use PlanoAcaoItemHistoricoTrait;

    protected $repository;
    protected $repositoryHistorico;
    protected $repositoryItem;
    protected $repositoryItemHistorico;

    protected $service;

    public function __construct(PlanoAcaoRepository $repository, PlanoAcaoHistoricoRepository $repositoryHistorico, PlanoAcaoItemHistoricoRepository $repositoryItemHistorico, PlanoAcaoItemRepository $repositoryItem, PlanoAcaoService $service)
    {
        $this->repository = $repository;
        $this->repositoryHistorico = $repositoryHistorico;
        $this->repositoryItem = $repositoryItem;
        $this->repositoryItemHistorico = $repositoryItemHistorico;
        $this->service = $service;
    }

    /**
     * Listagem do PDA Individual/Formulário
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function index(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.plano_acao.datatable', ['produtor' => @$produtor]);

        $title = 'Planos de Ação Ativos';

        $showLinkExcluidos = true;

        return view('backend.core.plano_acao.index', compact('datatableUrl', 'title', 'produtor', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "index()"
     *
     * @param  ProdutorModel $produtor
     * @param  bool $trashed
     * @return void
     */
    public function datatable(ProdutorModel $produtor)
    {
        $data = PlanoAcaoModel::with(['produtor:id,nome', 'unidade_produtiva:id,nome'])->individual()->select("plano_acoes.*");
        if (@$produtor->id) {
            $data->where("produtor_id", $produtor->id);
        }

        return DataTables::of($data)
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if (in_array($row->status, [PlanoAcaoStatusEnum::Cancelado, PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::NaoIniciado])) {
                    $classBadge = 'text-danger';
                }

                return '<span class="' . $classBadge . ' font-weight-normal">' . PlanoAcaoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('checklist_unidade_produtiva_id', function ($row) {
                return @$row->checklist_unidade_produtiva_id ? 'Sim' : 'Não';
            })->addColumn('checklist', function ($row) {
                return @$row->checklist_unidade_produtiva_id ? $row->checklist_unidade_produtiva->checklist->nome : '-';
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('prazo_formatted', function ($row) {
                return $row->prazo_formatted;
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.plano_acao.edit', $row->id);
                $viewUrl = route('admin.core.plano_acao.view', $row->id);
                $deleteUrl = route('admin.core.plano_acao.destroy', $row->id);
                $reopenUrl = route('admin.core.plano_acao.reopen', $row->id);

                $downloadUrl = route('admin.core.plano_acao.pdf', $row->id);
                $sendEmailUrl = route('admin.core.plano_acao.sendEmail', $row->id);

                $checklistUnidadeProdutivaUrl = null;
                if ($row->checklist_unidade_produtiva_id) {
                    $checklistUnidadeProdutivaUrl = route('admin.core.checklist_unidade_produtiva.view', $row->checklist_unidade_produtiva_id);
                }

                $messageDelete = __('strings.backend.general.are_you_sure');
                if ($row->checklist_unidade_produtiva_id) {
                    $messageDelete = 'Existe um formulário vinculado a este Plano de Ação. Tem certeza que deseja excluir? Esta ação não implicará na exclusão do formulário vinculado.';
                }

                return view('backend.core.plano_acao.form_actions', compact('editUrl', 'deleteUrl', 'reopenUrl', 'viewUrl', 'downloadUrl', 'sendEmailUrl', 'messageDelete', 'checklistUnidadeProdutivaUrl', 'row'));
            })->orderColumn('checklist_unidade_produtiva_id', function ($query, $order) {
                $query->orderBy('checklist_unidade_produtiva_id', $order);
            })->orderColumn('checklist', function ($query, $order) {
                $query->orderBy('checklist_unidade_produtiva_id', $order);
            })->filterColumn('checklist', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('checklist_unidade_produtiva', function ($q) use ($keyword) {
                        $q->whereHas('checklist', function ($qq) use ($keyword) {
                            $qq->where('checklists.nome', 'like', '%' . $keyword . '%');
                        });
                    });
                }
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Listagem dos PDA Individuais/Formulários removidos
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function indexExcluidos(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.plano_acao.datatableExcluidos', ['produtor' => $produtor]);

        $title = 'Planos de Ação Excluídos';

        $showLinkExcluidos = false;

        return view('backend.core.plano_acao.index', compact('datatableUrl', 'title', 'produtor', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "indexExcluidos"
     *
     * @param  mixed $produtor
     * @param  mixed $trashed
     * @return void
     */
    public function datatableExcluidos(ProdutorModel $produtor, $trashed = false)
    {
        $data = PlanoAcaoModel::onlyTrashed()->with(['produtor:id,nome', 'unidade_produtiva:id,nome'])->individual()->select("plano_acoes.*");
        if (@$produtor->id) {
            $data->where("produtor_id", $produtor->id);
        }

        return DataTables::of($data)
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if (in_array($row->status, [PlanoAcaoStatusEnum::Cancelado, PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::NaoIniciado])) {
                    $classBadge = 'text-danger';
                }

                return '<span class="' . $classBadge . ' font-weight-normal">' . PlanoAcaoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('checklist_unidade_produtiva_id', function ($row) {
                return @$row->checklist_unidade_produtiva_id ? 'Sim' : 'Não';
            })->addColumn('checklist', function ($row) {
                return @$row->checklist_unidade_produtiva_id ? 'Sim' : 'Não';
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('prazo_formatted', function ($row) {
                return $row->prazo_formatted;
            })->addColumn('actions', function ($row) {
                $restoreUrl = route('admin.core.plano_acao.restore', ['planoAcao' => $row->id]);
                $forceDeleteUrl = route('admin.core.plano_acao.forceDelete', ['planoAcao' => $row->id]);
                return view('backend.core.plano_acao.form_actions', compact('restoreUrl', 'forceDeleteUrl', 'row'));
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Visualização do Plano de ação
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function view(PlanoAcaoModel $planoAcao)
    {
        $title = 'Plano de Ação';

        $back = AppHelper::prevUrl(route('admin.core.plano_acao.index'));

        $datatableUrl = route('admin.core.plano_acao.item.datatable', ["planoAcao" => $planoAcao]);
        $datatableAcompanhamentoUrl = route('admin.core.plano_acao.historico.datatable', ["planoAcaoId" => $planoAcao->id]);

        $addHistoricoUrl = route('admin.core.plano_acao.historico.create_and_list', ["planoAcao" => $planoAcao]);

        return view('backend.core.plano_acao.view', compact('back', 'datatableUrl', 'addHistoricoUrl', 'datatableAcompanhamentoUrl', 'planoAcao'));
    }

    /**
     * Cadastro de um PDA Individual/Formulário
     *
     * @param  FormBuilder $formBuilder
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function create(FormBuilder $formBuilder, ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva)
    {
        if (!$this->service->permiteCriarPda($produtor, $unidadeProdutiva)) {
            return redirect()->route('admin.core.plano_acao.index', ['produtor' => $produtor])->withFlashDanger('Já existe um plano de ação individual iniciado para a unidade produtiva/produtor selecionado. Verifique no perfil do produtor/a.');
        }

        $form = $formBuilder->create(PlanoAcaoIndividualForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' =>  ['produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id],
            'data' => ['produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva]
        ]);

        $title = 'Criar Plano de Ação';

        $planoAcao = null;

        return view('backend.core.plano_acao.create_update', compact('form', 'title', 'planoAcao'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(PlanoAcaoIndividualForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'prazo', 'produtor_id', 'unidade_produtiva_id']);
        $data['user_id'] = auth()->user()->id;

        $model =  $this->repository->create($data);

        $redirect = route('admin.core.plano_acao.edit', ['planoAcao' => $model]);
        return redirect($redirect)->withFlashSuccess('Plano de Ação criado com sucesso!');
    }

    /**
     * Edição de um PDA Individual/Formulário
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(PlanoAcaoModel $planoAcao, FormBuilder $formBuilder)
    {
        //Valida se o PDA já foi concluido/cancelado, se sim, não permite editar
        if ($planoAcao->status === PlanoAcaoStatusEnum::Concluido || $planoAcao->status === PlanoAcaoStatusEnum::Cancelado) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger('Não é possível editar um Plano de ação concluído!');
        } else if ($planoAcao->status === PlanoAcaoStatusEnum::AguardandoAprovacao) {
            //Valida se PDA esta aguardando aprovação, se sim, não permite editar
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger('Não é possível editar um Plano de ação em revisão!');
        }

        //Redireciona caso ainda esteja na fase de detalhamento
        if ($planoAcao->status == 'rascunho') {
            return redirect(route('admin.core.plano_acao.edit_com_checklist', ['planoAcao' => $planoAcao]));
        }

        $form = $formBuilder->create(PlanoAcaoIndividualForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.plano_acao.update', compact('planoAcao')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $planoAcao,
            'data' => ['produtor' => $planoAcao->produtor, 'unidadeProdutiva' => $planoAcao->unidade_produtiva, 'checklistUnidadeProdutiva' => $planoAcao->checklist_unidade_produtiva]
        ]);

        $title = 'Editar plano de ação';

        //Iframe dos históricos
        $historicoId = 'iframeHistorico';
        $historicoSrc = route('admin.core.plano_acao.historico.index', compact('planoAcao'));

        //Iframe dos itens do PDA
        $itemId = 'iframeItem';
        $itemSrc = route('admin.core.plano_acao.item.index', compact('planoAcao'));

        return view('backend.core.plano_acao.create_update', compact('form', 'title', 'historicoId', 'historicoSrc', 'itemId', 'itemSrc', 'planoAcao'));
    }

    /**
     * Atualização - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function update(Request $request, PlanoAcaoModel $planoAcao)
    {
        $form = $this->form(PlanoAcaoIndividualForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'status', 'prazo', 'produtor_id', 'unidade_produtiva_id']);
        $this->repository->update($planoAcao, $data);

        return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de Ação alterado com sucesso!');
    }

    /**
     * Remover PDA
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function destroy(PlanoAcaoModel $planoAcao)
    {
        $this->repository->delete($planoAcao);

        return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de ação removido com sucesso!');
    }

    /**
     * Remover Físicamente um PDA
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function forceDelete(PlanoAcaoModel $planoAcao)
    {
        $this->repository->forceDelete($planoAcao);

        return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de ação removido com sucesso!');
    }

    /**
     * Restaurar um PDA que já foi finalizado/concluído
     *
     * Existe uma regra adicional que só pode restaurar caso não tenha nenhum PDA individual vigente ... isso esta tratado no Policy
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function restore(PlanoAcaoModel $planoAcao)
    {
        $this->repository->restore($planoAcao);

        return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de ação restaurado com sucesso!');
    }

    /**
     * Listagem dos produtores/unidades produtivas p/ serem selecionados (no momento de um cadastro de PDA)
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function produtorUnidadeProdutiva(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.plano_acao.datatableProdutorUnidadeProdutiva', ['produtor' => $produtor]);

        $urlBack = route('admin.core.plano_acao.index', ['produtor' => $produtor]);

        return view('backend.core.plano_acao.produtor_unidade_produtiva', compact('datatableUrl', 'urlBack'));
    }

    /**
     * API Datatable "produtorUnidadeProdutiva()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatableProdutorUnidadeProdutiva(ProdutorModel $produtor)
    {
        $sql = UnidadeProdutivaModel::with(['estado', 'cidade'])
            ->select('produtores.uid', 'produtores.nome', 'produtores.cpf', 'produtores.cnpj', 'produtores.id as produtor_id', 'unidade_produtivas.uid as unidade_produtiva_uid', 'unidade_produtivas.id as unidade_produtiva_id', 'unidade_produtivas.nome as unidade_produtiva', 'unidade_produtivas.cidade_id', 'unidade_produtivas.estado_id', 'unidade_produtivas.socios')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');

        if (@$produtor->id) {
            $sql->where('produtores.id', $produtor->id);
        }

        return DataTables::of($sql)
            ->editColumn('uid', function ($row) {
                return $row->uid . ' - ' . $row->unidade_produtiva_uid;
            })->addColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) {
                $addUrl = route('admin.core.plano_acao.create', ['produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]);

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
     * Listagem dos formulários aplicados que ainda não possuem nenhum PDA aplicado
     *
     * @param  ProdutorModel $produtor
     * @param  PlanoAcaoRepository $repository
     * @return void
     */
    public function checklistUnidadeProdutiva(ProdutorModel $produtor, PlanoAcaoRepository $repository)
    {
        $datatableUrl = route('admin.core.plano_acao.datatableChecklistUnidadeProdutiva', ['produtor' => $produtor]);

        $urlBack = route('admin.core.plano_acao.index', ['produtor' => $produtor]);

        return view('backend.core.plano_acao.checklist_unidade_produtiva', compact('datatableUrl', 'urlBack'));
    }

    /**
     * API Datatable "checklistUnidadeProdutiva()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatableChecklistUnidadeProdutiva(ProdutorModel $produtor)
    {
        $data = ChecklistUnidadeProdutivaModel::with(['checklist', 'produtor', 'unidade_produtiva', 'usuario', 'plano_acao_principal'])
            ->whereIn('checklist_unidade_produtivas.status', [ChecklistStatusEnum::Finalizado, ChecklistStatusEnum::Rascunho, ChecklistStatusEnum::AguardandoAprovacao, ChecklistStatusEnum::AguardandoPda])
            ->whereHas('checklistScoped', function ($q) {
                $q->whereIn('checklists.plano_acao',  [PlanoAcaoEnum::Obrigatorio, PlanoAcaoEnum::Opcional]);
            })
            ->doesntHave('plano_acao_principal')
            ->select("checklist_unidade_produtivas.*");

        $data = @$produtor->id ? $data->where('produtor_id', $produtor->id) : $data;

        return DataTables::of($data)
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if ($row->status == ChecklistStatusEnum::Rascunho || $row->status ==  ChecklistStatusEnum::Cancelado)
                    $classBadge = 'text-danger';

                return '<span class="' . $classBadge . ' font-weight-normal">' . ChecklistStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->editColumn('status_flow', function ($row) {
                if (!$row->status_flow) {
                    return '-';
                }

                return  @ChecklistStatusFlowEnum::toSelectArray()[$row->status_flow];
            })->addColumn('actions', function ($row) {
                $addPdaUrl = route('admin.core.plano_acao.create_com_checklist', ['checklistUnidadeProdutiva' => $row->id]);
                return view('backend.core.checklist_unidade_produtiva.form_actions', compact('addPdaUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })
            ->rawColumns(['status', 'status_flow'])
            ->make(true);
    }

    /**
     * Cadastro - PDA com Formulário (Checklist)
     *
     * @param  FormBuilder $formBuilder
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function createComChecklist(FormBuilder $formBuilder, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if (!$this->service->permiteCriarPdaComChecklistPerguntas($checklistUnidadeProdutiva->checklist)) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger('Não é possível criar um Plano de Ação com o formulário selecionado, ele não possui perguntas para compor o plano de ação.');
        }

        if (!$this->service->permiteCriarPdaComChecklist($checklistUnidadeProdutiva)) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger('Já existe um plano de ação a partir deste formulário iniciado para a unidade produtiva/produtor selecionado. Verifique no perfil do produtor/a.');
        }

        if (!$this->service->permiteCriarPdaComChecklistConcluido($checklistUnidadeProdutiva)) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger('Já existe um plano de ação a partir deste formulário concluído para a unidade produtiva/produtor selecionado. Você precisa aplicar um novo formulário.');
        }

        $checklist = $checklistUnidadeProdutiva->checklist;
        $produtor = $checklistUnidadeProdutiva->produtor;
        $unidadeProdutiva = $checklistUnidadeProdutiva->unidade_produtiva;

        $form = $formBuilder->create(PlanoAcaoComFormularioForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.store_com_checklist'),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' =>  ['nome' => $checklist->nome, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id, 'checklist_unidade_produtiva_id' => $checklistUnidadeProdutiva->id],
            'data' => ['produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva, 'checklist' => $checklist]
        ]);

        $title = 'Criar Plano de Ação com Formulário';

        $planoAcao = null;

        return view('backend.core.plano_acao.create_update_com_checklist', compact('form', 'title', 'planoAcao'));
    }

    /**
     * Cadastro PDA com Formulário - POST
     *
     * @param  Request $request
     * @return void
     */
    public function storeComChecklist(Request $request)
    {
        $form = $this->form(PlanoAcaoComFormularioForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'prazo', 'status', 'produtor_id', 'unidade_produtiva_id', 'checklist_unidade_produtiva_id']);
        $data['user_id'] = auth()->user()->id;

        $model =  $this->repository->create($data);

        $redirect = route('admin.core.plano_acao.edit_com_checklist', ['planoAcao' => $model]);
        return redirect($redirect)->withFlashSuccess('Plano de Ação criado com sucesso!');
    }

    /**
     * Atualização de um PDA com Formulário
     *
     * @param  FormBuilder $formBuilder
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function editComChecklist(FormBuilder $formBuilder, PlanoAcaoModel $planoAcao)
    {
        //Força a atualização das prioridades das ações quando acessa o detalhamento ... isso foi necessário para não gerar updates/syncs no mobile em Plano de ações que não estão sendo manipulados naquele momento
        $this->repository->updatePrioridades($planoAcao);

        $checklistUnidadeProdutiva = $planoAcao->checklist_unidade_produtiva;
        $checklist = $checklistUnidadeProdutiva->checklist;

        $form = $formBuilder->create(PlanoAcaoComFormularioForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.plano_acao.update_com_checklist', compact('planoAcao')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $planoAcao,
            'data' => ['produtor' => $planoAcao->produtor, 'unidadeProdutiva' => $planoAcao->unidade_produtiva, 'checklist' => $checklist]
        ]);

        $title = 'Editar Plano de Ação';

        $itemId = 'iframeItem';
        $itemSrc = route('admin.core.plano_acao.item.item_index_com_checklist', compact('planoAcao'));

        return view('backend.core.plano_acao.create_update_com_checklist', compact('form', 'title', 'itemId', 'itemSrc', 'planoAcao'));
    }

    /**
     * Atualização de um PDA com formulário - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function updateComChecklist(Request $request, PlanoAcaoModel $planoAcao)
    {
        $form = $this->form(PlanoAcaoComFormularioForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'status', 'prazo', 'produtor_id', 'unidade_produtiva_id']);

        $planoAcao = $this->repository->update($planoAcao, $data);

        if ($planoAcao->status == PlanoAcaoStatusEnum::AguardandoAprovacao) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de Ação enviado com sucesso, aguarde a revisão!');
        } else if ($planoAcao->status == PlanoAcaoStatusEnum::NaoIniciado) {
            return redirect()->route('admin.core.plano_acao.edit', compact('planoAcao'))->withFlashSuccess('Plano de Ação alterado com sucesso!');
        } else {
            return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de Ação alterado com sucesso!');
        }
    }

    /**
     * PDA do plano de ação individual/formulário
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoNotificationService $service
     * @param  bool $debug
     * @return void
     */
    public function pdf(PlanoAcaoModel $planoAcao, PlanoAcaoNotificationService $service, $debug = false)
    {
        if ($debug) {
            return view('backend.core.plano_acao.pdf', compact('planoAcao'));
        }

        return $service->getPlanoAcaoPDF($planoAcao)
            ->download($planoAcao->nome . '.pdf');
    }

    /**
     * Disparo de email do PDA Individual/Formulário com o PDF anexado
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoNotificationService $service
     * @return void
     */
    public function sendEmail(PlanoAcaoModel $planoAcao, PlanoAcaoNotificationService $service)
    {
        try {
            // return (new \App\Mail\Backend\PlanoAcao\SendPlanoAcao($planoAcao, null))->render();
            $service->sendMail($planoAcao);
            return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('E-mail enviado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.core.plano_acao.index')->withFlashDanger($e->getMessage());
        }
    }

    /**
     * Reabre um plano de ação
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function reopen(PlanoAcaoModel $planoAcao)
    {
        $this->repository->reopen($planoAcao);

        return redirect()->route('admin.core.plano_acao.index')->withFlashSuccess('Plano de ação reaberto com sucesso!');
    }
}
