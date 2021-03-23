<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado na Unidade Produtiva (UnidadeProdutivaModel)
 */
class PressaoSocialModel extends Model
{
    use SoftDeletes;

    protected $table = 'pressao_sociais';

    protected $fillable = ['nome'];
}
