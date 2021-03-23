<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado na Unidade Produtiva
 */
class RiscoContaminacaoAguaModel extends Model
{
    use SoftDeletes;

    protected $table = 'risco_contaminacao_aguas';

    protected $fillable = ['nome'];
}
