<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Core\UnidadeProdutivaModel;
use App\Services\ReportService;

/**
 * Class MapaController.
 */
class MapaController extends Controller
{
    private $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        //$action = route('admin.core.mapa.data');
        $action = config('app.endpoint_bi') . 'admin/mapa/data';

        $viewFilter = $this->service->viewFilter($action, true, false, false);

        return \Response::view('backend.core.report.mapa.index', ['viewFilter' => $viewFilter])
            ->header('Content-Security-Policy', ""); //workaround load leaflet-omnivore.min.js
    }

    public function data(Request $request)
    {
        try {
            $data = $request->only(
                [
                    'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                    'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                    'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva'
                ]
            );

            $query = UnidadeProdutivaModel::select('unidade_produtivas.id', 'unidade_produtivas.nome', 'unidade_produtivas.socios', 'produtores.id as produtor_id', 'produtores.nome as produtor_nome',  'lat', 'lng')
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

            $list = $query->get();

            // if ($this->service->isPrivateData()) {
            if (\Auth::user()->can('report restricted')) {
                $list->makeHidden(['nome', 'socios', 'produtor_nome']);
            }

            return response()->json($list);
        } catch (\Exception $e) {
            return response()->json($e, 404);
        }
    }
}
