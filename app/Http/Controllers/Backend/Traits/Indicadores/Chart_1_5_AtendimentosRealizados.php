<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use DataTables;
use Illuminate\Http\Request;

trait Chart_1_5_AtendimentosRealizados
{
    function getChart_1_5_AtendimentosRealizados(Request $request)
    {
        $data = $this->getQueryChart_1_5_AtendimentosRealizados($request);

        return $data;
    }

    function dataChart_1_5_AtendimentosRealizados(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!$this->service->existFilterAtuacao($requestData)) {
            $requestData['atuacao_tecnico_id'] = $this->service->getAllTecnicosAtuacao();
        }

        $data = $this->getQueryChart_1_13b_DistribuicaoAtendimentoTecnicoDatatable($request);

        return DataTables::of($data)
            ->editColumn('uid', function ($row) {
                return $row->uid;
            })
            ->addColumn('nome', function ($row) {
                return @$row->nome ? $row->nome : '-';
            })
            ->addColumn('unidadeProdutiva', function ($row) {
                if (@$row->unidadeProdutiva) {
                    return $row->unidadeProdutiva;
                } else if ($row->produtor_id) {
                    return ProdutorModel::find($row->produtor_id)->unidadesProdutivas->pluck('nome')->join(", ");
                } else {
                    return '-';
                }
            })
            ->addColumn('actions', function ($row) {
                if ($row->produtor_id) {
                    $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                } else if ($row->type == 'Plano de Ação') {
                    $pda = PlanoAcaoModel::where('id', $row->id)->get(['id', 'plano_acao_coletivo_id', 'fl_coletivo'])->first();

                    if ($pda->fl_coletivo) {
                        $externalDashUrl = route('admin.core.plano_acao_coletivo.view', $pda->plano_acao_coletivo_id ? $pda->plano_acao_coletivo_id : $pda->id);
                    }
                }

                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->filterColumn('uid', function ($query, $param) {
                $query->where('C1.uid', '=', $param);
            })
            ->filterColumn('unidadeProdutiva', function ($query, $param) {
                $query->where('UP.nome', 'like', '%' . $param . '%');
            })
            ->filterColumn('nome', function ($query, $param) {
                $query->where('P.nome', 'like', '%' . $param . '%');
            })
            ->make(true);
    }


    function getQueryChart_1_5_AtendimentosRealizados(Request $request)
    {
        $list = $this->getQueryChart_1_13b_DistribuicaoAtendimentoTecnico($request);

        $total = array_reduce($list, function($carry, $item) {
            $carry += $item['total']*1;
            return $carry;
        },0);

        return $total;
    }
}
