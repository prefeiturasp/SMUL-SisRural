<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_6_CanalComercializacao
{
    function getChart_2_6_CanalComercializacao(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $data = $this->getQueryChart_2_6_CanalComercializacao($request)
            ->select('CC.nome', \DB::raw('count(UPCC.unidade_produtiva_id) as total'))
            ->groupBy('CC.nome')
            ->get()
            ->toArray();

        //Nao comercializa
        $totalNaoComercializa = $this->chartService->getUnidadesProdutivas($requestData)
            ->where('fl_comercializacao', 0)
            ->count();
        if ($totalNaoComercializa > 0) {
            $data[] = [
                'id' => '0',
                'nome' => 'Não comercializa',
                'total' => $totalNaoComercializa
            ];
        }

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->doesntHave('canaisComercializacao')
            ->count();

        return ['itens'=>$data, 'nao_respondeu'=>$naoRespondeu];
    }


    function getQueryChart_2_6_CanalComercializacao(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $unidadesProdutivas = $this->chartService->getUnidadesProdutivas($requestData)
            ->join('unidade_produtiva_canal_comercializacoes as UPCC', 'UPCC.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('canal_comercializacoes as CC', 'CC.id', 'UPCC.canal_comercializacao_id');

        return $unidadesProdutivas;
    }
}
