<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_15_DestinacaoResiduosSolidos
{
    function getChart_2_15_DestinacaoResiduosSolidos(Request $request)
    {
        $query = $this->getQueryChart_2_15_DestinacaoResiduosSolidos($request);

        $data = $query->select('RS.id', 'RS.nome', \DB::raw('count(UPRS.unidade_produtiva_id) as count'))
            ->groupBy('RS.id', 'RS.nome')
            ->get()
            ->toArray();

        $requestData = $this->service->getFilterData($request);

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->doesntHave('residuoSolidos')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }

    function getQueryChart_2_15_DestinacaoResiduosSolidos(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivas($requestData);

        $query
            ->join('unidade_produtiva_residuo_solidos as UPRS', 'UPRS.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('residuo_solidos as RS', 'RS.id', '=', 'UPRS.residuo_solido_id');

        return $query;
    }
}
