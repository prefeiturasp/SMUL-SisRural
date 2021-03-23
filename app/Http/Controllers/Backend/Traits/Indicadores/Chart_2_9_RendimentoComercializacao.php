<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_9_RendimentoComercializacao
{
    function getChart_2_9_RendimentoComercializacao(Request $request)
    {
        $query = $this->getQueryChart_2_9_RendimentoComercializacao($request);

        $data = $query->select('RC.id', 'RC.nome', \DB::raw('count(DISTINCT produtores.id) as total'))
            ->groupBy('RC.id', 'RC.nome')
            ->get()
            ->toArray();

        $dataRequest = $this->service->getFilterData($request);

        $naoRespondeu = $this->chartService->getUnidadesProdutivasJoinProdutores($dataRequest)
            ->whereNull('produtores.rendimento_comercializacao_id') //null é quando não respondeu
            ->distinct()
            ->count('produtores.id');

        return ['itens' => $data, 'nao_respondeu' => $naoRespondeu];
    }

    function getQueryChart_2_9_RendimentoComercializacao(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($data);

        $query
            ->join('rendimento_comercializacoes as RC', 'RC.id', '=', 'produtores.rendimento_comercializacao_id');

        return $query;
    }
}
