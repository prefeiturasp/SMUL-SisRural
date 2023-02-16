<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\ProdutorForm;
use App\Http\Controllers\Backend\Forms\ProdutorUnidadeProdutivaSemUnidadeForm;
use App\Http\Controllers\Controller;
use App\Models\Core\ProdutorModel;
use App\Repositories\Backend\Core\ProdutorRepository;
use DataTables;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Http\Controllers\Backend\Traits\ProdutorUnidadeProdutivaTrait;
use App\Http\Requests\Backend\ProdutorRequest;
use App\Models\Core\ProdutorUnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaModel;
use Carbon\Carbon;

# Includes por conta da necessidade de instanciar o ChecklistUnidadeProdutivaRepository
use App\Models\Core\ChecklistModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use \App\Models\Core\PlanoAcaoItemModel;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaRepository;
use App\Repositories\Backend\Core\PlanoAcaoItemRepository;
use App\Repositories\Backend\Core\PlanoAcaoRepository;


class ProdutorController extends Controller
{
    use FormBuilderTrait;
    use ProdutorUnidadeProdutivaTrait;

    /**
     * @var ProdutorRepository
     */
    protected $repository;

    public function __construct(ProdutorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Visualização do produtor
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function view(ProdutorModel $produtor)
    {
        return view('backend.core.produtor.view', compact('produtor'));
    }

    /**
     * Dashboard do produtor
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function dashboard(ProdutorModel $produtor)
    {
        return view('backend.core.produtor.dashboard', compact('produtor'));
    }

    /**
     * Listagem dos produtores
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.produtor.index');
    }

    /**
     * API Datatable "index()"
     *
     * @param  mixed $dashboard
     * @return void
     */
    public function datatable(bool $dashboard = false)
    {
        return DataTables::of(ProdutorModel::with([
            'estado:id,nome', 'cidade:id,nome', 'unidadesProdutivas:unidade_produtivas.id,socios,tags'
        ])->select("produtores.*"))
            ->editColumn('tags', function ($row) {
                return AppHelper::tableTags($row->tags);
            })
            ->editColumn('nome', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->id) . "' target='_self'>" . $row->nome . "</a>";
            })->editColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) use ($dashboard) {
                if ($dashboard) {
                    $dashUrl = route('admin.core.produtor.dashboard', $row->id);
                    return view('backend.components.form-actions.index', compact('dashUrl'));
                }

                $editUrl = route('admin.core.produtor.edit', $row->id);
                $deleteUrl = route('admin.core.produtor.destroy', $row->id);
                $viewUrl = route('admin.core.produtor.view', $row->id);
                $dashUrl = route('admin.core.produtor.dashboard', $row->id);

                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'viewUrl', 'dashUrl', 'row'));
            })->filterColumn('socios', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('unidadesProdutivas', function ($q) use ($keyword) {
                        $q->where('unidade_produtivas.socios', 'like', '%' . $keyword . '%');
                    });
                }
            })->orderColumn('socios', function ($query, $order) {
                $query->whereHas('unidadesProdutivas', function ($q) use ($order) {
                    $q->orderBy('unidade_produtivas.socios', $order);
                });
            })
            ->rawColumns(['nome', 'tags'])
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
        $form = $formBuilder->create(ProdutorForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.produtor.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Cadastrar Produtor';

        $produtor = null;
        $containerId = null;
        $containerSrc = null;

        return view('backend.core.produtor.create_update', compact('form', 'title', 'containerId', 'containerSrc', 'produtor'));
    }

    /**
     * Cadastro - POST
     *
     * @param  ProdutorRequest $request - normaliza o cpf/cnpj
     * @return void
     */
    public function store(ProdutorRequest $request)
    {
        if (@$request->cpf) {
            //Caso já tenha um produtor cadastrado com o cpf informado, não permite o cadastro
            $request->validate([
                'cpf' => 'unique:produtores',
            ], [
                'O CPF informado já encontra-se utilizado pelo produtor/a "' . @ProdutorModel::withoutGlobalScopes()->where("cpf", $request->cpf)->first()->nome . '".'
            ]);
        }

        $form = $this->form(ProdutorForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();

        $produtorModel = $this->repository->create($data);

        /*Custom Redirect*/
        // Caso o usuário clique para adicionar unidades produtivas, vai para a tela de edição scrollando para a sessão
        $redirect = route('admin.core.produtor.index');
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.produtor.edit', [$produtorModel->id, '#' . $data['custom-redirect']]);
        }
        /*End Custom Redirect*/

        return redirect($redirect)->withFlashSuccess('Produtor cadastrado com sucesso!');
    }

    /**
     *
     * Edição
     *
     * $unidadeProdutiva vem do flow Novo Produtor/Unidade Produtiva (outra rota)
     *
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva, FormBuilder $formBuilder)
    {
        //A edição pode vir de dois caminhos, da listagem do produtor (caminho padrão) ou de um "cadastro rápido" (NovoProdutorUnidadeProdutivaController), caso venha do cadastro rápido, o fluxo de atualização é outro.
        $urlForm = @$unidadeProdutiva->id ?
            route('admin.core.novo_produtor_unidade_produtiva.produtor_update', compact('produtor', 'unidadeProdutiva')) :
            route('admin.core.produtor.update', compact('produtor'));

        // Definição do checklist de dados adicionais da Produtora
        if(config('app.checklist_dados_adicionais_produtora')){
            // Aqui é necessário definir uma forma melhor de manter a UP. O ideal seria UP nula.
            $checklist_id = config('app.checklist_dados_adicionais_produtora');
            $checklist = ChecklistModel::find($checklist_id);
            $checklistRespostas = ChecklistUnidadeProdutivaController::getRespostas($checklist, $produtor, $unidadeProdutiva);
            $model = $produtor->toArray() + $unidadeProdutiva->toArray() + $checklistRespostas; // envia dados da produtora e dados do checklist serializado
        } else {
            $unidProdutivaRespostas = NULL;
            $checklist = NULL;
            $model = $produtor->toArray();
        }

        $form = $formBuilder->create(ProdutorForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => $urlForm,
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $model,
            'data' => ['checklist' => $checklist],
        ]);

        $title = 'Editar Produtor/a';

        $containerId = 'iframeUnidadeProdutiva';
        $containerSrc = route('admin.core.produtor.search-unidade-produtiva', compact('produtor'));

        return view('backend.core.produtor.create_update', compact('form', 'title', 'containerId', 'containerSrc', 'produtor', 'unidadeProdutiva'));
    }

    /**
     * Edição - POST
     *
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  ProdutorRequest $request
     * @return void
     */
    public function update(ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva, ProdutorRequest $request)
    {
        //Se o cpf informado é diferente do atual, verifica se já foi utilizado o CPF
        if (@$produtor->cpf !== @$request->cpf && @$request->cpf) {
            $request->validate([
                'cpf' => 'unique:produtores',
            ], [
                'O CPF informado já encontra-se utilizado pelo produtor/a "' . @ProdutorModel::withoutGlobalScopes()->where("cpf", $request->cpf)->first()->nome . '".'
            ]);
        }

        $form = $this->form(ProdutorForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();
        $this->repository->update($produtor, $data);

        if(config('app.checklist_dados_adicionais_produtora')){
            // Salvando dados das respostas do checklist. Para isso foi preciso longo caminho para
            // instanciar o ChecklistUnidadeProdutivaRepository                
            $planoAcaoItem = new PlanoAcaoItemModel();
            $planoAcaoItemRepository = new PlanoAcaoItemRepository($planoAcaoItem);
            $planoAcao = new PlanoAcaoModel();
            $planoAcaoRepository = new PlanoAcaoRepository($planoAcao, $planoAcaoItemRepository);
            $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::where('produtor_id', $produtor->id)->where('checklist_id', config('app.checklist_dados_adicionais_produtora'))->first();

            $data['status'] = "rascunho";

            if($checklistUnidadeProdutiva){
                // checklist existe e só precisa ser atualizado
                $checklistUnidadeProdutivaRepository = new ChecklistUnidadeProdutivaRepository($checklistUnidadeProdutiva, $planoAcaoRepository);                   
                $checklistUnidadeProdutivaRepository->update($checklistUnidadeProdutiva, $data);
            } else {
                // checklist a ser criado
                $checklistUnidadeProdutiva = new ChecklistUnidadeProdutivaModel();
                $checklistUnidadeProdutivaRepository = new ChecklistUnidadeProdutivaRepository($checklistUnidadeProdutiva, $planoAcaoRepository);
                $checklistUnidadeProdutivaRepository->create($data);
            }
        }

        //Rota especial quando a edição vem do "cadastro rápido de um produtor/unidade produtiva" (NovoProdutorUnidadeProdutivaController)
        if (@$unidadeProdutiva->id) {
            return redirect()->route('admin.core.novo_produtor_unidade_produtiva.unidade_produtiva_edit', ['unidadeProdutiva' => $unidadeProdutiva, 'produtor' => $produtor])->withFlashSuccess('Produtor alterado com sucesso!');
        } else {
            return redirect()->route('admin.core.produtor.index')->withFlashSuccess('Produtor alterado com sucesso!');
        }
    }

    /**
     * Ação p/ remover um produtor
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function destroy(ProdutorModel $produtor)
    {
        $this->repository->delete($produtor);

        return redirect()->route('admin.core.produtor.index')->withFlashSuccess('Produtor removido com sucesso!');
    }



    /**
     * Listagem dos produtores que não possuem nenhum relacionamento com outra unidade produtiva
     *
     * @return void
     */
    public function indexSemUnidade()
    {
        return view('backend.core.produtor.index_sem_unidade');
    }

    /**
     * API Datatable "index()"
     *
     * @param  mixed $dashboard
     * @return void
     */
    public function datatableSemUnidade()
    {
        return DataTables::of(ProdutorModel::withoutGlobalScopes()->doesntHave('unidadesProdutivasNS')->with([
            'estado:id,nome', 'cidade:id,nome', 'unidadesProdutivas:socios'
        ])->select("produtores.*"))
            ->editColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.produtor.edit_sem_unidade', $row->id);

                return view('backend.components.form-actions.index', compact('editUrl', 'row'));
            })->filterColumn('socios', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('unidadesProdutivas', function ($q) use ($keyword) {
                        $q->where('unidade_produtivas.socios', 'like', '%' . $keyword . '%');
                    });
                }
            })->orderColumn('socios', function ($query, $order) {
                $query->whereHas('unidadesProdutivas', function ($q) use ($order) {
                    $q->orderBy('unidade_produtivas.socios', $order);
                });
            })
            ->rawColumns(['cpf'])
            ->make(true);
    }

    /**
     *
     * Edição - Sem unidade produtiva
     *
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function editSemUnidade(ProdutorModel $produtorSemUnidade,  FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ProdutorUnidadeProdutivaSemUnidadeForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.produtor.update_sem_unidade', compact('produtorSemUnidade')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => ['produtor_id' => $produtorSemUnidade->id]
        ]);

        $title = 'Relacionar Produtor x Unidade Produtiva';

        return view('backend.core.produtor.create_update_sem_unidade', compact('form', 'title', 'produtorSemUnidade'));
    }

    /**
     * Edição - POST
     *
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @param  ProdutorRequest $request
     * @return void
     */
    public function updateSemUnidade(ProdutorModel $produtorSemUnidade, ProdutorRequest $request)
    {
        $data = $request->only(['unidade_produtiva_id', 'tipo_posse_id']);

        $unidade = $data['unidade_produtiva_id'];
        $tipo_posse_id = $data['tipo_posse_id'];

        $return = $produtorSemUnidade->unidadesProdutivasWithTrashed()->syncWithoutDetaching([$unidade => ['updated_at' => Carbon::now(), 'tipo_posse_id' => $tipo_posse_id]]);

        if (count($return['updated']) > 0) {
            ProdutorUnidadeProdutivaModel::withTrashed()->where('unidade_produtiva_id', $return['updated'][0])->where('produtor_id', $produtorSemUnidade->id)->restore();
        }

        return redirect()->route('admin.core.produtor.dashboard', $produtorSemUnidade->id)->withFlashSuccess('Produtor relacionado com sucesso!');
    }
}
