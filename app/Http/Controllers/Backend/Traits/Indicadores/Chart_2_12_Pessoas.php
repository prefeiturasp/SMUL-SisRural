<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_12_Pessoas
{
    function getChart_2_12_Pessoas(Request $request)
    {
        $data = $this->getQueryChart_2_12_Pessoas($request)
            ->select('R.nome', \DB::raw('count(C.unidade_produtiva_id) as total'))
            ->groupBy('R.nome')
            ->get();

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($this->service->getFilterData($request))
            ->doesntHave('colaboradores')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }


    function getQueryChart_2_12_Pessoas(Request $request)
    {
        $data = $this->service->getFilterData($request);

        $unidadesProdutivas = $this->chartService->getUnidadesProdutivas($data)
            ->join('colaboradores as C', 'C.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('relacoes as R', 'R.id', '=', 'C.relacao_id');

        return $unidadesProdutivas;
    }
}
