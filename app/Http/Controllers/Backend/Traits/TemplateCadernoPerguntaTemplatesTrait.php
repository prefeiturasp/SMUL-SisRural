<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Helpers\General\AppHelper;
use App\Models\Core\TemplateModel;
use App\Models\Core\TemplatePerguntaModel;
use App\Models\Core\TemplatePerguntaTemplatesModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait TemplateCadernoPerguntaTemplatesTrait
{
    /**
     * Lista de perguntas no template do caderno de campo
     *
     * @param  TemplateModel $template
     * @param  Request $request
     * @return void
     */
    public function perguntasIndex(TemplateModel $template, Request $request)
    {
        $title = $template->nome . ' / Perguntas Vinculadas';
        $urlAdd = route('admin.core.templates_caderno.perguntas.create', ["template" => $template]);
        $urlDatatable = route('admin.core.templates_caderno.perguntas.datatable', ["template" => $template]);

        return view('backend.core.templates_caderno.perguntas.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "perguntasIndex()"
     */
    public function perguntasDatatable(TemplateModel $template)
    {
        return DataTables::of($template->perguntas()->getQuery())
            ->addColumn('respostas', function ($row) {
                return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->addColumn('actions', function ($row) use ($template) {
                $params = ['template' => $row->template_id, 'templatePerguntaTemplates' => $row->id];

                $deleteUrl = route('admin.core.templates_caderno.perguntas.destroy', $params);

                $moveOrderUp = route('admin.core.templates_caderno.perguntas.moveOrderUp', $params);
                $moveOrderDown = route('admin.core.templates_caderno.perguntas.moveOrderDown', $params);
                $moveOrderTop = route('admin.core.templates_caderno.perguntas.moveOrderTop', $params);
                $moveOrderBack = route('admin.core.templates_caderno.perguntas.moveOrderBack', $params);

                $row = $template;
                return view('backend.components.form-actions.index', compact('deleteUrl', 'moveOrderUp', 'moveOrderDown', 'moveOrderTop', 'moveOrderBack', 'row'));
            })
            ->filterColumn('respostas', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('respostas', function ($q) use ($keyword) {
                        $q->where('template_respostas.descricao', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['respostas'])
            ->make(true);
    }

    /**
     * Lista de todas as perguntas do sistema com exceção das que já foram utilizadas
     *
     * @param  TemplateModel $template
     * @return void
     */
    public function todasPerguntasDatatable(TemplateModel $template)
    {
        $templatePerguntaTemplates = \DB::table('template_pergunta_templates')->select('template_pergunta_id')->where('template_id', $template->id)->where('deleted_at', null)->get()->pluck('template_pergunta_id')->toArray();

        return DataTables::of(TemplatePerguntaModel::whereNotIn('id', $templatePerguntaTemplates)->get())
            ->addColumn('respostas', function ($row) {
                return AppHelper::tableArrayToList($row->respostas->toArray(), 'descricao');
            })->addColumn('actions', function ($row) use ($template) {
                $params = ['template' => $template->id, 'templatePergunta' => $row->id];

                $addUrl = route('admin.core.templates_caderno.perguntas.store', $params);
                return view('backend.core.templates_caderno.perguntas.form_actions', compact('addUrl'));
            })
            ->rawColumns(['respostas'])
            ->make(true);
    }

    /**
     * Cadastro/vinculação de uma pergunta no template do caderno de campo
     *
     * @param  TemplateModel $template
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function perguntasCreate(TemplateModel $template, FormBuilder $formBuilder)
    {
        $title = 'Selecione a pergunta que deseja vincular';
        $urlDatatable = route('admin.core.templates_caderno.perguntas.todasPerguntasDatatable', ["template" => $template]);

        $urlBack = route('admin.core.templates_caderno.perguntas.index', ["template" => $template]);

        return view('backend.core.templates_caderno.perguntas.create_update', compact('urlDatatable', 'urlBack', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  TemplateModel $template
     * @param  TemplatePerguntaModel $templatePergunta
     * @return void
     */
    public function perguntasStore(Request $request, TemplateModel $template, TemplatePerguntaModel $templatePergunta)
    {
        $data = [];
        $data['template_id'] = $template->id;
        $data['template_pergunta_id'] = $templatePergunta->id;

        //Restaura caso seja softDelete
        $exist = TemplatePerguntaTemplatesModel::withTrashed()->where($data)->first();
        if ($exist) {
            $exist->restore();
        } else {
            TemplatePerguntaTemplatesModel::create($data);
        }

        return redirect()->route('admin.core.templates_caderno.perguntas.create', compact('template'))->withFlashSuccess('Pergunta vinculada com sucesso!');
    }

    /**
     * Ação p/ remover/desvincular uma pergunta no caderno de campo
     *
     * @param  TemplateModel $template
     * @param  TemplatePerguntaTemplatesModel $templatePerguntaTemplates
     * @return void
     */
    public function perguntasDestroy(TemplateModel $template, TemplatePerguntaTemplatesModel $templatePerguntaTemplates)
    {
        $templatePerguntaTemplates->delete();
        return redirect()->route('admin.core.templates_caderno.perguntas.index', compact('template'))->withFlashSuccess('Pergunta desvinculada com sucesso');
    }

    /**
     * Order - move up
     *
     * @param  TemplateModel $template
     * @param  TemplatePerguntaTemplatesModel $templatePerguntaTemplates
     * @return void
     */
    public function perguntasMoveOrderUp(TemplateModel $template, TemplatePerguntaTemplatesModel $templatePerguntaTemplates)
    {
        $templatePerguntaTemplates->moveOrderUp();
        return null;
    }

    /**
     * Order - move down
     *
     * @param  TemplateModel $template
     * @param  TemplatePerguntaTemplatesModel $templatePerguntaTemplates
     * @return void
     */
    public function perguntasMoveOrderDown(TemplateModel $template, TemplatePerguntaTemplatesModel $templatePerguntaTemplates)
    {
        $templatePerguntaTemplates->moveOrderDown();
        return null;
    }

    /**
     * Order - move top
     *
     * @param  TemplateModel $template
     * @param  TemplatePerguntaTemplatesModel $templatePerguntaTemplates
     * @return void
     */
    public function perguntasMoveOrderTop(TemplateModel $template, TemplatePerguntaTemplatesModel $templatePerguntaTemplates)
    {
        $templatePerguntaTemplates->moveToStart();
        return null;
    }

    /**
     * Order - move end
     *
     * @param  TemplateModel $template
     * @param  TemplatePerguntaTemplatesModel $templatePerguntaTemplates
     * @return void
     */
    public function perguntasMoveOrderBack(TemplateModel $template, TemplatePerguntaTemplatesModel $templatePerguntaTemplates)
    {
        $templatePerguntaTemplates->moveToEnd();
        return null;
    }
}
