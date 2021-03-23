<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use DataTables;
use Illuminate\Http\Request;

trait Chart_1_2_Produtor
{
    function getChart_1_2_Produtor(Request $request)
    {
        return $this->getQueryChart_1_2_Produtor($request)
            ->count('produtores.id');
    }

    function dataChart_1_2_Produtor(Request $request)
    {
        return DataTables::of(
            $this->getQueryChart_1_2_Produtor($request)
        )
            ->editColumn('nome', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->id) . "' target='_blank'>" . $row->nome . "</a>";
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->rawColumns(['nome'])
            ->make(true);
    }

    function getQueryChart_1_2_Produtor(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        //Desabilita filtros por Atuação
        $requestData['atuacao_dominio_id'] = null;
        $requestData['atuacao_unidade_operacional_id'] = null;
        $requestData['atuacao_tecnico_id'] = null;

        return $this->chartService->getProdutores($requestData);
    }
}
