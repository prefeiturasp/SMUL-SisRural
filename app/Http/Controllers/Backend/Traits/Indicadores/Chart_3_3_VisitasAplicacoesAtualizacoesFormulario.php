<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use Illuminate\Http\Request;

trait Chart_3_3_VisitasAplicacoesAtualizacoesFormulario
{
    function getChart_3_3_VisitasAplicacoesAtualizacoesFormulario(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $formularios = $this->chartService->getFormulariosComFiltroPergunta($requestData);

        $dataFormularios = $formularios->select(
            \DB::raw('count(checklist_unidade_produtivas.id) as formularios_count, DATE_FORMAT(updated_at, "%m/%Y") as date, DATE_FORMAT(updated_at, "%Y%m") as date_sort')
        )
            ->groupBy('date', 'date_sort')
            ->get(['date', 'date_sort', 'formularios_count'])
            ->toArray();

        $listMonths = $this->service->rangeDates($requestData['dt_ini'], $requestData['dt_end']);

        $dataFormularios = array_merge($dataFormularios, $listMonths);

        $ret = collect(array_merge($dataFormularios))
            ->groupBy('date_sort')
            ->sortKeys()
            ->map(function ($v) {
                return $v->collapse();
            })
            ->toArray();

        // dd(array_values($ret));

        return array_values($ret);
    }
}
