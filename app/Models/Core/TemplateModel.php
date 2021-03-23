<?php

namespace App\Models\Core;

use App\Models\Core\Traits\Scope\TemplatePermissionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Template (base) do caderno de campo
 */
class TemplateModel extends Model
{
    use SoftDeletes;

    protected $table = 'templates';

    protected $fillable = ['nome', 'tipo', 'dominio_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TemplatePermissionScope);
    }

    public function perguntas()
    {
        return $this->belongsToMany(TemplatePerguntaModel::class, 'template_pergunta_templates', 'template_id', 'template_pergunta_id')->whereNull('template_pergunta_templates.deleted_at')->withPivot('id', 'ordem')->withTimestamps();
    }

    public function perguntasWithTrashed()
    {
        return $this->belongsToMany(TemplatePerguntaModel::class, 'template_pergunta_templates', 'template_id', 'template_pergunta_id')->withTrashed()->withPivot('id', 'ordem')->withTimestamps();
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function perguntasOffline()
    {
        return $this->belongsToMany(TemplatePerguntaModel::class, 'template_pergunta_templates', 'template_id', 'template_pergunta_id')->withTimestamps();
    }

    public function templatePerguntasOffline()
    {
        return $this->hasMany(TemplatePerguntaTemplatesModel::class, 'template_id')->withTrashed();
    }

    /**
     * Retorno do domínio
     */
    public function dominio()
    {
        return $this->belongsTo(DominioModel::class, 'dominio_id')->withoutGlobalScopes();
    }

    /**
     * Utilizado somente pelo APP p/ saber se o usuário tem permissão p/ aplicar o Caderno de Campo listado
     */
    public function getCanApplyAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('apply', $this);
    }
}
