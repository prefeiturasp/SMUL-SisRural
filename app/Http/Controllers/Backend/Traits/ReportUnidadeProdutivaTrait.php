<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Enums\ProcessaProducaoEnum;
use App\Enums\UnidadeProdutivaCarEnum;
use App\Models\Core\InstalacaoTipoModel;
use App\Models\Core\RelacaoModel;
use App\Models\Core\SoloCategoriaModel;
use App\Models\Core\UnidadeProdutivaModel;
use Illuminate\Http\Request;

trait ReportUnidadeProdutivaTrait
{
    private function getColaboradoresValues($v, $relacoes)
    {
        $values = [];

        foreach ($relacoes as $vv) {
            $values[] = $v->colaboradores->where('relacao_id', $vv->id)->count();
        }

        return $values;
    }

    private function getInstalacoesValues($v, $infraEstruturas)
    {
        $values = [];

        foreach ($infraEstruturas as $vv) {
            $values[] = $v->instalacoes->where('instalacao_tipo_id', $vv->id)->count();
        }

        return $values;
    }

    private function getSoloCategoriasValues($v, $soloCategorias)
    {
        $values = [];

        foreach ($soloCategorias as $vv) {
            $values[] = $v->caracterizacoes->where('solo_categoria_id', $vv->id)->sum('area');
        }

        return $values;
    }

    public function unidadeProdutivaData(Request $request)
    {
        \Log::channel('sisrural')->info('Download CSV - Unidade Produtiva');

        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva'
                ]
            );

            //Query Base
            $query = UnidadeProdutivaModel::select('unidade_produtivas.*', 'produtor_unidade_produtiva.tipo_posse_id', 'produtores.id as produtor_id')
                ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
                ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
                ->with([
                    'cidade:id,nome', 'estado:id,nome', 'tipoPosse',
                    'outorga', 'certificacoes', 'pressaoSociais', 'canaisComercializacao', 'tiposFonteAgua', 'riscosContaminacaoAgua', 'solosCategoria', 'esgotamentoSanitarios', 'residuoSolidos', 'arquivos',
                    'produtor', 'produtor.genero', 'produtor.etinia', 'produtor.cidade:id,nome', 'produtor.estado:id,nome', 'produtor.rendaAgricultura', 'produtor.rendimentoComercializacao', 'produtor.grauInstrucao', 'produtor.assistenciaTecnicaTipo'
                ])
                ->whereNull('produtor_unidade_produtiva.deleted_at')
                ->orderBy('unidade_produtivas.uid');

            //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
            $query->where(function ($query) use ($data) {
                $this->service->queryDominios($query, 'unidadesOperacionaisNS', @$data['dominio_id']);
                $this->service->queryUnidadesOperacionais($query, 'unidadesOperacionaisNS', @$data['unidade_operacional_id']);
                $this->service->queryEstados($query, null, @$data['estado_id']);
                $this->service->queryCidades($query, null, @$data['cidade_id']);
                $this->service->queryRegioes($query, null, @$data['regiao_id']);
                $this->service->queryProdutores($query, null, @$data['produtor_id']);
                $this->service->queryUnidadesProdutivas($query, null, @$data['unidade_produtiva_id']);
            });

            //Atuação
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoProdutor($query, 'produtores', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoUnidadeProdutiva($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoCadernoDeCampo($query, 'produtores.cadernos', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoFormulario($query, 'checklists', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
                $this->service->queryAtuacaoPlanoAcao($query, 'planoAcoes', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });

            //Filtros Adicionais
            $this->service->queryChecklists($query, 'checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']);
            $this->service->queryCertificacoes($query, null, 'certificacoes', @$data['certificacao_id']);
            $this->service->querySoloCategoria($query, 'solosCategoria', 'caracterizacoes', @$data['solo_categoria_id']);
            $this->service->queryArea($query, null, @$data['area']);
            $this->service->queryGenero($query, 'produtores', @$data['genero_id']);
            $this->service->queryStatusUnidadeProdutiva($query, null, @$data['status_unidade_produtiva']);

            $relacoes = RelacaoModel::orderBy('nome')->get();
            $instalacaoTipos = InstalacaoTipoModel::orderBy('nome')->get();
            $soloCategorias = SoloCategoriaModel::orderBy('nome')->where('tipo', 'geral')->get();

            //CSV
            return $this->downloadCsv(
                $this->filename('unidades_produtivas_produtores'),
                $query,
                array_merge(
                    [
                        'Criado em',
                        'Atualizado em',
                        'ID Produtor/a',
                        'Produtor/a',
                        'Coproprietários/as',
                        'Status',
                        'Status - Observação',
                        'CPF',
                        'Telefone 1',
                        'Telefone 2',
                        'Nome Social',
                        'E-mail',
                        'Genero',
                        'Etnia',
                        'Portador de Necessidades Especiais?',
                        'Tipo de Necessidade Especial',
                        'Data de Nascimento',
                        'RG',
                        'Possui CNPJ?',
                        'CNPJ',
                        'Possui Nota Fiscal de Produtor?',
                        'Número Nota Fiscal de Produtor',
                        'É Agricultor Familiar?',
                        'Possui DAP?',
                        'Número DAP',
                        'Validade DAP',
                        'Recebe Assistência Técnica?',
                        'Qual tipo de assistencia técnica ID',
                        'Periodicidade da Assistência Técnica',
                        'É da Comunidade Tradicional?',
                        'Qual Comunidade Tradicional?',
                        'Acessa a Internet?',
                        'Participa de Cooperativa, Associação, Rede, Movimento ou Coletivo?',
                        'Qual?',
                        'Reside na Unidade Produtiva?',
                        'CEP Produtor',
                        'Endereço Produtor',
                        'Bairro Produtor',
                        'Município Produtor',
                        'Estado Produtor',
                        '% da renda advinda da agricultura',
                        'Rendimento da comercialização',
                        'Outras fontes de renda',
                        'Grau de instrução',
                        'ID Unidade Produtiva',
                        'Nome da Unidade Produtiva',
                        'Tipo da posse',
                        'Status',
                        'Status - Observação',
                        'CEP',
                        'Endereço',
                        'Bairro',
                        'Município',
                        'Estado',
                        'Latitude',
                        'Longitude',
                        'Possui Certificação?',
                        'Certificações ID`s',
                        'Cerificações - Descrição',
                        'Possui CAR?',
                        'CAR',
                        'Possui CCIR?',
                        'Possui ITR?',
                        'Possui Matricula?',
                        'Número da UPA',
                        'Sente pressões sociais e urbanas?',
                        'Pressões Sociais',
                        'Pressão Social - Descrição',
                        'Comercializa a Produção?',
                        'Canais de Comercialização ID`s',
                        'Gargalos da produção, processamento e comercialização',
                        'Possui Outorga?',
                        'Fontes de uso de Água ID`s',
                        'Há Risco de Contaminação?',
                        'Tipos de Contaminação',
                        'Observações quanto à contaminação',
                        'Área total da propriedade',
                        'Processa a produção?',
                        'Descreva o processamento da produção',
                        'Outros Usos ID`s',
                        'Outros Usos - Descrição',
                        'Bacia Hidrográfica',
                        'Esgotamento Sanitário',
                        'Destinação de resíduos sólidos',
                        'Galeria'
                    ],
                    $relacoes->pluck('nomeReport')->toArray(), //CK,CP
                    $instalacaoTipos->pluck('nomeReport')->toArray(), //CQ, CU
                    $soloCategorias->pluck('nomeReport')->toArray(), //CV,EB
                    [
                        'Quantidade cadernos de campo', //EC
                        'Quantidade formulários aplicados', //ED
                        'Quantidade Planos de Ação Individuais', //EE
                        'Quantidade Planos de Ação Coletivos', //EF
                    ]
                ),
                function ($handle, $v) use ($relacoes, $instalacaoTipos, $soloCategorias) {
                    $relacaoValues = $this->getColaboradoresValues($v, $relacoes);
                    $instalacaoValues = $this->getInstalacoesValues($v, $instalacaoTipos);
                    $soloCategoriaValues = $this->getSoloCategoriasValues($v, $soloCategorias);

                    if (!$v->produtor) {
                        return;
                    }

                    fputcsv(
                        $handle,
                        $this->removeBreakLine(
                            array_merge(
                                [
                                    $v->created_at_formatted,  // 'Criado em',
                                    $v->updated_at_formatted,  // 'Atualizado em',
                                    $this->privateData($v->produtor->uid), // 'ID Produtor',
                                    $this->privateData($v->produtor->nome), // 'Produtor',
                                    $this->privateData($v->socios), // 'Coproprietários',
                                    $v->produtor->status, // 'Status',
                                    $this->privateData($v->produtor->status_observacao), // 'Status - Observação',
                                    $this->privateData($this->scapeInt($v->produtor->cpf)), // 'CPF',
                                    $this->privateData($v->produtor->telefone_1), // 'Telefone 1',
                                    $this->privateData($v->produtor->telefone_2), // 'Telefone 2',
                                    $this->privateData($v->produtor->nome_social), // 'Nome Social',
                                    $this->privateData($v->produtor->email), // 'E-mail',
                                    $v->produtor->genero_id ? $v->produtor->genero->nome : null, // 'Genero',
                                    $this->privateData($v->produtor->etinia_id ? $v->produtor->etinia->nome : null), // 'Etnia',
                                    $this->privateData(boolean_sim_nao_sem_resposta($v->produtor->fl_portador_deficiencia)), // 'Portador de Necessidades Especiais?',
                                    $this->privateData($v->produtor->portador_deficiencia_obs), // 'Tipo de Necessidade Especial',
                                    $this->privateData($v->produtor->data_nascimento ? $this->formatDate($v->produtor->data_nascimento) : null), // 'Data de Nascimento',
                                    $this->privateData($v->produtor->rg), // 'RG',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_cnpj), // 'Possui CNPJ?',
                                    $this->privateData($v->produtor->cnpj), // 'CNPJ',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_nota_fiscal_produtor), // 'Possui Nota Fiscal de Produtor?',
                                    $this->privateData($v->produtor->nota_fiscal_produtor), // 'Número Nota Fiscal de Produtor',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_agricultor_familiar), // 'É Agricultor Familiar?',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_agricultor_familiar_dap), // 'Possui DAP?',
                                    $this->privateData($v->produtor->agricultor_familiar_numero), // 'Número DAP',
                                    $this->privateData($this->formatDate($v->produtor->agricultor_familiar_data)), // 'Validade DAP',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_assistencia_tecnica), // 'Recebe Assistência Técnica?',
                                    $v->produtor->assistencia_tecnica_tipo_id ? $v->produtor->assistenciaTecnicaTipo->nome : null, // 'QUAL TIPO DE ASSISTENCIA TÉCNICA (ID)',
                                    $v->produtor->assistencia_tecnica_periodo, // 'Periodicidade da Assistência Técnica',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_comunidade_tradicional), // 'É da Comunidade Tradicional?',
                                    $v->produtor->comunidade_tradicional_obs, // 'Qual Comunidade Tradicional?',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_internet), // 'Acessa a Internet?',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_tipo_parceria), // 'Participa de Cooperativa, Associação, Rede, Movimento ou Coletivo?',
                                    $v->produtor->tipo_parcerias_obs, // 'Qual?',
                                    boolean_sim_nao_sem_resposta($v->produtor->fl_reside_unidade_produtiva), // 'Reside na Unidade Produtiva?',
                                    $this->privateData($v->produtor->cep), // 'CEP Produtor',
                                    $this->privateData($v->produtor->endereco), // 'Endereço Produtor',
                                    $v->produtor->bairro, // 'Bairro Produtor',
                                    @$v->produtor->cidade->nome, // 'Município Produtor',
                                    @$v->produtor->estado->nome, // 'Estado Produtor',
                                    @$v->produtor->rendaAgricultura->nome, //renda_agricultura_id, // '% da renda advinda da agricultura',
                                    @$v->produtor->rendimentoComercializacao->nome, //rendimento_comercializacao_id, // 'Rendimento da comercialização',
                                    $v->produtor->outras_fontes_renda, // 'Outras fontes de renda',
                                    @$v->produtor->grauInstrucao->nome, //grau_instrucao_id, // 'Grau de instrução',
                                    $this->privateData($v->uid), // 'ID Unidade Produtiva',
                                    // $v->uid, // 'UID Unidade Produtiva',
                                    $this->privateData($v->nome), // 'Nome da Unidade Produtiva',
                                    @$v->tipoPosse->nome, //$v->tipo_posse_id, // 'Tipo da posse',
                                    $v->status, // 'Status',
                                    $this->privateData($v->status_observacao), // 'Status - Observação',
                                    $this->privateData($v->cep), // 'CEP',
                                    $this->privateData($v->endereco), // 'Endereço',
                                    $v->bairro, // 'Bairro',
                                    $v->cidade->nome, // 'Município',
                                    $v->estado->nome, // 'Estado',
                                    $this->privateData($this->scapeInt($v->lat)), // 'Latitude',
                                    $this->privateData($this->scapeInt($v->lng)), // 'Longitude',
                                    boolean_sim_nao_sem_resposta($v->fl_certificacoes), // 'Possui Certificação?',
                                    $v->certificacoes->pluck('nome')->implode(","), // '*Certificações (IDS separados por virgula)',
                                    $this->privateData($v->certificacoes_descricao), // 'Cerificações - Descrição',
                                    UnidadeProdutivaCarEnum::toSelectArray()[$v->fl_car], // 'Possui CAR?',
                                    $this->privateData($v->car), // 'CAR',
                                    boolean_sim_nao_sem_resposta($v->fl_ccir), // 'Possui CCIR?',
                                    boolean_sim_nao_sem_resposta($v->fl_itr), // 'Possui ITR?',
                                    boolean_sim_nao_sem_resposta($v->fl_matricula), // 'Possui Matricula?',
                                    $v->upa, // 'Número da UPA',
                                    boolean_sim_nao_sem_resposta($v->fl_pressao_social), // 'Sente pressões sociais e urbanas?',
                                    $v->pressaoSociais->pluck('nome')->implode(","), // '*Pressões Sociais',
                                    $v->pressao_social_descricao, // 'Pressão Social - Descrição',
                                    boolean_sim_nao_sem_resposta($v->fl_comercializacao), // 'Comercializa a Produção?',
                                    $v->canaisComercializacao->pluck('nome')->implode(","), // '*Canais de Comercialização (IDS separados por virgula)',
                                    $this->privateData($v->gargalos), // 'Gargalos da produção, processamento e comercialização',
                                    $v->outorga_id ? $v->outorga->nome : null, // 'Possui Outorga?',
                                    $v->tiposFonteAgua->pluck('nome')->implode(","), // '*Fontes de uso de Água (IDS separados por virgula)',
                                    boolean_sim_nao_sem_resposta($v->fl_risco_contaminacao), // 'Há Risco de Contaminação?',
                                    $v->riscosContaminacaoAgua->pluck('nome')->implode(","), // '*Selecione os Tipos de Contaminação  (IDS separados por virgula)',
                                    $this->privateData($v->risco_contaminacao_observacoes), // 'Observações quanto à contaminação',
                                    $v->area_total_solo, // 'Área total da propriedade',
                                    $v->fl_producao_processa ? ProcessaProducaoEnum::toSelectArray()[$v->fl_producao_processa] : 'Sem resposta', // 'Processa a produção?',
                                    $v->producao_processa_descricao, // 'Descreva o processamento da produção',
                                    $v->solosCategoria->pluck('nome')->implode(","), // '*Outros Usos (IDS separados por virgula)',
                                    $v->outros_usos_descricao, // 'Outros Usos - Descrição',
                                    $v->bacia_hidrografica, // 'Bacia Hidrográfica',
                                    $v->esgotamentoSanitarios->pluck('nome')->implode(","), // 'Esgotamento Sanitário',
                                    $v->residuoSolidos->pluck('nome')->implode(","), // 'Destinação de resíduos sólidos',
                                    $v->arquivos->pluck('url')->implode(","), //'Galeria',
                                ],
                                $relacaoValues, //Valores das relações (colaboradores)
                                $instalacaoValues, //Valores das infra-estrutura
                                $soloCategoriaValues, //Valoes do uso do solo
                                [
                                    $v->cadernos->count(), //Quantidade cadernos de campo
                                    $v->checklists->count(), //Quantidade formulários aplicados
                                    $v->planoAcoes()->where('fl_coletivo', 0)->count(), //Quantidade Planos de Ação Individuais
                                    $v->planoAcoes()->where('fl_coletivo', 1)->count(), //Quantidade Planos de Ação Coletivos
                                ]
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

    private function formatDate($strDate) {
        $date = @\Carbon\Carbon::parse($strDate);

        if (@$date) {
            return $date->format('d/m/Y');
        }

        return $strDate;
    }
}
