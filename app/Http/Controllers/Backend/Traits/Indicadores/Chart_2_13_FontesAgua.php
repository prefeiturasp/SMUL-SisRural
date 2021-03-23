<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_13_FontesAgua
{
    function getChart_2_13_FontesAgua(Request $request)
    {
        $query = $this->getQueryChart_2_13_FontesAgua($request);

        $data = $query->select(
            'TFA.id',
            'TFA.nome',
            \DB::raw('count(UPTFA.unidade_produtiva_id) as count')
         )
            ->groupBy('TFA.id', 'TFA.nome')
            ->get()
            ->toArray();

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($this->service->getFilterData($request))
            ->doesntHave('tiposFonteAgua')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }

    function getQueryChart_2_13_FontesAgua(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivas($requestData);

        $query
            ->join('unidade_produtiva_tipo_fonte_aguas as UPTFA', 'UPTFA.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('tipo_fonte_aguas as TFA', 'TFA.id', '=', 'UPTFA.tipo_fonte_agua_id');

        return $query;
    }
}
