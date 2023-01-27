<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class StatusAcompanhamentoModel extends Model
{
    protected $table = 'status_acompanhamentos';

    protected $fillable = ['nome'];
}
