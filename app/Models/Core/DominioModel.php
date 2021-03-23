<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Core\Traits\Scope\DominioPermissionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Dominio do sistema.
 *
 * Entidade BASE para todo funcionamento.
 *
 * Um domínio possuí N unidades operacionais que possuem N unidades produtivas (De acordo com a abrangência da unid. operacional e domínio).
 *
 */
class DominioModel extends Model
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

        static::addGlobalScope(new DominioPermissionScope);
    }

    protected $table = 'dominios';

    protected $fillable = ['nome'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dominios', 'dominio_id', 'user_id')->whereNull('user_dominios.deleted_at')->withPivot('id')->withTimestamps();
    }

    public function unidadesOperacionais()
    {
        return $this->hasMany(UnidadeOperacionalModel::class, 'dominio_id', 'id')->whereNull('unidade_operacionais.deleted_at');
    }

    /**
     * Retorna a unidade operacional relacionada ao Dominio
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     * @return mixed
     */
    public function unidadesOperacionaisWithoutGlobalScopes()
    {
        return $this->hasMany(UnidadeOperacionalModel::class, 'dominio_id', 'id')->whereNull('unidade_operacionais.deleted_at')->withoutGlobalScopes();
    }

    public function abrangenciaRegional()
    {
        return $this->belongsToMany(RegiaoModel::class, 'dominio_abrangencia_regioes', 'dominio_id', 'regiao_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaMunicipal()
    {
        return $this->belongsToMany(CidadeModel::class, 'dominio_abrangencia_cidades', 'dominio_id', 'cidade_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaEstadual()
    {
        return $this->belongsToMany(EstadoModel::class, 'dominio_abrangencia_estados', 'dominio_id', 'estado_id')->withPivot('id')->withTimestamps();
    }
}
