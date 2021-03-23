<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pela UnidadeProdutiva
 */
class GrauInstrucaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'grau_instrucoes';

    protected $fillable = ['nome'];
}
