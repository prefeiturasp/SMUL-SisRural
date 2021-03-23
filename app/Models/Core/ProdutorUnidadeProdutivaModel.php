<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Classe utilizada p/ criar os relacionamentos entre Produtor vs Unidade Produtiva
 *
 * É um PIVOT, em vários relacionamentos "belong" essa classe é utilizada
 */
class ProdutorUnidadeProdutivaModel extends Pivot
{
    use SoftDeletes;

    // public $incrementing = true; //Fix $class:updateOrCreate return $id
    public $incrementing = false;

    protected $table = 'produtor_unidade_produtiva';

    protected $fillable = ['id', 'unidade_produtiva_id', 'tipo_posse_id', 'produtor_id', "contato", "deleted_at", "updated_at"];

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
    }

    public function tipoPosse()
    {
        return $this->belongsTo(TipoPosseModel::class, 'tipo_posse_id');
    }
}
