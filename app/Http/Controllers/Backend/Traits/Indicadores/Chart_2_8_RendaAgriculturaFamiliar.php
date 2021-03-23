<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_8_RendaAgriculturaFamiliar
{
    function getChart_2_8_RendaAgriculturaFamiliar(Request $request)
    {
        $query = $this->getQueryChart_2_8_RendaAgriculturaFamiliar($request);

        $data = $query->select( 'RA.id', 'RA.nome', \DB::raw('count(DISTINCT produtores.id) as total'))
            ->groupBy('RA.id', 'RA.nome')
            ->get()
            ->toArray();

        $dataRequest = $this->service->getFilterData($request);

        $naoRespondeu = $this->chartService->getUnidadesProdutivasJoinProdutores($dataRequest)
            ->whereNull('produtores.renda_agricultura_id') //null é quando não respondeu
            ->distinct()
            ->count('produtores.id');

        return ['itens' => $data, 'nao_respondeu' => $naoRespondeu];
    }

    function getQueryChart_2_8_RendaAgriculturaFamiliar(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($data);

        $query
            ->join('renda_agriculturas as RA', 'RA.id', '=', 'produtores.renda_agricultura_id');

        return $query;
    }
}
