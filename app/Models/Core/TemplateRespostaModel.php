<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Resposta da pergunta de um template (caderno de campo)
 */
class TemplateRespostaModel extends Model implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    protected $table = 'template_respostas';

    protected $fillable = ['descricao', 'ordem', 'template_pergunta_id'];

    public $sortable = [
        'order_column_name' => 'ordem',
        'sort_when_creating' => true,
    ];

    public function templatePergunta()
    {
        return $this->belongsTo(TemplatePerguntaModel::class);
    }

    public function buildSortQuery()
    {
        return static::query()->where('template_pergunta_id', $this->template_pergunta_id);
    }
}
