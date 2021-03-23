<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use DataTables;
use Illuminate\Http\Request;

trait Chart_1_1_UnidadeProdutiva
{
    function getChart_1_1_UnidadeProdutiva(Request $request)
    {
        return $this->getQueryChart_1_1_UnidadeProdutiva($request)
            ->count();
    }

    function dataChart_1_1_UnidadeProdutiva(Request $request)
    {
        return DataTables::of($this->getQueryChart_1_1_UnidadeProdutiva($request))
            ->editColumn('produtores', function ($row) {
                return $row->produtores->pluck('nome')->join(", ");
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtores->first()->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->filterColumn('produtores', function ($query, $param) {
                $query->whereHas('produtores', function ($q) use ($param) {
                    $q->where('nome', 'like', '%' . $param . '%');
                });
            })
            ->make(true);
    }

    function getQueryChart_1_1_UnidadeProdutiva(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        //Desabilita filtros por Atuação
        $requestData['atuacao_dominio_id'] = null;
        $requestData['atuacao_unidade_operacional_id'] = null;
        $requestData['atuacao_tecnico_id'] = null;

        return $this->chartService->getUnidadesProdutivas($requestData);
    }
}
