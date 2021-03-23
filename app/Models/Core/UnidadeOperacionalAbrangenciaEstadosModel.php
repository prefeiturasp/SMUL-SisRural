<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Abrangencia da Unidade Operacional vs Estado
 */
class UnidadeOperacionalAbrangenciaEstadosModel extends Model
{
    use SoftDeletes;

    protected $table = 'unidade_operacional_abrangencia_estados';

    protected $fillable = ['unidade_operacional_id', 'estado_id'];
}
