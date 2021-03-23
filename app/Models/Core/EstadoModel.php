<?php

namespace App\Models\Core;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class EstadoModel extends Model
{
    use SpatialTrait;

    protected $table = 'estados';

    protected $fillable = ['nome', 'poligono'];

    protected $spatialFields = ['poligono'];

    public function cidades()
    {
        return $this->hasMany(CidadeModel::class, 'estado_id', 'id');
    }

    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_abrangencia_estados', 'estado_id', 'unidade_operacional_id')->withPivot('id')->withTimestamps();
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
        return $this->belongsToMany(DadoModel::class, 'dado_abrangencia_estados', 'estado_id', 'dado_id')->withPivot('id')->withTimestamps()->withoutGlobalScopes();
    }
}
