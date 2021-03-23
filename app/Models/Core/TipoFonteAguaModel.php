<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado na Unidade Produtiva 
 */
class TipoFonteAguaModel extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_fonte_aguas';

    protected $fillable = ['nome'];
}
