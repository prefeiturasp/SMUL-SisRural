<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RendaAgriculturaModel extends Model
{
    use SoftDeletes;

    protected $table = 'renda_agriculturas';

    protected $fillable = ['nome'];
}
