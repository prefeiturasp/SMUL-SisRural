<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Traits\DateFormat;
use Arcanedev\Support\Database\Model;
use Uuid;

/**
 * Utilizado pelo Checklist (ChecklistUnidadeProdutivaModel)
 *
 * Logs de aprovação de um formulário aplicado (formulário com fluxo de aprovação)
 */
class ChecklistAprovacaoLogsModel extends Model
{
    use DateFormat;

    public $incrementing = true;

    protected $table = 'checklist_aprovacao_logs';

    protected $fillable = ['id', 'checklist_unidade_produtiva_id', 'user_id', 'status', 'message'];

    protected static function boot()
    {
        parent::boot();

        //Método para o "id" único do tipo string, consumido pelo APP (sync)
        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = (string) Uuid::generate(4);
        });
    }

    /**
     * Retorna a referencia do formulário aplicado
     *
     * @return void
     */
    public function checklistUnidadeProdutiva()
    {
        return $this->belongsTo(ChecklistUnidadeProdutivaModel::class, 'checklist_unidade_produtiva_id');
    }

    /**
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes()->withTrashed();
    }
}
