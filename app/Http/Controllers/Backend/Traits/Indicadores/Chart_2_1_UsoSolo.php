<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_1_UsoSolo
{
    function getChart_2_1_UsoSolo(Request $request)
    {

        $data = $this->getQueryChart_2_1_UsoSolo($request)
            ->select('SC.nome', \DB::raw('sum(UPC.area) as area'), \DB::raw('count(UPC.unidade_produtiva_id) as upas'))
            ->groupBy('SC.nome')
            ->get();

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($this->service->getFilterData($request))
            ->doesntHave('caracterizacoes')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }


    function getQueryChart_2_1_UsoSolo(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $unidadesProdutivas = $this->chartService->getUnidadesProdutivas($data)
            ->join('unidade_produtiva_caracterizacoes as UPC', 'UPC.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('solo_categorias as SC', 'SC.id', 'UPC.solo_categoria_id');

        return $unidadesProdutivas;
    }
}
