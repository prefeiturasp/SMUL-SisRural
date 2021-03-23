<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_11_Genero
{
    function getChart_2_11_Genero(Request $request)
    {
        $query = $this->getQueryChart_2_11_Genero($request);

        $data = $query->select('G.id', 'G.nome', \DB::raw('count(DISTINCT produtores.id) as total'))
            ->groupBy('G.id', 'G.nome')
            ->get()
            ->toArray();

        $dataRequest = $this->service->getFilterData($request);

        $naoRespondeu = $this->chartService->getUnidadesProdutivasJoinProdutores($dataRequest)
            ->select('produtores.id', 'produtores.genero_id')
            ->whereNull('produtores.genero_id') //null é quando não respondeu
            ->distinct()
            ->count('produtores.id');

        return ['itens' => $data, 'nao_respondeu' => $naoRespondeu];
    }

    function getQueryChart_2_11_Genero(Request $request)
    {
        $dataRequest = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($dataRequest);

        $query
            ->join('generos as G', 'G.id', '=', 'produtores.genero_id');

        return $query;
    }
}
