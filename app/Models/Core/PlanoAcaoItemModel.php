<?php

namespace App\Models\Core;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

class PlanoAcaoItemModel extends Model
{
    use SoftDeletes;
    use DateFormat;

    public $incrementing = false;

    protected $table = 'plano_acao_itens';

    protected $fillable = ['id', 'plano_acao_id', 'checklist_pergunta_id', 'checklist_snapshot_resposta_id', 'descricao', 'prioridade', 'status', 'prazo', 'finished_at', 'deleted_at', 'ultima_observacao', 'ultima_observacao_data', 'fl_coletivo', 'plano_acao_item_coletivo_id'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

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
             * Se o status for alterado p/ Concluido, armazena a data que foi concluído.
             */
            if ($model->isDirty('status') && $model->status == PlanoAcaoItemStatusEnum::Concluido) {
                $model->finished_at = \Carbon\Carbon::now();
            }

            /**
             * Se o status for alterado e for diferente de "rascunho", gera um "log" automatico (PlanoAcaoItemHistoricoModel)
             */
            if ($model->isDirty('status') && $model->plano_acao->status != PlanoAcaoStatusEnum::Rascunho) {
                PlanoAcaoItemHistoricoModel::create(['plano_acao_item_id' => $model->id, 'user_id' => \Auth::user()->id, 'texto' => 'Status alterado p/ ' . PlanoAcaoItemStatusEnum::toSelectArray()[$model->status]]);
            }
        });
    }

    /**
     * Plano de ação vinculado ao item
     */
    public function plano_acao()
    {
        return $this->belongsTo(PlanoAcaoModel::class, 'plano_acao_id');
    }

    /**
     * Históricos do item
     */
    public function historicos()
    {
        return $this->hasMany(PlanoAcaoItemHistoricoModel::class, 'plano_acao_item_id');
    }

    /**
     * Retorna a pergunta associada ao item gerado (quando é originado de um Formulário)
     */
    public function checklist_pergunta()
    {
        return $this->belongsTo(ChecklistPerguntaModel::class, 'checklist_pergunta_id');
    }

    /**
     * Retorna qual foi a resposta do formulário. (quando é originado de um Formulário)
     */
    public function checklist_snapshot_resposta()
    {
        return $this->belongsTo(ChecklistSnapshotRespostaModel::class, 'checklist_snapshot_resposta_id');
    }

    /**
     * Atributo, p/ retornar a data formatada "ultima_observacao_data_formatted"
     *
     * @return string
     */
    public function getUltimaObservacaoDataFormattedAttribute()
    {
        if ($this->ultima_observacao_data)
            return AppHelper::formatDate($this->ultima_observacao_data, 'd/m/Y H:i');
        else
            return null;
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function historicosManyOffline()
    {
        return $this->hasMany(PlanoAcaoItemHistoricoModel::class, 'plano_acao_item_id')->withTrashed();
    }

    /**
     * Retorna todos os itens "filhos" (cópias).
     *
     * É utilizado pelo "Plano de ação Coletivo"
     */
    public function plano_acao_item_filhos()
    {
        return $this->hasMany(PlanoAcaoItemModel::class, 'plano_acao_item_coletivo_id', 'id');
    }
}
