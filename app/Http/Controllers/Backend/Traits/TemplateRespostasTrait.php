<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\TemplateRespostaForm;
use App\Models\Core\TemplatePerguntaModel;
use App\Models\Core\TemplateRespostaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait TemplateRespostasTrait
{
    /**
     * Lista de respostas das perguntas de um template - caderno de campo
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  Request $request
     * @return void
     */
    public function respostasIndex(TemplatePerguntaModel $templatePergunta, Request $request)
    {
        $title = $templatePergunta->pergunta . ' / Respostas';
        $urlAdd = route('admin.core.template_perguntas.respostas.create', ["templatePergunta" => $templatePergunta]);
        $urlDatatable = route('admin.core.template_perguntas.respostas.datatable', ["templatePergunta" => $templatePergunta]);

        return view('backend.core.template_perguntas.respostas.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "respostasIndex()"
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @return void
     */
    public function respostasDatatable(TemplatePerguntaModel $templatePergunta)
    {
        return DataTables::of($templatePergunta->respostas()->getQuery())
            ->addColumn('actions', function ($row) {
                $params = ['templatePergunta' => $row->template_pergunta_id, 'templateResposta' => $row->id];

                $editUrl = route('admin.core.template_perguntas.respostas.edit', $params);
                $deleteUrl = route('admin.core.template_perguntas.respostas.destroy', $params);
                $moveOrderUp = route('admin.core.template_perguntas.respostas.moveOrderUp', $params);
                $moveOrderDown = route('admin.core.template_perguntas.respostas.moveOrderDown', $params);
                $moveOrderTop = route('admin.core.template_perguntas.respostas.moveOrderTop', $params);
                $moveOrderBack = route('admin.core.template_perguntas.respostas.moveOrderBack', $params);

                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'moveOrderUp', 'moveOrderDown', 'moveOrderTop', 'moveOrderBack', 'row'));
            })
            ->make(true);
    }

    /**
     * Cadastro resposta
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function respostasCreate(TemplatePerguntaModel $templatePergunta, FormBuilder $formBuilder)
    {
        $last = TemplateRespostaModel::orderBy('ordem', 'desc')->where('template_pergunta_id', $templatePergunta->id)->first();

        $form = $formBuilder->create(TemplateRespostaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.template_perguntas.respostas.store', ['templatePergunta' => $templatePergunta]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => ['ordem' => @$last->ordem + 1]
        ]);

        $title = 'Cadastrar resposta';

        $back = route('admin.core.template_perguntas.respostas.index', compact('templatePergunta'));

        return view('backend.core.template_perguntas.respostas.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro resposta - POST
     *
     * @param  Request $request
     * @param  TemplatePerguntaModel $templatePergunta
     * @return void
     */
    public function respostasStore(Request $request, TemplatePerguntaModel $templatePergunta)
    {
        $data = $request->only(['descricao']);
        $data['template_pergunta_id'] = $templatePergunta->id;

        TemplateRespostaModel::create($data);

        return redirect()->route('admin.core.template_perguntas.respostas.index', compact('templatePergunta'))->withFlashSuccess('Resposta criada com sucesso!');
    }

    /**
     * Edição resposta
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function respostasEdit(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(TemplateRespostaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.template_perguntas.respostas.update', ['templatePergunta' => $templatePergunta, 'templateResposta' => $templateResposta]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $templateResposta,
        ]);

        $title = 'Editar resposta';

        $back = route('admin.core.template_perguntas.respostas.index', compact('templatePergunta'));

        return view('backend.core.template_perguntas.respostas.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição resposta - POST
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @param  Request $request
     * @return void
     */
    public function respostasUpdate(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta, Request $request)
    {
        $data = $request->only(['descricao']);
        $data['template_pergunta_id'] = $templatePergunta->id;

        $templateResposta->update($data);

        return redirect()->route('admin.core.template_perguntas.respostas.index', compact('templatePergunta'))->withFlashSuccess('Resposta atualizada com sucesso!');
    }

    /**
     * Remover resposta (regras no TemplateRespostaPolicy)
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @return void
     */
    public function respostasDestroy(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta)
    {
        $templateResposta->delete();

        return redirect()->route('admin.core.template_perguntas.respostas.index', compact('templatePergunta'))->withFlashSuccess('Resposta deletada com sucesso');
    }

    /**
     * Order - move up
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @return void
     */
    public function respostasMoveOrderUp(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta)
    {
        $templateResposta->moveOrderUp();
        return null;
    }

    /**
     * Order - move down
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @return void
     */
    public function respostasMoveOrderDown(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta)
    {
        $templateResposta->moveOrderDown();
        return null;
    }

    /**
     * Order - move top
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @return void
     */
    public function respostasMoveOrderTop(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta)
    {
        $templateResposta->moveToStart();
        return null;
    }

    /**
     * Order - move end
     *
     * @param  TemplatePerguntaModel $templatePergunta
     * @param  TemplateRespostaModel $templateResposta
     * @return void
     */
    public function respostasMoveOrderBack(TemplatePerguntaModel $templatePergunta, TemplateRespostaModel $templateResposta)
    {
        $templateResposta->moveToEnd();
        return null;
    }
}
