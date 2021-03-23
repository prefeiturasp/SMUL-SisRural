<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pelo Produtor (ProdutorModel)
 */
class AssistenciaTecnicaTipoModel extends Model
{
    use SoftDeletes;

    protected $table = 'assistencia_tecnica_tipos';

    protected $fillable = ['nome'];
}
