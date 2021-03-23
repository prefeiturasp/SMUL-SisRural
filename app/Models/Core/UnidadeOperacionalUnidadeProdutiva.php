<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Relacionamento entre Unidade Operacional e Unidade Produtiva
 *
 * "add_manual" é uma flag, quando a unidade produtiva é adicionada manualmente na unidade operacional
 */
class UnidadeOperacionalUnidadeProdutiva extends Pivot
{
    public $incrementing = true; //Fix $class:updateOrCreate return $id

    protected $table = 'unidade_operacional_unidade_produtiva';

    protected $fillable = ['unidade_produtiva_id', 'unidade_operacional_id', 'add_manual'];
}
