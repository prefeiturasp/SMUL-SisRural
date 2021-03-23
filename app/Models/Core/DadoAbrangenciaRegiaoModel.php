<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Abrangencia da Dado vs Região
 */
class DadoAbrangenciaRegiaoModel extends Model
{
    protected $table = 'dado_abrangencia_regioes';

    protected $fillable = ['dado_id', 'regiao_id'];
}
