<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Perguntas vinculadas ao Checklist
 *
 * Existe uma base de "Perguntas" dentro do sistema. Todos os "Domínios" podem utilizar.
 *
 * O "ChecklistPergunta" serve como uma referência para o "Formulario" -> "Pergunta".
 *
 * "Formulário" -> "Categorias" -> "ChecklistPerguntas" -> "Perguntas" -> "Respostas"
 *
 * Com isso, as respostas das "unidades produtivas" são propagados para todo sistema, independente de qual domínio aplicou o formulário. (Resposta ÚNICA para a Pergunta)
 *
 */
class ChecklistPerguntaModel extends Pivot implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    public $incrementing = true;

    protected $table = 'checklist_perguntas';

    protected $fillable = ['checklist_categoria_id', 'pergunta_id', 'peso_pergunta', 'fl_obrigatorio', 'fl_plano_acao', 'plano_acao_prioridade', 'ordem'];

    public $sortable = [
        'order_column_name' => 'ordem',
        'sort_when_creating' => true,
    ];

    /**
     * Retorna a categoria que o "checklist_pergunta" esta atrelada
     * @return mixed
     */
    public function categoria()
    {
        return $this->belongsTo(ChecklistCategoriaModel::class, 'checklist_categoria_id');
    }

    /**
     * Retorna a pergunta que o "checklist_pergunta" esta atrelada
     * @return mixed
     */
    public function pergunta()
    {
        return $this->belongsTo(PerguntaModel::class, 'pergunta_id');
    }

    /**
     * Retorna as respostas das perguntas "checklist_pergunta", para ser extraído os pesos de forma simples
     * @return mixed
     */
    public function perguntaRespostasPesos()
    {
        return $this->hasMany(ChecklistPerguntaRespostaModel::class, 'checklist_pergunta_id', 'id');
    }

    public function buildSortQuery()
    {
        return static::query()->where('checklist_categoria_id', $this->checklist_categoria_id);
    }
}
