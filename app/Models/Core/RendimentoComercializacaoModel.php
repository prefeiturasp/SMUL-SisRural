<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RendimentoComercializacaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'rendimento_comercializacoes';

    protected $fillable = ['nome'];
}
