<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\CadernoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait OfflineCadernoCampoTrait
{

    public function cadernoCampo(Request $request)
    {

        $data = [];

        /**
         * Caderno de Campo
         */

        // \DB::enableQueryLog();

        $data['cadernos'] = CadernoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_cadernos'))->get()->toArray();

        $cadernos_all = CadernoModel::withTrashed()
            ->select('id') //Otimização
            ->with(['respostasManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_caderno_resposta_caderno'));
            }])->with(['arquivosManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_caderno_arquivos'));
            }])->get()->toArray();

        $data['caderno_resposta_caderno'] = $this->mergeOfflineData($cadernos_all, 'respostas_many_offline');

        $data['caderno_arquivos'] = $this->mergeOfflineData($cadernos_all, 'arquivos_many_offline');
        $data['caderno_arquivos'] = $this->resolveLinks('arquivo', $data['caderno_arquivos']);

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
