<?php 

namespace App\Services;

use App\Helpers\General\GeoHelper;
use App\Models\Core\DominioModel;
 
class DominioService
{
    public function consultaRestricaoAbrangencia($data, DominioModel $dominio) {

        if(!isset($data['abrangenciaEstadual']) && !isset($data['abrangenciaMunicipal']) && !isset($data['abrangenciaRegional'])) {
            return true;
        }

        $unidadesOperacionais = $dominio->unidadesOperacionais()->get();
        foreach($unidadesOperacionais AS $unidadeOperacional) {
            
            if(!$unidadeOperacional->abrangenciaEstadual()->exists() && 
               !$unidadeOperacional->abrangenciaMunicipal()->exists() && 
               !$unidadeOperacional->regioes()->exists()) {
                continue;
            }

            if(!GeoHelper::consultaAbrangencia($unidadeOperacional->abrangenciaEstadual->pluck('id')->toArray(),
                                               $unidadeOperacional->abrangenciaMunicipal->pluck('id')->toArray(),
                                               $unidadeOperacional->regioes->pluck('id')->toArray(),
                                               @$data['abrangenciaEstadual'], 
                                               @$data['abrangenciaMunicipal'], 
                                               @$data['abrangenciaRegional'])) {
                return false;
            }
        }
        return true;
    }
}