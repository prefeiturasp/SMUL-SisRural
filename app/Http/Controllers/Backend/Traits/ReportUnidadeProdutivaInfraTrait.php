<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Models\Core\InstalacaoModel;
use Illuminate\Http\Request;

trait ReportUnidadeProdutivaInfraTrait
{
    public function unidadeProdutivaInfraData(Request $request)
    {
        \Log::channel('sisrural')->info('Download CSV - Infra Estrutura');

        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva'
                ]
            );

            //Query Base
            //whereHas porque não existe "permissionScope" configurado para o Colaborador, sem ele vai retornar todos os colaboradores do sistema
            $query = InstalacaoModel::with(['instalacaoTipo', 'unidadeProdutiva:id,uid', 'instalacaoTipo'])
                ->whereHas('unidadeProdutiva');

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
                $this->service->queryAtuacaoCadernoDeCampo($query, 'unidadeProdutiva.produtores.cadernos', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoFormulario($query, 'unidadeProdutiva.checklists', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoPlanoAcao($query, 'unidadeProdutiva.planoAcoes', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });

            //Filtros Adicionais
            $this->service->queryChecklists($query, 'unidadeProdutiva.checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']);
            $this->service->queryCertificacoes($query, 'unidadeProdutiva', 'certificacoes', @$data['certificacao_id']);
            $this->service->querySoloCategoria($query, 'unidadeProdutiva.solosCategoria', 'unidadeProdutiva.caracterizacoes', @$data['solo_categoria_id']);
            $this->service->queryArea($query, 'unidadeProdutiva', @$data['area']);
            $this->service->queryGenero($query, 'unidadeProdutiva.produtores', @$data['genero_id']);
            $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutiva', @$data['status_unidade_produtiva']);

            //CSV
            return $this->downloadCsv(
                $this->filename('unidades_produtivas_infraestruturas'),
                $query,
                [
                    'ID',
                    'ID da Unidade Produtiva',
                    'Tipo',
                    'Descrição',
                    'Quantidade',
                    'Área ('.config('app.area_sigla').')',
                    'Observação',
                    'Localização',
                ],
                function ($handle, $v) {
                    fputcsv(
                        $handle,
                        $this->removeBreakLine(
                            [
                                $v->uid, //$v->id, //  'ID',
                                $this->privateData($v->unidadeProdutiva->uid), //unidade_produtiva_id,  // 'ID DA UNIDADE PRODUTIVA',
                                $v->instalacaoTipo ? $v->instalacaoTipo->nome : null, //'Tipo',
                                $v->descricao, //'Descrição',
                                $v->quantidade, //'Quantidade',
                                $v->area, //'Área (Hectares)',
                                $v->observacao, //'Observação',
                                $v->localizacao, //'Localização',
                            ]
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
