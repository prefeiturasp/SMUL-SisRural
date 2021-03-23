<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\ChecklistStatusEnum;
use App\Enums\TipoPontuacaoEnum;
use Illuminate\Http\Request;

trait Chart_3_7_PontuacoesFinais
{
    function getChart_3_7_PontuacoesFinais(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        $list = $this->getQueryChart_3_7_PontuacoesFinais($request)
            ->get();

        $ret = $list->groupBy('checklist_id')
            ->map(function($v) {
                return $v->map(function($vv) {
                    $vv->pontuacao = str_replace("%", "", $vv->pontuacao)*1;
                    return $vv;
                });
            })
            ->map(function($v) {
                $min = $v->min('pontuacao');
                $max = $v->max('pontuacao');
                $media = round($v->sum('pontuacao')/$v->count(), 2);
                return [
                    'formulario'=>$v[0]->formulario,
                    'minimo'=>$min*1,
                    'maximo'=>$max*1,
                    'media' =>$media*1,
                    'total' => count($v)
                ];
            })->values();

        return $ret;
    }

    function getQueryChart_3_7_PontuacoesFinais(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        //Retorno para o front: item.formulario, item.media, item.maximo, item.minimo

        $fromSub = $this->chartService->getFormulariosComFiltroPergunta($requestData)
            ->where('status', ChecklistStatusEnum::Finalizado)
            ->whereHas('checklist', function($q) {
                $q->where('tipo_pontuacao', '!=', TipoPontuacaoEnum::SemPontuacao);
            })
            ->addSelect('checklist_unidade_produtivas.unidade_produtiva_id');

        //extrai o ultimo formulario aplicado dentro do período selecionado
        $fromSubFormularios = \DB::query()
            ->select(\DB::raw('max(uid) as uid, checklist_id, produtor_id, unidade_produtiva_id'))
            ->fromSub($fromSub, 'C1')
            ->groupBy('checklist_id', 'produtor_id', 'unidade_produtiva_id');

        //pega a listagem de todos p/ extrair a pontuacao
        $list = \DB::query()
            ->addSelect('C.nome as formulario', 'CUP.uid', 'CUP.checklist_id', 'CUP.unidade_produtiva_id', 'CUP.produtor_id', 'CUP.pontuacaoFinal as pontuacao', 'P.nome', 'UP.nome', 'UP.socios')
            ->fromSub($fromSubFormularios, 'C2')
            ->join('checklist_unidade_produtivas as CUP', 'CUP.uid', '=', 'C2.uid')
            ->join('checklists as C', 'C.id', '=', 'CUP.checklist_id')
            ->join('produtores as P', 'P.id', '=', 'CUP.produtor_id')
            ->join('unidade_produtivas as UP', 'UP.id', '=', 'CUP.unidade_produtiva_id');

        return $list;
    }

}
