<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_10_RelacaoPropriedade
{
    function getChart_2_10_RelacaoPropriedade(Request $request)
    {
        $query = $this->getQueryChart_2_10_RelacaoPropriedade($request);

        $data = $query->select('TP.id', 'TP.nome', \DB::raw('count(unidade_produtivas.id) as total'))
            ->groupBy('TP.id', 'TP.nome')
            ->get()
            ->toArray();

        return $data;
    }

    function getQueryChart_2_10_RelacaoPropriedade(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($data);

        $query
            ->join('tipo_posses as TP', 'TP.id', '=', 'tipo_posse_id');

        return $query;
    }
}
