<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado no bloco "Pessoas" dentro da Unidade Produtiva. (ColaboradorModel)
 *
 * É o tipo de decicação que o usuário tem com a propriedade
 */
class DedicacaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'dedicacoes';

    protected $fillable = ['nome'];
}
