<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Http\Controllers\Backend\Forms\TermosDeUsoForm;
use App\Models\Core\TermosDeUsoModel;
use App\Repositories\Backend\Core\TermosDeUsoRepository;

class TermosDeUsoController extends Controller
{
    use FormBuilderTrait;

    protected $repository;

    public function __construct(TermosDeUsoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listagem de termos de uso
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.termos_de_uso.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(TermosDeUsoModel::query())
            ->editColumn('texto', function ($row) {
                return @$row->texto;
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.termos_de_uso.edit', $row->id);

                return view('backend.components.form-actions.index', compact('editUrl', 'row'));
            })
            ->rawColumns(['texto'])
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
        $form = $formBuilder->create(TermosDeUsoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.termos_de_uso.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Termo de Uso';

        return view('backend.core.termos_de_uso.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(TermosDeUsoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['texto']);

        $this->repository->create($data);

        return redirect()->route('admin.core.termos_de_uso.index')->withFlashSuccess('Termo de Uso criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  TermosDeUsoModel $termosDeUso
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(TermosDeUsoModel $termosDeUso, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(TermosDeUsoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.termos_de_uso.update', compact('termosDeUso')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $termosDeUso,
        ]);

        $title = 'Editar termo de uso';

        return view('backend.core.termos_de_uso.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  TermosDeUsoModel $termosDeUso
     * @return void
     */
    public function update(Request $request, TermosDeUsoModel $termosDeUso)
    {
        $form = $this->form(TermosDeUsoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['texto']);
        $this->repository->update($termosDeUso, $data);

        return redirect()->route('admin.core.termos_de_uso.index')->withFlashSuccess('Termo de Uso alterado com sucesso!');
    }

    /**
     * Remover
     *
     * @param  mixed $termosDeUso
     * @return void
     */
    public function destroy(TermosDeUsoModel $termosDeUso)
    {
        $this->repository->delete($termosDeUso);
        return redirect()->route('admin.core.termos_de_uso.index')->withFlashSuccess('Termo de Uso removido com sucesso!');
    }
}
