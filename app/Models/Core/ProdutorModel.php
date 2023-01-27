<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Core\Traits\ImportFillableCreatedAt;
use App\Models\Core\Traits\Scope\ProdutorPermissionScope;
use App\Models\Core\Traits\Scope\UnidadeProdutivaPermissionScope;
use App\Models\Traits\DateFormat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;
use Wildside\Userstamps\Userstamps;

/**
 * Uma das entidades bases do projeto.
 *
 * Um produtor tem N unidades produtivas.
 */
class ProdutorModel extends Model
{
    use SoftDeletes;
    use DateFormat;
    use ImportFillableCreatedAt;
    use Userstamps;    

    public $incrementing = false;

    protected $table = 'produtores';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        // 'tipo',
        'nome',
        'fl_agricultor_familiar',
        'fl_agricultor_familiar_dap',
        'agricultor_familiar_numero',
        'agricultor_familiar_data',
        'fl_assistencia_tecnica',
        'assistencia_tecnica_tipo_id',
        'assistencia_tecnica_periodo',
        'fl_contrata_mao_de_obra_externa',
        'mao_de_obra_externa_tipo',
        'mao_de_obra_externa_periodicidade',
        'fl_internet',
        // 'internet_tipo_id',
        // 'internet_operadora_id',
        'tipo_parcerias_obs',
        'genero_id',
        'nome_social',
        'etinia_id',
        'fl_portador_deficiencia',
        'portador_deficiencia_obs',
        'data_nascimento',
        'rg',
        'cpf',
        'cnpj',
        'nota_fiscal_produtor',
        'cep',
        'endereco',
        'bairro',
        'subprefeitura',

        'estado_id',
        'cidade_id',

        'telefone_1',
        'telefone_2',
        'email',
        'fl_comunidade_tradicional',
        'comunidade_tradicional_obs',
        'fl_cnpj',
        'fl_nota_fiscal_produtor',
        'fl_tipo_parceria',
        'fl_reside_unidade_produtiva',

        'status',
        'status_observacao',
        'renda_agricultura_id',
        'rendimento_comercializacao_id',
        'outras_fontes_renda',
        'grau_instrucao_id',
        'situacao_social_id',

        'user_id',

        'tags'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ProdutorPermissionScope);

        self::creating(function ($model) {
            $model->user_id = \Auth::user()->id;

            if ($model->id)
                return;

            //Método para o "id" único do tipo string, consumido pelo APP (sync)
            $model->id = (string) Uuid::generate(4);
        });
    }

    public function estado()
    {
        return $this->belongsTo(EstadoModel::class, 'estado_id')->select(['id', 'nome', 'uf']);
    }

    public function cidade()
    {
        return $this->belongsTo(CidadeModel::class, 'cidade_id')->select(['id', 'nome']);
    }

    public function etinia()
    {
        return $this->belongsTo(EtiniaModel::class, 'etinia_id');
    }

    public function assistenciaTecnicaTipo()
    {
        return $this->belongsTo(AssistenciaTecnicaTipoModel::class, 'assistencia_tecnica_tipo_id');
    }

    public function genero()
    {
        return $this->belongsTo(GeneroModel::class, 'genero_id');
    }

    public function rendaAgricultura()
    {
        return $this->belongsTo(RendaAgriculturaModel::class, 'renda_agricultura_id');
    }

    public function rendimentoComercializacao()
    {
        return $this->belongsTo(RendimentoComercializacaoModel::class, 'rendimento_comercializacao_id');
    }

    public function grauInstrucao()
    {
        return $this->belongsTo(GrauInstrucaoModel::class, 'grau_instrucao_id');
    }

    public function situacaoSocial()
    {
        return $this->belongsTo(SituacaoSocialModel::class, 'situacao_social_id');
    }


    /**
     * Retorna as unidades produtivas do produtor
     */
    public function unidadesProdutivas()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'produtor_unidade_produtiva', 'produtor_id', 'unidade_produtiva_id')->using(ProdutorUnidadeProdutivaModel::class)->whereNull('produtor_unidade_produtiva.deleted_at')->withPivot('id', 'tipo_posse_id')->withTimestamps();
    }

    /**
     * Utilizado no permission scope e no policy do Produtor
     *
     * Retorna todos relacionamentos de unidades produtivas com produtores, independente do "scope"
     */
    public function unidadesProdutivasNS()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'produtor_unidade_produtiva', 'produtor_id', 'unidade_produtiva_id')->using(ProdutorUnidadeProdutivaModel::class)->whereNull('produtor_unidade_produtiva.deleted_at')->withPivot('id', 'tipo_posse_id')->withTimestamps()->withoutGlobalScopes([UnidadeProdutivaPermissionScope::class]);
    }

    /**
     * Retorna todos os relacionamentos com unidade produtiva (inclusive os "trasheds")
     *
     * Essa função é utilizada para restaurar as unidades produtivas (sync) na hora de relacionr uma unidade produtiva que já tinha sido relacionada (mas esta removida)
     */
    public function unidadesProdutivasWithTrashed()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'produtor_unidade_produtiva', 'produtor_id', 'unidade_produtiva_id')->using(ProdutorUnidadeProdutivaModel::class)->withPivot('id', 'tipo_posse_id')->withTimestamps();
    }

    /**
     * Retorna os cadernos de campo aplicados
     */
    public function cadernos()
    {
        return $this->hasMany(CadernoModel::class, 'produtor_id');
    }

    /**
     * Retorna os formulários aplicados
     */
    public function checklists()
    {
        return $this->hasMany(ChecklistUnidadeProdutivaModel::class, 'produtor_id')->withoutGlobalScopes()->withoutTrashed();
    }

    /**
     * Retorna os planos de ações
     */
    public function plano_acao()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'produtor_id')->individual()->withoutTrashed();
    }

    public function plano_acao_coletivo()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'produtor_id')->withoutGlobalScopes()->coletivoFilho()->withoutTrashed();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function unidadesProdutivasOffline()
    {
        return $this->hasMany(ProdutorUnidadeProdutivaModel::class, 'produtor_id')->withTrashed();
    }

    /*
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
    */
    public function unidadesProdutivasScoped()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'produtor_unidade_produtiva', 'produtor_id', 'unidade_produtiva_id')->using(ProdutorUnidadeProdutivaModel::class)->whereNull('produtor_unidade_produtiva.deleted_at');
    }

    /**
     *
     */
    public function unidadesProdutivasWithTrashedScoped()
    {
        return $this->belongsToMany(UnidadeProdutivaModel::class, 'produtor_unidade_produtiva', 'produtor_id', 'unidade_produtiva_id')->using(ProdutorUnidadeProdutivaModel::class);
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
}
