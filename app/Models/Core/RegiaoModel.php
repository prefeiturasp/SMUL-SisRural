<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

/**
 * Regiões do sistema, é uma das formas utilizadas para definir as abrangências de um domínio / unidade operacional
 */
class RegiaoModel extends Model
{
    use SpatialTrait;

    protected $table = 'regioes';

    protected $fillable = ['nome', 'poligono'];
    protected $spatialFields = ['poligono'];

    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_regioes', 'regiao_id', 'unidade_operacional_id')->withPivot('id')->withTimestamps();
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
        return $this->belongsToMany(DadoModel::class, 'dado_abrangencia_regioes', 'regiao_id', 'dado_id')->withPivot('id')->withTimestamps()->withoutGlobalScopes();
    }
}
