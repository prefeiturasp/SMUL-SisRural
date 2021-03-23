<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Entidade p/ liberar acesso a um grupo (abrangência) de Unidades Produtivas. (API Sampa+Rural)
 *
 * Dado -> Abrangências (Estaduais, Municipais, Regioes) -> Unidades produtivas que estão dentro da abrangência
 *
 * Importante remover o "globalScopes" dos Models requisitados, pois o "permissionScope" funciona com base de um "User" (Model).
 *
 * O acesso da api de dados funciona em base de um "DadoModel".
 *
 * Foi criado um custom provider (e custom guard) p/ retornar o acesso.
 *
 * O token passado determina qual "DadoModel" é acessado e através dele é possível acessar as unidades produtivas.
 */
class DadoModel extends Model
{
    use SoftDeletes;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    protected $table = 'dados';

    protected $fillable = ['nome', 'api_token'];

    /**
     * Retorna todas as unidades produtivas vinculadas com a unidade operacional (tabela unidade_operacional_unidade_produtiva)
     *
     * Foi ignorado o scope (withoutGlobalScopes) porque quem vai definir o retorno dos dados é as abrangências relacionadas ao DadoModel (e não ao DominioModel/UnidadeOperacionalModel)
     */
    public function unidadesProdutivas()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'dado_unidade_produtivas', 'dado_id', 'unidade_produtiva_id')->using(DadoUnidadeProdutivaModel::class)->withoutGlobalScopes();
    }

    /**
     * Métodos que fazem parte do cadastro de abrangência do Dado
     */
    public function regioes()
    {
        return $this->belongsToMany(RegiaoModel::class, 'dado_abrangencia_regioes', 'dado_id', 'regiao_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaMunicipal()
    {
        return $this->belongsToMany(CidadeModel::class, 'dado_abrangencia_cidades', 'dado_id', 'cidade_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaEstadual()
    {
        return $this->belongsToMany(EstadoModel::class, 'dado_abrangencia_estados', 'dado_id', 'estado_id')->withPivot('id')->withTimestamps();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
}
