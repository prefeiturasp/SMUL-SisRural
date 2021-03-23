<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Core\Traits\Scope\DominioPermissionScope;
use App\Models\Core\Traits\Scope\UnidadeOperacionalPermissionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Uma das entidades bases do sistema -> Unidade Operacional
 *
 * Domínio -> N Unidades Operacionais -> N unidades produtivas/produtores (definidos através da abrangência)
 */
class UnidadeOperacionalModel extends Model
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

        static::addGlobalScope(new UnidadeOperacionalPermissionScope);
    }

    protected $table = 'unidade_operacionais';

    protected $fillable = ['nome', 'telefone', 'endereco', 'dominio_id', 'abrangencia_at'];

    /**
     * Domínio que a unidade operacional faz parte
     */
    public function dominio()
    {
        return $this->belongsTo(DominioModel::class, 'dominio_id');
    }

    /**
     * Domínio que a unidade operacional faz parte ignorando o "Permission Scope" do projeto
     *
     * É utilizado somente na função singleDominio no User.php
     */
    public function dominioNS()
    {
        return $this->belongsTo(DominioModel::class, 'dominio_id')->withoutGlobalScopes([DominioPermissionScope::class]);
    }

    /**
     * Retorna todas as unidades produtivas vinculadas com a undiade operacional (tabela unidade_operacional_unidade_produtiva)
     */
    public function unidadesProdutivas()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_operacional_id', 'unidade_produtiva_id')->using(UnidadeOperacionalUnidadeProdutiva::class)->withPivot('id', 'add_manual')->withTimestamps();
    }

    /**
     * Unidades produtivas que foram adicionadas automaticamente pelo sistema (através da abrangência)
     */
    public function unidadesProdutivasAutomaticas()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_operacional_id', 'unidade_produtiva_id')
            ->using(UnidadeOperacionalUnidadeProdutiva::class)
            ->wherePivot('add_manual', false)
            ->withTimestamps();
    }

    /**
     * Unidades produtivas que foram adicionadas manualmente no sistema
     */
    public function unidadesProdutivasManuais()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_operacional_id', 'unidade_produtiva_id')
            ->using(UnidadeOperacionalUnidadeProdutiva::class)
            ->wherePivot('add_manual', true)
            ->withPivot('id', 'add_manual')
            ->withTimestamps();
    }

    /**
     * @deprecated Parece não estar sendo utilizado, revisar
     */
    public function unidadesProdutivasWithTrashed()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_operacional_id', 'unidade_produtiva_id')->using(UnidadeOperacionalUnidadeProdutiva::class)->withTrashed()->withPivot('id')->withTimestamps();
    }

    /**
     * Usuários que fazem parte das Unidades Operacionais. Sistema de permissões
     *
     * Nessa lista só vai ter usuários do tipo Unidade Operacional ou Técnico
     *
     * Usuários do tipo Domínio estarão na tabela "user_dominios"
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_unidade_operacionais', 'unidade_operacional_id', 'user_id')->whereNull('user_unidade_operacionais.deleted_at')->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos que fazem parte do cadastro de abrangência da Unidade Operacional
     */
    public function regioes()
    {
        return $this->belongsToMany(RegiaoModel::class, 'unidade_operacional_regioes', 'unidade_operacional_id', 'regiao_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaMunicipal()
    {
        return $this->belongsToMany(CidadeModel::class, 'unidade_operacional_abrangencia_cidades', 'unidade_operacional_id', 'cidade_id')->withPivot('id')->withTimestamps();
    }

    public function abrangenciaEstadual()
    {
        return $this->belongsToMany(EstadoModel::class, 'unidade_operacional_abrangencia_estados', 'unidade_operacional_id', 'estado_id')->withPivot('id')->withTimestamps();
    }

    /**
     * Atualiza a data que foi atualizado a abrangencia para ser utilizado pelo Sync Mobile
     *
     * Caso a data do mobile seja diferente, ele desloga o usuário p/ recarregar a nova abrangencia.
     */
    public function touchAbrangenciaAt() {
        $this->abrangencia_at = $this->freshTimestamp();
        $this->save();
    }
}
