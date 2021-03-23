<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TipoTemplatePerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\TemplatePerguntaForm;
use App\Http\Controllers\Backend\Traits\TemplateRespostasTrait;
use App\Http\Controllers\Controller;
use App\Models\Core\TemplatePerguntaModel;
use App\Repositories\Backend\Core\TemplatePerguntasRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class TemplatePerguntasController extends Controller
{
    use FormBuilderTrait;
    use TemplateRespostasTrait;

    protected $repository;

    public function __construct(TemplatePerguntasRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listagem de perguntas - Caderno de Campo (TemplateModel)
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.template_perguntas.index');
    }

    /**
     * Datatable API "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(TemplatePerguntaModel::query())
            ->editColumn('respostas', function ($row) {
                return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->editColumn('tags', function ($row) {
                return AppHelper::tableTags($row->tags);
            })->editColumn('tipo', function ($row) {
                return TipoTemplatePerguntaEnum::toSelectArray()[$row->tipo];
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.template_perguntas.edit', $row->id);
                $deleteUrl = route('admin.core.template_perguntas.destroy', $row->id);

                $respostasUrl = route('admin.core.template_perguntas.respostas.index', $row->id);
                if ($row->tipo === 'text') {
                    $respostasUrl = '';
                }

                return view('backend.core.template_perguntas.form_actions', compact('respostasUrl', 'editUrl', 'deleteUrl', 'row'));
            })->filterColumn('respostas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('respostas', function ($q) use ($keyword) {
                        $q->where('template_respostas.descricao', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['respostas', 'tags'])
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
        $form = $formBuilder->create(TemplatePerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.template_perguntas.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Criar Pergunta';

        return view('backend.core.template_perguntas.create_update', compact('form', 'title'));
    }


    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(TemplatePerguntaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['pergunta', 'tipo', 'tags', 'custom-redirect']);

        $templatePergunta = $this->repository->create($data);

        if (@$data['custom-redirect']) {
            return redirect(route('admin.core.template_perguntas.respostas.index', $templatePergunta->id))->withFlashSuccess('Pergunta criado com sucesso!');
        }

        return redirect()->route('admin.core.template_perguntas.index')->withFlashSuccess('Pergunta criada com sucesso!');
    }


    /**
     * Edição
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(TemplatePerguntaModel $templatePergunta, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(TemplatePerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.template_perguntas.update', compact('templatePergunta')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $templatePergunta
        ]);

        $title = 'Editar pergunta';

        return view('backend.core.template_perguntas.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  TemplatePerguntaModel $request
     * @param  FormBuilder $templatePergunta
     * @return void
     */
    public function update(Request $request, TemplatePerguntaModel $templatePergunta)
    {
        $form = $this->form(TemplatePerguntaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['pergunta', 'tipo', 'tags', 'custom-redirect']);

        $this->repository->update($templatePergunta, $data);

        if (@$data['custom-redirect']) {
            return redirect(route('admin.core.template_perguntas.respostas.index', $templatePergunta->id))->withFlashSuccess('Pergunta alterada com sucesso!');
        }

        return redirect()->route('admin.core.template_perguntas.index')->withFlashSuccess('Pergunta alterada com sucesso!');
    }

    /**
     * Remover pergunta, (regras no TemplatePerguntasPolicy)
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @return void
     */
    public function destroy(TemplatePerguntaModel $templatePergunta)
    {
        $this->repository->delete($templatePergunta);

        return redirect()->route('admin.core.template_perguntas.index')->withFlashSuccess('Pergunta removida com sucesso!');
    }
}
