<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\CategoriaPerguntaModel;
use App\Models\Core\ChecklistCategoriaModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\TemplateModel;
use App\Models\Core\UnidadeOperacionalModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait OfflineDadosGeraisAuthTrait
{

    public function dadosGeraisAuth(Request $request)
    {
        $data = [];

        // 'templates',
        // 'template_pergunta_templates',
        // 'template_perguntas',
        // 'template_respostas',
        // 'unidade_operacionais',
        // 'checklists',
        // 'checklist_perguntas',
        // 'perguntas',
        // 'respostas'

        /**
         * Templates - Escopo Domínios
         */

        $data['templates'] = TemplateModel::withoutGlobalScopes()->withTrashed()->whereUpdatedAt($request->input('updated_at_templates'))->get()->toArray();

        $template_all =  TemplateModel::withoutGlobalScopes()
            ->select('id') //Otimização
            ->withTrashed()->with(['templatePerguntasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_template_pergunta_templates'));
            }])->with(['perguntasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_template_perguntas'));
            }])->get()->toArray();

        $data['template_pergunta_templates'] = $this->mergeOfflineData($template_all, 'template_perguntas_offline');
        $data['template_perguntas'] = $this->mergeOfflineData($template_all, 'perguntas_offline');


        /**
         * Respostas só dos Templates utilizados
         */
        $template_all =  TemplateModel::withoutGlobalScopes()
            ->select('id') //Otimização
            ->withTrashed()->with(['perguntasOffline' => function ($query) use ($request) {
                $query->with(['respostasOffline' => function ($query) use ($request) {
                    $query->whereUpdatedAt($request->input('updated_at_template_respostas'));
                }]);
            }])->get()->toArray();

        $template_perguntas = $this->mergeOfflineData($template_all, 'perguntas_offline');

        $data['template_respostas'] = $this->mergeOfflineData($template_perguntas, 'respostas_offline');


        /**
         * Unidades Operacionais
         */

        $data['unidade_operacionais'] = UnidadeOperacionalModel::withTrashed()->whereUpdatedAt($request->input('updated_at_unidade_operacionais'))->get()->toArray();


        /**
         * Templates - Checklist
         */

        //'perguntas', 'respostas', 'checklists', 'checklist_categorias', 'checklist_perguntas', 'checklist_pergunta_respostas'

        //Retorna todos os templates do sistema, porque o usuário pode visualizar templates que não faz parte de sua "abrangência".
        $data['checklists'] = ChecklistModel::withoutGlobalScopes()->withTrashed()->whereUpdatedAt($request->input('updated_at_checklists'))->get()->toArray();

        //Categorias
        $checklist_all =  ChecklistModel::withoutGlobalScopes()
            ->select('id') //Otimização
            ->withTrashed()->with(['categoriasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_checklist_categorias'));
            }])->get()->toArray();

        $data['checklist_categorias'] = $this->mergeOfflineData($checklist_all, 'categorias_offline');

        //Checklist Perguntas
        $checklist_all =  ChecklistModel::withoutGlobalScopes()
            ->select('id') //Otimização
            ->withTrashed()->with(['categoriasOffline.checklistPerguntasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_checklist_perguntas'));
            }])->with(['categoriasOffline.perguntasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_perguntas'));
            }])->get()->toArray();

        $categorias_all = $this->mergeOfflineData($checklist_all, 'categorias_offline');
        $data['checklist_perguntas'] = $this->mergeOfflineData($categorias_all, 'checklist_perguntas_offline');
        $data['perguntas'] = $this->mergeOfflineData($categorias_all, 'perguntas_offline');

        //Respostas
        $checklist_all =  ChecklistModel::withoutGlobalScopes()
            ->select('id') //Otimização
            ->withTrashed()->with(['categoriasOffline.perguntasOffline.respostasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_respostas'));
            }])->get()->toArray();

        $data_all = $this->mergeOfflineData($checklist_all, 'categorias_offline');
        $data_all = $this->mergeOfflineData($data_all, 'perguntas_offline');
        $data['respostas'] = $this->mergeOfflineData($data_all, 'respostas_offline');

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
