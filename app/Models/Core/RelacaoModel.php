<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado no bloco "Pessoas" da Unidade Produtiva (ColaboradorModel)
 *
 * É o tipo de relação da pessoa com a propriedade
 */
class RelacaoModel extends Model
{
    use SoftDeletes;

    protected $table = 'relacoes';

    protected $fillable = ['nome'];

    public function getNomeReportAttribute()
    {
        return 'Pessoas - ' . $this->nome;
    }
}
