<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Certificações na Unidade Produtiva
 */
class UnidadeProdutivaCertificacaoModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_certificacoes';

    protected $fillable = ['id', 'unidade_produtiva_id', 'certificacao_id', 'deleted_at'];

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
}
