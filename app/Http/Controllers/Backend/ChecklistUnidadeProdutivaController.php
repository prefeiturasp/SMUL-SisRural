<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\ChecklistAprovacaoLogsForm;
use App\Http\Controllers\Backend\Forms\ChecklistUnidadeProdutivaCompararForm;
use App\Http\Controllers\Backend\Forms\ChecklistUnidadeProdutivaForm;
use App\Http\Controllers\Backend\Traits\ChecklistUnidadeProdutivaArquivosTrait;
use App\Models\Core\ChecklistAprovacaoLogsModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaArquivoRepository;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaRepository;
use App\Services\ChecklistNotificationService;
use Kris\LaravelFormBuilder\Form;
use App\Enums\TipoPerguntaEnum;

class ChecklistUnidadeProdutivaController extends Controller
{
    use FormBuilderTrait;
    use ChecklistUnidadeProdutivaArquivosTrait;

    protected $repository;
    protected $repositoryArquivo;

    public function __construct(ChecklistUnidadeProdutivaRepository $repository, ChecklistUnidadeProdutivaArquivoRepository $repositoryArquivo)
    {
        $this->repository = $repository;
        $this->repositoryArquivo = $repositoryArquivo;
    }

    /**
     * Listagem dos formulários aplicados de acordo com o ChecklistUnidadeProdutivaPermissionScope
     *
     * É possível filtrar por produtor, retornando apenas os formulários do produtor selecionado (via dashboard do produtor)
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function index(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.checklist_unidade_produtiva.datatable', ['produtor' => $produtor]);
        $aplicarUrl = route('admin.core.checklist_unidade_produtiva.template');
        if ($produtor->id) {
            $aplicarUrl = route('admin.core.checklist_unidade_produtiva.template', ['produtor' => $produtor]);
        }

        $title = 'Formulários Ativos';
        $showLinkExcluidos = true;

        return view('backend.core.checklist_unidade_produtiva.index', compact('datatableUrl', 'aplicarUrl', 'title', 'showLinkExcluidos'));
    }

    /**
     * API datatable "index()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatable(ProdutorModel $produtor)
    {
        $data = ChecklistUnidadeProdutivaModel::with(['checklist:id,nome', 'produtor:id,nome', 'unidade_produtiva:id,nome,socios', 'usuario:id,first_name,last_name', 'plano_acao:id,checklist_unidade_produtiva_id,nome,created_at'])
            ->select("checklist_unidade_produtivas.*");

        $data = @$produtor->id ? $data->where('produtor_id', $produtor->id) : $data;

        return DataTables::of($data)
            ->editColumn('usuario.first_name', function ($row) {
                return $row->usuario->full_name;
            })
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if ($row->status == ChecklistStatusEnum::Rascunho)
                    $classBadge = 'text-danger';

                return '<span class="' . $classBadge . ' font-weight-normal">' . @ChecklistStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->editColumn('status_flow', function ($row) {
                if (!$row->status_flow) {
                    return '-';
                }

                return  @ChecklistStatusFlowEnum::toSelectArray()[$row->status_flow];
            })->addColumn('actions', function ($row) {
                $viewUrl = route('admin.core.checklist_unidade_produtiva.view', $row->id);
                $editUrl = route('admin.core.checklist_unidade_produtiva.edit', $row->id);
                $downloadUrl = route('admin.core.checklist_unidade_produtiva.pdf', $row->id);
                $sendEmailUrl = route('admin.core.checklist_unidade_produtiva.sendEmail', $row->id);
                $deleteUrl = route('admin.core.checklist_unidade_produtiva.destroy', $row->id);

                $planoAcaoUrl = null;
                if ($row->plano_acao_principal) {
                    $planoAcaoUrl = route('admin.core.plano_acao.view', $row->plano_acao_principal->id);
                }

                $messageDelete = 'Tem certeza?';
                if (count($row->plano_acao) > 0) {
                    $messageDelete = '<div>Existe um Plano de Ação vinculado a este Formulário. Tem certeza que deseja excluir? Esta ação não implicará na exclusão do Plano de Ação vinculado.<br><br>';
                    $messageDelete .= '<h3>';
                    foreach ($row->plano_acao as $k => $v) {
                        $messageDelete .= $v->nome . ' - ' . $v->created_at_formatted . '<br>';
                    }
                    $messageDelete .= '</h3></div>';
                }

                return view('backend.core.checklist_unidade_produtiva.form_actions', compact('editUrl', 'viewUrl', 'downloadUrl', 'sendEmailUrl', 'deleteUrl', 'messageDelete', 'planoAcaoUrl', 'row'));
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['status', 'status_flow'])
            ->make(true);
    }

    /**
     * PDF do formulário aplicado
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  ChecklistNotificationService $service
     * @return void
     */
    public function pdf(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, ChecklistNotificationService $service)
    {
        $pdf = $service->getChecklistPDF($checklistUnidadeProdutiva);

        // Para testar "inline" o pdf que foi gerado
        // return $pdf->inline();

        return $pdf->download($checklistUnidadeProdutiva->checklist->nome . '-' . $checklistUnidadeProdutiva->unidade_produtiva->nome . '.pdf');
    }

    /**
     * Disparo de email (email cadastrado no produtor) do formulário aplicado
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  ChecklistNotificationService $service
     * @return void
     */
    public function sendEmail(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, ChecklistNotificationService $service)
    {
        try {
            //Descomentar essa linha para ver como ficou o template do email p/ os dados passados
            // return (new \App\Mail\Backend\Checklist\SendChecklist($checklistUnidadeProdutiva, null))->render();

            $service->sendMail($checklistUnidadeProdutiva);
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('E-mail enviado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashDanger($e->getMessage());
        }
    }

    /**
     * Visualização do formulário aplicado
     *
     * Retorna junto o bloco de análise e o formulário de análise (esse formulário só aparece caso o formulário tenha o status "AguardandoAprovacao" e o usuário tenha permissão para analisar"
     *
     * @param  FormBuilder $formBuilder
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function view(FormBuilder $formBuilder, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $title = 'Formulário';

        $categorias = $checklistUnidadeProdutiva->getCategoriasAndRespostasChecklist();

        $score = $checklistUnidadeProdutiva->score();

        //Logs de análise
        $analises = ChecklistAprovacaoLogsModel::where('checklist_unidade_produtiva_id', '=', $checklistUnidadeProdutiva->id)->orderBy('created_at', 'desc')->get();

        //Plano de ação do formulário aplicado
        $pdaItens = $checklistUnidadeProdutiva->plano_acao_principal ? $checklistUnidadeProdutiva->plano_acao_principal->itens->keyBy('checklist_pergunta_id') : null;
        $respostas = $checklistUnidadeProdutiva->getRespostas();

        //Formulário de análise
        $analiseForm = $formBuilder->create(ChecklistAprovacaoLogsForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist_unidade_produtiva.analiseStore', ['checklistUnidadeProdutiva' => $checklistUnidadeProdutiva]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['status' => ChecklistStatusFlowEnum::toSelectArray()]
        ]);

        $arquivos = $checklistUnidadeProdutiva->arquivos;

        $back = AppHelper::prevUrl(route('admin.core.checklist_unidade_produtiva.index'));
        return view('backend.core.checklist_unidade_produtiva.view', compact('back', 'checklistUnidadeProdutiva', 'title', 'categorias', 'score', 'analises', 'analiseForm', 'pdaItens', 'respostas', 'arquivos'));
    }

    /**
     * Listagem de templates de formulário de acordo com o ChecklistPermissionScope.
     *
     * O usuário vai escolher um template p/ aplicar. Após a seleção, vai para a tela de escolher o produtor/unidade produtiva, p/ aplicar o formulário selecionado.
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function template(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.checklist_unidade_produtiva.datatableTemplate', ['produtor' => $produtor]);

        $urlBack = route('admin.core.checklist_unidade_produtiva.index');
        if (@$produtor->id) {
            $urlBack = route('admin.core.produtor.dashboard', ['produtor' => $produtor]);
        }

        return view('backend.core.checklist_unidade_produtiva.template', compact('datatableUrl', 'urlBack'));
    }

    /**
     * API datatable "template()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatableTemplate(ProdutorModel $produtor)
    {
        return DataTables::of(ChecklistModel::with('dominioWithoutGlobalScopes:id,nome')->publicado())
            ->editColumn('dominioWithoutGlobalScopes.nome', function ($row) {
                return $row->dominioWithoutGlobalScopes->nome;
            })
            ->addColumn('actions', function ($row) use ($produtor) {
                $addUrl = route('admin.core.checklist_unidade_produtiva.produtor_unidade_produtiva', ['checklist' => $row, 'produtor' => $produtor]);

                return view('backend.components.form-actions.index', compact('addUrl', 'row'));
            })
            ->rawColumns(['perguntas'])
            ->make(true);
    }

    /**
     * Listagem de produtor/unidade produtiva p/ aplicar um template de formulário
     *
     * @param  ChecklistModel $checklist
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function produtorUnidadeProdutiva(ChecklistModel $checklist, ProdutorModel $produtor)
    {
        //Caso só exista um produtor, redireciona direto para a tela de aplicação do formulário (create)
        $data = $this->getProdutorUnidadeProdutiva($produtor);
        if ($data->count() == 1) {
            $row = $data->first();
            $addUrl = route('admin.core.checklist_unidade_produtiva.create', ['checklist' => $checklist, 'produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]);
            return redirect($addUrl);
        }

        $datatableUrl = route('admin.core.checklist_unidade_produtiva.datatableProdutorUnidadeProdutiva', ['checklist' => $checklist, 'produtor' => $produtor]);

        $urlBack = route('admin.core.checklist_unidade_produtiva.template', ['produtor' => $produtor]);

        return view('backend.core.checklist_unidade_produtiva.produtor_unidade_produtiva', compact('datatableUrl', 'urlBack'));
    }

    /**
     * Retorna a lista de produtor/unidade produtiva de acordo com o ProdutorPermissionScope + UnidadeProdutivaPermissionScope
     *
     * @param  ProdutorModel $produtor
     * @return Builder
     */
    private function getProdutorUnidadeProdutiva(ProdutorModel $produtor)
    {
        $sql = UnidadeProdutivaModel::with(['estado', 'cidade'])
            ->select('produtores.uid', 'produtores.nome', 'produtores.cpf', 'produtores.cnpj', 'produtores.id as produtor_id', 'unidade_produtivas.uid as unidade_produtiva_uid', 'unidade_produtivas.id as unidade_produtiva_id', 'unidade_produtivas.nome as unidade_produtiva', 'unidade_produtivas.cidade_id', 'unidade_produtivas.estado_id', 'unidade_produtivas.socios')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');

        if (@$produtor->id) {
            $sql->where('produtores.id', $produtor->id);
        }

        return $sql;
    }

    /**
     * API Datatable - "produtorUnidadeProdutiva()"
     *
     * @param  ChecklistModel $checklist
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatableProdutorUnidadeProdutiva(ChecklistModel $checklist, ProdutorModel $produtor)
    {
        $sql = $this->getProdutorUnidadeProdutiva($produtor);

        return DataTables::of($sql)
            ->editColumn('uid', function ($row) {
                return $row->uid . ' - ' . $row->unidade_produtiva_uid;
            })->addColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) use ($checklist) {
                $addUrl = route('admin.core.checklist_unidade_produtiva.create', ['checklist' => $checklist, 'produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]);

                return view('backend.components.form-actions.index', compact('addUrl'));
            })->filterColumn('unidade_produtiva', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('unidade_produtivas.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('cpf', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.cpf', 'like', '%' . $keyword . '%');
                }
            })
            ->make(true);
    }

    /**
     * Retorna todas as respostas da "unidade produtiva" selecionada (tabela unidade_produtiva_respostas)
     *
     * @param  ChecklistModel $checklist
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return mixed
     */
    public static function getRespostas(ChecklistModel $checklist,  ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $respostas = UnidadeProdutivaRespostaModel::where('unidade_produtiva_id', $unidadeProdutiva->id)->get()->toArray();

        $unidProdutivaRespostas = ['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id];

        foreach ($respostas as $k => $v) {
            $value = @$v['resposta_id'] ? $v['resposta_id'] : $v['resposta'];

            if (@$unidProdutivaRespostas[$v['pergunta_id']]) { //array

                $oldValue = $unidProdutivaRespostas[$v['pergunta_id']];
                if (!is_array($oldValue)) {
                    $oldValue = [$oldValue];
                }

                $value = array_merge($oldValue, [$value]);
            }

            $unidProdutivaRespostas[$v['pergunta_id']] = $value;
        }

        return $unidProdutivaRespostas;
    }

    /**
     * Cadastro
     *
     * @param  FormBuilder $formBuilder
     * @param  ChecklistModel $checklist - template do formulário
     * @param  ProdutorModel $produtor - produtor selecionado
     * @param  UnidadeProdutivaModel $unidadeProdutiva - unidade produtiva selecionada
     * @return void
     */
    public function create(FormBuilder $formBuilder, ChecklistModel $checklist, ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva)
    {
        //Se já existir um formulário aplicado com o "checklist/template", "produtor", "unidade produtiva" e estiver com o status "rascunho", redireciona o usuário para a "EDIÇÃO" desse formulário aplicado
        $checklistUnidProdutiva = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id, 'status' => ChecklistStatusEnum::Rascunho])->first();
        if (@$checklistUnidProdutiva) {
            return redirect()->route('admin.core.checklist_unidade_produtiva.edit', $checklistUnidProdutiva->id);
        }

        //Se já existir um formulário aplicado com o "checklist","produtor","unidade produtiva" e estiver com o status "aguardando revisão", retorna um "danger" avisando o usuário que precisa ser revisado antes de aplicar um novo formulário
        $checklistUnidProdutiva = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id])
            ->whereIn('status', [ChecklistStatusEnum::AguardandoAprovacao])->first();
        if (@$checklistUnidProdutiva) {
            return redirect()->back()->withFlashDanger('Não é possível aplicar um novo formulário. Espere a revisão do formulário aplicado.');
        }

        $checklistUnidProdutiva = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id])
            ->whereIn('status', [ChecklistStatusEnum::AguardandoPda])->first();
        if (@$checklistUnidProdutiva) {
            return redirect()->back()->withFlashDanger('Não é possível aplicar um novo formulário. O formulário esta aguardando a criação de um plano de ação.');
        }

        $unidProdutivaRespostas = $this->getRespostas($checklist, $produtor, $unidadeProdutiva);

        //Último PDA aplicado com o status Concluído p/ o mesmo Template/Produtor/Unidade Produtiva
        $itensUltimoPda = $this->getItensUltimoPda($unidadeProdutiva->id, $produtor->id, $checklist->id);

        $form = $formBuilder->create(ChecklistUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist_unidade_produtiva.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $unidProdutivaRespostas,
            'data' => ['checklist' => $checklist, 'produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva, 'itensUltimoPda' => $itensUltimoPda]
        ]);

        $title = 'Aplicar Formulário';

        $back = AppHelper::prevUrl(route('admin.core.checklist_unidade_produtiva.index'));
        return view('backend.core.checklist_unidade_produtiva.create_update', compact('form', 'checklist', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(ChecklistUnidadeProdutivaForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();
        $model = $this->repository->create($data);

        if ($data['redirect_pda']) {
            return $this->redirectPda($model, 'Formulário criado com sucesso!');
        }

        $redirect = route('admin.core.checklist_unidade_produtiva.index');
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.checklist_unidade_produtiva.edit', [$model->id, '#' . $data['custom-redirect']]);
        }

        return redirect($redirect)->withFlashSuccess('Formulário criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, FormBuilder $formBuilder)
    {
        //Se o formulário já estiver "finalizado", não permite editar
        if ($checklistUnidadeProdutiva->status === ChecklistStatusEnum::Finalizado) {
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashDanger('Não é possível editar um Formulário finalizado!');
        } else if ($checklistUnidadeProdutiva->status === ChecklistStatusEnum::AguardandoAprovacao) {
            //Se o formulário estiver "aguardando aprovação", não permite editar
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashDanger('Não é possível editar um Formulário que esta em revisão!');
        }

        $checklist = $checklistUnidadeProdutiva->checklist;
        $produtor = $checklistUnidadeProdutiva->produtor;
        $unidadeProdutiva = $checklistUnidadeProdutiva->unidade_produtiva;
        $usuario = $checklistUnidadeProdutiva->usuario;

        //Retorna as respostas da "unidade produtiva"
        $unidProdutivaRespostas = $this->getRespostas($checklist, $produtor, $unidadeProdutiva);

        //Retorna os logs de analise
        $analises = ChecklistAprovacaoLogsModel::where('checklist_unidade_produtiva_id', '=', $checklistUnidadeProdutiva->id)->orderBy('created_at', 'desc')->get();

        //Último PDA aplicado com o status Concluído p/ o mesmo Template/Produtor/Unidade Produtiva
        $itensUltimoPda = $this->getItensUltimoPda($unidadeProdutiva->id, $produtor->id, $checklist->id);

        $form = $formBuilder->create(ChecklistUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.checklist_unidade_produtiva.update', compact('checklistUnidadeProdutiva')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $checklistUnidadeProdutiva->toArray() + $unidProdutivaRespostas,
            'data' => ['checklist' => $checklist, 'produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva, 'usuario' => $usuario, 'itensUltimoPda' => $itensUltimoPda]
        ]);

        $title = 'Editar formulário';

        //Iframe dos arquivos vinculados ao caderno (ver ChecklistUnidadeProdutivaArquivosTrait.php)
        $arquivosId = 'iframeArquivos';
        $arquivosSrc = route('admin.core.checklist_unidade_produtiva.arquivos.index', compact('checklistUnidadeProdutiva'));

        $back = AppHelper::prevUrl(route('admin.core.checklist_unidade_produtiva.index'));
        return view('backend.core.checklist_unidade_produtiva.create_update', compact('form', 'checklistUnidadeProdutiva', 'arquivosId', 'arquivosSrc', 'checklist', 'title', 'analises', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function update(Request $request, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $form = $this->form(ChecklistUnidadeProdutivaForm::class, [], ['checklist' => $checklistUnidadeProdutiva->checklist]);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();
        $model = $this->repository->update($checklistUnidadeProdutiva, $data);

        if ($data['redirect_pda']) {
            return $this->redirectPda($model, 'Formulário aplicado alterado com sucesso!');
        }

        return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('Formulário aplicado alterado com sucesso!');
    }

    /**
     * Retorna o último PDA aplicado p/ Produtor + Unidade Produtiva + Formulário
     */
    private function getItensUltimoPda($unidadeProdutivaId, $produtorId, $checklistId)
    {
        $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::where(['unidade_produtiva_id' => $unidadeProdutivaId, 'produtor_id' => $produtorId, 'checklist_id' => $checklistId, 'status' => ChecklistStatusEnum::Finalizado])->orderBy('created_at', 'DESC')->first();

        if (!@$checklistUnidadeProdutiva) {
            return null;
        }

        $pda = @$checklistUnidadeProdutiva->plano_acao_principal;

        $itens = null;
        if ($pda && @$pda->itens) {
            $itens = $pda->itens->keyBy('checklist_pergunta_id');
        }

        return $itens;
    }

    /**
     * Redireciona para o Plano de ação com uma mensagem custom
     *
     * @param  ChecklistUnidadeProdutivaModel $model
     * @param  string  $message
     * @return void
     */
    private function redirectPda(ChecklistUnidadeProdutivaModel $model, string $message)
    {
        if (@$model->plano_acao_principal) {
            return redirect()->route('admin.core.plano_acao.edit', $model->plano_acao_principal->id)->withFlashSuccess($message);
        } else {
            return redirect()->route('admin.core.plano_acao.create_com_checklist', ['checklistUnidadeProdutiva' => $model->id])->withFlashSuccess($message);
        }
    }

    /**
     * Remover formulário aplicado (regras no ChecklistUnidadeProdutivaPolicy)
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function destroy(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $this->repository->delete($checklistUnidadeProdutiva);

        if (count($checklistUnidadeProdutiva->plano_acao_trashed) > 0) {
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('Formulário aplicado removido com sucesso. O plano de ação atrelado ao formulário também foi excluído.');
        } else {
            return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('Formulário aplicado removido com sucesso!');
        }
    }

    /**
     * Formulário de comparação de uma lista de formulários e outros filtros.
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function comparar(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistUnidadeProdutivaCompararForm::class, [
            'id' => 'form-builder',
            'method' => 'GET',
            'url' => route('admin.core.checklist_unidade_produtiva.compareView'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Comparar Formulário';

        $back = AppHelper::prevUrl(route('admin.core.checklist_unidade_produtiva.index'));
        return view('backend.core.checklist_unidade_produtiva.comparar', compact('form', 'title', 'back'));
    }

    /**
     * Visualização da comparação de formulários
     *
     * Recebe: templates dos formulários, unidades produtivas, o status, data inicial, data final
     *
     * @param  Request $request
     * @return void
     */
    public function compararView(Request $request)
    {
        $form = $this->form(ChecklistUnidadeProdutivaCompararForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['checklists', 'unidades_produtivas', 'status', 'data_inicial', 'data_final']);

        //Retorna a listagem de formulários aplicados conforme a seleção do filtro
        $listChecklists = ChecklistUnidadeProdutivaModel::whereIn("unidade_produtiva_id", $data['unidades_produtivas'])
            ->whereIn('checklist_id', $data['checklists']) //Template Formulário
            ->where('status', $data['status']) //Status
            ->whereBetween('created_at', [$data['data_inicial'].'00:00:00', $data['data_final'].' 23:59:59']) //Range Datas
            ->with('unidade_produtiva')
            ->orderBy('created_at', 'DESC')
            ->get();


        //Verifica se tem algum registro para mostrar
        if (count($listChecklists) == 0) {
            return redirect()->back()->withErrors(['Não há formulários aplicados com os filtros aplicados.'])->withInput();
        }

        $checklists = array();

        //Resolve as respostas de cada checklist retornado
        foreach ($listChecklists as $k => $v) {
            $categorias = $v->getCategoriasAndRespostasChecklist();

            $respostas = array();
            foreach ($categorias as $kCategoria => $vCategoria) {
                foreach ($vCategoria->perguntas as $kPergunta => $vPergunta) {
                    $respostas[$vPergunta['id']] = $vPergunta->toArray();
                }
            }

            $checklist = $v->toArray();
            $checklist['respostas'] = $respostas;
            $checklist['score'] = $v->score();
            $checklist['created_at_formatted'] = $v->created_at_formatted;

            $checklists[] = $checklist;
        }

        //Utilizado como template p/ Categorias e Perguntas
        $categorias = ChecklistModel::whereIn('id', $data['checklists'])->distinct()->with(['categorias' => function ($q) {
            $q->orderBy('ordem', 'ASC');
        }])->get();

        $categoriasPerguntas = $categorias->pluck('categorias')->collapse()->all();

        //Agrupa todas as perguntas
        $perguntas = array();
        foreach ($categoriasPerguntas as $kCategoria => $categoria) {
            foreach ($categoria->perguntas as $pergunta) {
                $perguntas[$pergunta->id] = $pergunta;
            }
        }

        //Converte categorias em um array dinamico
        $categorias = $categorias->pluck('categorias')->collapse()->pluck('nome', 'id')->all();

        return view('backend.core.checklist_unidade_produtiva.comparar_view', compact('categorias', 'perguntas', 'checklists'));
    }

    /**
     * Visualização de todos formulários aplicados que o usuário tem permissão para "ANALISAR"
     *
     * @return void
     */
    public function analiseIndex()
    {
        $datatableUrl = route('admin.core.checklist_unidade_produtiva.analiseDatatable');

        return view('backend.core.checklist_unidade_produtiva.analise_index', compact('datatableUrl'));
    }

    /**
     * API Datatable "analiseIndex()"
     *
     * @return void
     */
    public function analiseDatatable()
    {
        $data = ChecklistUnidadeProdutivaModel::with(['checklist', 'produtor', 'unidade_produtiva', 'usuario'])
            ->analistaAutorizado()
            ->select("checklist_unidade_produtivas.*")
            ->orderByRaw("FIELD(status,'aguardando_aprovacao', 'finalizado', 'cancelado', 'rascunho', 'aguardando_pda'), created_at DESC");
        // ->where(function ($query) {
        //     $query->where("status", ChecklistStatusEnum::AguardandoAprovacao)
        //         ->orWhere("status_flow", ChecklistStatusFlowEnum::AguardandoRevisao);
        // });

        return DataTables::of($data)
            ->editColumn('usuario.first_name', function ($row) {
                return $row->usuario->full_name;
            })
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if ($row->status == ChecklistStatusEnum::Rascunho)
                    $classBadge = 'text-danger';

                return '<span class="' . $classBadge . ' font-weight-normal">' . ChecklistStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->editColumn('status_flow', function ($row) {
                $classBadge = 'text-primary';
                if ($row->status_flow == ChecklistStatusFlowEnum::Reprovado) {
                    $classBadge = 'text-danger';
                }

                return ($row->status_flow) ? '<span class="' . $classBadge . ' font-weight-normal">' . @ChecklistStatusFlowEnum::toSelectArray()[$row->status_flow] . '</span>' : '<span class="' . $classBadge . ' font-weight-normal"> - </span>';
            })->addColumn('actions', function ($row) {
                $viewUrl = route('admin.core.checklist_unidade_produtiva.view', $row->id);
                $reanalyseUrl = route('admin.core.checklist_unidade_produtiva.reanalyse', $row->id);

                return view('backend.core.checklist_unidade_produtiva.form_actions_analise', compact('viewUrl', 'reanalyseUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })
            ->rawColumns(['status', 'status_flow'])
            ->make(true);
    }

    /**
     * Ação para reabrir um formulário que esta com o status_flow "aguardando revisão"
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function reanalyse(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $this->repository->reanalyse($checklistUnidadeProdutiva);

        return redirect()->route('admin.core.checklist_unidade_produtiva.analiseIndex')->withFlashSuccess('Formulário revertido para nova análise com sucesso!');
    }

    /**
     * Listagem de formulários aplicados que foram removidos
     *
     * @return void
     */
    public function indexExcluidos()
    {
        $datatableUrl = route('admin.core.checklist_unidade_produtiva.datatableExcluidos');

        $title = 'Formulários Excluídos';

        $showLinkExcluidos = false;

        return view('backend.core.checklist_unidade_produtiva.index', compact('datatableUrl', 'title', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "indexExcluidos()"
     *
     * @return void
     */
    public function datatableExcluidos()
    {
        $data = ChecklistUnidadeProdutivaModel::onlyTrashed()
            ->with(['checklist', 'produtor', 'unidade_produtiva', 'usuario'])
            ->select("checklist_unidade_produtivas.*");

        return DataTables::of($data)
            ->editColumn('status', function ($row) {
                $classBadge = 'text-primary';
                if ($row->status == ChecklistStatusEnum::Rascunho)
                    $classBadge = 'text-danger';

                return '<span class="' . $classBadge . ' font-weight-normal">' . ChecklistStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->editColumn('status_flow', function ($row) {
                if (!$row->status_flow) {
                    return '-';
                }

                return  @ChecklistStatusFlowEnum::toSelectArray()[$row->status_flow];
            })->addColumn('actions', function ($row) {
                $restoreUrl = route('admin.core.checklist_unidade_produtiva.restore', $row->id);
                $forceDeleteUrl = route('admin.core.checklist_unidade_produtiva.forceDelete', $row->id);

                $messageRestore = 'Você tem certeza que deseja restaurar?';
                if (count($row->plano_acao_trashed) > 0) {
                    $messageRestore = '<div>Você tem certeza que deseja restaurar?';
                    $messageRestore .= '<br><br>Existem planos de ações que foram excluídos, vinculados a esse formulário.<br><br>';
                    $messageRestore .= '<h3>';
                    foreach ($row->plano_acao_trashed as $k => $v) {
                        $messageRestore .= $v->nome . ' - ' . $v->created_at_formatted . '<br>';
                    }
                    $messageRestore .= '</h3></div>';
                }

                return view('backend.core.checklist_unidade_produtiva.form_actions', compact('restoreUrl', 'messageRestore', 'forceDeleteUrl', 'row'));
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['status', 'status_flow'])
            ->make(true);
    }

    /**
     * Ação p/ restaurar um formulário aplicado que foi removido (ver regras no ChecklistUnidadeProdutivaPolicy)
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function restore(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        // $checklist = $checklistUnidadeProdutiva->checklist;
        // $produtor = $checklistUnidadeProdutiva->produtor;
        // $unidadeProdutiva = $checklistUnidadeProdutiva->unidade_produtiva;

        // //Se for rascunho, verifica se não tem Formulários em aberto
        // if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
        //     $checklistUnidProdutiva = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id, 'status' => ChecklistStatusEnum::Rascunho])->first();
        //     if (@$checklistUnidProdutiva) {
        //         return redirect()->back()->withFlashDanger('Não é possível restaurar o formulário. Existe um formulário em modo rascunho.');
        //     }

        //     $checklistUnidProdutiva = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id, 'status' => ChecklistStatusEnum::AguardandoAprovacao])->first();
        //     if (@$checklistUnidProdutiva) {
        //         return redirect()->back()->withFlashDanger('Não é possível restaurar o formulário. Existe um formulário em modo de revisão.');
        //     }
        // }

        //Caso tenha um em modo rascunho/aguardando aprovação, nao permite ... isso é tratado no policy, por isso a regra não esta aqui.
        $this->repository->restore($checklistUnidadeProdutiva);

        return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('Formulário restaurado com sucesso!');
    }

    /**
     * Ação para remover "fisicamente" o registro
     *
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function forceDelete(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $this->repository->forceDelete($checklistUnidadeProdutiva);

        return redirect()->route('admin.core.checklist_unidade_produtiva.index')->withFlashSuccess('Formulário aplicado removido com sucesso!');
    }

    /**
     * Adiciona ao formulário com os campos referentes a cada tipo de pergunta
     *
     * @param ChecklistModel $checklist     
     * @param Form $form          
     */
    public static function getForm(ChecklistModel $checklist, Form $form)
    {        
        $categorias = $checklist->categorias()->with('perguntas', 'perguntas.respostas')->get();

        foreach ($categorias as $k => $categoria) {
            //Ignora categorias sem perguntas
            if (count($categoria->perguntas) == 0) {
                continue;
            }

            $form->add('card-start-' . $categoria->id, 'card-start', [
                'title' => $categoria->nome,
                'titleTag' => 'h2'
            ]);

            foreach ($categoria->perguntas as $k => $v) { //checklist_pergunta
                // $helperTextPda = '';
                // if ($itensUltimoPda) {
                //     $checklist_pergunta_id = $v->pivot->id;
                //     $itemPda = @$itensUltimoPda[$checklist_pergunta_id];

                //     if ($itemPda) {
                //         $helperTextPda = 'Ação planejada anteriormente: ' . $itemPda->descricao . '<span class="text-primary"><br>Status: ' . PlanoAcaoItemStatusEnum::toSelectArray()[$itemPda->status] . '</span>';
                //     } else {
                //         $helperTextPda = 'Não tem.';
                //     }
                // }

                $tipo_pergunta = $v->tipo_pergunta;

                $labelPergunta = $v->pergunta . ($v->pivot->fl_obrigatorio ? '*' : '');

                $textoApoio = $v['texto_apoio'];// . ($v['texto_apoio'] ? '<br>' . $helperTextPda : $helperTextPda);

                if ($tipo_pergunta == TipoPerguntaEnum::Semaforica || $tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $tipo_pergunta == TipoPerguntaEnum::Binaria || $tipo_pergunta == TipoPerguntaEnum::BinariaCinza) {
                    $respostas = collect($v['respostas']);
                    $respostasColorAr = $respostas->pluck('cor', 'id')->toArray();
                    $respostasAr = $respostas->pluck('descricao', 'id')->toArray();

                    $form->add(
                        $v['id'],
                        'select',
                        [
                            'label' => $labelPergunta,
                            'choices' => $respostasAr,
                            'empty_value' => 'Selecione',
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                            'attr' => [
                                'data-option-color' => join(",", $respostasColorAr),
                            ]
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::NumericaPontuacao || $tipo_pergunta == TipoPerguntaEnum::Numerica) {
                    $form->add(
                        $v['id'],
                        'number',
                        [
                            'label' => $labelPergunta,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                            'attr' => [
                                'step' => 'any'
                            ]
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::Texto) {
                    $form->add(
                        $v['id'],
                        'textarea',
                        [
                            'label' => $labelPergunta,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                            'attr' => [
                                'rows' => 2
                            ]
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::Data) {
                    $form->add(
                        $v['id'],
                        'date',
                        [
                            'label' => $labelPergunta,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::Hora) {
                    $form->add(
                        $v['id'],
                        'time',
                        [
                            'label' => $labelPergunta,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::MultiplaEscolha) {
                    $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                    $form->add(
                        $v['id'],
                        'select',
                        [
                            'label' => $labelPergunta,
                            'choices' => $respostas,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                            'attr' => [
                                'multiple' => 'multiple',
                            ],
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::EscolhaSimples || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                    $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                    $form->add(
                        $v['id'],
                        'select',
                        [
                            'label' => $labelPergunta,
                            'choices' => $respostas,
                            'empty_value' => 'Selecione',
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::Tabela) {
                    $form->add(
                        $v['id'],
                        'text',
                        [
                            'label' => $labelPergunta,
                            'help_block' => [
                                'text' => $textoApoio
                            ],
                            'attr' => [
                                'class' => 'form-control input-tabela',
                                'data-colunas' => $v['tabela_colunas'],
                                'data-linhas' => $v['tabela_linhas']
                            ],
                        ]
                    );
                } else if ($tipo_pergunta == TipoPerguntaEnum::Anexo) {
                    $upload_max_filesize = AppHelper::return_bytes(ini_get('upload_max_filesize'));
                    $form->add(
                        $v['id'],
                        'file',
                        [
                            'label' => $labelPergunta,
                            'rules' => 'max:' . $upload_max_filesize . '|mimes:doc,docx,pdf,ppt,pptx,xls,xlsx,png,jpg,jpeg,gif,txt,kml,shp', //required|
                            "maxlength" => $upload_max_filesize,
                            'help_block' => [
                                'text' => $textoApoio . '<br>Tamanho máximo do arquivo: ' . ini_get('upload_max_filesize'),
                            ],
                        ]
                    );
                }
            }
            $form->add('card-end-' . $categoria->id, 'card-end');
        }

    }
}
