<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use Illuminate\Http\Request;

trait Chart_1_9_PlanoAcoes_Acoes
{
    function getChart_1_9_PlanoAcoes_Acoes(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $dataIndividuais = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Individuais($request)
            ->select(\DB::raw('count(plano_acao_itens.status) as total_status, plano_acao_itens.status'))
            ->groupBy('plano_acao_itens.status')
            ->get();

        $dataIndividuaisUpas = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Individuais($request)
            ->select(\DB::raw('count(distinct unidade_produtiva_id) as total'))
            ->groupBy('unidade_produtiva_id')
            ->first();

        $individuais = $this->makeAcoes($dataIndividuais);
        $individuais['nome'] = 'Individuais';
        if($dataIndividuaisUpas){
          $individuais['upas'] = $dataIndividuaisUpas['total'];
        } else {
          $individuais['upas'] = NULL;
        }


        $dataFormularios = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Formularios($request)
            ->select(\DB::raw('count(plano_acao_itens.status) as total_status, plano_acao_itens.status, plano_acao_prioridade, checklists.nome'))
            ->groupBy('plano_acao_itens.status', 'plano_acao_prioridade', 'checklists.nome')
            ->get()
            ->groupBy('nome');

        /*
        dd(
            \DB::query()
                ->select(\DB::raw('count(distinct unidade_produtiva_id) as total, nome'))
                ->fromSub(
                    $this->getQueryChart_1_9_PlanoAcoes_Acoes_Formularios($request)
                        ->select('plano_acoes.unidade_produtiva_id', 'checklists.nome'),
                    'formularios'
                )
                ->groupBy('nome')
                ->get()
        );
        */

        $dataFormulariosUpas = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Formularios($request)
            ->select(\DB::raw('count(distinct plano_acoes.unidade_produtiva_id) as total, checklists.nome'))
            ->groupBy('checklists.nome')
            ->get()
            ->groupBy('nome');

        $formularios = [];
        foreach ($dataFormularios as $k => $v) {
            $ret = $this->makeAcoes($v);
            $ret['nome'] = $k;
            $ret['upas'] = @$dataFormulariosUpas[$k]->first()['total'];
            $formularios[] = $ret;
        }

        $dataColetivo = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Coletivo($request)
            ->select(\DB::raw('count(plano_acao_itens.status) as total_status, plano_acao_itens.status, plano_acoes.nome'))
            ->groupBy('checklist_unidade_produtiva_id', 'plano_acao_itens.status', 'plano_acoes.nome')
            ->get()
            ->groupBy('nome');


        $dataColetivoUpas = $this->getQueryChart_1_9_PlanoAcoes_Acoes_Coletivo($request)
            ->select(\DB::raw('count(distinct plano_acoes.unidade_produtiva_id) as total, plano_acoes.nome'))
            ->groupBy('plano_acoes.nome')
            ->get()
            ->groupBy('nome');

        $coletivos = [];
        foreach ($dataColetivo as $k => $v) {
            $ret = $this->makeAcoes($v);
            $ret['nome'] = $k;
            $ret['upas'] = @$dataColetivoUpas[$k]->first()['total'];
            $coletivos[] = $ret;
        }

        //Retorna vazio caso não tenha registro p/ retornar
        if (count($coletivos) == 0 && count($formularios) == 0 && ($individuais['nao_iniciado'] == 0 && $individuais['em_andamento'] == 0 && $individuais['concluido'] == 0 && $individuais['cancelado'] == 0)) {
            return null;
        }

        return array_merge([$individuais], $formularios, $coletivos);
    }

    private function makeAcoes($data)
    {
        $ret = [];

        foreach ($data as $k => $v) {
            $ret[$v['status']] = $v['total_status'];
        }

        foreach (PlanoAcaoItemStatusEnum::getValues() as $v) {
            if (@!$ret[$v]) {
                $ret[$v] = 0;
            }
        }

        return $ret;
    }

    function getQueryChart_1_9_PlanoAcoes_Acoes_Individuais(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getPdasAtualizacoes($requestData)
            ->where('plano_acoes.fl_coletivo', '=', 0)
            ->whereNull('checklist_unidade_produtiva_id')
            ->join('plano_acao_itens', 'plano_acao_itens.plano_acao_id', 'plano_acoes.id');

        return $query;
    }

    function getQueryChart_1_9_PlanoAcoes_Acoes_Formularios(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getPdasAtualizacoes($requestData)
            ->whereNotNull('checklist_unidade_produtiva_id')
            ->join('plano_acao_itens', 'plano_acao_itens.plano_acao_id', 'plano_acoes.id')
            ->join('checklist_perguntas', 'checklist_perguntas.id', 'plano_acao_itens.checklist_pergunta_id')
            ->join('checklist_unidade_produtivas', 'checklist_unidade_produtivas.id', 'plano_acoes.checklist_unidade_produtiva_id')
            ->join('checklists', 'checklists.id', 'checklist_unidade_produtivas.checklist_id')
            ->where('plano_acao_prioridade', PlanoAcaoPrioridadeEnum::PriorizacaoTecnica);

        return $query;
    }

    function getQueryChart_1_9_PlanoAcoes_Acoes_Coletivo(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $query = $this->chartService->getPdasAtualizacoes($requestData)
            ->where('plano_acoes.fl_coletivo', 1)
            ->whereNotNull('unidade_produtiva_id')
            ->join('plano_acao_itens', 'plano_acao_itens.plano_acao_id', 'plano_acoes.id');

        return $query;
    }
}
