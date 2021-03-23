<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

/**
 * Utilizado baiscamente pelo Produtor/Unidade Produtiva
 */
class CidadeModel extends Model
{
    use SpatialTrait;

    protected $table = 'cidades';

    protected $fillable = ['nome', 'poligono', 'lat', 'lng', 'fl_center_point', 'fl_center_point_ok', 'fl_inside_state', 'fl_inside_state_ok'];
    protected $spatialFields = ['poligono'];

    public function estado()
    {
        return $this->belongsTo('App\Models\Core\EstadoModel');
    }

    /**
     * Cidades que fazem parte de determinadas unidades operacionais. Regra de abrangência.
     *
     * @return mixed
     */
    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_abrangencia_cidades', 'cidade_id', 'unidade_operacional_id')->withPivot('id')->withTimestamps();
    }

    public function unidadesOperacionaisNS()
    {
        return $this->unidadesOperacionais()->withoutGlobalScopes();
    }

    /**
     * Integração com "Sampa+Rural", ou qualquer outra liberação de dados
     */
    public function dadosNS()
    {
        return $this->belongsToMany(DadoModel::class, 'dado_abrangencia_cidades', 'cidade_id', 'dado_id')->withPivot('id')->withTimestamps()->withoutGlobalScopes();
    }
}
