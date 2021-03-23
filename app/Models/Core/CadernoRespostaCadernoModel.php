<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado pelo caderno de campo (TemplatePerguntaModel vs CadernoModel), respostas do caderno de campo
 */
class CadernoRespostaCadernoModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'caderno_resposta_caderno';

    protected $fillable = ['id', 'caderno_id', 'template_pergunta_id', 'template_resposta_id', 'resposta', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        //Método para o "id" único do tipo string, consumido pelo APP (sync)
        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = (string) Uuid::generate(4);
        });
    }

    /**
     * Retorna o caderno relacionado com a resposta
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function caderno()
    {
        return $this->belongsTo(CadernoModel::class)->withoutGlobalScopes();
    }

    /**
     * Retorna a pergunta relacionada com a resposta
     * @return mixed
     */
    public function pergunta()
    {
        return $this->belongsTo(TemplatePerguntaModel::class, 'template_pergunta_id');
    }

    /**
     * Retorna a resposta da pergunta (caso tenha um id atrelado)
     *
     * Porque a resposta pode ser através de um "id" (template_resposta_id) ou uma resposta do tipo "string", ai o parametro é "resposta"
     *
     * @return mixed
     */
    public function templateResposta()
    {
        return $this->belongsTo(TemplateRespostaModel::class, 'template_resposta_id')->withTrashed();
    }
}
