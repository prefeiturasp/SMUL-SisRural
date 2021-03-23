<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Helpers\General\AppHelper;
use DataTables;
use Illuminate\Http\Request;

trait Chart_1_3_NovosProdutores
{
    function getChart_1_3_NovosProdutores(Request $request)
    {
        $data = $this->getQueryChart_1_3_NovosProdutores($request);

        if (!$data) {
            return '-';
        }

        return $data->count('id');
    }

    function dataChart_1_3_NovosProdutores(Request $request)
    {
        $data = $this->getQueryChart_1_3_NovosProdutores($request);

        if (!$data) {
            $data = [];
        }

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->make(true);
    }

    function getQueryChart_1_3_NovosProdutores(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!$requestData['dt_ini'] || !$requestData['dt_end']) {
            return null;
        }

        $query = $this->chartService->getProdutores($requestData)
            ->whereBetween('produtores.created_at', AppHelper::dateBetween(@$requestData['dt_ini'], @$requestData['dt_end']));

        return $query;
    }
}
