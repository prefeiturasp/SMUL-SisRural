<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Vinculação da Pergunta com o Template (Caderno de campo)
 */
class TemplatePerguntaTemplatesModel extends Pivot implements Sortable
{
    use SoftDeletes;
    use SortableTrait;

    public $incrementing = true;

    protected $table = 'template_pergunta_templates';

    protected $fillable = ['id', 'template_pergunta_id', 'template_id', 'ordem'];

    public $sortable = [
        'order_column_name' => 'ordem',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('template_id', $this->template_id);
    }
}
