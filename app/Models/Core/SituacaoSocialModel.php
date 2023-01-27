<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SituacaoSocialModel extends Model
{
    protected $table = 'situacao_sociais';

    protected $fillable = ['nome'];    
}
