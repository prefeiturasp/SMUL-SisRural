<?php

namespace App\Models\Core;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Models\Auth\Traits\Scope\UserPermissionScope;
use App\Models\Auth\User;
use App\Models\Core\Traits\Attribute\RolesAppAttribute;
use App\Models\Core\Traits\Scope\PlanoAcaoPermissionScope;
use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

class PlanoAcaoModel extends Model
{
    use SoftDeletes;
    use DateFormat;
    use RolesAppAttribute;

    public $incrementing = false;

    protected $table = 'plano_acoes';

    protected $fillable = ['id', 'nome', 'checklist_unidade_produtiva_id', 'unidade_produtiva_id', 'produtor_id', 'status', 'prazo', 'deleted_at', 'fl_coletivo', 'plano_acao_coletivo_id', 'user_id'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PlanoAcaoPermissionScope);

        self::creating(function ($model) {
            if ($model->id)
                return;

            //Método para o "id" único do tipo string, consumido pelo APP (sync)
            $model->id = (string) Uuid::generate(4);
        });

        self::updating(function ($model) {
            if (\Config::get('app_sync')) {
                return;
            }

            /**
             * Se o status for alterado, gera log
             */
            if ($model->isDirty('status')) {
                // $user_id = $model->status == PlanoAcaoStatusEnum::EmAndamento ? $model->user_id : \Auth::user()->id; //Em andamento é quando o plano de ação é aprovado e o usuário autenticado é o Domínio
                PlanoAcaoHistoricoModel::create(['plano_acao_id' => $model->id, 'user_id' => \Auth::user()->id, 'texto' => 'Status alterado p/ ' . PlanoAcaoStatusEnum::toSelectArray()[$model->status]]);
            }

            /**
             * Se o nome for alterado, gera log
             */
            if ($model->isDirty('nome')) {
                PlanoAcaoHistoricoModel::create(['plano_acao_id' => $model->id, 'user_id' => \Auth::user()->id, 'texto' => 'Nome alterado de "' . $model->getOriginal('nome') . '" p/ "' . $model->nome . '"']);
            }

            /**
             * Se o prazo for alterado, gera log
             */
            if ($model->isDirty('prazo')) {
                PlanoAcaoHistoricoModel::create(['plano_acao_id' => $model->id, 'user_id' => \Auth::user()->id, 'texto' => 'Prazo alterado de "' . \Carbon\Carbon::parse(@$model->getOriginal('prazo'))->format('d/m/Y') . '" p/ ' . @$model->prazo_formatted . '"']);
            }
        });
    }


    /**
     * Escopo para retornar apenas os "Planos de Ação" que não são coletivo (fl_coletivo = 0)
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeIndividual($query)
    {
        $query->where("fl_coletivo", 0);
    }

    /**
     * Retorna os formulários relacionados com o Plano de ação
     *
     * withTrashed() -> porque precisa aparecer na listagem do PDA mesmo que tenha sido removido
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     */
    public function checklist_unidade_produtiva()
    {
        return $this->belongsTo(ChecklistUnidadeProdutivaModel::class, 'checklist_unidade_produtiva_id')->withTrashed()->withoutGlobalScopes();
    }

    /**
     * Unidades produtivas do plano de ação
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     */
    public function unidade_produtiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id')->withoutGlobalScopes();
    }

    /**
     * Produtor do plano de ação
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     */
    public function produtor()
    {
        return $this->belongsTo(ProdutorModel::class, 'produtor_id')->withoutGlobalScopes();
    }

    /**
     * Históricos gerados para o plano de ação
     */
    public function historicos()
    {
        return $this->hasMany(PlanoAcaoHistoricoModel::class, 'plano_acao_id');
    }

    /**
     * Itens do plano de ação
     */
    public function itens()
    {
        return $this->hasMany(PlanoAcaoItemModel::class, 'plano_acao_id')->orderBy('prioridade');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function itensManyOffline()
    {
        return $this->hasMany(PlanoAcaoItemModel::class, 'plano_acao_id')->withTrashed();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function itensManyOfflineHistoricoItens()
    {
        return $this->itensManyOffline();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function historicosManyOffline()
    {
        return $this->hasMany(PlanoAcaoHistoricoModel::class, 'plano_acao_id')->withTrashed();
    }


    /**
     * Retorna os Planos de Ação Coletivos "Pai"
     *
     * PDA principal, que da origem aos filhos quando é adicionado uma nova "unidade produtiva"
     */
    public function scopeColetivo($query)
    {
        $query->where("fl_coletivo", 1)->whereNull("plano_acao_coletivo_id");
    }

    /**
     * Retorna os Planos de Ação Coletivos "Filhos"
     *
     * PDA filhos, que são originados de um plano de ação "pai". São os PDAS das "unidades produtivas" (do pda coletivo)
     */
    public function scopeColetivoFilho($query)
    {
        $query->where("fl_coletivo", 1)->whereNotNull("plano_acao_coletivo_id");
    }

    /**
     * Retorna os planos de ações filhos
     *
     * withoutGlobalScopes: Se ele tem acesso ao pai, pode acessar todos os filhos
     */
    public function plano_acao_filhos()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'plano_acao_coletivo_id', 'id')->withoutGlobalScopes();
    }

    /**
     * Retorna os itens dos planos de ações filhos com uma contagem, é usado nas tabelas p/ mostrar o percentual de cada "status"
     */
    public function plano_acao_filhos_with_count_status()
    {
        return $this->plano_acao_filhos()->withCount(
            [
                'itens as nao_iniciado' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::NaoIniciado);
                },
                'itens as em_andamento' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::EmAndamento);
                },
                'itens as concluido' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::Concluido);
                },
                'itens as cancelado' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::Cancelado);
                },
                'itens as total'
            ]
        );
    }

    /**
     * Retorna os itens dos planos de ações principais com uma contagem, é usado nas tabelas p/ mostrar o percentual de cada "status"
     */
    public function itens_with_count_status()
    {
        return $this->itens()->withCount(
            [
                'plano_acao_item_filhos as nao_iniciado' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::NaoIniciado);
                    $q->whereHas('plano_acao'); //Força pegar apenas filhos que tenham plano de ações válidos (Que não foram removidos). Ex: remover uma unidade produtiva do plano de ação coletivo, o count estava errado
                },
                'plano_acao_item_filhos as em_andamento' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::EmAndamento);
                    $q->whereHas('plano_acao');
                },
                'plano_acao_item_filhos as concluido' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::Concluido);
                    $q->whereHas('plano_acao');
                },
                'plano_acao_item_filhos as cancelado' => function ($q) {
                    $q->where('status', PlanoAcaoItemStatusEnum::Cancelado);
                    $q->whereHas('plano_acao');
                },
                'plano_acao_item_filhos as total'
            ]
        );
    }

    /**
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes()->withTrashed();
    }


    /**
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
     *
     * @return mixed
     */
    public function unidadeProdutivaScoped()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    /**
     * withTrashed, se o "formulário aplicado" for removido, o PDA deixa de aparecer na listagem
     */
    public function checklistUnidadeProdutivaScoped()
    {
        return $this->belongsTo(ChecklistUnidadeProdutivaModel::class, 'checklist_unidade_produtiva_id')->withTrashed();
    }

    public function usuarioScoped()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(UserPermissionScope::class)->unidadesOperacionaisComTecnicos()->withTrashed();
    }

    public function planoAcaoFilhosScoped()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'plano_acao_coletivo_id', 'id')->withoutGlobalScopes();
    }

    public function planoAcaoPaiScoped()
    {
        return $this->belongsTo(PlanoAcaoModel::class, 'plano_acao_coletivo_id')->withoutGlobalScopes();
    }

    /**
     * Determina se o usuário pode visualizar ou não o registro
     *
     * Regra utilizada pelo APP, no CMS é desabilitado essa função
     *
     * @return bool
     */
    public function getCanReopenAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('reopen', $this);
    }

    /**
     * Determina se o usuário pode cadastrar um history quando for coletivo
     *
     * Regra utilizada pelo APP, no CMS é desabilitado essa função
     *
     * @return bool
     */
    public function getCanHistoryAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('history', $this);
    }


}
