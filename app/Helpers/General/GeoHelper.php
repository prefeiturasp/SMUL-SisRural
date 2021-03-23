<?php

namespace App\Helpers\General;

use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Driver\PDOConnection;

class GeoHelper
{

    /**
     * Método utilizado para extrair a latitude/longitude do arquivo.
     *
     * @param string $filename
     * @return void
     */
    public static function extractLatLngFile($filename)
    {
        $exif = @exif_read_data($filename);
        $lat = @self::gps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
        $lng = @self::gps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);

        return ['lat' => $lat, 'lng' => $lng];
    }

    /**
     * Normaliza a coordenadas para retorno da latitude/longitude
     *
     * @param string $coordinate
     * @param string $hemisphere
     * @return void
     */
    private static function gps($coordinate, $hemisphere)
    {
        if (!$coordinate) {
            return "";
        }

        if (is_string($coordinate)) {
            $coordinate = array_map("trim", explode(",", $coordinate));
        }

        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordinate[$i]);
            if (count($part) == 1) {
                $coordinate[$i] = $part[0];
            } else if (count($part) == 2) {
                $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
            } else {
                $coordinate[$i] = 0;
            }
        }

        list($degrees, $minutes, $seconds) = $coordinate;
        $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;

        return $sign * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    /**
     * Concatena os ids dos estados, cidades e regiões (polignos)
     */
    public static function concatenaPoligonos($estados, $cidades, $regioes)
    {
        $estados = implode(',', (array)$estados);
        $cidades = implode(',', (array)$cidades);
        $regioes = implode(',', (array)$regioes);
        $result = null;

        $db = DB::connection()->getPdo();

        $queryResult = $db->prepare('call concatenaPoligonos(?, ?, ?, @result)');
        $queryResult->bindParam(1, $estados, PDOConnection::PARAM_STR);
        $queryResult->bindParam(2, $cidades, PDOConnection::PARAM_STR);
        $queryResult->bindParam(3, $regioes, PDOConnection::PARAM_STR);
        $queryResult->execute();
        $result = $db->query("select @result as result")->fetch(PDOConnection::FETCH_ASSOC);

        return @$result['result'];
    }

    /**
     * Consulta a abrangencia, para saber se esta contido ou não dentro do estado / cidade ou região
     *
     * @return void
     */
    public static function consultaAbrangencia(
        $estadosConsulta,
        $cidadesConsulta,
        $regioesConsulta,
        $estadosAbrangencia,
        $cidadesAbrangencia,
        $regioesAbrangencia
    ) {

        $estadosConsulta = implode(',', (array)$estadosConsulta);
        $cidadesConsulta = implode(',', (array)$cidadesConsulta);
        $regioesConsulta = implode(',', (array)$regioesConsulta);
        $estadosAbrangencia = implode(',', (array)$estadosAbrangencia);
        $cidadesAbrangencia = implode(',', (array)$cidadesAbrangencia);
        $regioesAbrangencia = implode(',', (array)$regioesAbrangencia);
        $result = null;

        $db = DB::connection()->getPdo();

        $queryResult = $db->prepare('call consultaAbrangencia(?, ?, ?, ?, ?, ?, @result)');
        $queryResult->bindParam(1, $estadosConsulta, PDOConnection::PARAM_STR);
        $queryResult->bindParam(2, $cidadesConsulta, PDOConnection::PARAM_STR);
        $queryResult->bindParam(3, $regioesConsulta, PDOConnection::PARAM_STR);
        $queryResult->bindParam(4, $estadosAbrangencia, PDOConnection::PARAM_STR);
        $queryResult->bindParam(5, $cidadesAbrangencia, PDOConnection::PARAM_STR);
        $queryResult->bindParam(6, $regioesAbrangencia, PDOConnection::PARAM_STR);
        $queryResult->execute();
        $result = $db->query("select @result as result")->fetch(PDOConnection::FETCH_ASSOC);

        return @$result['result'];
    }

    /**
     * Método para validar se o poligno é válido.
     *
     * $poligno = MultiPolygon::fromWKT($kml->toWkt())
     *
     * @param  mixed $poligono
     * @return void
     */
    public static function validaPoligono($poligono)
    {
        $result = DB::select("SELECT geoIsValid('MULTIPOLYGON ($poligono)') AS status");
        return $result[0]->status;
    }
}
