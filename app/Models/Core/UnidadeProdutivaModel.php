<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Core\Traits\ImportFillableCreatedAt;
use App\Models\Core\Traits\Scope\UnidadeProdutivaPermissionScope;
use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Uma das entidades bases do sistema, Unidade Produtiva
 *
 * Uma unidade produtiva pode ter N produtores.
 */
class UnidadeProdutivaModel extends Model
{
    use SoftDeletes;
    use DateFormat;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'unidade_produtivas';

    protected $fillable = ['id', 'nome', 'cep', 'endereco', 'bairro', 'subprefeitura', 'cidade_id', 'estado_id', 'car', 'ccir', 'itr', 'matricula', 'upa', 'gargalos', 'outorga_id', 'agua_qualidade', 'agua_disponibilidade', 'fl_risco_contaminacao', 'risco_contaminacao_observacoes', 'irrigacao', 'irrigacao_area_coberta', 'instalacao_maquinas_observacao', 'croqui_propriedade', 'fl_certificacoes', 'fl_car', 'fl_ccir', 'fl_itr', 'fl_matricula', 'fl_comercializacao', 'outros_usos_descricao', 'fl_producao_processa', 'producao_processa_descricao', 'area_total_solo', 'lat', 'lng', 'certificacoes_descricao', 'pressao_social_descricao', 'fl_pressao_social', 'owner_id', 'fl_fora_da_abrangencia_app', 'bacia_hidrografica', 'status', 'status_observacao', 'user_id', 'tags'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UnidadeProdutivaPermissionScope);

        self::creating(function ($model) {
            $model->user_id = \Auth::user()->id;

            if ($model->id)
                return;

            $model->id = (string) Uuid::generate(4);
        });
    }

    public function arquivos()
    {
        return $this->hasMany(UnidadeProdutivaArquivoModel::class, 'unidade_produtiva_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function arquivosManyOffline()
    {
        return $this->hasMany(UnidadeProdutivaArquivoModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function estado()
    {
        return $this->belongsTo(EstadoModel::class, 'estado_id')->select(['id', 'nome', 'uf']);
    }

    public function cidade()
    {
        return $this->belongsTo(CidadeModel::class, 'cidade_id')->select(['id', 'nome']);
    }

    public function outorga()
    {
        return $this->belongsTo(OutorgaModel::class);
    }

    public function produtores()
    {
        return $this->belongsToMany(ProdutorModel::class, 'produtor_unidade_produtiva', 'unidade_produtiva_id', 'produtor_id')->whereNull('produtor_unidade_produtiva.deleted_at')->withPivot('id', 'tipo_posse_id')->withTimestamps();
    }

    /**
     * Utilizado para fazer o sync dos dados
     */
    public function produtoresWithTrashed()
    {
        return $this->belongsToMany(ProdutorModel::class, 'produtor_unidade_produtiva', 'unidade_produtiva_id', 'produtor_id')->using(ProdutorUnidadeProdutivaModel::class)->withPivot('id', 'tipo_posse_id')->withTimestamps()->withTrashed();
    }

    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_produtiva_id', 'unidade_operacional_id')->whereNull('unidade_operacional_unidade_produtiva.deleted_at')->withPivot('id', 'add_manual')->withTimestamps();
    }

    public function unidadesOperacionaisNS()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_produtiva_id', 'unidade_operacional_id')
            ->whereNull('unidade_operacional_unidade_produtiva.deleted_at')
            ->withPivot('id', 'add_manual')
            ->withTimestamps()
            ->withoutGlobalScopes();
    }

    /**
     * Método utilizado para o sync de abrangência
     */
    public function unidadesOperacionaisAutomaticas()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_produtiva_id', 'unidade_operacional_id')
            ->whereNull('unidade_operacional_unidade_produtiva.deleted_at')
            ->where('unidade_operacional_unidade_produtiva.add_manual', false)
            ->withPivot('id', 'add_manual')
            ->withTimestamps();
    }

    /**
     * Método utilizado para o sync de abrangência
     */
    public function unidadesOperacionaisManuais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_produtiva_id', 'unidade_operacional_id')
            ->whereNull('unidade_operacional_unidade_produtiva.deleted_at')
            ->where('unidade_operacional_unidade_produtiva.add_manual', true)
            ->withPivot('id', 'add_manual')
            ->withTimestamps();
    }

    public function colaboradores()
    {
        return $this->hasMany(ColaboradorModel::class, 'unidade_produtiva_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function colaboradoresOffline()
    {
        return $this->hasMany(ColaboradorModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function instalacoes()
    {
        return $this->hasMany(InstalacaoModel::class, 'unidade_produtiva_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function instalacoesOffline()
    {
        return $this->hasMany(InstalacaoModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function caracterizacoes()
    {
        return $this->hasMany(UnidadeProdutivaCaracterizacaoModel::class, 'unidade_produtiva_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function caracterizacoesOffline()
    {
        return $this->hasMany(UnidadeProdutivaCaracterizacaoModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function caracterizacoesOfflineAll()
    {
        return $this->caracterizacoesOffline();
    }

    public function canaisComercializacao()
    {
        return $this->belongsToMany(CanalComercializacaoModel::class, 'unidade_produtiva_canal_comercializacoes', 'unidade_produtiva_id', 'canal_comercializacao_id')->using(UnidadeProdutivaCanalComercializacaoModel::class)->whereNull('unidade_produtiva_canal_comercializacoes.deleted_at')->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function canaisComercializacaoWithTrashed()
    {
        return $this->belongsToMany(CanalComercializacaoModel::class, 'unidade_produtiva_canal_comercializacoes', 'unidade_produtiva_id', 'canal_comercializacao_id')->using(UnidadeProdutivaCanalComercializacaoModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function canaisComercializacaoOffline()
    {
        return $this->hasMany(UnidadeProdutivaCanalComercializacaoModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function solosCategoria()
    {
        return $this->belongsToMany(SoloCategoriaModel::class, 'unidade_produtiva_solo_categorias', 'unidade_produtiva_id', 'solo_categoria_id')->whereNull('unidade_produtiva_solo_categorias.deleted_at')->using(UnidadeProdutivaSoloCategoriaModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function solosCategoriaWithTrashed()
    {
        return $this->belongsToMany(SoloCategoriaModel::class, 'unidade_produtiva_solo_categorias', 'unidade_produtiva_id', 'solo_categoria_id')->using(UnidadeProdutivaSoloCategoriaModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function solosCategoriaOffline()
    {
        return $this->hasMany(UnidadeProdutivaSoloCategoriaModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function riscosContaminacaoAgua()
    {
        return $this->belongsToMany(RiscoContaminacaoAguaModel::class, 'unidade_produtiva_risco_contaminacao_aguas', 'unidade_produtiva_id', 'risco_contaminacao_agua_id')->whereNull('unidade_produtiva_risco_contaminacao_aguas.deleted_at')->using(UnidadeProdutivaRiscoContaminacaoAguaModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function riscosContaminacaoAguaWithTrashed()
    {
        return $this->belongsToMany(RiscoContaminacaoAguaModel::class, 'unidade_produtiva_risco_contaminacao_aguas', 'unidade_produtiva_id', 'risco_contaminacao_agua_id')->using(UnidadeProdutivaRiscoContaminacaoAguaModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function riscosContaminacaoAguaOffline()
    {
        return $this->hasMany(UnidadeProdutivaRiscoContaminacaoAguaModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function tiposFonteAgua()
    {
        return $this->belongsToMany(TipoFonteAguaModel::class, 'unidade_produtiva_tipo_fonte_aguas', 'unidade_produtiva_id', 'tipo_fonte_agua_id')->whereNull('unidade_produtiva_tipo_fonte_aguas.deleted_at')->using(UnidadeProdutivaTipoFonteAguaModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function tiposFonteAguaWithTrashed()
    {
        return $this->belongsToMany(TipoFonteAguaModel::class, 'unidade_produtiva_tipo_fonte_aguas', 'unidade_produtiva_id', 'tipo_fonte_agua_id')->using(UnidadeProdutivaTipoFonteAguaModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function tiposFonteAguaOffline()
    {
        return $this->hasMany(UnidadeProdutivaTipoFonteAguaModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function certificacoes()
    {
        return $this->belongsToMany(CertificacaoModel::class, 'unidade_produtiva_certificacoes', 'unidade_produtiva_id', 'certificacao_id')->whereNull('unidade_produtiva_certificacoes.deleted_at')->using(UnidadeProdutivaCertificacaoModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function certificacoesWithTrashed()
    {
        return $this->belongsToMany(CertificacaoModel::class, 'unidade_produtiva_certificacoes', 'unidade_produtiva_id', 'certificacao_id')->using(UnidadeProdutivaCertificacaoModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function certificacoesOffline()
    {
        return $this->hasMany(UnidadeProdutivaCertificacaoModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function residuoSolidos()
    {
        return $this->belongsToMany(ResiduoSolidoModel::class, 'unidade_produtiva_residuo_solidos', 'unidade_produtiva_id', 'residuo_solido_id')->whereNull('unidade_produtiva_residuo_solidos.deleted_at')->using(UnidadeProdutivaResiduoSolidoModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function residuoSolidosWithTrashed()
    {
        return $this->belongsToMany(ResiduoSolidoModel::class, 'unidade_produtiva_residuo_solidos', 'unidade_produtiva_id', 'residuo_solido_id')->using(UnidadeProdutivaResiduoSolidoModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function residuoSolidosOffline()
    {
        return $this->hasMany(UnidadeProdutivaResiduoSolidoModel::class, 'unidade_produtiva_id')->withTrashed();
    }


    public function esgotamentoSanitarios()
    {
        return $this->belongsToMany(EsgotamentoSanitarioModel::class, 'unidade_produtiva_esgotamento_sanitarios', 'unidade_produtiva_id', 'esgotamento_sanitario_id')->whereNull('unidade_produtiva_esgotamento_sanitarios.deleted_at')->using(UnidadeProdutivaEsgotamentoSanitarioModel::class)->withPivot('id')->withTimestamps();
    }
    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function esgotamentoSanitariosWithTrashed()
    {
        return $this->belongsToMany(EsgotamentoSanitarioModel::class, 'unidade_produtiva_esgotamento_sanitarios', 'unidade_produtiva_id', 'esgotamento_sanitario_id')->using(UnidadeProdutivaEsgotamentoSanitarioModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function esgotamentoSanitariosOffline()
    {
        return $this->hasMany(UnidadeProdutivaEsgotamentoSanitarioModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function pressaoSociais()
    {
        return $this->belongsToMany(PressaoSocialModel::class, 'unidade_produtiva_pressao_sociais', 'unidade_produtiva_id', 'pressao_social_id')->whereNull('unidade_produtiva_pressao_sociais.deleted_at')->using(UnidadeProdutivaPressaoSocialModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Utilizado pelo método "syncSoftDelete"
     */
    public function pressaoSociaisWithTrashed()
    {
        return $this->belongsToMany(PressaoSocialModel::class, 'unidade_produtiva_pressao_sociais', 'unidade_produtiva_id', 'pressao_social_id')->using(UnidadeProdutivaPressaoSocialModel::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function pressaoSociaisOffline()
    {
        return $this->hasMany(UnidadeProdutivaPressaoSocialModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function respostasMany()
    {
        return $this->hasMany(UnidadeProdutivaRespostaModel::class, 'unidade_produtiva_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function respostasManyOffline()
    {
        return $this->hasMany(UnidadeProdutivaRespostaModel::class, 'unidade_produtiva_id')->withTrashed();
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistUnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    /*
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
    */
    public function unidadesOperacionaisScoped()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'unidade_operacional_unidade_produtiva', 'unidade_produtiva_id', 'unidade_operacional_id')->whereNull('unidade_operacional_unidade_produtiva.deleted_at');
    }

    /**
     * Utilizado pelo Sync da Abrangência
     */
    public function unidadesOperacionaisAddManualNS()
    {
        return $this->unidadesOperacionaisScoped()
            ->where('add_manual', true)
            ->withoutGlobalScopes();
    }

    /*
     * Métodos sem escopo, para bypassar o permissionScope
     *
     * Eles são utilizados dentro do ApiDadoController
    */
    public function produtoresWithoutGlobalScopes()
    {
        return $this->belongsToMany(ProdutorModel::class, 'produtor_unidade_produtiva', 'unidade_produtiva_id', 'produtor_id')->whereNull('produtor_unidade_produtiva.deleted_at')->withPivot('id', 'tipo_posse_id')->withTimestamps()->withoutGlobalScopes();
    }

    /**
     * Método utilizado para o sync de abrangência com o "dado" (Sampa+Rural).
     */
    public function dados()
    {
        return $this->belongsToMany(DadoModel::class, 'dado_unidade_produtivas', 'unidade_produtiva_id', 'dado_id')
            ->whereNull('dado_unidade_produtivas.deleted_at')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Utilizado pelo a filtragem do Mapa/Bi/Relatórios, retornando todas referencias de plano de ação, independente do formulário.
     */
    public function planoAcoes()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'unidade_produtiva_id');
    }

    /**
     * Retorna qual foi o usuário que criou o formulário
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes();
    }

    /**
     * UTILIZAR APENAS NO REPORT, porque o SQL deve ter um campo "produtor_id"
     *
     * NÃO UTILIZAR EM OUTRA PARTE DO SISTEMA
     */
    public function produtor()
    {
        return $this->belongsTo(ProdutorModel::class, 'produtor_id');
    }

    /**
     * UTILIZAR APENAS NO REPORT, porque o SQL deve ter um campo "tipo_posse_id"
     *
     * NÃO UTILIZAR EM OUTRA PARTE DO SISTEMA
     */
    public function tipoPosse()
    {
        return $this->belongsTo(TipoPosseModel::class, 'tipo_posse_id');
    }

    /**
     * UTILIZAR APENAS NO REPORT
     *
     * NÃO UTILIZAR EM OUTRA PARTE DO SISTEMA
     */
    public function cadernos()
    {
        return $this->hasMany(CadernoModel::class, 'unidade_produtiva_id');
    }
}
