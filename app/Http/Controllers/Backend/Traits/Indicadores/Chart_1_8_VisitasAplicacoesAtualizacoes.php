<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Helpers\General\AppHelper;
use DataTables;
use Illuminate\Http\Request;

trait Chart_1_8_VisitasAplicacoesAtualizacoes
{
    function getChart_1_8_VisitasAplicacoesAtualizacoes(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $produtores = $this->chartService->getProdutores($requestData)
            ->whereBetween('produtores.created_at', AppHelper::dateBetween(@$requestData['dt_ini'], @$requestData['dt_end']));

        $dataProdutores = $produtores->select(
            \DB::raw('count(DISTINCT produtores.id) as produtores_count, DATE_FORMAT(produtores.created_at, "%m/%Y") as date, DATE_FORMAT(produtores.created_at, "%Y%m") as date_sort')
        )
            ->groupBy('date', 'date_sort')
            ->get(['date', 'date_sort', 'produtores_count'])
            ->toArray();

        $cadernos = $this->chartService->getCadernos($requestData)
            ->where('cadernos.status', CadernoStatusEnum::Finalizado)
            ->whereBetween('cadernos.finished_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']));

        //Total de Cadernos Finalizados agrupados por Mês
        $dataCadernos = $cadernos->select(\DB::raw('count(cadernos.id) as cadernos_count, DATE_FORMAT(finished_at, "%m/%Y") as date, DATE_FORMAT(finished_at, "%Y%m") as date_sort'))
            ->groupBy('date', 'date_sort')
            ->get(['date', 'date_sort', 'cadernos_count'])
            ->toArray();

        $formularios = $this->chartService->getFormularios($requestData);

        //Total de Formulários Finalizados agrupados por Mês
        $dataFormularios = $formularios->select(\DB::raw('count(checklist_unidade_produtivas.id) as formularios_count, DATE_FORMAT(updated_at, "%m/%Y") as date, DATE_FORMAT(updated_at, "%Y%m") as date_sort'))
            ->groupBy('date', 'date_sort')
            ->get(['date', 'date_sort', 'formularios_count'])
            ->toArray();

        //Planos de Ações criados agrupados por Mês
        $pdasCriados = $this->chartService->getPdasCreated($requestData)
            ->select(\DB::raw('plano_acoes.id as id, plano_acoes.uid as vid, DATE_FORMAT(plano_acoes.created_at, "%m/%Y") as date, DATE_FORMAT(plano_acoes.created_at, "%Y%m") as date_sort'));

        // dd($pdasCriados)

        //Planos de Ações - Acompanhamento, agrupados por Mês
        $pdaHistoricos = $this->chartService->getPdasHistoricos($requestData)
            ->select(\DB::raw('plano_acoes.id as id, plano_acao_historicos.uid as vid, DATE_FORMAT(plano_acao_historicos.created_at, "%m/%Y") as date, DATE_FORMAT(plano_acao_historicos.created_at, "%Y%m") as date_sort')); //->distinct();

        //Planos de Ações - Ações, agrupados por Mês
        $pdaItens = $this->chartService->getPdasAcoes($requestData)
            ->select(\DB::raw('plano_acoes.id as id, plano_acao_itens.uid as vid, DATE_FORMAT(plano_acao_itens.created_at, "%m/%Y") as date, DATE_FORMAT(plano_acao_itens.created_at, "%Y%m") as date_sort'));

        //Planos de Ações - Ações - Acompanhamento, agrupados por Mês
        $pdaItensHistorico = $this->chartService->getPdasAcoesHistoricos($requestData)
            ->select(\DB::raw('plano_acoes.id as id, plano_acao_item_historicos.uid as vid, DATE_FORMAT(plano_acao_item_historicos.created_at, "%m/%Y") as date, DATE_FORMAT(plano_acao_item_historicos.created_at, "%Y%m") as date_sort'));

        //Após extrair os planos de ações (PDA, Ação, Acompanhamento, Ação - Acompanhamento) alterados/atualizados/criados por mês deve ser "unido" os valores p/ contabilizar
        $unionPdas = $pdasCriados
            ->union($pdaHistoricos)
            ->union($pdaItens)
            ->union($pdaItensHistorico);

        // dd($pdasCriados->get(), $pdaHistoricos->get(), $pdaItens->get(), $pdaItensHistorico->get());

        //Após a união, é normalizado os valores, porque não queremos a contagem TOTAL, queremos apenas se teve alguma interação no Mês ou não
        //Com isso é extraído o date e date_sort
        $dataPdas = \DB::table(\DB::raw('(' . $unionPdas->toSql() . ') as a'))
            ->setBindings($unionPdas->getBindings())
            ->selectRaw('id, date, date_sort')
            ->groupBy('id', 'date', 'date_sort');

        //Agora nós fazemos o count por Plano de Ação
        $dataPdasDistinct = \DB::table(\DB::raw('(' . $dataPdas->toSql() . ') as a'))
            ->setBindings($dataPdas->getBindings())
            ->distinct()
            ->select(\DB::raw('count(id) as pdas_count, date, date_sort'))
            ->groupBy('date', 'date_sort')
            ->get()
            ->toArray();

        //Casting (o retorno do item no DB::table vem como objeto)
        $dataPdasDistinct = array_map(function ($v) {
            return (array) $v;
        }, $dataPdasDistinct);

        $values = array_merge($dataCadernos, $dataFormularios, $dataPdasDistinct, $dataProdutores);

        $listMonths = $this->service->rangeDates($requestData['dt_ini'], $requestData['dt_end']);

        $values = array_merge($values, $listMonths);

        //dd($values);

        $ret = collect($values)
            ->groupBy('date_sort')
            ->sortKeys()
            ->map(function ($v) {
                return $v->collapse();
            })
            ->map(function ($v) {
                if (@!$v['cadernos_count']) {
                    $v['cadernos_count'] = 0;
                }

                if (@!$v['formularios_count']) {
                    $v['formularios_count'] = 0;
                }

                if (@!$v['pdas_count']) {
                    $v['pdas_count'] = 0;
                }

                if (@!$v['produtores_count']) {
                    $v['produtores_count'] = 0;
                }

                return $v;
            })
            ->toArray();

        // dd(array_values($ret));

        return array_values($ret);
    }

    function dataChart_1_8_Produtores(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $periodBetween = $this->service->period($request->get('period'), $requestData['dt_ini'], $requestData['dt_end']);

        $query = $this->chartService->getProdutores($requestData)
            ->addSelect("produtores.id as id")
            ->whereBetween('produtores.created_at', AppHelper::dateBetween(@$requestData['dt_ini'], @$requestData['dt_end'])) //Seleção do período no filtro geral
            ->whereBetween('produtores.created_at', $periodBetween); //Seleção do período no Gráfico

        return DataTables::of($query)
            ->editColumn('nome', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor_id) . "' target='_blank'>" . $row->nome . "</a>";
            })
            ->editColumn('unidadeProdutiva', function ($row) {
                return  @$row->unidadesProdutivasNS->pluck("nome")->join(", ");
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->filterColumn('unidadeProdutiva', function ($query, $param) {
                $query->where('unidade_produtivas.nome', 'like', '%' . $param . '%');
            })
            ->rawColumns(['nome'])
            ->make(true);
    }

    function dataChart_1_8_Cadernos(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $periodBetween = $this->service->period($request->get('period'), $requestData['dt_ini'], $requestData['dt_end']);

        $query = $this->chartService->getCadernos($requestData)
            ->with(['datatable_unidade_produtiva:id,nome'])
            ->addSelect('cadernos.unidade_produtiva_id', 'cadernos.created_at', 'cadernos.finished_at')
            ->whereBetween('cadernos.finished_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end'])) //Range do filtro padrão
            ->whereBetween('cadernos.finished_at', $periodBetween) //Range da seleção, clique do gráfico
            ->where('cadernos.status', CadernoStatusEnum::Finalizado);

        return DataTables::of($query)
            ->editColumn('produtor.nome', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor->id) . "' target='_blank'>" . $row->produtor->nome . "</a>";
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.cadernos.view', $row->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->addColumn('finished_at_formatted', function ($row) {
                return $row->finished_at_formatted;
            })
            ->rawColumns(['produtor.nome'])
            ->make(true);
    }

    function dataChart_1_8_Formularios(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $periodBetween = $this->service->period($request->get('period'), $requestData['dt_ini'], $requestData['dt_end']);

        $query = $this->chartService->getFormularios($requestData)
            ->addSelect('checklist_unidade_produtivas.unidade_produtiva_id', 'checklist_unidade_produtivas.created_at', 'checklist_unidade_produtivas.finished_at')
            ->whereBetween('checklist_unidade_produtivas.updated_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end'])) //Range do filtro padrão
            ->whereBetween('checklist_unidade_produtivas.updated_at', $periodBetween); //Range da seleção, clique do gráfico

        return DataTables::of($query)
            ->editColumn('produtor.nome', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor->id) . "' target='_blank'>" . $row->produtor->nome . "</a>";
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->addColumn('finished_at_formatted', function ($row) {
                return $row->finished_at_formatted;
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.checklist_unidade_produtiva.view', $row->id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->rawColumns(['produtor.nome'])
            ->make(true);
    }

    function dataChart_1_8_PlanoAcoes(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $periodBetween = $this->service->period($request->get('period'), $requestData['dt_ini'], $requestData['dt_end']);
        $dt_ini = $periodBetween[0];
        $dt_end = $periodBetween[1];

        $query = $this->chartService->getPdasAtualizacoesAcompanhamentos($requestData, true)
            ->with(['produtor:id,nome']);

        $query->where(function ($q) use ($dt_ini, $dt_end) {
            $q->whereBetween('plano_acoes.created_at', AppHelper::dateBetween($dt_ini, $dt_end));
            $q->orWhereBetween('plano_acoes.updated_at', AppHelper::dateBetween($dt_ini, $dt_end));

            $q->orWhereHas('historicos', function ($qq) use ($dt_ini, $dt_end) {
                $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
            });

            $q->orWhereHas('itens', function ($qq) use ($dt_ini, $dt_end) {
                $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
            });

            $q->orWhereHas('itens.historicos', function ($qq) use ($dt_ini, $dt_end) {
                $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
            });
        });

        return DataTables::of($query)
            ->editColumn('produtor.nome', function ($row) {
                if (!$row->produtor) {
                    return '-';
                }

                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor->id) . "' target='_blank'>" . $row->produtor->nome . "</a>";
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->editColumn('unidadeProdutiva', function ($row) {
                if (!$row->unidadeProdutivaScoped) {
                    return '-';
                }

                return $row->unidadeProdutivaScoped->nome;
            })
            ->editColumn('fl_coletivo', function ($row) {
                return boolean_sim_nao($row->fl_coletivo);
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.plano_acao.view', $row->id);

                if ($row->fl_coletivo && @!$row->plano_acao_coletivo_id) {
                    $externalDashUrl = route('admin.core.plano_acao_coletivo.view', $row->id);
                } else if ($row->fl_coletivo && $row->plano_acao_coletivo_id) {
                    $externalDashUrl = route('admin.core.plano_acao_coletivo.view', $row->plano_acao_coletivo_id);
                }

                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })->filterColumn('unidadeProdutiva', function ($query, $param) {
                $query->whereHas('unidadeProdutivaScoped', function ($q) use ($param) {
                    $q->where('nome', 'like', '%' . $param . '%');
                });
            })
            ->rawColumns(['produtor.nome'])
            ->make(true);
    }
}
