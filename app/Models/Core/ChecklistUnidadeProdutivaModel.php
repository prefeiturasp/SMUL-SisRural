<?php

namespace App\Models\Core;

use App\Enums\ChecklistStatusEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Enums\TipoPerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Jobs\ProcessChecklistUnidadeProdutivas;
use App\Models\Auth\User;
use App\Models\Core\Traits\Attribute\RolesAppAttribute;
use App\Models\Core\Traits\ChecklistUnidadeProdutivaScore;
use App\Models\Core\Traits\ImportFillableCreatedAt;
use App\Models\Core\Traits\Scope\ChecklistUnidadeProdutivaPermissionScope;
use App\Models\Traits\DateFormat;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Referente a aplicação de um Template de formulário em uma Unidade Produtiva
 *
 * Caso seja utilizado no "router", retorna o registro mesmo que ele tenha sido removido (RouteServiceProvider.php)
 */
class ChecklistUnidadeProdutivaModel extends Model
{
    use SoftDeletes;
    use DateFormat;
    use RolesAppAttribute;
    use ImportFillableCreatedAt;

    /**
     * Trait com as funções de score/pontuação do Formulário aplicado
     */
    use ChecklistUnidadeProdutivaScore;

    public $incrementing = false;

    protected $table = 'checklist_unidade_produtivas';

    protected $fillable = ['id', 'checklist_id', 'unidade_produtiva_id', 'produtor_id', 'user_id', 'status', 'status_flow', 'deleted_at', 'finish_user_id', 'finished_at', 'pontuacao', 'pontuacaoFinal', 'pontuacaoPercentual'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ChecklistUnidadeProdutivaPermissionScope);

        //Método para o "id" único do tipo string, consumido pelo APP (sync)
        self::creating(function ($model) {
            if ($model->status == ChecklistStatusEnum::Finalizado) {
                if (!$model->finished_at) {
                    $model->finished_at = \Carbon\Carbon::now();
                }
                if (!$model->finish_user_id) {
                    $model->finish_user_id = \Auth::user() ? \Auth::user()->id : null;
                }
            }

            if ($model->id)
                return;

            $model->id = (string) Uuid::generate(4);
        });

        /*
        * Caso o status do formulário é alterado de XXX p/ Finalizado, atualiza a data (finished_at) que foi finalizado
        */
        self::updating(function ($model) {
            if ($model->isDirty('status') && $model->status == ChecklistStatusEnum::Finalizado && $model->getOriginal('status') == ChecklistStatusEnum::AguardandoAprovacao) {
                $model->finished_at = \Carbon\Carbon::now();
                $model->finish_user_id = $model->user_id; //Quem cria fica como finalizador (solicitado pelo cliente)
            } else if ($model->isDirty('status') && $model->status == ChecklistStatusEnum::Finalizado) {
                $model->finished_at = \Carbon\Carbon::now();
                $model->finish_user_id = \Auth::user() ? \Auth::user()->id : null;
            }
        });
    }

    public function plano_acao_principal()
    {
        return $this->hasOne(PlanoAcaoModel::class, 'checklist_unidade_produtiva_id', 'id')->where("status", "!=", PlanoAcaoStatusEnum::Cancelado);
    }

    public function plano_acao()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'checklist_unidade_produtiva_id', 'id');
    }

    public function plano_acao_trashed()
    {
        return $this->hasMany(PlanoAcaoModel::class, 'checklist_unidade_produtiva_id', 'id')->onlyTrashed();
    }


    /**
     * Retorna qual template do formulário aplicado
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     * @return mixed
     */
    public function checklist()
    {
        return $this->belongsTo(ChecklistModel::class, 'checklist_id')->withoutGlobalScopes();
    }

    /**
     * Retorna qual o produtor do formulário aplicado
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function produtor()
    {
        return $this->belongsTo(ProdutorModel::class, 'produtor_id')->withoutGlobalScopes();
    }

    /**
     * Retorna qual a unidade produtiva do formulário aplicado
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function unidade_produtiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id')->withoutGlobalScopes();
    }

    /**
     * Utilizado para validar se o usuário tem permissão para editar o formulário aplicado, só pode editar
     * formulários que o produtor/unidade produtiva é visível (Abrangencia) + Liberado para aplicação (template)
     *
     * @return mixed
     */
    public function unidadeProdutivaScoped()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
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
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes()->withTrashed();
    }

    public function usuarioFinish()
    {
        return $this->belongsTo(User::class, 'finish_user_id')->withoutGlobalScopes();
    }

    /**
     * Retorna todas as respostas das unidades produtivas p/ cada pergunta de um formulário.
     *
     * @return mixed
     */
    public function respostasUnidadeProdutivaMany()
    {
        return $this->hasMany(UnidadeProdutivaRespostaModel::class, 'unidade_produtiva_id', 'unidade_produtiva_id');
    }

    /**
     * Retorna as respostas criadas para o formulário aplicado (Quando este estiver finalizado, porque esta pegando do "snapshot")
     *
     * @return mixed
     */
    public function respostasMany()
    {
        return $this->hasMany(ChecklistSnapshotRespostaModel::class, 'checklist_unidade_produtiva_id');
    }

    /**
     * Método utilizado para retornar todas as respostas para o APP (sync)
     *
     * @return mixed
     */
    public function respostasManyOffline()
    {
        return $this->hasMany(ChecklistSnapshotRespostaModel::class, 'checklist_unidade_produtiva_id')->withTrashed();
    }

    /**
     * Retorna todos os logs gerados para o formulário aplicado (formulário possuí fluxo de aprovação)
     *
     * @return mixed
     */
    public function analiseLogs()
    {
        return $this->hasMany(ChecklistAprovacaoLogsModel::class, 'checklist_unidade_produtiva_id');
    }

    /**
     * Utilizado para validar se o usuário tem permissão para aplicação do formulário (template)
     * @return mixed
     */
    public function checklistScoped()
    {
        return $this->belongsTo(ChecklistModel::class, 'checklist_id');
    }

    /*
        a) Perguntas
        - remover pergunta
        [OK] a) não é possível remover uma pergunta (é possível arquivar)
        [OK] b) removendo no banco (deleted_at, manualmente), a pergunta some da listagem de visualização ... não foi feito nenhum tratamento no código p/ continuar mostrando.

        - remover resposta
        [OK] a) é possível remover uma resposta (mas ela permanece na visualização do checklist aplicado caso o usuário tenha marcado essa resposta)

        - editar resposta
        [OK] a) editar resposta (o novo valor da resposta é visualizado em um checklist aplicado, independente se ele esta em modo rascunho ou se já foi finalizado)

        b) Template checklist

        c) Aplicação do checklist
        [OK] - ao salvar um checklist aplicado em modo finalizado, não deve propagar respostas de perguntas Removidas ou Inativas

        d) Visualização do checklist
        [OK] - Se uma pergunta foi removida e não tem resposta, não deve mostrar na visualização, mas se tiver resposta, mostra

        @return mixed
    */
    public function getCategoriasAndRespostasChecklist()
    {
        $isRascunho = $this->status == ChecklistStatusEnum::Rascunho || $this->status == ChecklistStatusEnum::AguardandoAprovacao || $this->status == ChecklistStatusEnum::AguardandoPda;

        if ($isRascunho) {
            //Não retorna perguntas e nem respostas "removidas".
            $categorias = $this->checklist->categorias()->with('perguntas')->get();
            $respostas = @$this->respostasUnidadeProdutivaMany()->with('respostas')->get()->toArray();
        } else {
            //Retorna perguntas e respostas "removidas"
            $categorias =  $this->checklist->categorias()->with('perguntasWithTrashed')->get();
            $respostas = @$this->respostasMany()->with('respostasWithTrashed')->get()->toArray();
        }

        foreach ($categorias as $kk => &$categoria) {
            $perguntas = $isRascunho ? $categoria->perguntas : $categoria->perguntasWithTrashed;

            $possuiRespostas = false;

            foreach ($perguntas as $k => &$pergunta) {
                $resposta = @array_values(array_filter($respostas, function ($resposta) use ($pergunta) {
                    return $resposta['pergunta_id'] === $pergunta['id'];
                }));

                $value = [];
                $cor = null;

                $tipo_pergunta = $pergunta['tipo_pergunta'];
                if ($tipo_pergunta == TipoPerguntaEnum::Semaforica || $tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $tipo_pergunta == TipoPerguntaEnum::Binaria || $tipo_pergunta == TipoPerguntaEnum::BinariaCinza || $tipo_pergunta == TipoPerguntaEnum::MultiplaEscolha || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimples || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                    foreach ($resposta as $kk => $vv) {
                        //Checklist Finalizado
                        if (@$vv['respostas_with_trashed']) {
                            $value[] = $vv['respostas_with_trashed']['descricao'];
                            $cor = $vv['respostas_with_trashed']['cor'];
                        } else {
                            //Checklist em Rascunho
                            $value[] = $vv['respostas']['descricao'];
                            $cor = $vv['respostas']['cor'];
                        }
                    }
                } else if ($tipo_pergunta == TipoPerguntaEnum::Tabela) {
                    $columns = explode(",", $pergunta['tabela_colunas']);
                    $lines = $pergunta['tabela_linhas'] ? explode(",", $pergunta['tabela_linhas']) : [];

                    //Só preenche com valores caso tenha colunas para renderizar
                    if (@count($resposta) > 0 && count($columns) > 0 && $pergunta['tabela_colunas']) {
                        $values = AppHelper::transpose(json_decode($resposta[0]['resposta'], true));
                        $value = [AppHelper::getDataTable($columns, $lines, $values)];
                    }
                } else {
                    $value = [@$resposta[0]['resposta']];
                }

                $pergunta['resposta'] = join(', ', $value);
                $pergunta['resposta_cor'] = $cor;

                $possuiRespostas = $pergunta['resposta'] ? true : $possuiRespostas;
            }

            $categoria['possuiRespostas'] = $possuiRespostas;
            $categoria['perguntas'] = $perguntas; //Referencia para o "withTrashed"
        }

        return $categorias;
    }

    public function getRespostas()
    {
        $categorias =  $this->getCategoriasAndRespostasChecklist();

        $respostas = array();
        foreach ($categorias as $vCategoria) {
            foreach ($vCategoria->perguntas as $vPergunta) {
                $respostas[$vPergunta['id']] = $vPergunta->toArray();
            }
        }
        return $respostas;
    }

    /**
     * Escopo para liberar somente Formulários que o usuário possuí permissão para "Analisar"
     *
     * @param  mixed $query
     * @return mixed
     */
    public function scopeAnalistaAutorizado($query)
    {
        return $query->withoutGlobalScopes()
            ->whereHas('checklist', function ($q) {
                $q->withoutGlobalScopes();

                $q->where('fl_fluxo_aprovacao', 1);

                $q->whereHas('usuariosAprovacao', function ($q2) {
                    $q2->where('user_id', Auth::id());
                });
            })
            ->whereNull('checklist_unidade_produtivas.deleted_at');
    }

    /**
     * @deprecated Remover futuramente, não é utilizado mais no sistema
     */
    public function userHierarchicalBelongs(&$qry, $user, $checklistApply = true)
    {
    }

    /**
     * Retorna os arquivos que o caderno de campo possuí
     * @return mixed
     */
    public function arquivos()
    {
        return $this->hasMany(ChecklistUnidadeProdutivaArquivoModel::class, 'checklist_unidade_produtiva_id');
    }

    /**
     * Retorna os arquivos p/ serem consumidos pelo APP
     *
     * Métodos "offline" utilizados p/ o download de dados do APP
     *
     * @return mixed
     */
    public function arquivosManyOffline()
    {
        return $this->hasMany(ChecklistUnidadeProdutivaArquivoModel::class, 'checklist_unidade_produtiva_id')->withTrashed();
    }
}
