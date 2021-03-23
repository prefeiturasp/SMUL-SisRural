<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Abrangencia da Dado vs Cidade
 */
class DadoAbrangenciaCidadeModel extends Model
{
    protected $table = 'dado_abrangencia_cidades';

    protected $fillable = ['dado_id', 'cidade_id'];
}
