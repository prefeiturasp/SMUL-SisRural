<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\ChecklistStatusEnum;
use App\Enums\TipoPerguntaEnum;
use App\Helpers\General\AppHelper;
use DataTables;
use Illuminate\Http\Request;

trait Chart_3_X_PerguntasFormularios
{
    function getChart_3_X_PerguntasFormularios(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        // dd($this->getQueryChart_3_X_PerguntasFormularios($request));

        return $this->getQueryChart_3_X_PerguntasFormularios($request);
    }

    function getQueryChart_3_X_PerguntasFormularios(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $respostas = $this->getRespostasFormularios($requestData, true)
            ->whereIn('P.tipo_pergunta', [
                TipoPerguntaEnum::Semaforica,
                TipoPerguntaEnum::SemaforicaCinza,
                TipoPerguntaEnum::Binaria,
                TipoPerguntaEnum::BinariaCinza,
                TipoPerguntaEnum::SemaforicaCinza,
                TipoPerguntaEnum::MultiplaEscolha,
                TipoPerguntaEnum::EscolhaSimples,
                TipoPerguntaEnum::EscolhaSimplesPontuacao,
                TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza
            ])
            // ->addSelect(\DB::raw('count(resposta_id) as count')) //Gráfico
            ->addSelect('CC.id as checklist_categoria_id', 'CC.nome as categoria', 'R.cor')
            ->addSelect(\DB::raw('sum(IF(CSR.resposta_id = R.id, 1, 0)) as count'))
            ->groupBy('C.nome', 'CUP.checklist_id', 'CSR.pergunta_id', 'P.pergunta', 'P.tipo_pergunta', 'R.id', 'CSR.resposta', 'R.descricao', 'CC.id', 'CC.nome', 'R.cor')
            ->orderByRaw('CUP.checklist_id ASC, CC.ordem ASC, CP.ordem ASC, R.ordem ASC')
            ->get();

        $checklists = $respostas
            ->groupBy('checklist_id')
            ->map(function ($checklist) {
                return [
                    'id' => $checklist[0]->checklist_id,
                    'checklist' => $checklist[0]->checklist,
                    'categorias' => $checklist
                        ->groupBy('checklist_categoria_id')
                        ->map(function ($categoria) {
                            return [
                                'id' => $categoria[0]->checklist_categoria_id,
                                'nome' => $categoria[0]->categoria,
                                'perguntas' => array_values(
                                    $categoria
                                        ->groupBy('pergunta_id')
                                        ->map(function ($pergunta) {
                                            $respostas = array_map(function ($value) {
                                                return (array) $value;
                                            }, $pergunta->toArray());

                                            return [
                                                'id' => $pergunta[0]->pergunta_id,
                                                'pergunta' => $pergunta[0]->pergunta,
                                                'tipo_pergunta' => $pergunta[0]->tipo_pergunta,
                                                'respostas' => $respostas
                                            ];
                                        })->toArray()
                                )
                            ];
                        })
                        ->values()
                        ->toArray()
                ];
            })
            ->toArray();

        // dd(array_values($checklists));

        return array_values($checklists);
    }

    /*
    //Outra forma de fazer o retorno do LAST, mas nao sei se no nosso caso, consigo usar isso, porque preciso o retorno do getFormularios()
    SELECT m1.*
        FROM checklist_unidade_produtivas m1 LEFT JOIN checklist_unidade_produtivas m2
        ON (m1.checklist_id = m2.checklist_id AND m1.unidade_produtiva_id = m2.unidade_produtiva_id AND m1.produtor_id = m2.produtor_id AND m1.uid < m2.uid)
        where m2.uid is null and m1.checklist_id = 20;
    */
    private function getRespostasFormularios(array $requestData, $leftJoinAll = true)
    {
        // $fromSub = $this->chartService->getFormularios($requestData, false)
        $fromSub = $this->chartService->getFormulariosComFiltroPergunta($requestData)
            ->where('status', ChecklistStatusEnum::Finalizado)
            ->addSelect('checklist_unidade_produtivas.unidade_produtiva_id');

        //extrai o ultimo formulario aplicado dentro do período selecionado
        $fromSubFormularios = \DB::query()
            ->select(\DB::raw('max(uid) as uid, checklist_id, produtor_id, unidade_produtiva_id'))
            ->fromSub($fromSub, 'C1')
            ->groupBy('checklist_id', 'produtor_id', 'unidade_produtiva_id');

        // dd($fromSubFormularios->get());

        $respostas = \DB::query()
            //->addSelect('CUP.unidade_produtiva_id', 'CUP.produtor_id', 'CUP.uid as cup_uid') //DataTable
            ->addSelect('C.nome as checklist', 'CUP.checklist_id', 'CSR.pergunta_id', 'P.pergunta', 'P.tipo_pergunta', 'R.id as resposta_id', 'CSR.resposta', 'R.descricao as resposta_descricao')
            ->fromSub($fromSubFormularios, 'C2')
            ->join('checklist_unidade_produtivas as CUP', 'CUP.uid', '=', 'C2.uid')
            ->join('checklists as C', 'C.id', '=', 'CUP.checklist_id')
            ->join('checklist_snapshot_respostas as CSR', 'CSR.checklist_unidade_produtiva_id', '=', 'CUP.id')
            ->join('perguntas as P', 'P.id', '=', 'CSR.pergunta_id')
            ->join('checklist_categorias as CC', 'CC.checklist_id', '=', 'C.id')
            ->join('checklist_perguntas as CP', function ($join) {
                $join->on('CP.checklist_categoria_id', '=', 'CC.id')
                    ->on('CP.pergunta_id', '=', 'P.id');
            })
            ->whereNull('CSR.deleted_at');

        if (@$requestData['pergunta_id'] && count($requestData['pergunta_id']) > 0) {
            $respostas->whereIn('P.id', $requestData['pergunta_id']);
        }

        //leftJoinAll pega todas respostas, mesmo as que não foram respondidas no conjunto de dados
        if ($leftJoinAll) {
            $respostas->leftJoin('respostas as R', 'R.pergunta_id', '=', 'P.id');
        } else {
            $respostas->leftJoin('respostas as R', 'R.id', '=', 'CSR.resposta_id');
        }

        // $respostas->addSelect('CSR.id');
        // $respostas->orderBy('CSR.pergunta_id');
        // dd($respostas->get());

        return $respostas;
    }

    function dataChart_3_X_PerguntasFormularios(Request $request)
    {
        $requestData = array_merge(
            $this->service->getFilterData($request),
            $request->only(['filter_checklist_id', 'filter_pergunta_id', 'filter_resposta_id'])
        );

        $query = $this->getRespostasFormularios($requestData, false)
            ->addSelect('CUP.id', 'CUP.uid', 'CUP.produtor_id', 'CUP.unidade_produtiva_id', 'PP.nome as produtor', 'UP.nome as unidade_produtiva', 'socios')
            ->addSelect('P.tabela_colunas', 'P.tabela_linhas')
            ->join('produtores as PP', 'PP.id', '=', 'CUP.produtor_id')
            ->join('unidade_produtivas as UP', 'UP.id', '=', 'CUP.unidade_produtiva_id')
            ->join('produtor_unidade_produtiva as PUP', function($join) {
                $join->on('PUP.produtor_id', '=', 'CUP.produtor_id')
                    ->on('PUP.unidade_produtiva_id', '=', 'CUP.unidade_produtiva_id');
            })
            ->orderBy('P.id');

        // dd($query->get());

        if (@$requestData['filter_checklist_id']) {
            $query->where('CUP.checklist_id', $requestData['filter_checklist_id']);
        }

        if (@$requestData['filter_pergunta_id']) {
            $query->where('P.id', $requestData['filter_pergunta_id']);
        }

        if (@$requestData['filter_resposta_id']) {
            $query->where('R.id', $requestData['filter_resposta_id']);
        }

        //Se for passado apenas o "checklist", significa que o Datatable é das perguntas que não possuem "resposta_id" (Numerica, Texto, Tabela ...)
        if (@$requestData['filter_checklist_id'] && !@$requestData['filter_pergunta_id'] && !@$requestData['filter_resposta_id']) {
            $query->whereIn('P.tipo_pergunta', [
                TipoPerguntaEnum::NumericaPontuacao,
                TipoPerguntaEnum::Numerica,
                TipoPerguntaEnum::Texto,
                TipoPerguntaEnum::Tabela,
                TipoPerguntaEnum::Anexo
            ]);
        }

        return DataTables::of($query)
            ->editColumn('produtor', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor_id) . "' target='_blank'>" . $row->produtor . "</a>";
            })
            ->editColumn('resposta', function ($row) {
                if ($row->resposta_id) {
                    return $row->resposta_descricao;
                } else if ($row->tipo_pergunta == TipoPerguntaEnum::Anexo) {
                    return '<a href="' . \Storage::url('/') . $row->resposta . '" target="_blank">' . $row->resposta . '</a>';
                } else if ($row->tipo_pergunta == TipoPerguntaEnum::Tabela) {
                    if (!$row->resposta) {
                        return '';
                    }

                    $columns = explode(",", $row->tabela_colunas);
                    $lines =  $row->tabela_linhas ? explode(",", $row->tabela_linhas) : [];
                    $values = AppHelper::transpose(json_decode($row->resposta, true));

                    return AppHelper::getDataTable($columns, $lines, $values);
                } else {
                    return $row->resposta;
                }
            })
            ->addColumn('actions', function ($row) {
                // $externalDashUrl = route('admin.core.checklist_unidade_produtiva.view', $row->id);
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                return view('backend.components.form-actions.index', compact('externalDashUrl', 'row'));
            })
            ->filterColumn('uid', function ($row, $keyword) {
                $row->where('CUP.uid', '=', $keyword);
            })
            ->filterColumn('produtor_id', function ($row, $keyword) {
                $row->where('CUP.produtor_id', '=', $keyword);
            })
            ->filterColumn('produtor', function ($row, $keyword) {
                $row->where('PP.nome', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('unidade_produtiva', function ($row, $keyword) {
                $row->where('UP.nome', 'like', '%' . $keyword . '%');
            })
            ->rawColumns(['produtor', 'resposta'])
            ->make(true);
    }
}
