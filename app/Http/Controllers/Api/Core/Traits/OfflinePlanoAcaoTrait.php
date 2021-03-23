<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait OfflinePlanoAcaoTrait
{
    public function plano_acoes(Request $request)
    {
        $data = [];

        // \DB::enableQueryLog();

        //'plano_acoes', 'plano_acao_itens', 'plano_acao_historicos', 'plano_acao_item_historicos'
        $plano_acoes = PlanoAcaoModel::withTrashed()->select("plano_acoes.*")->whereUpdatedAt($request->input('updated_at_plano_acoes'))->get()->toArray();

        $data['plano_acoes'] = $plano_acoes;

        $plano_acoes_all = PlanoAcaoModel::withTrashed()
            ->select('id') //Otimização
            ->with(['itensManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_plano_acao_itens'));
            }])->with(['historicosManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_plano_acao_historicos'));
            }])->with(['itensManyOfflineHistoricoItens' => function ($query) use ($request) {
                $query->with(['historicosManyOffline' => function ($q) use ($request) {
                    $q->whereUpdatedAt($request->input('updated_at_plano_acao_item_historicos'));
                }]);
            }])->get()->toArray();

        $data['plano_acao_itens'] = $this->mergeOfflineData($plano_acoes_all, 'itens_many_offline');
        $data['plano_acao_historicos'] = $this->mergeOfflineData($plano_acoes_all, 'historicos_many_offline');

        $plano_acoes_items_all = $this->mergeOfflineData($plano_acoes_all, 'itens_many_offline_historico_itens');

        $data['plano_acao_item_historicos'] = $this->mergeOfflineData($plano_acoes_items_all, 'historicos_many_offline');

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
