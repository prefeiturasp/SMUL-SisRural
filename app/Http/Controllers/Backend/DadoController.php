<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\DadoForm;
use App\Http\Controllers\Controller;
use App\Models\Core\DadoModel;
use App\Repositories\Backend\Core\DadoRepository;
use App\Services\DadoService;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class DadoController extends Controller
{
    use FormBuilderTrait;

    protected $repository;

    /**
     * @var DadoService
     */
    protected $service;

    public function __construct(DadoRepository $repository, DadoService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Listagem dos acessos aos dados
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.dado.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(DadoModel::query())
            ->addColumn('abrangenciaEstadual', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaEstadual->toArray(), 'nome');
            })
            ->addColumn('abrangenciaMunicipal', function ($row) {
                return AppHelper::tableArrayToList($row->abrangenciaMunicipal->toArray(), 'nome');
            })
            ->addColumn('abrangenciaRegiao', function ($row) {
                return AppHelper::tableArrayToList($row->regioes->toArray(), 'nome');
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.dado.edit', $row->id);
                $deleteUrl = route('admin.core.dado.destroy', $row->id);
                $viewUrl = route('admin.core.dado.view', $row->id);

                return view('backend.core.dado.form_actions', compact('editUrl', 'deleteUrl', 'viewUrl'));
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
            ->rawColumns(['abrangenciaEstadual', 'abrangenciaMunicipal', 'abrangenciaRegiao'])
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
        $form = $formBuilder->create(DadoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.dado.store'),
            'class' => 'needs-validation',
            'novalidate' => true
        ]);

        $title = 'Criar - Sampa+Rural';

        return view('backend.core.dado.create_update', compact('form', 'title'));
    }


    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(DadoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'api_token', 'regioes', 'abrangenciaEstadual', 'abrangenciaMunicipal']);

        $model = $this->repository->create($data);
        $this->service->syncAbrangencias($model);

        return redirect()->route('admin.core.dado.index')->withFlashSuccess('Acesso aos dados criado com sucesso!');
    }


    /**
     * Edição
     *
     * @param  DadoModel $dado
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(DadoModel $dado, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(DadoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.dado.update', compact('dado')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $dado
        ]);

        $title = 'Editar - Sampa+Rural';

        return view('backend.core.dado.create_update', compact('form', 'title'));
    }


    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  DadoModel $dado
     * @return void
     */
    public function update(Request $request, DadoModel $dado)
    {
        $form = $this->form(DadoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'api_token', 'regioes', 'abrangenciaEstadual', 'abrangenciaMunicipal']);

        //Não usa o atributo direto "dado->abrangenciaEstadual" porque vai cachear no model e o "syncAbrangencia" não pega. Futuramente revisar o "syncAbrangencia" p/ não dar esse tipo de problema.
        $isAbrangenciaChanged = $this->relatedDiff(@$dado->abrangenciaEstadual()->get(), @$data['abrangenciaEstadual'])
            || $this->relatedDiff(@$dado->abrangenciaMunicipal()->get(), @$data['abrangenciaMunicipal'])
            || $this->relatedDiff(@$dado->regioes()->get(), @$data['regioes']);

        $this->repository->update($dado, $data);

        /**
         * Só faz a chamada do Sync se os dados realmente foram alterados.
         */
        if ($isAbrangenciaChanged) {
            $this->service->syncAbrangencias($dado);
        }

        return redirect()->route('admin.core.dado.index')->withFlashSuccess('Acesso aos dados alterado com sucesso!');
    }

    /**
     * Verifica os dois arrays de entrada e valida se é igual ou não
     */
    private function relatedDiff($currentValues, $nextValues)
    {
        //Normaliza o array que vem do "form"
        $nextValues = @collect($nextValues);

        //Se o count é diferente, já retorna
        if ($nextValues->count() != $currentValues->count()) {
            return true;
        }

        //Map p/ normalizar os valores
        if ($nextValues->map(function ($v) {
            return $v . '';
        })->diff($currentValues->pluck('id')->map(function ($v) {
            return $v . '';
        }))->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Remover
     *
     * @param  DadoModel $dado
     * @return void
     */
    public function destroy(DadoModel $dado)
    {
        $this->repository->delete($dado);

        return redirect()->route('admin.core.dado.index')->withFlashSuccess('Acesso aos dados removido com sucesso!');
    }

    /**
     * Dados de acesso
     *
     * @param  DadoModel $dado
     * @return void
     */
    public function view(DadoModel $dado)
    {
        return view('backend.core.dado.view', compact('dado'));
    }
}
