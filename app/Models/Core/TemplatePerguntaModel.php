<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pergunta do template do caderno de campo (TemplateModel)
 */
class TemplatePerguntaModel extends Model
{
    use SoftDeletes;

    protected $table = 'template_perguntas';

    protected $fillable = ['pergunta', 'tipo', 'tags'];

    public function respostas()
    {
        return $this->hasMany(TemplateRespostaModel::class, 'template_pergunta_id')->whereNull('template_respostas.deleted_at');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function respostasOffline()
    {
        return $this->hasMany(TemplateRespostaModel::class, 'template_pergunta_id')->withTrashed();
    }
}
