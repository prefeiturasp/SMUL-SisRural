<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_11b_ComunidadeTradicional
{
    function getChart_2_11b_ComunidadeTradicional(Request $request)
    {
        $dataRequest = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($dataRequest);

        $data = $query->select('produtores.fl_comunidade_tradicional', \DB::raw('count(DISTINCT produtores.id) as total'))
            ->groupBy('produtores.fl_comunidade_tradicional')
            ->get()
            ->pluck('total', 'fl_comunidade_tradicional');

        $ret = [
            ['nome'=>'Sim', 'total'=> @$data[1]*1],
            ['nome'=>'Não', 'total'=> @$data[0]*1],
        ];

        return ['itens' => $ret, 'nao_respondeu' => @$data[null]*1];
    }
}
