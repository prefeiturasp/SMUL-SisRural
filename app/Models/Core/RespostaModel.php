<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Relacionado a Pergunta (PerguntaModel), quando o tipo da pergunta é de seleção (multipla escolha, únida escolha, semafórica. binária ...)
 */
class RespostaModel extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    protected $table = 'respostas';

    protected $fillable = ['pergunta_id', 'descricao', 'cor', 'texto_apoio', 'ordem'];

    public $sortable = [
        'order_column_name' => 'ordem',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('pergunta_id', $this->pergunta_id);
    }
}
