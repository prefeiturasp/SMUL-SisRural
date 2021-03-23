<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Helpers\General\AppHelper;
use Illuminate\Http\Request;

trait Chart_5_2_CadernoCampo_Upas
{
    function getChart_5_2_CadernoCampo_Upas(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $query = $this->getQueryChart_5_2_CadernoCampo_Upas($request)
            ->select('unidade_produtiva_id')
            ->distinct();

        return $query->count('unidade_produtiva_id');
    }

    function getQueryChart_5_2_CadernoCampo_Upas(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        return $this->chartService->getCadernos($requestData, true)
            ->where('cadernos.status', CadernoStatusEnum::Finalizado)
            ->whereBetween('cadernos.updated_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']));
    }
}
