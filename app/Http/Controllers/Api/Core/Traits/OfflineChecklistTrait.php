<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PerguntaModel;
use App\Models\Core\UnidadeProdutivaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait OfflineChecklistTrait
{
    public function checklists(Request $request)
    {
        $data = [];

        //'checklist_unidade_produtivas', 'unidade_produtiva_respostas', 'checklist_snapshot_respostas'

        // \DB::enableQueryLog();

        $perguntas = PerguntaModel::where("tipo_pergunta", "anexo")->pluck('id', 'id')->toArray();

        //Checklists Aplicados (Unidades produtivas)
        $data['checklist_unidade_produtivas'] = ChecklistUnidadeProdutivaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_checklist_unidade_produtivas'))->get()->toArray();

        //Checklist Respostas (Snapshot)
        $checklist_all = ChecklistUnidadeProdutivaModel::withTrashed()
            ->select('id') //Otimização
            ->with(['respostasManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_checklist_snapshot_respostas'));
            }])->with(['analiseLogs' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_checklist_aprovacao_logs'));
            }])->with(['arquivosManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_checklist_unidade_produtiva_arquivos'));
            }])->get()->toArray();


        $data['checklist_snapshot_respostas'] = $this->mergeOfflineData($checklist_all, 'respostas_many_offline');
        foreach ($data['checklist_snapshot_respostas'] as $k => &$v) {
            if (@$perguntas[$v['pergunta_id']] && @$v['resposta']) {
                $v['resposta'] = \Storage::url('/') . $v['resposta'];
            }
        }

        //Unidade Produtiva x Respostas
        $unidade_produtivas_all = UnidadeProdutivaModel::withTrashed()
            ->select('id') //Otimização
            ->with(['respostasManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_respostas'));
                $query->with('arquivosManyOffline');
            }])->get()->toArray();

        $data['unidade_produtiva_respostas'] = $this->mergeOfflineData($unidade_produtivas_all, 'respostas_many_offline');
        foreach ($data['unidade_produtiva_respostas'] as $k => &$v) {
            if (@$perguntas[$v['pergunta_id']] && @$v['resposta']) {
                $v['resposta'] = \Storage::url('/') . $v['resposta'];
            }
        }

        $data['unidade_produtiva_resposta_arquivos'] = $this->mergeOfflineData($data['unidade_produtiva_respostas'], 'arquivos_many_offline');
        $data['unidade_produtiva_resposta_arquivos'] = $this->resolveLinks('arquivo', $data['unidade_produtiva_resposta_arquivos']);

        $data['checklist_aprovacao_logs'] = $this->mergeOfflineData($checklist_all, 'analise_logs');

        $data['checklist_unidade_produtiva_arquivos'] = $this->mergeOfflineData($checklist_all, 'arquivos_many_offline');
        $data['checklist_unidade_produtiva_arquivos'] = $this->resolveLinks('arquivo', $data['checklist_unidade_produtiva_arquivos']);

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
