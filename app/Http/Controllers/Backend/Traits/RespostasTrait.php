<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\CorEnum;
use App\Http\Controllers\Backend\Forms\RespostaForm;
use App\Models\Core\PerguntaModel;
use App\Models\Core\RespostaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait RespostasTrait
{
    /**
     * Listagem das respostas vinculadas as perguntas (template formulário)
     *
     * @param  PerguntaModel $pergunta
     * @param  Request $request
     * @return void
     */
    public function respostasIndex(PerguntaModel $pergunta, Request $request)
    {
        $title = 'Respostas';
        $urlAdd = route('admin.core.perguntas.respostas.create', ["pergunta" => $pergunta]);
        $urlDatatable = route('admin.core.perguntas.respostas.datatable', ["pergunta" => $pergunta]);

        return view('backend.core.perguntas.respostas.index', compact('urlAdd', 'urlDatatable', 'title', 'pergunta'));
    }

    /**
     * API Datatable "respostasDatatable()"
     *
     * @param  PerguntaModel $pergunta
     * @return void
     */
    public function respostasDatatable(PerguntaModel $pergunta)
    {
        return DataTables::of($pergunta->respostas()->getQuery())
            ->editColumn('cor', function ($row) {
                if (!$row->cor) {
                    return 'Nenhuma';
                }

                return @CorEnum::toSelectArray()[$row->cor];
            })->addColumn('actions', function ($row) {
                $params = ['pergunta' => $row->pergunta_id, 'resposta' => $row->id];

                $editUrl = route('admin.core.perguntas.respostas.edit', $params);
                $deleteUrl = route('admin.core.perguntas.respostas.destroy', $params);
                $moveOrderUp = route('admin.core.perguntas.respostas.moveOrderUp', $params);
                $moveOrderDown = route('admin.core.perguntas.respostas.moveOrderDown', $params);
                $moveOrderTop = route('admin.core.perguntas.respostas.moveOrderTop', $params);
                $moveOrderBack = route('admin.core.perguntas.respostas.moveOrderBack', $params);

                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'moveOrderUp', 'moveOrderDown', 'moveOrderTop', 'moveOrderBack', 'row'));
            })
            ->make(true);
    }

    /**
     * Cadastro de resposta em uma pergunta
     *
     * @param  PerguntaModel $pergunta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function respostasCreate(PerguntaModel $pergunta, FormBuilder $formBuilder)
    {
        //Retorna o último valor do campo "order"
        $last = RespostaModel::orderBy('ordem', 'desc')->where('pergunta_id', $pergunta->id)->first();

        $form = $formBuilder->create(RespostaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.perguntas.respostas.store', ['pergunta' => $pergunta]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => $pergunta->toArray(),
            'model' => ['ordem' => @$last->ordem + 1]
        ]);

        $title = 'Cadastrar resposta';

        $back = route('admin.core.perguntas.respostas.index', compact('pergunta'));

        return view('backend.core.perguntas.respostas.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  PerguntaModel $pergunta
     * @return void
     */
    public function respostasStore(Request $request, PerguntaModel $pergunta)
    {
        $data = $request->only(['descricao', 'cor']);
        $data['pergunta_id'] = $pergunta->id;

        $this->repositoryRespostas->create($data);

        return redirect()->route('admin.core.perguntas.respostas.index', compact('pergunta'))->withFlashSuccess('Resposta criada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function respostasEdit(PerguntaModel $pergunta, RespostaModel $resposta, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(RespostaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.perguntas.respostas.update', ['pergunta' => $pergunta, 'resposta' => $resposta]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => $pergunta->toArray(),
            'model' => $resposta,
        ]);

        $title = 'Editar resposta';

        $back = route('admin.core.perguntas.respostas.index', compact('pergunta'));

        return view('backend.core.perguntas.respostas.create_update', compact('form', 'title', 'back', 'resposta'));
    }

    /**
     * Edição - POST
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @param  Request $request
     * @return void
     */
    public function respostasUpdate(PerguntaModel $pergunta, RespostaModel $resposta, Request $request)
    {
        $data = $request->only(['descricao', 'cor']);
        $data['pergunta_id'] = $pergunta->id;

        $this->repositoryRespostas->update($resposta, $data);

        return redirect()->route('admin.core.perguntas.respostas.index', compact('pergunta'))->withFlashSuccess('Resposta atualizada com sucesso!');
    }

    /**
     * Remover respota (regras no RespostaPolicy)
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @return void
     */
    public function respostasDestroy(PerguntaModel $pergunta, RespostaModel $resposta)
    {
        $this->repositoryRespostas->delete($resposta);

        return redirect()->route('admin.core.perguntas.respostas.index', compact('pergunta'))->withFlashSuccess('Resposta deletada com sucesso');
    }

    /**
     * Order - move up
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @return void
     */
    public function respostasMoveOrderUp(PerguntaModel $pergunta, RespostaModel $resposta)
    {
        $resposta->moveOrderUp();
        return null;
    }

    /**
     * Order - move down
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @return void
     */
    public function respostasMoveOrderDown(PerguntaModel $pergunta, RespostaModel $resposta)
    {
        $resposta->moveOrderDown();
        return null;
    }

    /**
     * Order - move top
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @return void
     */
    public function respostasMoveOrderTop(PerguntaModel $pergunta, RespostaModel $resposta)
    {
        $resposta->moveToStart();
        return null;
    }

    /**
     * Order - move end
     *
     * @param  PerguntaModel $pergunta
     * @param  RespostaModel $resposta
     * @return void
     */
    public function respostasMoveOrderBack(PerguntaModel $pergunta, RespostaModel $resposta)
    {
        $resposta->moveToEnd();
        return null;
    }
}
