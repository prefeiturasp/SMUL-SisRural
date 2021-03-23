<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\DominioForm;
use App\Http\Controllers\Controller;
use App\Models\Core\DominioModel;
use App\Models\Core\Traits\Scope\DominioPermissionScope;
use App\Repositories\Backend\Core\DominioRepository;
use App\Services\DominioService;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class DominioController extends Controller
{
    use FormBuilderTrait;

    /**
     * @var DominioRepository
     */
    protected $repository;

    /**
     * @var DominioService
     */
    protected $service;

    public function __construct(DominioRepository $repository, DominioService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Listagem dos domínios, ignora o DominioPermissionScope
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.dominio.index');
    }

    /**
     * API consumida pelo DataTable
     * Referência: ttps://medium.com/@BishoAtif/integrating-laravel-with-datatables-975b0bbf4009
     */
    public function datatable()
    {
        return DataTables::of(DominioModel::withoutGlobalScopes([DominioPermissionScope::class]))
            ->addColumn('unidadesOperacionais', function ($row) {
                return AppHelper::tableArrayToList($row->unidadesOperacionaisWithoutGlobalScopes->toArray(), 'nome');
            })->addColumn('abrangenciaEstadual', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaEstadual->toArray(), 'nome');
            })->addColumn('abrangenciaMunicipal', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaMunicipal->toArray(), 'nome');
            })->addColumn('abrangenciaRegiao', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaRegional->toArray(), 'nome');
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.dominio.edit', $row->id);
                return view('backend.components.form-actions.index', compact('editUrl', 'row'));
            })
            ->rawColumns(['unidadesOperacionais', 'abrangenciaEstadual', 'abrangenciaMunicipal', 'abrangenciaRegiao'])
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
        $form = $formBuilder->create(DominioForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.dominio.store'),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Criar Domínio';

        return view('backend.core.dominio.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(DominioForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'abrangenciaRegional', 'abrangenciaEstadual', 'abrangenciaMunicipal']);
        $this->repository->create($data);

        return redirect()->route('admin.core.dominio.index')->withFlashSuccess('Domínio criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  DominioModel $dominio
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(DominioModel $dominio, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(DominioForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.dominio.update', compact('dominio')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $dominio
        ]);

        $title = 'Editar domínio';

        return view('backend.core.dominio.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  DominioModel $dominio
     * @param  Request $request
     * @return void
     */
    public function update(DominioModel $dominio, Request $request)
    {
        $form = $this->form(DominioForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'abrangenciaRegional', 'abrangenciaEstadual', 'abrangenciaMunicipal']);

        if (!$this->service->consultaRestricaoAbrangencia($data, $dominio)) {
            return redirect()->back()->withErrors(__('validation.unit_op_coverage_fails'))->withInput();
        }

        $this->repository->update($dominio, $data);

        return redirect()->route('admin.core.dominio.index')->withFlashSuccess('Domínio alterado com sucesso!');
    }

    /**
     * Ação p/ remover um domínio
     *
     * @param  DominioModel $dominio
     * @return void
     */
    public function destroy(DominioModel $dominio)
    {
        $this->repository->delete($dominio);

        return redirect()->route('admin.core.dominio.index')->withFlashSuccess('Domínio removido com sucesso!');
    }
}
