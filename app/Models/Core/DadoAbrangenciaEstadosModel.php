<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Abrangencia da Dado vs Estado
 */
class DadoAbrangenciaEstadosModel extends Model
{
    protected $table = 'dado_abrangencia_estados';

    protected $fillable = ['dado_id', 'estado_id'];
}
