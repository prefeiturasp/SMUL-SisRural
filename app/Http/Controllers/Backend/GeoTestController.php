<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\GeoHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\CidadeModel;
use App\Models\Core\EstadoModel;
use Grimzy\LaravelMysqlSpatial\Types\Point;

/**
 * Rodar o migrate add_columns_estados_cidades, descomentando as colunas fl_inside_state, fl_inside_state_ok, fl_center_point, fl_center_point_ok
 */
class GeoTestController extends Controller
{

    /**
     * Verifica se a cidade (poligno) esta contido no estado (poligno)
     */
    public function checkCidadesDentroEstados()
    {
        $cidades = CidadeModel::with('estado:id,nome,uf')->where('fl_inside_state', 0)->get(['id', 'nome', 'estado_id']);
        foreach ($cidades as $v) {
            $ret = GeoHelper::consultaAbrangencia(null, [$v->id], null, [$v->estado->id], null, null);
            $v->update(['fl_inside_state' => 1, 'fl_inside_state_ok' => $ret ? 1 : 0]);
        }

        $cidadesNOk = CidadeModel::where(['fl_inside_state_ok' => 0, 'fl_inside_state' => 1])->get(['id', 'nome'])->pluck('nome', 'id');
        foreach ($cidadesNOk as $k => $v) {
            echo '[NÃO_OK] ' . $k . ' - ' . $v . '<br>';
        }

        die();
    }

    /**
     * Verifica se a lat/lng (coluna lat e lng) das cidades estão contidas no poligno da cidade
     */
    public function checkCidadesCenterPoint()
    {
        $cities = CidadeModel::where("fl_center_point", 0)->get(['id', 'nome', 'lat', 'lng']);

        foreach ($cities as $v) {
            $ret = $this->checkLatLngCidade($v->lat, $v->lng, $v->id);
            $v->update(['fl_center_point' => 1, 'fl_center_point_ok' => $ret ? 1 : 0]);
        }

        $cidadesNOk = CidadeModel::where(['fl_center_point_ok' => 0, 'fl_center_point' => 1])->get(['id', 'nome'])->pluck('nome', 'id');
        foreach ($cidadesNOk as $k => $v) {
            echo '[NÃO_OK] ' . $k . ' - ' . $v . '<br>';
        }

        die();
    }

    /**
     * Verifica um ponto dentro do poligno no estado.
     */
    public function checkEstados()
    {
        $states = EstadoModel::get(['id', 'nome', 'uf']);

        $latLngStates = [
            'AC' => [-8.77, -70.55], 'AL' => [-9.672888, -36.622562], 'AM' => [-3.07, -61.66], 'AP' => [1.41, -51.77], 'BA' => [-12.962179, -38.504396], 'CE' => [-5.467545, -39.390530], 'DF' => [-15.83, -47.86], 'ES' => [-19.19, -40.34], 'GO' => [-16.64, -49.31], 'MA' => [-4.169525, -44.799387], 'MT' => [-12.64, -55.42], 'MS' => [-20.51, -54.54], 'MG' => [-18.10, -44.38], 'PA' => [-5.53, -52.29], 'PB' => [-7.06, -35.55], 'PR' => [-24.89, -51.55], 'PE' => [-8.28, -35.07], 'PI' => [-8.28, -43.68], 'RJ' => [-22.922086, -43.200267], 'RN' => [-5.22, -36.52], 'RO' => [-11.22, -62.80], 'RS' => [-30.01, -51.22], 'RR' => [1.89, -61.22], 'SC' => [-27.33, -49.44], 'SE' => [-10.90, -37.07], 'SP' => [-23.55, -46.64], 'TO' => [-10.25, -48.25]
        ];

        foreach ($states as $v) {
            $ret = $this->checkLatLngEstado($latLngStates[$v->uf][0], $latLngStates[$v->uf][1], $v->id);
            if (!$ret) {
                echo '[NÃO_OK] ' . $v->nome . '<br>';
            } else {
                echo '[OK] ' . $v->nome . '<br>';
            }
        }

        die();
    }

    protected function checkLatLngCidade($lat, $lng, $cityId)
    {
        $point = new Point($lat, $lng);

        if (CidadeModel::findOrFail($cityId)->contains('poligono', $point)->exists()) {
            return true;
        }

        return false;
    }

    protected function checkLatLngEstado($lat, $lng, $stateId)
    {
        $point = new Point($lat, $lng);

        if (EstadoModel::findOrFail($stateId)->contains('poligono', $point)->exists()) {
            return true;
        }

        return false;
    }
}
