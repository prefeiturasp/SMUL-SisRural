<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\UnidadeOperacionalForm;
use App\Http\Controllers\Controller;
use App\Models\Core\UnidadeOperacionalModel;
use App\Repositories\Backend\Core\UnidadeOperacionalRepository;
use App\Services\UnidadeOperacionalService;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class UnidadeOperacionalController extends Controller
{
    use FormBuilderTrait;

    protected $repository;

    /**
     * @var UnidadeOperacionalService
     */
    protected $service;

    public function __construct(UnidadeOperacionalRepository $repository, UnidadeOperacionalService $service)
    {
        $this->repository = $repository;
        $this->service    = $service;
    }

    /**
     * Listagem das unidades operacionais baseado no UnidadeOperacionalPermissionScope
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.unidade_operacional.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(UnidadeOperacionalModel::with(['dominio'])->select("unidade_operacionais.*"))
            ->addColumn('abrangenciaEstadual', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaEstadual->toArray(), 'nome');
            })
            ->addColumn('abrangenciaMunicipal', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaMunicipal->toArray(), 'nome');
            })
            ->addColumn('abrangenciaRegiao', function ($row) {
                return AppHelper::tableArrayToList($row->regioes->toArray(), 'nome');
            })
            ->addColumn('unidadesProdutivasManuais', function ($row) {
                return AppHelper::tableArrayToList($row->unidadesProdutivasManuais->toArray(), 'nome');
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.unidade_operacional.edit', $row->id);
                $deleteUrl = route('admin.core.unidade_operacional.destroy', $row->id);
                // $unidadesProdutivasUrl = route('admin.core.unidade_operacional.unidade_produtiva.index', $row->id);

                return view('backend.core.unidade_operacional.form_actions', compact(/*'unidadesProdutivasUrl',*/ 'editUrl', 'deleteUrl'));
            })
            ->filterColumn('abrangenciaEstadual', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('abrangenciaEstadual', function ($q) use ($keyword) {
                        $q->where('estados.nome', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->filterColumn('abrangenciaMunicipal', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('abrangenciaMunicipal', function ($q) use ($keyword) {
                        $q->where('cidades.nome', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->filterColumn('unidadesProdutivasManuais', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('unidadesProdutivasManuais', function ($q) use ($keyword) {
                        $q->where('unidade_produtivas.nome', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['abrangenciaEstadual', 'abrangenciaMunicipal', 'abrangenciaRegiao', 'unidadesProdutivasManuais'])
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
        $form = $formBuilder->create(UnidadeOperacionalForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.unidade_operacional.store'),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Criar Unidade Operacional';
        return view('backend.core.unidade_operacional.create_update', compact('form', 'title'));
    }


    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(UnidadeOperacionalForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'telefone', 'endereco', 'dominio_id', 'regioes', 'abrangenciaEstadual', 'abrangenciaMunicipal', 'unidadesProdutivasManuais']);

        if (!$this->service->consultaRestricaoAbrangencia($data)) {
            return redirect()->back()->withErrors(__('validation.domain_coverage_fails'))->withInput();
        }

        $model = $this->repository->create($data);
        $this->service->syncAbrangencias($model);

        return redirect()->route('admin.core.unidade_operacional.index')->withFlashSuccess('Unidade Operacional criada com sucesso!');
    }


    /**
     * Edição
     *
     * @param  UnidadeOperacionalModel $unidadeOperacional
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(UnidadeOperacionalModel $unidadeOperacional, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(UnidadeOperacionalForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.unidade_operacional.update', compact('unidadeOperacional')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $unidadeOperacional
        ]);

        $title = 'Editar unidade operacional';

        return view('backend.core.unidade_operacional.create_update', compact('form', 'title'));
    }


    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  UnidadeOperacionalModel $unidadeOperacional
     * @return void
     */
    public function update(Request $request, UnidadeOperacionalModel $unidadeOperacional)
    {
        $form = $this->form(UnidadeOperacionalForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'telefone', 'endereco', 'dominio_id', 'regioes', 'abrangenciaEstadual', 'abrangenciaMunicipal', 'unidadesProdutivasManuais']);

        if (!$this->service->consultaRestricaoAbrangencia($data)) {
            return redirect()->back()->withErrors(__('validation.domain_coverage_fails'))->withInput();
        }

        $this->repository->update($unidadeOperacional, $data);
        $this->service->syncAbrangencias($unidadeOperacional);

        return redirect()->route('admin.core.unidade_operacional.index')->withFlashSuccess('Unidade Operacional alterada com sucesso!');
    }


    /**
     * Remover
     *
     * @param  UnidadeOperacionalModel $unidadeOperacional
     * @return void
     */
    public function destroy(UnidadeOperacionalModel $unidadeOperacional)
    {
        $this->repository->delete($unidadeOperacional);

        return redirect()->route('admin.core.unidade_operacional.index')->withFlashSuccess('Unidade Operacional removida com sucesso!');
    }
}
