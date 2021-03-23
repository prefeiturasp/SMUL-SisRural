<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pela UnidadeProdutiva
 */
class EsgotamentoSanitarioModel extends Model
{
    use SoftDeletes;

    protected $table = 'esgotamento_sanitarios';

    protected $fillable = ['nome'];
}
