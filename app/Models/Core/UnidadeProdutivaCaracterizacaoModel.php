<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Uso do Solo na Unidade Produtiva
 */
class UnidadeProdutivaCaracterizacaoModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_caracterizacoes';

    protected $fillable = ['id', 'area', 'quantidade', 'descricao', 'unidade_produtiva_id', 'solo_categoria_id', 'deleted_at'];

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

    public function unidadeProdutiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    public function categoria()
    {
        return $this->belongsTo(SoloCategoriaModel::class, 'solo_categoria_id');
    }
}
