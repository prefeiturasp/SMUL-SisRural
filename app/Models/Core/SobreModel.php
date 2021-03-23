<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * SobreModel
 */
class SobreModel extends Model
{
    protected $table = 'sobre';

    protected $fillable = ['texto'];
}
