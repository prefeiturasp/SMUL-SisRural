<?php

namespace App\Services;

use App\Enums\CadernoStatusEnum;

use App\Enums\ChecklistStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;

class ChartService
{
    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    /**
     * Retorna os IDS dos usuários com o mesmo nome (Significa o mesmo CPF)
     *
     * Não deve retornar usuários do tipo Domínio, isso invalida os dados do 1_13b
     *
     * @param string $fullname
     * @param [int] $dominios_ids
     * @param [int] $unidades_ids
     *
     * @return [int]
     */
    public function getUserIDSByName($fullname, $dominios_ids = [], $unidades_ids = [])
    {
        $users = User::withoutGlobalScopes()
            ->where(function($q) use ($fullname) {
                $q->where(\DB::raw("concat(first_name, ' ', last_name)"), $fullname);
                $q->orWhere(\DB::raw("first_name"), $fullname);
            })->whereHas('roles', function ($q) {
                $q->where('name', 'Tecnico');
                $q->orWhere('name', 'Unidade Operacional');
            });

        return $users->get()
            ->pluck('id')
            ->toArray();
    }

    /**
     * Produtores
     *
     * Chart_1_2
     * Chart_1_3
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     */
    public function getProdutores(array $data)
    {
        $query = ProdutorModel::query()
            ->addSelect('produtores.id as produtor_id')
            ->addSelect('produtores.nome as nome')
            ->addSelect('produtores.uid as uid')
            ->addSelect('produtores.created_at as created_at');
            // ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            // ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            // ->whereNull('produtor_unidade_produtiva.deleted_at')

        //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
        $query->where(function ($query) use ($data) {
            $this->service->queryDominios($query, 'unidadesProdutivasNS.unidadesOperacionaisNS', @$data['dominio_id']);
            $this->service->queryUnidadesOperacionais($query, 'unidadesProdutivasNS.unidadesOperacionaisNS', @$data['unidade_operacional_id']);
            $this->service->queryEstados($query, 'unidadesProdutivasNS', @$data['estado_id']);
            $this->service->queryCidades($query, 'unidadesProdutivasNS', @$data['cidade_id']);
            $this->service->queryRegioes($query, 'unidadesProdutivasNS', @$data['regiao_id']);
            $this->service->queryProdutores($query, null, @$data['produtor_id']);
            $this->service->queryUnidadesProdutivas($query, 'unidadesProdutivasNS', @$data['unidade_produtiva_id']);
        });

        //Atuação
        $query->where(function ($query) use ($data) {
            $this->service->queryAtuacaoProdutor($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            $this->service->queryAtuacaoUnidadeProdutiva($query, 'unidadesProdutivasNS', @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
        });

        //Filtros Adicionais
        $this->service->queryChecklists($query, 'checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']);
        $this->service->queryCertificacoes($query, 'unidadesProdutivasNS', 'certificacoes', @$data['certificacao_id']);
        $this->service->querySoloCategoria($query, 'unidadesProdutivasNS.solosCategoria', 'unidadesProdutivasNS.caracterizacoes', @$data['solo_categoria_id']);
        $this->service->queryArea($query, 'unidadesProdutivasNS', @$data['area']);
        $this->service->queryGenero($query, null, @$data['genero_id']);
        $this->service->queryStatusUnidadeProdutiva($query, 'unidadesProdutivasNS', @$data['status_unidade_produtiva']);

        return $query->distinct();

        // return \DB::query()
        //     ->select('*')
        //     ->fromSub(
        //         $query->distinct(),
        //         'produtores'
        //     );
    }

    public function getUnidadesProdutivasJoinProdutores(array $data)
    {
        //Query Base
        $query = UnidadeProdutivaModel::select('unidade_produtivas.*')
            ->addSelect('produtores.id as produtor_id')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');

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

        return $query;
    }


    /**
     * Unidades produtivas
     *
     * Chart_1_1 - Retirado do $data os regitros de $atuacao p/ não filtrar por Atuação
     * Chart_1_4 - Força a busca por atuação do Produtor/Unidade Produtiva/Caderno de Campo/Formulario/PDA
     */
    public function getUnidadesProdutivas(array $data)
    {
        //Query Base
        $query = UnidadeProdutivaModel::select('unidade_produtivas.*');
            /*->addSelect('produtores.id as produtor_id')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');*/

        //Abrangência territorial (OR entre os pares, AND entre os outros blocos de filtro)
        $query->where(function ($query) use ($data) {
            $this->service->queryDominios($query, 'unidadesOperacionaisNS', @$data['dominio_id']);
            $this->service->queryUnidadesOperacionais($query, 'unidadesOperacionaisNS', @$data['unidade_operacional_id']);
            $this->service->queryEstados($query, null, @$data['estado_id']);
            $this->service->queryCidades($query, null, @$data['cidade_id']);
            $this->service->queryRegioes($query, null, @$data['regiao_id']);
            $this->service->queryProdutores($query, 'produtores', @$data['produtor_id']);
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

        return $query;
    }

    /**
     * @param array $data
     *
     * @return QueryBuilder
     */
    public function getCadernosFinalizados(array $data) {
        $tecnicos_id = $this->service->getTecnicos(@$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id']);

        return $this->getCadernos($data)
            ->where('cadernos.status', CadernoStatusEnum::Finalizado)
            ->whereIn('cadernos.finish_user_id', $tecnicos_id);
    }

    /**
     * @param array $data
     *
     * @return QueryBuilder
     */
    public function getFormulariosFinalizadosAtuacao(array $data) {
        $tecnicos_id = $this->service->getTecnicos(@$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id']);

        return $this->getFormularios($data)
            ->whereIn('checklist_unidade_produtivas.finish_user_id', $tecnicos_id);
    }

     /**
     * @param array $data
     *
     * @return QueryBuilder
     */
    public function getFormulariosComFiltroPergunta(array $data) {
        $query = $this->getFormularios($data);

        if (@$data['pergunta_id']) {
            $query->whereHas('respostasMany', function($q) use ($data){
                $q->whereIn('pergunta_id', $data['pergunta_id']);
            });
        }

        return $query;
    }


    /**
     * Retorna os cadernos de campo
     *
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     * Chart_5_1
     * Chart_5_X
     */
    public function getCadernos(array $data)
    {
        //Query Base
        $query = CadernoModel::with(
            [
                'produtor:id,uid,nome',
                'unidadeProdutiva:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
            ]
        )
            ->select('cadernos.id', 'cadernos.uid', 'cadernos.produtor_id');

        //Filtro template caderno
        if (@$data['template_caderno_id'] && count($data['template_caderno_id']) > 0) {
            $query->whereIn('template_id', $data['template_caderno_id']);
        }

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
            $this->service->queryAtuacaoCadernoDeCampo($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
        });

        //Filtros Adicionais
        $this->service->queryChecklists($query, 'unidadeProdutiva.checklists', @$data['checklist_id']); //Especificação pediu p/ retirar o filtro daqui, @$data['dt_ini'], @$data['dt_end']
        $this->service->queryCertificacoes($query, 'unidadeProdutiva', 'certificacoes', @$data['certificacao_id']);
        $this->service->querySoloCategoria($query, 'unidadeProdutiva.solosCategoria', 'unidadeProdutiva.caracterizacoes', @$data['solo_categoria_id']);
        $this->service->queryArea($query, 'unidadeProdutiva', @$data['area']);
        $this->service->queryGenero($query, 'unidadeProdutiva.produtores', @$data['genero_id']);
        $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutiva', @$data['status_unidade_produtiva']);

        return $query;
    }

    /**
     * Retorna os formulários
     *
     *  - status != rascunho
     *  - updated_at between //Simulando a data que foi finalizado (Não é usado o finished_at por causa do status "Aguardando PDA" "Aguardando Aprovação")
     *
     * Chart_1_6
     * Chart_1_7
     * Chart_1_8
     * Chart_1_13b
     * Chart_1_15
     * Chart_3_3
     * Chart_3_X_PerguntasFormularios
     * Chart_3_X_PerguntasFormulariosPeriodo
     *
     * $queryChecklistsOnlyFinished Não é utilizado por ninguém (por hora) //Avaliar p/ remover do código
     */
    public function getFormularios(array $data, $queryChecklistsOnlyFinished = true)
    {
        //Query Base
        $query = ChecklistUnidadeProdutivaModel::with(
            [
                'checklist:id,nome',
                'produtor:id,uid,nome',
                'unidade_produtiva:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
            ]
        )
            ->select('checklist_unidade_produtivas.id', 'checklist_unidade_produtivas.uid', 'checklist_unidade_produtivas.checklist_id', 'checklist_unidade_produtivas.produtor_id')
            ->whereNotIn('checklist_unidade_produtivas.status', [ChecklistStatusEnum::Rascunho]);

        //Força um template p/ ser utilizado na listagem (DataTable)
        if (@$data['filter_checklist_id']) {
            $query->where('checklist_id', $data['filter_checklist_id']);
        }

        //whereIn quando usuário informar qual formulário ele quer extrair. Se não tiver, é TODOS (Ai não precisa executar o where)
        if (@$data['checklist_id'] && count($data['checklist_id']) > 0 && !@$data['filter_checklist_id']) {
            $query->whereIn('checklist_id', $data['checklist_id']);
        }

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $query->whereBetween('checklist_unidade_produtivas.updated_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
        }

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
            $this->service->queryAtuacaoFormulario($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
        });

        //Filtros Adicionais
        if ($queryChecklistsOnlyFinished) {
            //Filtra o status finalizado = true
            $this->service->queryChecklists($query, 'unidade_produtiva.checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']);
        } else {
            //Filtra qualquer status
            $this->service->queryChecklistsAllStatus($query, 'unidade_produtiva.checklists', @$data['checklist_id'], @$data['dt_ini'], @$data['dt_end']);
        }

        $this->service->queryCertificacoes($query, 'unidade_produtiva', 'certificacoes', @$data['certificacao_id']);
        $this->service->querySoloCategoria($query, 'unidade_produtiva.solosCategoria', 'unidade_produtiva.caracterizacoes', @$data['solo_categoria_id']);
        $this->service->queryArea($query, 'unidade_produtiva', @$data['area']);
        $this->service->queryGenero($query, 'unidade_produtiva.produtores', @$data['genero_id']);
        $this->service->queryStatusUnidadeProdutiva($query, 'unidade_produtiva', @$data['status_unidade_produtiva']);

        return $query;
    }

    /**
     * PDAS que foram manipulados em um range de datas
     *  - created_at between
     *
     * Esta função IGNORA os formulários PAIS (Coletivo)
     *
     * Chart_1_6
     * Chart_1_8
     * Chart_1_9
     */
    public function getPdasAtualizacoes(array $data, $withAtuacao = true)
    {
        //Query Base
        $query = PlanoAcaoModel::with(
            [
                'unidadeProdutivaScoped:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
            ]
        );

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $dt_ini = $data['dt_ini'];
            $dt_end = $data['dt_end'];

            //Estou ignorando o plano de ação coletivo PAI, só pega os vinculados com UNIDADES PRODUTIVAS
            $query->where(function ($q) {
                $q->whereNotNull('plano_acoes.unidade_produtiva_id');
            });

            $query->where(function ($q) use ($dt_ini, $dt_end) {
                $q->whereBetween('plano_acoes.created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                $q->orWhereBetween('plano_acoes.updated_at', AppHelper::dateBetween($dt_ini, $dt_end));

                $q->orWhereHas('historicos', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });

                $q->orWhereHas('itens', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });

                $q->orWhereHas('itens.historicos', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });
            });
        }

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
        if ($withAtuacao) {
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoPlanoAcao($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });
        }

        //Filtros Adicionais
        $this->service->queryChecklists($query, 'unidadeProdutivaScoped.checklists', @$data['checklist_id']); //Especificação, não vai data nesse filtro, @$data['dt_ini'], @$data['dt_end']
        $this->service->queryCertificacoes($query, 'unidadeProdutivaScoped', 'certificacoes', @$data['certificacao_id']);
        $this->service->querySoloCategoria($query, 'unidadeProdutivaScoped.solosCategoria', 'unidadeProdutivaScoped.caracterizacoes', @$data['solo_categoria_id']);
        $this->service->queryArea($query, 'unidadeProdutivaScoped', @$data['area']);
        $this->service->queryGenero($query, 'unidadeProdutivaScoped.produtores', @$data['genero_id']);
        $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutivaScoped', @$data['status_unidade_produtiva']);

        return $query;
    }

    /**
     * PDAS que foram manipulados em um range de datas
     *  - created_at between
     *
     * Chart_1_13B (Acompanhamentos)
     */
    public function getPdasAtualizacoesAcompanhamentos(array $data, $withAtuacao = true)
    {
        //Query Base
        $query = PlanoAcaoModel::with(
            [
                'unidadeProdutivaScoped:id,uid,nome,socios,lat,lng,cidade_id,estado_id',
            ]
        );

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $dt_ini = $data['dt_ini'];
            $dt_end = $data['dt_end'];

            $query->where(function ($q) use ($dt_ini, $dt_end) {
                $q->whereBetween('plano_acoes.created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                $q->orWhereBetween('plano_acoes.updated_at', AppHelper::dateBetween($dt_ini, $dt_end));

                $q->orWhereHas('historicos', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });

                $q->orWhereHas('itens', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });

                $q->orWhereHas('itens.historicos', function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('created_at', AppHelper::dateBetween($dt_ini, $dt_end));
                });
            });
        }

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
        if ($withAtuacao) {
            $query->where(function ($query) use ($data) {
                $this->service->queryAtuacaoPlanoAcao($query, null, @$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id'], @$data['dt_ini'], @$data['dt_end']);
            });
        }

        //Filtros Adicionais
        $this->service->queryChecklists($query, 'unidadeProdutivaScoped.checklists', @$data['checklist_id']); //Especificação, não vai data nesse filtro, @$data['dt_ini'], @$data['dt_end']
        $this->service->queryCertificacoes($query, 'unidadeProdutivaScoped', 'certificacoes', @$data['certificacao_id']);
        $this->service->querySoloCategoria($query, 'unidadeProdutivaScoped.solosCategoria', 'unidadeProdutivaScoped.caracterizacoes', @$data['solo_categoria_id']);
        $this->service->queryArea($query, 'unidadeProdutivaScoped', @$data['area']);
        $this->service->queryGenero($query, 'unidadeProdutivaScoped.produtores', @$data['genero_id']);
        $this->service->queryStatusUnidadeProdutiva($query, 'unidadeProdutivaScoped', @$data['status_unidade_produtiva']);

        return $query;
    }

    /**
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     *
     * $betweenCreatedAt = É possível desabilitar por causa do Chart_1_8, que deve retornar no datatable sem o "between"
     */
    public function getPdasCreated(array $data, $withAtuacao = true)
    {
        $query = $this->getPdasAtualizacoes($data, $withAtuacao);

        //Garantir que a atuação fica restrita ao "plano_acoes" (Chart 1_13b)
        $tecnicos_id = $this->service->getTecnicos(@$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id']);
        if (count($tecnicos_id) > 0) {
            $query->whereIn('plano_acoes.user_id', $tecnicos_id);
        }

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $query->whereBetween('plano_acoes.created_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
        }

        return $query;
    }

    /**
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     */
    public function getPdasHistoricos(array $data, $withAtuacao = true)
    {
        $query = $this->getPdasAtualizacoesAcompanhamentos($data, $withAtuacao)
            ->join('plano_acao_historicos', 'plano_acoes.id', '=', 'plano_acao_historicos.plano_acao_id');

        //Garantir que a atuação fica restrita ao "plano_acao_historicos" (Chart 1_13b)
        $tecnicos_id = $this->service->getTecnicos(@$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id']);

        if (count($tecnicos_id) > 0) {
            $query->whereIn('plano_acao_historicos.user_id', $tecnicos_id);
        }

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $query->whereBetween('plano_acao_historicos.created_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
        }

        return $query;
    }

    /**
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     */
    public function getPdasAcoes(array $data, $withAtuacao = true)
    {
        $query = $this->getPdasAtualizacoes($data, $withAtuacao)
            ->join('plano_acao_itens', 'plano_acoes.id', '=', 'plano_acao_itens.plano_acao_id');

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $query->whereBetween('plano_acao_itens.created_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
        }

        return $query;
    }

    /**
     * Chart_1_6
     * Chart_1_8
     * Chart_1_13b
     */
    public function getPdasAcoesHistoricos(array $data, $withAtuacao = true)
    {
        // dd($this->getPdasAtualizacoes($data)->get());

        $query = $this->getPdasAtualizacoesAcompanhamentos($data, $withAtuacao)
            ->join('plano_acao_itens as PAI', 'plano_acoes.id', '=', 'PAI.plano_acao_id')
            ->join('plano_acao_item_historicos', 'PAI.id', '=', 'plano_acao_item_historicos.plano_acao_item_id');

        //Garantir que a atuação fica restrita ao "plano_acoes" (Chart 1_13b)
        $tecnicos_id = $this->service->getTecnicos(@$data['atuacao_dominio_id'], @$data['atuacao_unidade_operacional_id'], @$data['atuacao_tecnico_id']);
        if (count($tecnicos_id) > 0) {
            $query->whereIn('plano_acao_item_historicos.user_id', $tecnicos_id);
        }

        if (@$data['dt_ini'] && @$data['dt_end']) {
            $query->whereBetween('plano_acao_item_historicos.created_at', AppHelper::dateBetween($data['dt_ini'], $data['dt_end']));
        }

        // dd($query->get());

        return $query;
    }
}
