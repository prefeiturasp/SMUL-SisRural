<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\TemplateCadernoForm;
use App\Http\Controllers\Backend\Traits\TemplateCadernoPerguntaTemplatesTrait;
use App\Http\Controllers\Controller;
use App\Models\Core\TemplateModel;
use App\Repositories\Backend\Core\TemplateCadernoRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class TemplateCadernoController extends Controller
{
    use FormBuilderTrait;
    use TemplateCadernoPerguntaTemplatesTrait;

    protected $repository;

    public function __construct(TemplateCadernoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listagem de templates do caderno de campo
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.templates_caderno.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(TemplateModel::query())
            ->editColumn('perguntas', function ($row) {
                return AppHelper::tableArrayToList($row->perguntas->toArray(), 'pergunta');
            })
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.templates_caderno.edit', $row->id);
                return view('backend.components.form-actions.index', compact('editUrl', 'row'));
            })
            ->filterColumn('perguntas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('perguntas', function ($q) use ($keyword) {
                        $q->where('template_perguntas.pergunta', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['perguntas'])
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
        $form = $formBuilder->create(TemplateCadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.templates_caderno.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Criar Modelo do Caderno de Campo';

        return view('backend.core.templates_caderno.create_update', compact('form', 'title'));
    }


    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(TemplateCadernoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'dominio_id', 'custom-redirect']);
        $data['tipo'] = 'caderno';

        $template = $this->repository->create($data);

        if (@$data['custom-redirect']) {
            return redirect(route('admin.core.templates_caderno.edit', $template->id))->withFlashSuccess('Modelo criado com sucesso!');
        }

        return redirect()->route('admin.core.templates_caderno.index')->withFlashSuccess('Modelo criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  TemplateModel $template
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(TemplateModel $template, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(TemplateCadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.templates_caderno.update', compact('template')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $template
        ]);

        $title = 'Editar modelo do caderno de campo';

        $perguntasId = 'iframePerguntas';
        $perguntasSrc = route('admin.core.templates_caderno.perguntas.index', compact('template'));

        return view('backend.core.templates_caderno.create_update', compact('form', 'title', 'template', 'perguntasId', 'perguntasSrc'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  TemplateModel $template
     * @return void
     */
    public function update(Request $request, TemplateModel $template)
    {
        $form = $this->form(TemplateCadernoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['nome', 'dominio_id', 'custom-redirect']);
        $data['tipo'] = 'caderno';

        $this->repository->update($template, $data);

        if (@$data['custom-redirect']) {
            return redirect(route('admin.core.templates_caderno.perguntas.create', $template->id))->withFlashSuccess('Modelo alterado com sucesso!');
        }

        return redirect()->route('admin.core.templates_caderno.index')->withFlashSuccess('Modelo alterado com sucesso!');
    }

    /**
     * Remover template do caderno de campo
     *
     * @param  mixed $template
     * @return void
     */
    public function destroy(TemplateModel $template)
    {
        $this->repository->delete($template);

        return redirect()->route('admin.core.templates_caderno.index')->withFlashSuccess('Modelo removido com sucesso!');
    }
}
