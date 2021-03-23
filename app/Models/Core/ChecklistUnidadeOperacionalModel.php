<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizada no Checklist (ChecklistModel)
 *
 * Determina quais "unidades operacionais" podem aplicar o template do formulário
 */
class ChecklistUnidadeOperacionalModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = true;

    protected $table = 'checklist_unidade_operacionais';

    protected $fillable = ['checklist_id', 'unidade_operacional_id'];
}
