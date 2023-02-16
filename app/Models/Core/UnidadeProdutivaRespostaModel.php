<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Respostas únicas dos formulários aplicados vinculados a Unidade Produtiva.
 *
 * Quando o usuário responde uma pergunta de um formulário, a resposta é salva na "unidade_produtiva_respostas"
 *
 * Quando ele responde um outro formulário, mas a mesma pergunta, essa resposta é atualizada
 *
 * Assim temos uma resposta única em todo sistema (independente do Domínio)
 *
 * No momento de finalizar a aplicação do formulário, é feito uma "cópia/snapshot" da resposta em outra tabela (checklist_snapshot_respostas)
 *
 */
class UnidadeProdutivaRespostaModel extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_respostas';

    protected $fillable = ['id', 'pergunta_id', 'resposta_id', 'unidade_produtiva_id', 'produtor_id', 'resposta', 'deleted_at'];

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

    public function pergunta()
    {
        return $this->belongsTo(PerguntaModel::class, 'pergunta_id');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function arquivosManyOffline()
    {
        return $this->hasMany(UnidadeProdutivaRespostaArquivoModel::class, 'unidade_produtiva_resposta_id')->withTrashed();
    }
}
