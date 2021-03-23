<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Utilizado pelo Checklist (ChecklistModel)
 *
 * São as categorias de um determinado checklist
 */
class ChecklistCategoriaModel extends Model implements Sortable
{
    use SortableTrait;
    use SoftDeletes;

    public $incrementing = true;

    protected $table = 'checklist_categorias';

    protected $fillable = ['checklist_id', 'nome', 'ordem'];

    public $sortable = [
        'order_column_name' => 'ordem',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('checklist_id', $this->checklist_id);
    }

    public function checklist() {
        return $this->belongsTo(ChecklistModel::class, 'checklist_id');
    }

    /**
     * Retorna as perguntas da categoria, p/ mostrar na listagem (Não pega as removidas, porque não interessa nessa parte)
     *
     * @return void
     */
    public function perguntas()
    {
        return $this->belongsToMany(PerguntaModel::class, 'checklist_perguntas', 'checklist_categoria_id', 'pergunta_id')->using(ChecklistPerguntaModel::class)->whereNull('checklist_perguntas.deleted_at')->withPivot('id', 'ordem', 'peso_pergunta', 'fl_plano_acao', 'plano_acao_prioridade', 'fl_obrigatorio')->withTimestamps()->orderBy('ordem', 'ASC');
    }

    /**
     * Retorna as perguntas removidas para continuar mostrando na visualização de um formulário (caso essa pergunta tenha sido respondida)
     *
     * @return mixed
     */
    public function perguntasWithTrashed()
    {
        return $this->belongsToMany(PerguntaModel::class, 'checklist_perguntas', 'checklist_categoria_id', 'pergunta_id')->withTrashed()->using(ChecklistPerguntaModel::class)->withPivot('id', 'ordem', 'peso_pergunta', 'fl_plano_acao', 'plano_acao_prioridade', 'fl_obrigatorio')->withTimestamps()->orderBy('ordem', 'ASC');
    }

    /**
     * Método "offline" utilizados p/ o download de dados do APP
     * @return mixed
     */
    public function checklistPerguntasOffline()
    {
        return $this->hasMany(ChecklistPerguntaModel::class, 'checklist_categoria_id')->withTrashed();
    }

    /**
     * Método "offline" utilizados p/ o download de dados do APP
     * @return mixed
     */
    public function perguntasOffline()
    {
        return $this->belongsToMany(PerguntaModel::class, 'checklist_perguntas', 'checklist_categoria_id', 'pergunta_id')->withTrashed()->withTimestamps();
    }

    /**
     * Utilizado para Calculo das questões semafóricas e numéricas
     *
     * @return mixed
     */
    public function perguntasComPontuacao()
    {
        return $this->belongsToMany(PerguntaModel::class, 'checklist_perguntas', 'checklist_categoria_id', 'pergunta_id')->using(ChecklistPerguntaModel::class)->whereNull('checklist_perguntas.deleted_at')
            ->where(function ($query) {
                $query->where('tipo_pergunta', 'semaforica')
                    ->orWhere('tipo_pergunta', 'semaforica-cinza')
                    ->orWhere('tipo_pergunta', 'binaria')
                    ->orWhere('tipo_pergunta', 'binaria-cinza')
                    ->orWhere('tipo_pergunta', 'numerica-pontuacao')
                    ->orWhere('tipo_pergunta', 'escolha-simples-pontuacao')
                    ->orWhere('tipo_pergunta', 'escolha-simples-pontuacao-cinza');
            })->withPivot('id', 'ordem', 'peso_pergunta')->withTimestamps()->orderBy('ordem', 'ASC');
    }
}
