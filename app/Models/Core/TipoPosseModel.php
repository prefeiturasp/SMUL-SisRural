<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado no relacionamento do Produtor vs Unidade Produtiva (tabela: produtor_unidade_produtiva)
 */
class TipoPosseModel extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_posses';

    protected $fillable = ['nome'];
}
