<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado dentro do InstalacaoModel -> Unidade Produtiva
 */
class InstalacaoTipoModel extends Model
{
    use SoftDeletes;

    protected $table = 'instalacao_tipos';

    protected $fillable = ['nome'];

    public function getNomeReportAttribute()
    {
        return 'Infraestrutura - ' . $this->nome;
    }
}
