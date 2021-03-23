<?php

namespace App\Services;

use App\Models\Core\CidadeModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\RegiaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use Auth;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UnidadeProdutivaService
{
    /**
     * Regiao, Cidade, Estado ignora PermissionScope (NS) pois precisa retornar todas as Unidades Operacionais para fazer o sync.
     */
    public function syncAbrangencias(UnidadeProdutivaModel $model)
    {
        return DB::transaction(function () use ($model) {
            $point = new Point($model->lat, $model->lng);
            $regioes = RegiaoModel::contains('poligono', $point)->get();
            $cidade = CidadeModel::contains('poligono', $point)->first();

            $unidadesOperacionais = [];

            /**
             * Abrangência
             * Unidade Produtiva adicionada manualmente na Unidade Operacional
             */
            $unidadesOperacionaisUpaAddManual = $model->unidadesOperacionaisAddManualNS->pluck('id')->toArray();
            $unidadesOperacionais = array_merge($unidadesOperacionais, $unidadesOperacionaisUpaAddManual);

            /**
             * Abrangência Regional
             */
            foreach ($regioes as $regiao) {
                $unidadesOperacionais = array_merge($unidadesOperacionais, $regiao->unidadesOperacionaisNS->pluck('id')->toArray());
            }

            if ($cidade) {
                /**
                 * Abrangência Municipal
                 */
                $unidadesOperacionais = array_merge($unidadesOperacionais, $cidade->unidadesOperacionaisNS->pluck('id')->toArray());

                /**
                 * Abrangência Estadual
                 */
                $unidadesOperacionais = array_merge($unidadesOperacionais, EstadoModel::find($cidade->estado_id)->unidadesOperacionaisNS->pluck('id')->toArray());
            }

            $model->unidadesOperacionaisAutomaticas()->sync($unidadesOperacionais);

            /**
             * Integração com o Sampa+Rural (DadoModel)
             */
            $dados = [];

            //Regional
            foreach ($regioes as $regiao) {
                $dados = array_merge($dados, $regiao->dadosNS->pluck('id')->toArray());
            }

            if ($cidade) {
                //Municipal
                $dados = array_merge($dados, $cidade->dadosNS->pluck('id')->toArray());

                //Estadual
                $dados = array_merge($dados, EstadoModel::find($cidade->estado_id)->dadosNS->pluck('id')->toArray());
            }

            $model->dados()->sync($dados);

            return true;
        });
    }

    public function consultaAbrangencia($lat, $lng)
    {
        $point = new Point($lat, $lng);

        if (null !== RegiaoModel::whereHas('unidadesOperacionais', function (Builder $q) {
            $q->whereIn('unidade_operacional_id', Auth::user()->unidadesOperacionais->pluck('id'));
        })->contains('poligono', $point)->first()) {
            return true;
        }

        if (null !== EstadoModel::whereHas('unidadesOperacionais', function (Builder $q) {
            $q->whereIn('unidade_operacional_id', Auth::user()->unidadesOperacionais->pluck('id'));
        })->contains('poligono', $point)->first()) {
            return true;
        }

        if (null !== CidadeModel::whereHas('unidadesOperacionais', function (Builder $q) {
            $q->whereIn('unidade_operacional_id', Auth::user()->unidadesOperacionais->pluck('id'));
        })->contains('poligono', $point)->first()) {
            return true;
        }

        return false;
    }
}
