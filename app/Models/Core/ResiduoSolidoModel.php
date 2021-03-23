<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResiduoSolidoModel extends Model
{
    use SoftDeletes;

    protected $table = 'residuo_solidos';

    protected $fillable = ['nome'];
}
