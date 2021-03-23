<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Models\Auth\User;
use DataTables;
use Illuminate\Http\Request;

/**
 * - Cadernos com atuação no período
 * - Formulários com atuação no período
 * - PDAS com atuação no período (os 4 tipos)
 * - Unidades Produtivas com cadastro no periodo
 *
 * - Agrupar por user_id todas as consultas e fazer o count dos técnicos
 */
trait Chart_1_6_TecnicosAtivos
{
    function getChart_1_6_TecnicosAtivos(Request $request)
    {
        $query = $this->getQueryChart_1_6_TecnicosAtivos($request);

        $data = $query
            ->select('first_name', 'last_name')
            ->groupBy('first_name', 'last_name')
            ->get()
            ->count();

        return $data;
    }

    function dataChart_1_6_TecnicosAtivos(Request $request)
    {
        $query = $this->getQueryChart_1_6_TecnicosAtivos($request)
            ->select('first_name', 'last_name')
            ->groupBy('first_name', 'last_name')
            ->distinct();

        return DataTables::of($query->get(['first_name', 'last_name']))
            ->addColumn('name', function ($row) {
                return $row->full_name;
            })
            /*->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })*/
            ->make(true);
    }

    function getQueryChart_1_6_TecnicosAtivos(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!$this->service->existFilterAtuacao($requestData)) {
            $requestData['atuacao_tecnico_id'] = $this->service->getAllTecnicosAtuacao();
        }

        $allTecnicos = $this->service->getTecnicos(@$requestData['atuacao_dominio_id'], @$requestData['atuacao_unidade_operacional_id'], @$requestData['atuacao_tecnico_id']);

        $userProdutores = $this->chartService->getProdutores($requestData)
            ->select('produtores.user_id')
            ->distinct()
            ->get()
            ->pluck('user_id')
            ->toArray();

        $userCadernos = $this->chartService->getCadernos($requestData)
            ->select('cadernos.user_id', 'cadernos.finish_user_id')
            ->distinct()
            ->get()
            ->reduce(function ($carry, $item) {
                return array_merge($carry ? $carry : [], [$item['user_id']], [$item['finish_user_id']]);
            });

        $userCadernos = $userCadernos ? $userCadernos : [];
        $userCadernos = array_intersect($userCadernos, $allTecnicos);

        $userFormularios = $this->chartService->getFormularios($requestData)
            ->select('checklist_unidade_produtivas.user_id', 'checklist_unidade_produtivas.finish_user_id')
            ->distinct()
            ->get()
            ->reduce(function ($carry, $item) {
                return array_merge($carry ? $carry : [], [$item['user_id']], [$item['finish_user_id']]);
            });

        $userFormularios = $userFormularios ? $userFormularios : [];
        $userFormularios = array_intersect($userFormularios, $allTecnicos);

        $userPdasCriados = $this->chartService->getPdasCreated($requestData)
            ->select('plano_acoes.user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $userPdaHistoricos = $this->chartService->getPdasHistoricos($requestData)
            ->select('plano_acao_historicos.user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $userPdaItens = $this->chartService->getPdasAcoes($requestData)
            ->select('plano_acoes.user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $userPdaItensHistorico = $this->chartService->getPdasAcoesHistoricos($requestData)
            ->select('plano_acao_item_historicos.user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $userIds = collect(array_merge($userProdutores, $userCadernos, $userFormularios, $userPdasCriados, $userPdaHistoricos, $userPdaItensHistorico, $userPdaItens, $userPdaItensHistorico))
            ->unique()
            ->filter()
            ->toArray();

        return User::withoutGlobalScopes()->withTrashed()->whereIn('id', $userIds);
    }
}
