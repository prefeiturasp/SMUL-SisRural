<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\GeoHelper;
use App\Http\Controllers\Backend\Forms\RegiaoForm;
use App\Http\Controllers\Controller;
use App\Models\Core\RegiaoModel;
use App\Repositories\Backend\Core\RegiaoRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class RegiaoController extends Controller
{
    use FormBuilderTrait;

    /**
     * @var RegiaoRepository
     */
    protected $repository;

    public function __construct(RegiaoRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Listagem das regiões (kmls)
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.regiao.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(RegiaoModel::query()->select('id', 'nome'))
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.regiao.edit', $row->id);
                $deleteUrl = route('admin.core.regiao.destroy', $row->id);
                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'row'));
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
        $form = $formBuilder->create(RegiaoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.regiao.store'),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Criar Região';
        return view('backend.core.regiao.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(RegiaoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if (!GeoHelper::validaPoligono($this->repository->getMultiPolygon($request->poligono))) {
            return redirect()->back()->withErrors('O polígono não é válido, verifique as regras constantes em: <a href="https://dev.mysql.com/doc/refman/5.7/en/geometry-well-formedness-validity.html" target="_blank">Mysql Docs</a>')->withInput();
        }

        $data = $request->only(['nome', 'poligono']);
        $this->repository->create($data);

        return redirect()->route('admin.core.regiao.index')->withFlashSuccess('Região criada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  RegiaoModel $regiao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(RegiaoModel $regiao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(RegiaoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.regiao.update', compact('regiao')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $regiao
        ]);

        $title = 'Editar região';

        return view('backend.core.regiao.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  RegiaoModel $regiao
     * @param  Request $request
     * @return void
     */
    public function update(RegiaoModel $regiao, Request $request)
    {
        $form = $this->form(RegiaoForm::class, ['model' => $regiao]);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome']);
        $this->repository->update($regiao, $data);

        return redirect()->route('admin.core.regiao.index')->withFlashSuccess('Região alterada com sucesso!');
    }

    /**
     * Ação p/ remover região, regras no RegiaoPolicy
     *
     * @param  RegiaoModel $regiao
     * @return void
     */
    public function destroy(RegiaoModel $regiao)
    {
        $this->repository->delete($regiao);

        return redirect()->route('admin.core.regiao.index')->withFlashSuccess('Região removida com sucesso!');
    }
}
