<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Classe utilizada no Checklist (ChecklistModel)
 *
 * Determina quais "domínios" podem aplicar o template do formulário
 */
class ChecklistDominioModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = true; //Fix $class:updateOrCreate return $id

    protected $table = 'checklist_dominios';

    protected $fillable = ['checklist_id', 'dominio_id'];
}
