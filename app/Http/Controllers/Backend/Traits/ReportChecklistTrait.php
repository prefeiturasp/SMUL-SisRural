<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\ChecklistStatusEnum;
use App\Enums\TipoPontuacaoEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Http\Request;

/**
 * Referência
 */
trait ReportChecklistTrait
{
    public function checklistData(Request $request)
    {
        \Log::channel('sisrural')->info('Download CSV - Formulário');

        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva',
                ]
            );

            //Query Base
            $query = ChecklistUnidadeProdutivaModel::with(
                [
                    'produtor:id,uid,nome',
                    'unidade_produtiva:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
                    'usuario:id,first_name,last_name',
                    'usuarioFinish:id,first_name,last_name',
                    'unidade_produtiva.cidade:id,nome',
                    'unidade_produtiva.estado:id,nome',
                    'checklist:id,nome,tipo_pontuacao',
                    'plano_acao:id,nome,checklist_unidade_produtiva_id',
                    'arquivos',
                ]
            )
                // ->where('status', ChecklistStatusEnum::Finalizado) //Agora entra rascunho tb
                ->orderBy('uid');

            //whereIn quando usuário informar qual formulário ele quer extrair. Se não tiver, é TODOS (Ai não precisa executar o where)
            if (@$data['checklist_id'] && count($data['checklist_id']) > 0) {
                $query->whereIn('checklist_id', $data['checklist_id']);
            }

            //Normaliza a lista de checklist_id, porque precisa ser extraído as categorias/perguntas de cada checklist
            //Chamada para TESTE, NAO USAR EM PRODUÇÃO
            if (!@$data['checklist_id'] || count($data['checklist_id']) == 0) {
                // $data['checklist_id'] = ChecklistModel::get()->pluck('id')->toArray();
                $data['checklist_id'] = [ChecklistModel::first()->id];
            }

            if (@$data['dt_ini'] && @$data['dt_end']) {
                $query->where(function ($q) use ($data) {
                    $q->whereBetween('updated_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                    $q->orWhereBetween('finished_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                });
            }

            //Utilizado como template p/ Categorias e Perguntas
            $categorias = ChecklistModel::withoutGlobalScopes()
                ->whereIn('id', $data['checklist_id'])
                ->distinct()
                ->with(['categorias' => function ($q) {
                    $q->orderBy('ordem', 'ASC');
                }, 'categorias.perguntas'])
                ->get()
                ->pluck('categorias')
                ->collapse();

            $listCategorias = $categorias->pluck('nome', 'id')->toArray();

            //Agrupa todas as perguntas
            $perguntas = collect();
            foreach ($categorias as $categoria) {
                foreach ($categoria->perguntas as $pergunta) {
                    $perguntas[$pergunta->id] = $pergunta;
                }
            }
            $perguntas = $perguntas->pluck('pergunta', 'id')->toArray();

            //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
            $query->where(function ($query) use ($data) {
                $this->service->queryDominios($query, 'unidade_produtiva.unidadesOperacionaisNS', @$data['dominio_id']);
                $this->service->queryUnidadesOperacionais($query, 'unidade_produtiva.unidadesOperacionaisNS', @$data['unidade_operacional_id']);
                $this->service->queryEstados($query, 'unidade_produtiva', @$data['estado_id']);
                $this->service->queryCidades($query, 'unidade_produtiva', @$data['cidade_id']);
                $this->service->queryRegioes($query, 'unidade_produtiva', @$data['regiao_id']);
                $this->service->queryProdutores($query, 'unidade_produtiva.produtores', @$data['produtor_id']);
                $this->service->queryUnidadesProdutivas($query, 'unidade_produtiva', @$data['unidade_produtiva_id']);
            });

            //Atuação
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoProdutor($query, 'unidade_produtiva.produtores', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoUnidadeProdutiva($query, 'unidade_produtiva', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoCadernoDeCampo($query, 'unidade_produtiva.produtores.cadernos', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoFormulario($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoPlanoAcao($query, 'unidade_produtiva.planoAcoes', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });

            //Filtros Adicionais
            $this->service->queryChecklistsAllStatus($query, 'unidade_produtiva.checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']); //Especificação pediu p/ retirar o filtro daqui, @$data['dt_ini'], @$data['dt_end']
            $this->service->queryCertificacoes($query, 'unidade_produtiva', 'certificacoes', @$data['certificacao_id']);
            $this->service->querySoloCategoria($query, 'unidade_produtiva.solosCategoria', 'unidade_produtiva.caracterizacoes', @$data['solo_categoria_id']);
            $this->service->queryArea($query, 'unidade_produtiva', @$data['area']);
            $this->service->queryGenero($query, 'unidade_produtiva.produtores', @$data['genero_id']);
            $this->service->queryStatusUnidadeProdutiva($query, 'unidade_produtiva', @$data['status_unidade_produtiva']);

            //CSV
            return $this->downloadCsv(
                $this->filename('formularios'),
                $query,
                array_merge(
                    [
                        'ID Formulário',
                        'Criado em',
                        'Atualizado em',
                        'Finalizado em',
                        'ID Produtor/a',
                        'Produtor/a',
                        'Coproprietários/as',
                        'ID Unidade Produtiva',
                        'Unidade Produtiva',
                        'Latitude',
                        'Longitude',
                        'Cidade',
                        'Estado',
                        'Técnico/a',
                        'Técnico/a que Finalizou',
                        'Template do Formulário',
                        'Status',
                        'Possui plano de ação?',
                        'Galeria', //Q
                    ],
                    $perguntas,
                    [
                        'Tipo de Pontuação',
                        'Pontuação final',
                        'Verde',
                        'Amarelo',
                        'Vermelho',
                        'Não se aplica',
                        'Numérica/Escolha simples',
                        'Pontuação realizada',
                    ],
                    array_map(
                        function ($v) {
                            return 'Pontuação final - ' . $v;
                        },
                        $categorias->pluck('nome')->toArray()
                    )
                ),
                function ($handle, $v) use ($perguntas, $listCategorias) {
                    $respostas = $v->getRespostas();

                    $columnsRespostas = [];
                    foreach ($perguntas as $kPergunta => $vPergunta) {
                        $columnsRespostas[] = $this->privateData(@$respostas[$kPergunta]['resposta']);
                    }

                    $score = $v->score();

                    $columnsScoreCategorias = [];
                    foreach ($listCategorias as $kCat => $vCat) {
                        $columnsScoreCategorias[] = $this->privateData(@$score['categorias'][$kCat]['pontuacaoPercentual']);
                    }

                    fputcsv(
                        $handle,
                        $this->removeBreakLine(
                            array_merge(
                                [
                                    $v->uid, //id, //'ID Formulário',
                                    $v->created_at_formatted, //'Criado em',
                                    $v->updated_at_formatted, //'Atualizado em',
                                    $v->finished_at_formatted, //'Finalizado em',
                                    $v->produtor->uid, //produtor_id, //'ID Produtor',
                                    $this->privateData($v->produtor->nome), //'Produtor',
                                    $this->privateData($v->unidade_produtiva->socios), //'Coproprietários',
                                    $v->unidade_produtiva->uid, //unidade_produtiva_id, //'ID Unidade Produtiva',
                                    $this->privateData($v->unidade_produtiva->nome), //'Unidade Produtiva',
                                    $this->privateData($this->scapeInt($v->unidade_produtiva->lat)), //'Latitude',
                                    $this->privateData($this->scapeInt($v->unidade_produtiva->lng)), //'Longitude',
                                    $v->unidade_produtiva->cidade->nome, //'Cidade',
                                    $v->unidade_produtiva->estado->nome, //'Estado',
                                    $this->privateData($v->usuario->first_name . ' ' . $v->usuario->last_name), //'Técnico',
                                    $this->privateData($v->usuarioFinish ? $v->usuarioFinish->first_name . ' ' . $v->usuarioFinish->last_name : ''), //'Técnico que Finalizou',
                                    $v->checklist->nome, //'Template do Formulário'
                                    $v->status, //'Status',
                                    count($v->plano_acao) > 0 ? 'Sim' : 'Não', //'Possui plano de ação?',
                                    $this->privateData($v->arquivos->pluck('url')->implode(",")) //'Galeria',
                                ],
                                $columnsRespostas, //T - BM
                                [
                                    TipoPontuacaoEnum::toSelectArray()[$v->checklist->tipo_pontuacao], //'Tipo de Pontuação',
                                    $score['pontuacaoFinal'], //'Pontuação final',
                                    $score['coresRespostas']['verde'], //'Verde',
                                    $score['coresRespostas']['amarelo'], //'Amarelo',
                                    $score['coresRespostas']['vermelho'], //'Vermelho',
                                    $score['coresRespostas']['cinza'], //'Não se aplica',
                                    $score['coresRespostas']['numerica'], //'Numérica/Escolha simples',
                                    $score['pontuacao'], //'Pontuação realizada',
                                ],
                                $columnsScoreCategorias //BV - CF
                            )
                        ),
                        ';'
                    );
                }
            );
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
