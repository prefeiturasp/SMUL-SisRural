<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado por usuários do tipo "Técnico" / "Unidade Operacional"
 *
 * A relação aceita que um usuário possa ter N unidades operacionais.
 *
 * Na pratica isso acontece, com uma ressalva, um usuário só faz parte de N unidades operacionais de UM MESMO "Domínio"
 *
 * P/ o usuário fazer parte de Unidades Operacionais que não fazem parte do domínio, é adicionado um "clone" do usuário (com outro ID), vinculado ao domínio/unidade operacional
 *
 */
class UserUnidadeOperacionalModel extends Model
{
    use SoftDeletes;

    protected $table = 'user_unidade_operacionais';

    protected $fillable = ['user_id', 'unidade_operacional_id'];
}
