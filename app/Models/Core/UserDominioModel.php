<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado por usuários do tipo "Domínio"
 *
 * A relação aceita que um usuário possa ter N domínios.
 *
 * Na pratica isso não acontece, um usuário só faz parte de UM domínio.
 *
 * Futuramente essa parte deve ser refatorada, adicionado a coluna "dominio_id" na tabela "user"
 *
 * P/ o usuário fazer parte de N Domínios, é adicionado um "clone" do usuário (com outro ID), vinculado ao novo domínio.
 *
 */
class UserDominioModel extends Model
{
    use SoftDeletes;

    protected $table = 'user_dominios';

    protected $fillable = ['user_id', 'dominio_id'];
}
