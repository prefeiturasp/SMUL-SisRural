<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ChecklistStatusEnum;
use App\Enums\TemplateChecklistStatusEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\ChecklistForm;
use App\Http\Controllers\Backend\Forms\ChecklistUnidadeProdutivaForm;
use App\Http\Controllers\Backend\Traits\ChecklistCategoriaPerguntaTrait;
use App\Http\Controllers\Backend\Traits\ChecklistCategoriaTrait;
use App\Http\Controllers\Controller;
use App\Models\Core\ChecklistModel;
use App\Repositories\Backend\Core\ChecklistCategoriaRepository;
use App\Repositories\Backend\Core\ChecklistPerguntaRepository;
use App\Repositories\Backend\Core\ChecklistRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class ChecklistController extends Controller
{
    use FormBuilderTrait;
    use ChecklistCategoriaTrait;
    use ChecklistCategoriaPerguntaTrait; //Referenciado a Categoria

    protected $repository;
    protected $repositoryChecklistPergunta;
    protected $repositoryChecklistCategoria;

    public function __construct(ChecklistRepository $repository, ChecklistPerguntaRepository $repositoryChecklistPergunta, ChecklistCategoriaRepository $repositoryChecklistCategoria)
    {
        $this->repository = $repository;
        $this->repositoryChecklistPergunta = $repositoryChecklistPergunta;
        $this->repositoryChecklistCategoria = $repositoryChecklistCategoria;
    }

    /**
     * Listagem do Template de Formulário de acordo com o ChecklistPermissionScope
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.checklist.index');
    }

    /**
     * API Datatable "index()"
     *
     * Listagem do Template de Formulário - Retorno dos dados p/ consumo
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(ChecklistModel::with('categorias.perguntas'))
            ->addColumn('dominio', function ($row) {
                return $row->dominioWithoutGlobalScopes->nome;
            })->addColumn('perguntas', function ($row) {
                return AppHelper::tableArrayToListExpand($row->categorias->pluck('perguntas')->collapse()->all(), 'pergunta');
            })->addColumn('dominiosPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->dominios->toArray(), 'nome');
            })->addColumn('unidadesOperacionaisPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->unidadesOperacionais->toArray(), 'nome');
            })->addColumn('usuariosPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->usuarios->toArray(), 'full_name');
            })->addColumn('status', function ($row) {
                $classBadge = 'text-danger';
                if ($row->status == TemplateChecklistStatusEnum::Publicado)
                    $classBadge = 'text-primary';

                return '<span class="' . $classBadge . ' font-weight-normal">' . TemplateChecklistStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('fl_fluxo_aprovacao', function ($row) {
                return boolean_sim_nao($row->fl_fluxo_aprovacao);
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.checklist.edit', $row->id);
                $deleteUrl = route('admin.core.checklist.destroy', $row->id);
                $duplicateUrl = route('admin.core.checklist.duplicate', $row->id);

                return view('backend.components.form-actions.index', compact('editUrl', 'duplicateUrl', 'deleteUrl', 'row'));
            })->filterColumn('perguntas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('categorias.perguntas', function ($q) use ($keyword) {
                        $q->where('perguntas.pergunta', 'like', '%' . $keyword . '%');
                    });
                }
            })->orderColumn('fl_fluxo_aprovacao', function ($query, $order) {
                $query->orderBy('fl_fluxo_aprovacao', $order);
            })
            ->rawColumns(['status', 'perguntas', 'dominiosPermissao', 'unidadesOperacionaisPermissao', 'usuariosPermissao'])
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.checklist.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Criar Formulário';

        return view('backend.core.checklist.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(ChecklistForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'formula', 'formula_prefix', 'formula_sufix', 'status', 'plano_acao', 'tipo_pontuacao', 'dominio_id', 'dominios', 'unidadesOperacionais', 'usuarios', 'fl_fluxo_aprovacao', 'usuariosAprovacao', 'instrucoes', 'instrucoes_pda', 'custom-redirect', 'fl_nao_normalizar_percentual', 'fl_gallery']);

        //No cadastro inicial, o status inicial é sempre "rascunho"
        $data['status'] = ChecklistStatusEnum::Rascunho;

        $checklist =  $this->repository->create($data);

        //Se o usuário clicar em "Definição de categorias e perguntas", ele redireciona para a edição, scrollando até o "iframe"
        $redirect = route('admin.core.checklist.index');
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.checklist.edit', [$checklist->id, '#' . $data['custom-redirect']]);
        }

        return redirect($redirect)->withFlashSuccess('Formulário aplicado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  ChecklistModel $checklist
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(ChecklistModel $checklist, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ChecklistForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.checklist.update', compact('checklist')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $checklist
        ]);

        $title = 'Editar formulário';

        //Iframe p/ cadastro de categorias (ver ChecklistCategoriaTrait)
        $checklistCategoriasId = 'iframeChecklistCategorias';
        $checklistCategoriasSrc = route('admin.core.checklist.categorias.index', compact('checklist'));

        return view('backend.core.checklist.create_update', compact('form', 'title', 'checklist', 'checklistCategoriasId', 'checklistCategoriasSrc'));
    }

    /**
     * Edição do formulário - POST
     *
     * @param  Request $request
     * @param  ChecklistModel $checklist
     * @return void
     */
    public function update(Request $request, ChecklistModel $checklist)
    {
        //Normaliza a fórmula (talvez isso deveria estar dentro do Repo)
        $request['formula'] = $this->repository->normalizeFormula($request['formula']);

        $form = $this->form(ChecklistForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'formula', 'formula_prefix', 'formula_sufix', 'status', 'plano_acao', 'tipo_pontuacao', 'dominio_id', 'dominios', 'unidadesOperacionais', 'usuarios', 'fl_fluxo_aprovacao', 'usuariosAprovacao', 'instrucoes', 'instrucoes_pda', 'custom-redirect', 'fl_nao_normalizar_percentual', 'fl_gallery']);

        $this->repository->update($checklist, $data);

        //Se o usuário clicar em "Definição de categorias e perguntas", ele redireciona para a edição, scrollando até o "iframe"
        $redirect = route('admin.core.checklist.index');
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.checklist.edit', [$checklist->id, '#' . $data['custom-redirect']]);
        }

        return redirect($redirect)->withFlashSuccess('Formulário alterado com sucesso!');
    }

    /**
     * Remover template do formulário (ver regras no ChecklistPolicy)
     *
     * @param  ChecklistModel $checklist
     * @return void
     */
    public function destroy(ChecklistModel $checklist)
    {
        $this->repository->delete($checklist);

        return redirect()->route('admin.core.checklist.index')->withFlashSuccess('Formulário removido com sucesso!');
    }

    /**
     * Duplicar um template de formulário
     *
     * Bypass Global Scope (ChecklistModel)
     */
    public function duplicate($checklist)
    {
        if (!auth()->user()->can('duplicate checklist base')) {
            return redirect(route('admin.core.checklist.index'))->withFlashDanger('Você não tem permissão para fazer essa ação.');
        }

        $checklist = ChecklistModel::withoutGlobalScopes()->where('id', $checklist)->first();
        if (!$checklist) {
            return redirect(route('admin.core.checklist.index'))->withFlashDanger('Não foi encontrado o formulário solicitado!');
        }

        $model = $this->repository->duplicate($checklist);

        $editUrl = route('admin.core.checklist.edit', $model->id);

        return redirect($editUrl)->withFlashSuccess('Formulário duplicado com sucesso!');
    }

    /**
     * Visualização de um template de formulário
     *
     * @param  FormBuilder $formBuilder
     * @param  ChecklistModel $checklist
     * @return void
     *
     * @deprecated Ação não é mais permitida, pediram para esconder essa sessão
     */
    public function view(FormBuilder $formBuilder, ChecklistModel $checklist)
    {
        return;

        $form = $formBuilder->create(ChecklistUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'GET',
            'url' => route('admin.core.checklist.view', ['checklist' => $checklist]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => null,
            'data' => ['checklist' => $checklist]
        ]);

        $title = 'Exemplo de Aplicação do Formulário';

        return view('backend.core.checklist.view', compact('form', 'title'));
    }


    /**
     * Listagem de todos os templates de formulários p/ duplicar.
     *
     * @return void
     */
    public function biblioteca()
    {
        return view('backend.core.checklist.biblioteca');
    }

    /**
     * API Datatable -> "biblioteca()"
     *
     * @return void
     */
    public function datatableBiblioteca()
    {
        return DataTables::of(ChecklistModel::withoutGlobalScopes()->with('categorias.perguntas'))
            ->addColumn('dominio', function ($row) {
                return $row->dominioWithoutGlobalScopes->nome;
            })->addColumn('perguntas', function ($row) {
                return AppHelper::tableArrayToListExpand($row->categorias->pluck('perguntas')->collapse()->all(), 'pergunta');
            })->addColumn('dominiosPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->dominios->toArray(), 'nome');
            })->addColumn('unidadesOperacionaisPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->unidadesOperacionais->toArray(), 'nome');
            })->addColumn('usuariosPermissao', function ($row) {
                return AppHelper::tableArrayToList($row->usuarios->toArray(), 'full_name');
            })->addColumn('status', function ($row) {
                return TemplateChecklistStatusEnum::toSelectArray()[$row->status];
            })->addColumn('fl_fluxo_aprovacao', function ($row) {
                return boolean_sim_nao($row->fl_fluxo_aprovacao);
            })->addColumn('actions', function ($row) {
                $duplicateUrl = route('admin.core.checklist.duplicate', $row->id);

                return view('backend.components.form-actions.index', compact('duplicateUrl', 'row'));
            })->filterColumn('perguntas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('categorias.perguntas', function ($q) use ($keyword) {
                        $q->where('perguntas.pergunta', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['perguntas', 'dominiosPermissao', 'unidadesOperacionaisPermissao', 'usuariosPermissao'])
            ->make(true);
    }
}
