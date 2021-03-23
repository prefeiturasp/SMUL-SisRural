<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado na Unidade Produtiva
 */
class UnidadeProdutivaResiduoSolidoModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_residuo_solidos';

    protected $fillable = ['id', 'unidade_produtiva_id', 'residuo_solido_id', 'deleted_at'];

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
