<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use DataTables;
use Illuminate\Http\Request;

trait Chart_1_7_FormulariosAplicados
{
    function getChart_1_7_FormulariosAplicados(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getFormularios($requestData);

        $data = $query->select(\DB::raw('count(*) as checklist_count, checklist_id'))
            ->groupBy('checklist_id')
            ->get(['checklist.nome', 'checklist_count'])
            ->toArray();

        return array_map(function ($v) {
            return [
                'id' => $v['checklist']['id'],
                'nome' => $v['checklist']['nome'] . ' (' . $v['checklist_count'] . ')',
                'count' => $v['checklist_count'],
            ];
        }, $data);
    }

    function dataChart_1_7_FormulariosAplicados(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getFormularios($requestData);

        return DataTables::of($query)
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.checklist_unidade_produtiva.view', $row->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl', 'row'));
            })
            ->make(true);
    }
}
