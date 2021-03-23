<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pela Unidade Produtiva (UnidadeProdutivaModel)
 */
class CertificacaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'certificacoes';

    protected $fillable = ['nome', 'form'];
}
