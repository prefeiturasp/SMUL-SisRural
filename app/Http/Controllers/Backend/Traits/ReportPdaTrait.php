<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\PlanoAcaoModel;
use Illuminate\Http\Request;

trait ReportPdaTrait
{
    public function pdaData(Request $request)
    {
        \Log::channel('sisrural')->info('Download CSV - Plano de Ação');

        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva',
                    'type_pda'
                ]
            );

            //Query Base
            $query = PlanoAcaoModel::with(
                [
                    'produtor:id,uid,nome',
                    'unidadeProdutivaScoped:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
                    'unidadeProdutivaScoped.cidade:id,nome',
                    'unidadeProdutivaScoped.estado:id,nome',
                    'historicos',
                    'checklistUnidadeProdutivaScoped',
                    'itens.checklist_pergunta.pergunta',
                    'itens.historicos',
                    'planoAcaoPaiScoped:id,uid,nome',
                ]
            )
                ->orderBy('uid');

            if (@$data['dt_ini'] && @$data['dt_end']) {
                $query->where(function ($q) use ($data) {
                    $q->whereBetween('created_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                    $q->orWhereBetween('updated_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                });
            }

            //Tipo do PDA
            $query->where(function ($q) use ($data) {
                $type_pda = @$data['type_pda'];

                $q->whereRaw('1 != 1');

                if (!$type_pda) {
                    $type_pda = ['individual', 'coletivo'];
                }

                $q->orWhereHas('checklist_unidade_produtiva', function ($qq) use ($type_pda) {
                    $qq->whereIn('checklist_id', $type_pda);
                });

                if (array_search('coletivo', $type_pda) !== FALSE) {
                    $q->orWhere('fl_coletivo', 1);
                }

                if (array_search('individual', $type_pda) !== FALSE || count($type_pda) == 0) {
                    $q->orWhere('fl_coletivo', 0);
                    $q->whereNull('checklist_unidade_produtiva_id');
                }
            });

            //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
            $query->where(function ($query) use ($data) {
                $this->service->queryDominios($query, 'unidadeProdutivaScoped.unidadesOperacionaisNS', @$data['dominio_id']);
                $this->service->queryUnidadesOperacionais($query, 'unidadeProdutivaScoped.unidadesOperacionaisNS', @$data['unidade_operacional_id']);
                $this->service->queryEstados($query, 'unidadeProdutivaScoped', @$data['estado_id']);
                $this->service->queryCidades($query, 'unidadeProdutivaScoped', @$data['cidade_id']);
                $this->service->queryRegioes($query, 'unidadeProdutivaScoped', @$data['regiao_id']);
                $this->service->queryProdutores($query, 'unidadeProdutivaScoped.produtores', @$data['produtor_id']);
                $this->service->queryUnidadesProdutivas($query, 'unidadeProdutivaScoped', @$data['unidade_produtiva_id']);
            });

            //Atuação
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoProdutor($query, 'unidadeProdutivaScoped.produtores', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoUnidadeProdutiva($query, 'unidadeProdutivaScoped', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoCadernoDeCampo($query, 'unidadeProdutivaScoped.produtores.cadernos', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoFormulario($query, 'unidadeProdutivaScoped.checklists', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoPlanoAcao($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });

            //Filtros Adicionais
            $this->service->queryChecklists($query, 'unidadeProdutivaScoped.checklists', @$data['checklist_id']); //Especificação, não vai data nesse filtro, @$data['dt_ini'], @$data['dt_end']
            $this->service->queryCertificacoes($query, 'unidadeProdutivaScoped', 'certificacoes', @$data['certificacao_id']);
            $this->service->querySoloCategoria($query, 'unidadeProdutivaScoped.solosCategoria', 'unidadeProdutivaScoped.caracterizacoes', @$data['solo_categoria_id']);
            $this->service->queryArea($query, 'unidadeProdutivaScoped', @$data['area']);
            $this->service->queryGenero($query, 'unidadeProdutivaScoped.produtores', @$data['genero_id']);
            $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutivaScoped', @$data['status_unidade_produtiva']);

            //CSV
            return $this->downloadCsv(
                $this->filename('planos_de_acoes'),
                $query,
                [
                    'ID Plano de ação',
                    'Criado em',
                    'Atualizado em',
                    'ID Produtor',
                    'Produtor',
                    'Coproprietários',
                    'ID Unidade Produtiva',
                    'Unidade Produtiva',
                    'Latitude',
                    'Longitude',
                    'Cidade',
                    'Estado',
                    'Tipo de Plano de Ação',
                    'Nome do Plano de Ação',
                    'Template do Formulário',
                    'ID Formulário vinculado',
                    'ID Plano de Ação Coletivo',
                    'Status Plano de Ação',
                    'Prazo do Plano de Ação',
                    'Acompanhamentos do Plano de Ação',
                    'ID Pergunta',
                    'Pergunta',
                    'Resposta',
                    'Ação recomendada',
                    'Ação',
                    'Prioridade',
                    'Acompanhamentos da Ação',
                    'Data último acompanhamento',
                    'Status ação',
                    'Prazo',
                ],
                function ($handle, $v) {
                    $items = $v->itens;
                    if (count($items) == 0) {
                        $items = [null];
                    }

                    //Extrai as respostas do PDA caso seja do tipo Formulário
                    $isChecklist = $v->checklist_unidade_produtiva_id;
                    $respostas = null;
                    if ($isChecklist) {
                        $respostas = $v->checklist_unidade_produtiva->getRespostas();
                    }

                    foreach ($items as $vItem) {
                        //Extrai a resposta do PDA vs Formulário
                        $resposta = '';
                        if ($isChecklist && @$vItem->checklist_pergunta_id) {
                            $resposta = @$respostas[$vItem->checklist_pergunta->pergunta_id]['resposta'];
                        }

                        fputcsv(
                            $handle,
                            $this->removeBreakLine(
                                [
                                    $v->uid, //id, // 'ID Plano de ação',
                                    $v->created_at_formatted, // 'Criado em',
                                    $v->updated_at_formatted, // 'Atualizado em',
                                    $v->produtor ? $v->produtor->uid : null, //$v->produtor_id, // 'ID Produtor',
                                    $v->produtor ? $this->privateData($v->produtor->nome) : null, // 'Produtor',
                                    $v->unidadeProdutivaScoped ? $this->privateData($v->unidadeProdutivaScoped->socios) : null, // 'Coproprietários',
                                    $v->unidadeProdutivaScoped ? $v->unidadeProdutivaScoped->uid : null, //$v->unidade_produtiva_id, //'ID Unidade Produtiva',
                                    $v->unidadeProdutivaScoped ? $this->privateData($v->unidadeProdutivaScoped->nome) : null, // 'Unidade Produtiva',
                                    $v->unidadeProdutivaScoped ? $this->privateData($this->scapeInt($v->unidadeProdutivaScoped->lat)) : null, // 'Latitude',
                                    $v->unidadeProdutivaScoped ? $this->privateData($this->scapeInt($v->unidadeProdutivaScoped->lng)) : null, //'Longitude',
                                    $v->unidadeProdutivaScoped ? $v->unidadeProdutivaScoped->cidade->nome : null, //'Cidade',
                                    $v->unidadeProdutivaScoped ? $v->unidadeProdutivaScoped->estado->nome : null, //'Estado',
                                    $v->fl_coletivo ? 'Coletivo' : ($v->checklist_unidade_produtiva_id ? 'A partir de formulário' : 'Individual'), //'Tipo de Plano de Ação',
                                    $this->privateData($v->nome), //'Nome do Plano de Ação',
                                    $isChecklist ? $v->checklistUnidadeProdutivaScoped->checklist->nome : null, //'Template do Formulário',
                                    $isChecklist ? $v->checklistUnidadeProdutivaScoped->uid : null, //$v->checklist_unidade_produtiva_id, //'ID Formulário vinculado',
                                    $v->plano_acao_coletivo_id ? $v->planoAcaoPaiScoped->uid : ($v->fl_coletivo ? $v->uid : null), //plano_acao_coletivo_id, //'ID Plano de Ação Coletivo',
                                    PlanoAcaoStatusEnum::toSelectArray()[$v->status], //'Status Plano de Ação',
                                    $v->prazo ? AppHelper::formatDateUtc($v->prazo, 'd/m/Y') : null, //'Prazo do Plano de Ação',
                                    $this->privateData($v->historicos->pluck("textoReport")->implode(", ")), //Acompanhamentos do Plano de Ação
                                    $isChecklist && $vItem ? $this->privateData($vItem->checklist_pergunta->pergunta_id) : null, //'ID Pergunta',
                                    $isChecklist && $vItem ? $this->privateData($vItem->checklist_pergunta->pergunta->pergunta) : null, //'Pergunta',
                                    $this->privateData($resposta), // 'Resposta',
                                    $isChecklist && $vItem ? $this->privateData($vItem->checklist_pergunta->pergunta->plano_acao_default) : null, //'Ação recomendada',
                                    $vItem ? $this->privateData($vItem->descricao) : null, //'Ação',
                                    $vItem ? $this->privateData(PlanoAcaoPrioridadeEnum::toSelectArray()[$vItem->prioridade]) : null, //'Prioridade',
                                    $vItem ? $this->privateData($vItem->historicos->pluck('textoReport')->implode(", ")) : null, //'Acompanhamentos',
                                    $vItem && @$vItem->ultima_observacao_data ? $this->privateData(AppHelper::formatDate($vItem->ultima_observacao_data)) : null, //'Data último acompanhamento',
                                    $vItem ? $this->privateData(PlanoAcaoItemStatusEnum::toSelectArray()[$vItem->status]) : null, //'Status ação',
                                    $vItem && $vItem->prazo ? $this->privateData(AppHelper::formatDateUtc($vItem->prazo, 'd/m/Y')) : null, //'Prazo',
                                ]
                            ),
                            ';'
                        );
                    }
                }
            );
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
