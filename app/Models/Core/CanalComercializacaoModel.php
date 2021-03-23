<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pela Unidade Produtiva (UnidadeProdutivaModel)
 */
class CanalComercializacaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'canal_comercializacoes';

    protected $fillable = ['nome'];
}
