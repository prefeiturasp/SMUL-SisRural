<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado na Unidade Produtiva, no model UnidadeProdutivaCaracterizacaoModel (é a categoria do "uso do solo")
 */
class SoloCategoriaModel extends Model
{
    use SoftDeletes;

    protected $table = 'solo_categorias';

    protected $fillable = ['nome', 'tipo', 'tipo_form', 'min', 'max'];

    public function getNomeReportAttribute()
    {
        return 'Uso do Solo - ' . $this->nome;
    }

    public function agrobiodiversidade($totalEspecies) {
        $totalEspecies *= 1;
        
        if ($this->min || $this->max) {
            if ($totalEspecies <= $this->min) {
                return 'Baixo';
            } else if ($totalEspecies >= $this->max) {
                return 'Alto';
            } else {
                return 'Médio';
            }
        }

        return '-';
    }
}
