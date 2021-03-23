<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\CadernoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\CadernoModel;
use App\Models\Core\TemplateModel;
use Illuminate\Http\Request;

trait ReportCadernoCampoTrait
{
    public function cadernoCampoData(Request $request)
    {
        \Log::channel('sisrural')->info('Download CSV - Caderno de Campo');

        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva',
                    'template_caderno_id'
                ]
            );

            //Query Base
            $query = CadernoModel::with(
                [
                    'produtor:id,uid,nome',
                    'unidadeProdutiva:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
                    'usuario:id,first_name,last_name',
                    'unidadeProdutiva.cidade:id,nome',
                    'unidadeProdutiva.estado:id,nome',
                    'arquivos',
                    'respostasMany.templateResposta'
                ]
            )
                // ->where('status', CadernoStatusEnum::Finalizado) //Pediram para retirar
                ->orderBy('uid');

            if (@$data['dt_ini'] && @$data['dt_end']) {
                $query->where(function ($q) use ($data) {
                    $q->whereBetween('updated_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                    $q->orWhereBetween('finished_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
                });
            }

            //Wrap para facilitar os testes
            if (@!$data['template_caderno_id'] || count($data['template_caderno_id']) == 0) {
                $data['template_caderno_id'] = [TemplateModel::first()->id];
            }

            //Fixa o template do caderno p/ exportar
            $query->whereIn('template_id', $data['template_caderno_id']);

            $perguntas = TemplateModel::withoutGlobalScopes()
                ->whereIn('id', $data['template_caderno_id'])
                ->with(['perguntasWithTrashed' => function ($q) {
                    $q->orderBy('pivot_ordem');
                }, 'perguntasWithTrashed.respostas'])
                ->first()
                ->perguntasWithTrashed;

            //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
            $query->where(function ($query) use ($data) {
                $this->service->queryDominios($query, 'unidadeProdutiva.unidadesOperacionaisNS', @$data['dominio_id']);
                $this->service->queryUnidadesOperacionais($query, 'unidadeProdutiva.unidadesOperacionaisNS', @$data['unidade_operacional_id']);
                $this->service->queryEstados($query, 'unidadeProdutiva', @$data['estado_id']);
                $this->service->queryCidades($query, 'unidadeProdutiva', @$data['cidade_id']);
                $this->service->queryRegioes($query, 'unidadeProdutiva', @$data['regiao_id']);
                $this->service->queryProdutores($query, 'unidadeProdutiva.produtores', @$data['produtor_id']);
                $this->service->queryUnidadesProdutivas($query, 'unidadeProdutiva', @$data['unidade_produtiva_id']);
            });

            //Atuação
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoProdutor($query, 'unidadeProdutiva.produtores', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoUnidadeProdutiva($query, 'unidadeProdutiva', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoCadernoDeCampo($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoFormulario($query, 'unidadeProdutiva.checklists', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoPlanoAcao($query, 'unidadeProdutiva.planoAcoes', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });

            //Filtros Adicionais
            $this->service->queryChecklists($query, 'unidadeProdutiva.checklists', @$data['checklist_id']); //Especificação pediu p/ retirar o filtro daqui, @$data['dt_ini'], @$data['dt_end']
            $this->service->queryCertificacoes($query, 'unidadeProdutiva', 'certificacoes', @$data['certificacao_id']);
            $this->service->querySoloCategoria($query, 'unidadeProdutiva.solosCategoria', 'unidadeProdutiva.caracterizacoes', @$data['solo_categoria_id']);
            $this->service->queryArea($query, 'unidadeProdutiva', @$data['area']);
            $this->service->queryGenero($query, 'unidadeProdutiva.produtores', @$data['genero_id']);
            $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutiva', @$data['status_unidade_produtiva']);

            //CSV
            return $this->downloadCsv(
                $this->filename('cadernos'),
                $query,
                array_merge(
                    [
                        'ID Caderno de Campo',
                        'Protocolo',
                        'Criado em',
                        'Finalizado em',
                        'ID Produtor',
                        'Produtor',
                        'Coproprietários',
                        'ID Unidade Produtiva',
                        'Unidade Produtiva',
                        'Latitude',
                        'Longitude',
                        'Cidade',
                        'Estado',
                        'Técnico',
                        'Status',
                        'Galeria',
                    ],
                    $perguntas->pluck('pergunta')->toArray()
                ),
                function ($handle, $v) use ($perguntas) {
                    $perguntaRespostas = [];
                    foreach ($perguntas as $pergunta) {
                        $respostas = $v->respostasMany->toArray();

                        $resposta = @array_values(array_filter($respostas, function ($resposta) use ($pergunta) {
                            return $resposta['template_pergunta_id'] === $pergunta['id'];
                        }));

                        $value = array_map(function ($vResposta) {
                            return $vResposta['template_resposta_id'] ? $vResposta['template_resposta']['descricao'] : $vResposta['resposta'];
                        }, $resposta);

                        $perguntaRespostas[] = $this->privateData(join(', ', $value));
                    }

                    fputcsv(
                        $handle,
                        $this->removeBreakLine(
                            array_merge(
                                [
                                    $v->uid, //id, //'ID Caderno de Campo',
                                    $v->protocolo, //'Protocolo',
                                    $v->created_at_formatted, //'Criado em',
                                    $v->finished_at_formatted, //'Finalizado em',
                                    $v->produtor->uid, //produtor_id, //'ID Produtor',
                                    $this->privateData($v->produtor->nome), //'Produtor',
                                    $this->privateData($v->unidadeProdutiva->socios), //'Coproprietários',
                                    $v->unidadeProdutiva->uid, //,unidade_produtiva_id //'ID Unidade Produtiva',
                                    $this->privateData($v->unidadeProdutiva->nome), //'Unidade Produtiva',
                                    $this->privateData($this->scapeInt($v->unidadeProdutiva->lat)), //'Latitude',
                                    $this->privateData($this->scapeInt($v->unidadeProdutiva->lng)), //'Longitude',
                                    $v->unidadeProdutiva->cidade->nome, //'Cidade',
                                    $v->unidadeProdutiva->estado->nome, //'Estado',
                                    $this->privateData($v->usuario->first_name . ' ' . $v->usuario->last_name), //'Técnico',
                                    $v->status, //'Status',
                                    $this->privateData($v->arquivos->pluck('url')->implode(",")), //'Galeria',
                                ],
                                $perguntaRespostas
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
