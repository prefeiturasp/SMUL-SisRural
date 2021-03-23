<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use DataTables;
use Illuminate\Http\Request;

trait Chart_1_4_UpasAtendidas
{
    function getChart_1_4_UpasAtendidas(Request $request)
    {
        $data = $this->getQueryChart_1_4_UpasAtendidas($request);

        if (!$data) {
            return '-';
        }

        return $data->count();
    }

    function dataChart_1_4_UpasAtendidas(Request $request)
    {
        $data = $this->getQueryChart_1_4_UpasAtendidas($request);

        if (!$data) {
            $data = [];
        }

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->make(true);
    }

    function getQueryChart_1_4_UpasAtendidas(Request $request)
    {
        $data = $this->service->getFilterData($request);

        if (!@$data['dt_ini'] || !@$data['dt_end']) {
            return null;
        }

        //Força pegar todos técnicos p/ calcular as atuações do domínio
        if (!@$data['atuacao_dominio_id'] && !@$data['atuacao_unidade_operacional_id'] && !@$data['atuacao_tecnico_id']) {
            $data['atuacao_dominio_id'] = $this->service->getAllDominiosAtuacao();
        }
        //Fim Parte Custom

        return $this->chartService->getUnidadesProdutivas($data);
    }
}
