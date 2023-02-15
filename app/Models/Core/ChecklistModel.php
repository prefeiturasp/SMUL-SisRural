<?php

namespace App\Models\Core;

use App\Enums\TemplateChecklistStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\Traits\Scope\ChecklistPermissionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Template do formulário.
 *
 * a) Permissão de aplicação de um formulário
 * b) Permissão de Análise/Fluxo de aprovação
 * c) Tipo de pontuação (score)
 *
 */
class ChecklistModel extends Model
{
    use SoftDeletes;

    protected $table = 'checklists';

    protected $fillable = ['dominio_id', 'nome', 'status', 'formula', 'formula_prefix', 'formula_sufix', 'fl_fluxo_aprovacao', 'instrucoes', 'plano_acao', 'versao', 'copia_checklist_id', 'tipo_pontuacao', 'instrucoes_pda', 'fl_nao_normalizar_percentual', 'fl_gallery'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ChecklistPermissionScope);
    }

    /**
     * Utilizado apenas para não mostrar os checklists em modo "rascunho" e "inativos" na listagem de Aplicação e nem exibir os checklists usados para dados adicionais através do arquivo de configuração .env. Estes só podem ser preenchidos via formulário da produtora ou UP.
     */
    public function scopePublicado($query)
    {
        if(config('app.checklist_dados_adicionais_unidade_produtiva')){
            $query->where('id', '!=', config('app.checklist_dados_adicionais_unidade_produtiva'));
        }
        if(config('app.checklist_dados_adicionais_produtora')){
            $query->where('id', '!=', config('app.checklist_dados_adicionais_produtora'));
        }        
        return $query->where('status', TemplateChecklistStatusEnum::Publicado);
    }

    /**
     * Retorna as categorias do formulário selecionado
     * @return mixed
     */
    public function categorias()
    {
        return $this->hasMany(ChecklistCategoriaModel::class, 'checklist_id')->whereNull('checklist_categorias.deleted_at')->orderBy('ordem', 'ASC');
    }

    /**
     * Retorna as categorias p/ serem consumidas pelo APP (sync)
     * @return mixed
     */
    public function categoriasOffline()
    {
        return $this->hasMany(ChecklistCategoriaModel::class, 'checklist_id')->withTrashed()->orderBy('ordem', 'ASC');
    }

    /**
     * Domínio que CRIOU o checklist
     * @return mixed
     */
    public function dominio()
    {
        return $this->belongsTo(DominioModel::class, 'dominio_id');
    }

    /**
     * Retorna o domínio que criou o formulário.
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     * @return mixed
     */
    public function dominioWithoutGlobalScopes()
    {
        return $this->belongsTo(DominioModel::class, 'dominio_id')->withoutGlobalScopes();
    }

    /**
     * Retorna os "domínios" que podem "APLICAR" o formulário.
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     * @return mixed
     */
    public function dominios()
    {
        return $this->belongsToMany(DominioModel::class, 'checklist_dominios', 'checklist_id', 'dominio_id')->withoutGlobalScopes()->using(ChecklistDominioModel::class)->whereNull('checklist_dominios.deleted_at')->withPivot('id')->withTimestamps();
    }

    public function dominiosWithTrashed()
    {
        return $this->belongsToMany(DominioModel::class, 'checklist_dominios', 'checklist_id', 'dominio_id')->withoutGlobalScopes()->using(ChecklistDominioModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Retorna as "unidades operacionais" que podem "APLICAR" o formulário
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     * @return mixed
     */
    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'checklist_unidade_operacionais', 'checklist_id', 'unidade_operacional_id')->withoutGlobalScopes()->using(ChecklistUnidadeOperacionalModel::class)->whereNull('checklist_unidade_operacionais.deleted_at')->withPivot('id')->withTimestamps();
    }
    public function unidadesOperacionaisWithTrashed()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'checklist_unidade_operacionais', 'checklist_id', 'unidade_operacional_id')->withoutGlobalScopes()->using(ChecklistUnidadeOperacionalModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Retorna os "usuários" que podem "APLICAR" o formulário
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada

     * @return mixed
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'checklist_users', 'checklist_id', 'user_id')->using(ChecklistUserModel::class)->withoutGlobalScopes()->whereNull('checklist_users.deleted_at')->withPivot('id')->withTimestamps();
    }

    public function usuariosWithTrashed()
    {
        return $this->belongsToMany(User::class, 'checklist_users', 'checklist_id', 'user_id')->using(ChecklistUserModel::class)->withoutGlobalScopes()->withPivot('id')->withTimestamps();
    }



    /**
     * Retorna os "usuários" que podem "APROVAR" o formulário
     *
     * Essa Relation está desconsiderando o escopo porque o retorno precisa ser de todos os USUÁRIOS que tem permissão para aprovação
     */
    public function usuariosAprovacao()
    {
        return $this->belongsToMany(User::class, 'checklist_aprovacao_users', 'checklist_id', 'user_id')->using(ChecklistAprovacaoUsersModel::class)->withoutGlobalScopes()->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
     *
     * @deprecated Não é mais utilizado, apesar da lógica estar correta, foi feito uma redundância no ChecklistPermissionScope para otimizar a consulta
     */
    public function dominiosScoped()
    {
        return $this->belongsToMany(DominioModel::class, 'checklist_dominios', 'checklist_id', 'dominio_id')->using(ChecklistDominioModel::class)->whereNull('checklist_dominios.deleted_at')->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
     *
     */
    public function unidadesOperacionaisScoped()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'checklist_unidade_operacionais', 'checklist_id', 'unidade_operacional_id')->using(ChecklistUnidadeOperacionalModel::class)->whereNull('checklist_unidade_operacionais.deleted_at')->withPivot('id')->withTimestamps();
    }


    /**
     * Utilizado somente pelo APP p/ saber se o usuário tem permissão p/ aplicar o Caderno de Campo listado
     * @return mixed
     */
    public function getCanApplyAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('apply', $this);
    }
}
