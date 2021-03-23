<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado no momento que é finalizado uma aplicação de um formulário.
 *
 * Quando o formulário é finalizado, é "copiado" as respostas (da tabela unidade_produtiva_respostas).
 *
 */
class ChecklistSnapshotRespostaModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'checklist_snapshot_respostas';

    protected $fillable = ['id', 'checklist_unidade_produtiva_id', 'pergunta_id', 'resposta_id', 'resposta', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();


        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = (string) Uuid::generate(4);
        });
    }

    public function respostas()
    {
        return $this->belongsTo(RespostaModel::class, 'resposta_id');
    }

    /**
     * Utilizado na tela de visualização e comparação (porque mesmo removida, a resposta do usuário deve ser mantida)
     */
    public function respostasWithTrashed()
    {
        return $this->belongsTo(RespostaModel::class, 'resposta_id')->withTrashed();
    }

    public function pergunta()
    {
        return $this->belongsTo(PerguntaModel::class, 'pergunta_id');
    }

    public function checklistUnidadeProdutivas()
    {
        return $this->belongsTo(ChecklistUnidadeProdutivaModel::class, 'checklist_unidade_produtiva_id');
    }
}
