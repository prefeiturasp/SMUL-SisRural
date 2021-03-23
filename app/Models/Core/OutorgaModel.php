<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pela Unidade Produtiva (UnidadeProdutivaModel)
 */
class OutorgaModel extends Model
{
    use SoftDeletes;

    protected $table = 'outorgas';

    protected $fillable = ['nome'];
}
