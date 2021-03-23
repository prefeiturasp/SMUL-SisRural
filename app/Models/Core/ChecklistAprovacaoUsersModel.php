<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Utilizado pelo Checklist (ChecklistModel)
 *
 * São os usuários que tem permissão para aprovar um determinado template de formulário
 */
class ChecklistAprovacaoUsersModel extends Pivot
{
    public $incrementing = true;

    protected $table = 'checklist_aprovacao_users';

    protected $fillable = ['id', 'checklist_id', 'user_id'];
}
