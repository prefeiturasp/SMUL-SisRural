<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TipoPerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\PerguntaForm;
use App\Http\Controllers\Backend\Traits\RespostasTrait;
use App\Http\Controllers\Controller;
use App\Models\Core\ChecklistPerguntaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Models\Core\PerguntaModel;
use App\Repositories\Backend\Core\PerguntasRepository;
use App\Repositories\Backend\Core\RespostasRepository;

class PerguntasController extends Controller
{
    use FormBuilderTrait;
    use RespostasTrait;

    protected $repository;

    public function __construct(PerguntasRepository $repository, RespostasRepository $repositoryRespostas)
    {
        $this->repository = $repository;
        $this->repositoryRespostas = $repositoryRespostas;
    }

    /**
     * Listagem de perguntas
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.perguntas.index');
    }

    /**
     * API Datatable "index()"
     *
     * @return void
     */
    public function datatable()
    {
        return DataTables::of(PerguntaModel::query())
            ->editColumn('pergunta', function ($row) {
                return $row->pergunta_sinalizada;
            })
            ->editColumn('respostas', function ($row) {
                return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->editColumn('tipo_pergunta', function ($row) {
                return TipoPerguntaEnum::toSelectArray()[$row->tipo_pergunta];
            })->editColumn('tags', function ($row) {
                return AppHelper::tableTags($row->tags);
            })->editColumn('fl_arquivada', function ($row) {
                return $row->fl_arquivada ? 'Arquivada' : 'Ativa';
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.perguntas.edit', $row->id);
                $deleteUrl = route('admin.core.perguntas.destroy', $row->id);

                return view('backend.core.perguntas.form_actions', compact('editUrl', 'deleteUrl', 'row'));
            })->filterColumn('respostas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('respostas', function ($q) use ($keyword) {
                        $q->where('respostas.descricao', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['pergunta', 'respostas', 'tags'])
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
        $form = $formBuilder->create(PerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.perguntas.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Pergunta';

        return view('backend.core.perguntas.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $form = $this->form(PerguntaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['custom-redirect', 'tipo_pergunta', 'tabela_colunas', 'tabela_linhas', 'pergunta', 'plano_acao_default', 'texto_apoio', 'tags', 'fl_arquivada']);

        $pergunta = $this->repository->create($data);

        /*Custom Redirect*/
        //Caso o usuário clique em "adicionar respostas", redireciona para a edição scrollando até o bloco
        $redirect = route('admin.core.perguntas.index', ['pergunta' => $pergunta]);
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.perguntas.edit', [$pergunta->id, '#' . $data['custom-redirect']]);
        }
        /*End Custom Redirect*/

        return redirect($redirect)->withFlashSuccess('Pergunta criada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  PerguntaModel $pergunta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(PerguntaModel $pergunta, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PerguntaForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.perguntas.update', compact('pergunta')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $pergunta,
        ]);

        $title = 'Editar pergunta';

        $respostasId = 'iframeRespostas';
        $respostasSrc = route('admin.core.perguntas.respostas.index', compact('pergunta'));

        return view('backend.core.perguntas.create_update', compact('form', 'title', 'pergunta', 'respostasId', 'respostasSrc'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  PerguntaModel $pergunta
     * @return void
     */
    public function update(Request $request, PerguntaModel $pergunta)
    {
        $form = $this->form(PerguntaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['custom-redirect', 'tipo_pergunta', 'tabela_colunas', 'tabela_linhas', 'pergunta', 'plano_acao_default', 'texto_apoio', 'tags', 'fl_arquivada']);
        $this->repository->update($pergunta, $data);

        $redirect = route('admin.core.perguntas.index', ['pergunta' => $pergunta]);
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.perguntas.edit', [$pergunta->id, '#' . $data['custom-redirect']]);
        }

        return redirect($redirect)->withFlashSuccess('Pergunta alterada com sucesso!');
    }

    /**
     * Remover pergunta (regras no PerguntaPolicy)
     *
     * @param  PerguntaModel $pergunta
     * @return void
     */
    public function destroy(PerguntaModel $pergunta)
    {
        $this->repository->delete($pergunta);

        return redirect()->route('admin.core.perguntas.index')->withFlashSuccess('Pergunta removida com sucesso!');
    }
}
