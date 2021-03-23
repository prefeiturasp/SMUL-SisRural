<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_7_Associativismo
{
    function getChart_2_7_Associativismo(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($data);

        $data = $query->select('produtores.fl_tipo_parceria', \DB::raw('count(DISTINCT produtores.id) as total'))
            ->groupBy('produtores.fl_tipo_parceria')
            ->get()
            ->pluck('total', 'fl_tipo_parceria');

        $ret = [
            ['nome'=>'Sim', 'total'=> @$data[1]*1],
            ['nome'=>'Não', 'total'=> @$data[0]*1]
        ];

        return ['itens' => $ret, 'nao_respondeu' => @$data[null]*1];
    }

}
