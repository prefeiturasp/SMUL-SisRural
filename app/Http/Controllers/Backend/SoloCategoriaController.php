<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Forms\SoloCategoriaForm;
use App\Http\Controllers\Controller;
use App\Models\Core\SoloCategoriaModel;
use App\Repositories\Backend\Core\SoloCategoriaRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class SoloCategoriaController extends Controller
{
    use FormBuilderTrait;

    /**
     * @var SoloCategoriaRepository
     */
    protected $repository;

    public function __construct(SoloCategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listagem dos usos de solo
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.solo_categoria.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(SoloCategoriaModel::query())
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.solo_categoria.edit', $row->id);
                return view('backend.components.form-actions.index', compact('editUrl', 'row'));
            })
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
        $form = $formBuilder->create(SoloCategoriaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.solo_categoria.store'),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Uso do Solo - Categoria';
        return view('backend.core.solo_categoria.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(SoloCategoriaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'tipo', 'tipo_form', 'min', 'max']);
        $this->repository->create($data);

        return redirect()->route('admin.core.solo_categoria.index')->withFlashSuccess('Categoria criada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  SoloCategoriaModel $soloCategoria
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(SoloCategoriaModel $soloCategoria, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(SoloCategoriaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.solo_categoria.update', compact('soloCategoria')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $soloCategoria
        ]);

        $title = 'Editar categoria';

        return view('backend.core.solo_categoria.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  SoloCategoriaModel $soloCategoria
     * @param  Request $request
     * @return void
     */
    public function update(SoloCategoriaModel $soloCategoria, Request $request)
    {
        $form = $this->form(SoloCategoriaForm::class, ['model' => $soloCategoria]);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'tipo', 'tipo_form', 'min', 'max']);
        $this->repository->update($soloCategoria, $data);

        return redirect()->route('admin.core.solo_categoria.index')->withFlashSuccess('Categoria alterada com sucesso!');
    }
}
