<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_2_2_CertificacaoProducao
{
    function getChart_2_2_CertificacaoProducao(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->getQueryChart_2_2_CertificacaoProducao($request);

        $data = $query->select('certificacoes.id', 'certificacoes.nome', \DB::raw('count(*) as count'))
            ->groupBy('certificacoes.id', 'certificacoes.nome')
            ->get()
            ->toArray();

        $data[] = [
            'id' => '0',
            'nome' => 'Não possuí',
            'count' => $this->chartService->getUnidadesProdutivas($requestData)
                ->where('fl_certificacoes', 0)
                ->count()
        ];

        $naoRespondeu = $this->chartService->getUnidadesProdutivas($requestData)
            ->whereNull('fl_certificacoes') //null é quando não respondeu
            ->count();

        return ['itens' => $data, 'nao_respondeu' => $naoRespondeu];
    }

    function getQueryChart_2_2_CertificacaoProducao(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivas($requestData);

        $query
            ->join('unidade_produtiva_certificacoes', 'unidade_produtiva_certificacoes.unidade_produtiva_id', '=', 'unidade_produtivas.id')
            ->join('certificacoes', 'certificacoes.id', '=', 'unidade_produtiva_certificacoes.certificacao_id');

        // dd($query->get());

        return $query;
    }
}
