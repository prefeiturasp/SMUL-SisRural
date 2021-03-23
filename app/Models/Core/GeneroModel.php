<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pelo Produtor (ProdutorModel)
 */
class GeneroModel extends Model
{
    use SoftDeletes;

    protected $table = 'generos';

    protected $fillable = ['nome'];
}
