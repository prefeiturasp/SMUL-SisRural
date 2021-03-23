<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_3_TamanhoUpa
{
    function getChart_2_3_TamanhoUpa(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        // Até 0,5 Ha
        // De 0,5 a 1 Ha
        // De 1 a 5 Ha
        // De 5 a 10 Ha
        // De 10 a 20 Ha
        // De 20 a 50 ha
        // Acima de 50 ha

        $query = $this->chartService->getUnidadesProdutivas($requestData)
            ->select(
                'area_total_solo',
                \DB::raw('(area_total_solo*1 <= .5) as menor_0_5'),
                \DB::raw('(area_total_solo*1 > 0.5 && area_total_solo*1 <= 1) as menor_1'),
                \DB::raw('(area_total_solo*1 > 1 && area_total_solo*1 <= 5) as menor_5'),
                \DB::raw('(area_total_solo*1 > 5 && area_total_solo*1 <= 10) as menor_10'),
                \DB::raw('(area_total_solo*1 > 10 && area_total_solo*1 <= 20) as menor_20'),
                \DB::raw('(area_total_solo*1 > 20 && area_total_solo*1 <= 50) as menor_50'),
                \DB::raw('(area_total_solo*1 > 50) as maior_50')
            );

        $data = \DB::query()
            ->select(
                \DB::raw('sum(menor_0_5) as menor_0_5'),
                \DB::raw('sum(menor_1) as menor_1'),
                \DB::raw('sum(menor_5) as menor_5'),
                \DB::raw('sum(menor_10) as menor_10'),
                \DB::raw('sum(menor_20) as menor_20'),
                \DB::raw('sum(menor_50) as menor_50'),
                \DB::raw('sum(maior_50) as maior_50')
            )
            ->fromSub($query, 'C1')
            ->first();

        $ret = [
            [ 'nome' => 'Até 0,5 Ha', 'count' => $data->menor_0_5*1 ],
            [ 'nome' => 'De 0,5 a 1 Ha', 'count' => $data->menor_1*1 ],
            [ 'nome' => 'De 1 a 5 Ha', 'count' => $data->menor_5*1 ],
            [ 'nome' => 'De 5 a 10 Ha', 'count' => $data->menor_10*1 ],
            [ 'nome' => 'De 10 a 20 Ha', 'count' => $data->menor_20*1 ],
            [ 'nome' => 'De 20 a 50 ha', 'count' => $data->menor_50*1 ],
            [ 'nome' => 'Acima de 50 ha', 'count' => $data->maior_50*1 ],
        ];

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->whereNull('area_total_solo') //null é quando não respondeu
            ->count();

        return ['itens' => $ret, 'nao_respondeu' => $naoRespondeu];
    }
}
