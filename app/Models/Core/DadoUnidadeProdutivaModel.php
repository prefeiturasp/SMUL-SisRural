<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Relacionamento entre Dado e Unidade Produtiva
 */
class DadoUnidadeProdutivaModel extends Pivot
{
    public $incrementing = true;

    protected $table = 'dado_unidade_produtivas';

    protected $fillable = ['dado_id', 'unidade_produtiva_id', 'dado_id'];
}
