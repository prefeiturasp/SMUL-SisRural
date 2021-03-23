<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Termos de uso, acessado no APP (login) e no CMS (login)
 */
class TermosDeUsoModel extends Model
{
    use SoftDeletes;

    protected $table = 'termos_de_usos';

    protected $fillable = ['texto'];
}
