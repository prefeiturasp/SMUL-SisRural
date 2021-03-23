<?php 

namespace App\Services;

use App\Helpers\General\GeoHelper;
use App\Models\Core\CidadeModel;
use App\Models\Core\DominioModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\RegiaoModel;
use App\Models\Core\UnidadeOperacionalModel;
use App\Models\Core\UnidadeProdutivaModel;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;
 
class UnidadeOperacionalService
{
    public function syncAbrangencias(UnidadeOperacionalModel $model) {
        return DB::transaction(function () use ($model) {
            $unidadesProdutivas = [];

            if(is_null($model->abrangenciaEstadual()->first()) && 
               is_null($model->abrangenciaMunicipal()->first()) && 
               is_null($model->regioes()->first())) {
                $model->unidadesProdutivasAutomaticas()->sync($unidadesProdutivas);
                return true;
            }

            foreach(UnidadeProdutivaModel::withoutGlobalScopes()->get() as $unidadeProdutiva) {
                $point = new Point($unidadeProdutiva->lat, $unidadeProdutiva->lng);

                if(!is_null(RegiaoModel::contains('poligono', $point)->whereIn('id', $model->regioes->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                } 
                elseif(!is_null(CidadeModel::contains('poligono', $point)->whereIn('id', $model->abrangenciaMunicipal->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                }
                elseif(!is_null(EstadoModel::contains('poligono', $point)->whereIn('id', $model->abrangenciaEstadual->pluck('id'))->first())) {
                    $unidadesProdutivas[] = $unidadeProdutiva->id;
                }
            }

            $model->unidadesProdutivasAutomaticas()->detach();
            $model->unidadesProdutivas()->syncWithoutDetaching($unidadesProdutivas);

            return true;
        });
    }

    public function consultaRestricaoAbrangencia($data) {
        if(!isset($data['abrangenciaEstadual']) && !isset($data['abrangenciaMunicipal']) && !isset($data['regioes'])) {
            return true;
        }

        $dominio = DominioModel::find(@$data['dominio_id']);

        if(!$dominio->abrangenciaEstadual()->exists() && !$dominio->abrangenciaMunicipal()->exists() && !$dominio->abrangenciaRegional()->exists()) {
            return true;
        }

        return GeoHelper::consultaAbrangencia(@$data['abrangenciaEstadual'], 
                                              @$data['abrangenciaMunicipal'], 
                                              @$data['regioes'],
                                              $dominio->abrangenciaEstadual->pluck('id')->toArray(),
                                              $dominio->abrangenciaMunicipal->pluck('id')->toArray(),
                                              $dominio->abrangenciaRegional->pluck('id')->toArray());

    }
}