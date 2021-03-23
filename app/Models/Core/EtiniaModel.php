<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pelo Produtor (ProdutorModel)
 */
class EtiniaModel extends Model
{
    use SoftDeletes;

    protected $table = 'etinias';

    protected $fillable = ['nome'];
}
