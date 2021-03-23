<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_4_Infraestrutura
{
    function getChart_2_4_Infraestrutura(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->getQueryChart_2_4_Infraestrutura($request)
            ->select('IT.id', 'IT.nome', \DB::raw('count(unidade_produtivas.id) as total'))
            ->groupBy('IT.id', 'IT.nome')
            ->get();

        $others = \DB::table('instalacao_tipos')
            ->select('id', 'nome', \DB::raw('0 as total'))
            ->whereNotIn('id', $query->pluck('id'))
            ->get()
            ->values();

        $ret = array_merge(
            $query->toArray(),
            $others->map(
                function($v) {
                    return (array) $v;
                }
            )->toArray()
        );

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->doesntHave('instalacoes') //null é quando não respondeu
            ->count();

        return ['itens' => $ret, 'nao_respondeu' => $naoRespondeu];
    }

    function getQueryChart_2_4_Infraestrutura(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivas($requestData);

        $query
            ->join('instalacoes as I', 'I.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('instalacao_tipos as IT', 'IT.id', '=', 'I.instalacao_tipo_id');

        return $query;
    }
}
