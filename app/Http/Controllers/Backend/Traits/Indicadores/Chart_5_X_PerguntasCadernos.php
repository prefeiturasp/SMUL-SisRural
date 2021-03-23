<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\TemplateModel;
use DataTables;
use Illuminate\Http\Request;

trait Chart_5_X_PerguntasCadernos
{
    function getChart_5_X_PerguntasCadernos(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!@$requestData['dt_ini'] || !@$requestData['dt_end']) {
            return '-';
        }

        return $this->getQueryChart_5_X_PerguntasCadernos($request);
    }

    function getQueryChart_5_X_PerguntasCadernos(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        $respostas = $this->getRespostasCadernos($requestData, true)
            ->whereIn('TP.tipo', [
                TipoTemplatePerguntaEnum::Check,
                TipoTemplatePerguntaEnum::MultipleCheck,
            ])
            // ->addSelect(\DB::raw('count(resposta_id) as count')) //Gráfico
            ->addSelect(\DB::raw('sum(IF(CRC.template_resposta_id = TR.id, 1, 0)) as count'))
            ->groupBy('T.nome', 'C.template_id', 'CRC.template_pergunta_id', 'TP.pergunta', 'TP.tipo', 'TR.id', 'CRC.resposta', 'TR.descricao')
            ->orderByRaw('C.template_id ASC, TPT.ordem ASC, TR.ordem ASC')
            ->get();

        //Não retorna caderno caso todo ele seja do tipo Texto //ver o que fazer
        //dd($respostas);

        $cadernos = $respostas
            ->groupBy('template_id')
            ->map(function ($caderno) {
                return [
                    'id' => $caderno[0]->template_id,
                    'caderno' => $caderno[0]->caderno,
                    'perguntas' => array_values(
                        $caderno
                            ->groupBy('template_pergunta_id')
                            ->map(function ($pergunta) {
                                $respostas = array_map(function ($value) {
                                    return (array) $value;
                                }, $pergunta->toArray());

                                return [
                                    'id' => $pergunta[0]->template_pergunta_id,
                                    'pergunta' => $pergunta[0]->pergunta,
                                    'tipo_pergunta' => $pergunta[0]->tipo,
                                    'respostas' => $respostas
                                ];
                            })->toArray()
                    )
                ];
            })
            ->toArray();


        //Tratamento especial para caso o caderno de campo não tenha nenhuma pergunta/respota do tipo Check/MultipleCheck
        //Sem esse tratamento não mostrava a Datatable com a lista de respostas (porque não retornava nenhum 'template_id')
        $checkListCadernos = $this->getRespostasCadernos($requestData, true)
            ->select('T.nome as caderno', 'T.id as template_id')
            ->distinct()
            ->get();

        $newList = $checkListCadernos->filter(function($c) use ($cadernos) {
            return !@$cadernos[$c->template_id];
        });

        //Adiciona os cadernos que tinham apenas perguntas do tipo texto
        foreach ($newList as $v) {
            $cadernos[$v->template_id] = [
                'id' => $v->template_id,
                'caderno' => $v->caderno,
                'perguntas' => []
            ];
        }


        //Retorna as perguntas do tipo texto para cada template de caderno (Isso será utilizado como filtro)
        $cadernos = array_map(function($v) {
            $perguntasTexto = TemplateModel::withoutGlobalScopes()->with(['perguntas' => function($q) {
                $q->where('tipo', TipoTemplatePerguntaEnum::Text);
            }])
                ->where('id', $v['id'])
                ->first()
                ->perguntas
                ->pluck('pergunta', 'id')
                ->toArray();

            $v['perguntasTexto'] = $perguntasTexto;

            return $v;
        }, $cadernos);

        return array_values($cadernos);
    }

    private function getRespostasCadernos(array $requestData, $leftJoinAll = true)
    {
        $fromSub = $this->chartService->getCadernos($requestData)
            ->where('status', CadernoStatusEnum::Finalizado)
            ->whereBetween('cadernos.finished_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']))
            ->addSelect('cadernos.unidade_produtiva_id', 'cadernos.template_id');

        // dd($fromSub->get());

        //extrai o ultimo formulario aplicado dentro do período selecionado
        $fromSubCadernos = \DB::query()
            ->select(\DB::raw('max(uid) as uid, template_id, produtor_id, unidade_produtiva_id'))
            ->fromSub($fromSub, 'C1')
            ->groupBy('template_id', 'produtor_id', 'unidade_produtiva_id');

        // dd($fromSubCadernos->get());

        $respostas = \DB::query()
            //->addSelect('CUP.unidade_produtiva_id', 'CUP.produtor_id', 'CUP.uid as cup_uid') //DataTable
            ->addSelect('T.nome as caderno', 'C.template_id', 'CRC.template_pergunta_id', 'TP.pergunta', 'TP.tipo', 'TR.id as resposta_id', 'CRC.resposta', 'TR.descricao as resposta_descricao')
            ->fromSub($fromSubCadernos, 'C2')
            ->join('cadernos as C', 'C.uid', '=', 'C2.uid')
            ->join('templates as T', 'T.id', '=', 'C.template_id')
            ->join('caderno_resposta_caderno as CRC', 'CRC.caderno_id', '=', 'C.id')
            ->join('template_perguntas as TP', 'TP.id', '=', 'CRC.template_pergunta_id')
            ->leftJoin('template_pergunta_templates as TPT', function ($join) {
                $join->on('TPT.template_id', '=', 'T.id')
                    ->on('TPT.template_pergunta_id', '=', 'TP.id');
            });

        //leftJoinAll pega todas respostas, mesmo as que não foram respondidas no conjunto de dados
        if ($leftJoinAll) {
            $respostas->leftJoin('template_respostas as TR', 'TR.template_pergunta_id', '=', 'TP.id');
        } else {
            $respostas->leftJoin('template_respostas as TR', 'TR.id', '=', 'CRC.template_resposta_id');
        }

        // dd($respostas->get());

        return $respostas;
    }

    function dataChart_5_X_PerguntasCadernos(Request $request)
    {
        $requestData = array_merge(
            $this->service->getFilterData($request),
            $request->only(['filter_template_id', 'filter_pergunta_id', 'filter_resposta_id'])
        );

        $query = $this->getRespostasCadernos($requestData, false)
            ->addSelect('C.id', 'C.uid', 'C.produtor_id', 'C.unidade_produtiva_id', 'PP.uid as produtor_uid', 'PP.nome as produtor', 'UP.nome as unidade_produtiva', 'socios')
            ->join('produtores as PP', 'PP.id', '=', 'C.produtor_id')
            ->join('unidade_produtivas as UP', 'UP.id', '=', 'C.unidade_produtiva_id')
            ->orderBy('TP.id');

        if (@$requestData['filter_template_id']) {
            $query->where('C.template_id', $requestData['filter_template_id']);
        }

        if (@$requestData['filter_pergunta_id']) {
            $query->where('TP.id', $requestData['filter_pergunta_id']);
        }

        if (@$requestData['filter_resposta_id']) {
            $query->where('TR.id', $requestData['filter_resposta_id']);
        }

        //Se for passado apenas o "checklist", significa que o Datatable é das perguntas que não possuem "resposta_id" (Numerica, Texto, Tabela ...)
        if (@$requestData['filter_template_id'] && !@$requestData['filter_pergunta_id'] && !@$requestData['filter_resposta_id']) {
            $query->whereIn('TP.tipo', [
                TipoTemplatePerguntaEnum::Text,
            ]);
        }

        return DataTables::of($query)
            ->editColumn('produtor', function ($row) {
                return "<a href='" . route('admin.core.produtor.dashboard', $row->produtor_id) . "' target='_blank'>" . $row->produtor . "</a>";
            })
            ->editColumn('resposta', function ($row) {
                return $row->resposta_id ? $row->resposta_descricao : $row->resposta;
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                return view('backend.components.form-actions.index', compact('externalDashUrl', 'row'));
            })
            ->filterColumn('uid', function ($row, $keyword) {
                $row->where('C.uid', '=', $keyword);
            })
            ->filterColumn('produtor_uid', function ($row, $keyword) {
                $row->where('PP.uid', '=', $keyword);
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
