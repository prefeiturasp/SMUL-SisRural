<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Essa tabela é utilizada para persistir os arquivos gerados através de uma aplicação de um formulário.
 *
 * Foi criado para fazer o sync dos dados entre o APP -> CMS
 *
 * Ela só é utilizada para isso, no fluxo do CMS ela é ignorada
 */
class UnidadeProdutivaRespostaArquivoModel extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_resposta_arquivos';

    protected $fillable = ['id', 'unidade_produtiva_resposta_id',  'arquivo', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = Uuid::generate(4)->string;
        });
    }

    public function unidadeProdutivaResposta()
    {
        return $this->belongsTo(UnidadeProdutivaRespostaModel::class, 'unidade_produtiva_resposta_id');
    }
}
