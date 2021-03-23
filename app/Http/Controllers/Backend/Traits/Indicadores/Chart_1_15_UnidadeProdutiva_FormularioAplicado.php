<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Helpers\General\AppHelper;
use Illuminate\Http\Request;

trait Chart_1_15_UnidadeProdutiva_FormularioAplicado
{
    function getChart_1_15_UnidadeProdutiva_FormularioAplicado(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $query = $this->getQueryChart_1_15_UnidadeProdutiva_FormularioAplicado($request);

        $query
            ->select('unidade_produtiva_id')
            ->groupBy('unidade_produtiva_id');

        return $query->get()->count();
    }

    function getQueryChart_1_15_UnidadeProdutiva_FormularioAplicado(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        return $this->chartService->getFormulariosComFiltroPergunta($requestData)
            ->whereBetween('checklist_unidade_produtivas.updated_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']));
    }
}
