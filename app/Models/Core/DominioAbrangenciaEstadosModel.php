<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Abrangencia do Domínio vs Estado
 */
class DominioAbrangenciaEstadosModel extends Model
{
    use SoftDeletes;

    protected $table = 'dominio_abrangencia_estados';

    protected $fillable = ['dominio_id', 'estado_id'];
}
