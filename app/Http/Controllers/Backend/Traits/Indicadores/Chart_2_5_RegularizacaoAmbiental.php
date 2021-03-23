<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Helpers\General\AppHelper;
use App\Models\Core\ProdutorModel;
use DataTables;
use Illuminate\Http\Request;

trait Chart_2_5_RegularizacaoAmbiental
{
    function getChart_2_5_RegularizacaoAmbiental(Request $request)
    {
        $data = $this->service->getFilterData($request);

        /*
            Opções retornadas [null, 1, 0];

            'fl_cnpj',
            'fl_nota_fiscal_produtor',
            'fl_agricultor_familiar_dap'
        */
        $queryProdutor = $this->chartService->getUnidadesProdutivasJoinProdutores($data)
            ->select(
                'produtor_id',
                'fl_cnpj',
                \DB::raw('(fl_cnpj = 1) as fl_cnpj_sim'),
                \DB::raw('(fl_cnpj = 0) as fl_cnpj_nao'),
                \DB::raw('(fl_cnpj is null) as fl_cnpj_sem_resposta'),

                'fl_nota_fiscal_produtor',
                \DB::raw('(fl_nota_fiscal_produtor = 1) as fl_nota_fiscal_produtor_sim'),
                \DB::raw('(fl_nota_fiscal_produtor = 0) as fl_nota_fiscal_produtor_nao'),
                \DB::raw('(fl_nota_fiscal_produtor is null) as fl_nota_fiscal_produtor_sem_resposta'),

                'fl_agricultor_familiar_dap',
                \DB::raw('(fl_agricultor_familiar_dap = 1) as fl_agricultor_familiar_dap_sim'),
                \DB::raw('(fl_agricultor_familiar_dap = 0) as fl_agricultor_familiar_dap_nao'),
                \DB::raw('(fl_agricultor_familiar_dap is null) as fl_agricultor_familiar_dap_sem_resposta')
            )->distinct();

        $dataProdutores = \DB::query()
            ->select(
                \DB::raw('sum(fl_cnpj_sim) as fl_cnpj_sim'),
                \DB::raw('sum(fl_cnpj_nao) as fl_cnpj_nao'),
                \DB::raw('sum(fl_cnpj_sem_resposta) as fl_cnpj_sem_resposta'),
                \DB::raw('sum(fl_nota_fiscal_produtor_sim) as fl_nota_fiscal_produtor_sim'),
                \DB::raw('sum(fl_nota_fiscal_produtor_nao) as fl_nota_fiscal_produtor_nao'),
                \DB::raw('sum(fl_nota_fiscal_produtor_sem_resposta) as fl_nota_fiscal_produtor_sem_resposta'),
                \DB::raw('sum(fl_agricultor_familiar_dap_sim) as fl_agricultor_familiar_dap_sim'),
                \DB::raw('sum(fl_agricultor_familiar_dap_nao) as fl_agricultor_familiar_dap_nao'),
                \DB::raw('sum(fl_agricultor_familiar_dap_sem_resposta) as fl_agricultor_familiar_dap_sem_resposta')
            )
            ->fromSub($queryProdutor, 'C1')
            ->first();


        /*
            Opções retornadas [null, 1, 0];

            'fl_car', //Retorna tb 'nao_se_aplica'
            'fl_ccir',
            'fl_itr',
            'fl_matricula'
        */
        $queryUnidadeProdutiva = $this->chartService->getUnidadesProdutivasJoinProdutores($data)
            ->select(
                'unidade_produtiva_id',
                'fl_car',
                \DB::raw('(fl_car = "sim") as fl_car_sim'),
                \DB::raw('(fl_car = "nao") as fl_car_nao'),
                \DB::raw('(fl_car is null) as fl_car_sem_resposta'),
                \DB::raw('(fl_car = "nao_se_aplica") as fl_car_nao_se_aplica'),

                'fl_ccir',
                \DB::raw('(fl_ccir = 1) as fl_ccir_sim'),
                \DB::raw('(fl_ccir = 0) as fl_ccir_nao'),
                \DB::raw('(fl_ccir is null) as fl_ccir_sem_resposta'),

                'fl_itr',
                \DB::raw('(fl_itr = 1) as fl_itr_sim'),
                \DB::raw('(fl_itr = 0) as fl_itr_nao'),
                \DB::raw('(fl_itr is null) as fl_itr_sem_resposta'),

                'fl_matricula',
                \DB::raw('(fl_matricula = 1) as fl_matricula_sim'),
                \DB::raw('(fl_matricula = 0) as fl_matricula_nao'),
                \DB::raw('(fl_matricula is null) as fl_matricula_sem_resposta')
            )->distinct();


        $dataUnidadeProdutiva = \DB::query()
            ->select(
                \DB::raw('sum(fl_car_sim) as fl_car_sim'),
                \DB::raw('sum(fl_car_nao) as fl_car_nao'),
                \DB::raw('sum(fl_car_sem_resposta) as fl_car_sem_resposta'),
                \DB::raw('sum(fl_car_nao_se_aplica) as fl_car_nao_se_aplica'),
                \DB::raw('sum(fl_ccir_sim) as fl_ccir_sim'),
                \DB::raw('sum(fl_ccir_nao) as fl_ccir_nao'),
                \DB::raw('sum(fl_ccir_sem_resposta) as fl_ccir_sem_resposta'),
                \DB::raw('sum(fl_itr_sim) as fl_itr_sim'),
                \DB::raw('sum(fl_itr_nao) as fl_itr_nao'),
                \DB::raw('sum(fl_itr_sem_resposta) as fl_itr_sem_resposta'),
                \DB::raw('sum(fl_matricula_sim) as fl_matricula_sim'),
                \DB::raw('sum(fl_matricula_nao) as fl_matricula_nao'),
                \DB::raw('sum(fl_matricula_sem_resposta) as fl_matricula_sem_resposta')
            )
            ->fromSub($queryUnidadeProdutiva, 'C1')
            ->first();

        // dd($dataUnidadeProdutiva->get());

        $ret = [];

        $produtores = ['fl_cnpj'=>'CNPJ', 'fl_nota_fiscal_produtor'=>'Nota Fiscal de Produtor', 'fl_agricultor_familiar_dap'=>'Declaração de Aptidão ao Pronaf (DAP)'];
        foreach ($produtores as $k=>$v) {
            $ob = ['nome' => $v, 'sim'=> @$dataProdutores->{$k.'_sim'}*1, 'nao'=> @$dataProdutores->{$k.'_nao'}*1, 'sem_resposta'=> @$dataProdutores->{$k.'_sem_resposta'}*1, 'nao_se_aplica'=>@$dataProdutores->{$k.'_nao_se_aplica'}*1];
            $ret[] = $ob;
        }

        $unidadesProdutivas = ['fl_car'=>'Cadastro Ambiental Rural (CAR)', 'fl_ccir'=>'Certificação de Cadastro do Imóvel Rural (CCIR)', 'fl_itr'=>'Imposto Territorial Rural (ITR)', 'fl_matricula'=>'Matrícula do Imóvel'];
        foreach ($unidadesProdutivas as $k=>$v) {
            $ob = ['nome' => $v, 'sim'=> @$dataUnidadeProdutiva->{$k.'_sim'}*1, 'nao'=> @$dataUnidadeProdutiva->{$k.'_nao'}*1, 'sem_resposta'=> @$dataUnidadeProdutiva->{$k.'_sem_resposta'}*1, 'nao_se_aplica'=>@$dataUnidadeProdutiva->{$k.'_nao_se_aplica'}*1];
            $ret[] = $ob;
        }

        return $ret;
    }

    function dataChart_2_5_regularizacao_ambiental(Request $request)
    {
        $orderFields = [
            'fl_cnpj',
            'fl_nota_fiscal_produtor',
            'fl_agricultor_familiar_dap',
            'fl_car',
            'fl_ccir',
            'fl_itr',
            'fl_matricula'
        ];

        $produtorFields = [
            'fl_cnpj',
            'fl_nota_fiscal_produtor',
            'fl_agricultor_familiar_dap'
        ];

        $field = $orderFields[$request->get('filter_row')];
        $value = $request->get('filter_column');

        $data = $this->service->getFilterData($request);

        $query = $this->chartService->getUnidadesProdutivasJoinProdutores($data)
            ->where($field, '=', $value)
            ->distinct();

        if (array_search($field, $produtorFields) !== FALSE) {
            $query->select('produtores.id');

            return DataTables::of(ProdutorModel::withoutGlobalScopes()->whereIn('id', $query->get()->pluck('id')))
                ->editColumn('produtor', function ($row) {
                    return "<a href='" . route('admin.core.produtor.dashboard', $row->id) . "' target='_blank'>" . $row->nome . "</a>";
                })
                ->editColumn('unidadeProdutiva', function ($row) {
                    return $row->unidadesProdutivas->pluck('nome')->join(", ");
                })
                ->editColumn('socios', function ($row) {
                    return $row->unidadesProdutivas->pluck('socios')->join(", ");
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at_formatted;
                })
                ->addColumn('actions', function ($row) {
                    $externalDashUrl = route('admin.core.produtor.dashboard', $row->id);
                    return view('backend.components.form-actions.index', compact('externalDashUrl'));
                })
                ->filterColumn('produtor', function ($query, $param) {
                    $query->where('nome', 'like', '%' . $param . '%');
                })
                ->filterColumn('unidadeProdutiva', function ($query, $param) {
                    $query->whereHas('unidadesProdutivas', function ($q) use ($param) {
                        $q->where('nome', 'like', '%' . $param . '%');
                    });
                })
                ->filterColumn('socios', function ($query, $param) {
                    $query->whereHas('unidadesProdutivas', function ($q) use ($param) {
                        $q->where('socios', 'like', '%' . $param . '%');
                    });
                })
                ->rawColumns(['produtor'])
                ->make(true);
        } else {
            $query->select('unidade_produtivas.id', 'unidade_produtivas.uid', 'unidade_produtivas.nome', 'unidade_produtivas.socios');

            return DataTables::of($query)
                ->editColumn('produtor', function ($row) {
                    return $row->produtores->pluck('nome')->join(",");
                })
                ->editColumn('unidadeProdutiva', function ($row) {
                    return "<a href='" . route('admin.core.unidade_produtiva.view', $row->id) . "' target='_blank'>" . $row->nome . "</a>";
                })
                ->editColumn('socios', function ($row) {
                    return $row->socios;
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at_formatted;
                })
                ->addColumn('actions', function ($row) {
                    $externalDashUrl = route('admin.core.unidade_produtiva.view', $row->id);
                    return view('backend.components.form-actions.index', compact('externalDashUrl'));
                })
                ->filterColumn('produtor', function ($query, $param) {
                    $query->whereHas('produtores', function ($q) use ($param) {
                        $q->where('nome', 'like', '%' . $param . '%');
                    });
                })
                ->filterColumn('unidadeProdutiva', function ($query, $param) {
                    $query->where('unidade_produtivas.nome', 'like', '%' . $param . '%');
                })
                ->filterColumn('socios', function ($query, $param) {
                    $query->where('unidade_produtivas.socios', 'like', '%' . $param . '%');
                })
                ->rawColumns(['unidadeProdutiva'])
                ->make(true);
        }



    }
}
