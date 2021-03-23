<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado pelo ChecklistPergunta
 *
 * É armazenado qual o peso da pergunta (caso a pergunta tenha "peso")
 *
 * O peso é utilizado para o cálculo de pontuação do formulário
 */
class ChecklistPerguntaRespostaModel extends Model
{
    use SoftDeletes;

    protected $table = 'checklist_pergunta_respostas';

    protected $fillable = ['checklist_pergunta_id', 'resposta_id', 'peso'];
}
