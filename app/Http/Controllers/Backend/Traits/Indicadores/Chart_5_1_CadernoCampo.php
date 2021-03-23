<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\CadernoModel;
use Illuminate\Http\Request;

trait Chart_5_1_CadernoCampo
{
    function getChart_5_1_CadernoCampo(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        return $this->getQueryChart_5_1_CadernoCampo($request)
            ->count();
    }

    function getQueryChart_5_1_CadernoCampo(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        return $this->chartService->getCadernos($requestData)
            ->where('cadernos.status', CadernoStatusEnum::Finalizado)
            ->whereBetween('cadernos.finished_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']));
    }
}
