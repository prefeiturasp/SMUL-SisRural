<?php

namespace App\Services;

use App\Models\Core\CidadeModel;
use App\Models\Core\DadoModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\RegiaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;

class DadoService
{
    public function syncAbrangencias(DadoModel $model)
    {
        return DB::transaction(function () use ($model) {
            $unidadesProdutivas = [];

            if (
                is_null($model->abrangenciaEstadual()->first()) &&
                is_null($model->abrangenciaMunicipal()->first()) &&
                is_null($model->regioes()->first())
            ) {
                $model->unidadesProdutivas()->sync($unidadesProdutivas);
                return true;
            }

            foreach (UnidadeProdutivaModel::withoutGlobalScopes()->get() as $unidadeProdutiva) {
                $point = new Point($unidadeProdutiva->lat, $unidadeProdutiva->lng);

                if (!is_null(EstadoModel::contains('poligono', $point)->whereIn('id', $model->abrangenciaEstadual->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                } elseif (!is_null(CidadeModel::contains('poligono', $point)->whereIn('id', $model->abrangenciaMunicipal->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                } elseif (!is_null(RegiaoModel::contains('poligono', $point)->whereIn('id', $model->regioes->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                }
            }

            $model->unidadesProdutivas()->sync($unidadesProdutivas);

            return true;
        });
    }
}
