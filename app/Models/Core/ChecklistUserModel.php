<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Classe utilizada no Checklist (ChecklistModel)
 *
 * Determina quais "usuários" podem aplicar o template do formulário
 */
class ChecklistUserModel extends Pivot
{
    use SoftDeletes;

    public $incrementing = true;

    protected $table = 'checklist_users';

    protected $fillable = ['checklist_id', 'user_id'];
}
