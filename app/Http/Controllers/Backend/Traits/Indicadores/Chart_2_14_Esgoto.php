<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_14_Esgoto
{
    function getChart_2_14_Esgoto(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->getQueryChart_2_14_Esgoto($request);

        $data = $query->select('ES.id', 'ES.nome', \DB::raw('count(UPES.unidade_produtiva_id) as count'))
            ->groupBy('ES.id', 'ES.nome')
            ->get()
            ->toArray();

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->doesntHave('esgotamentoSanitarios')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }

    function getQueryChart_2_14_Esgoto(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivas($requestData);

        $query
            ->join('unidade_produtiva_esgotamento_sanitarios as UPES', 'UPES.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('esgotamento_sanitarios as ES', 'ES.id', '=', 'UPES.esgotamento_sanitario_id');

        return $query;
    }
}
