<?php

namespace App\Models\Core;

use App\Enums\TipoPerguntaEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Utilizado para compor os formulários aplicados.
 *
 * Um template de formulário (ChecklistModel) pode ter N perguntas.
 *
 * As perguntas funcionam como uma "base de perguntas", as escolhidas são vinculadas ao formulário.
 *
 */
class PerguntaModel extends Model
{
    use SoftDeletes;

    protected $table = 'perguntas';

    protected $fillable = ['tipo_pergunta', 'tabela_colunas', 'tabela_linhas', 'pergunta', 'plano_acao_default', 'texto_apoio', 'tags', 'fl_arquivada'];

    protected $appends = ['pergunta_sinalizada'];

    protected $hidden = ['pergunta_sinalizada'];

    public function respostas()
    {
        return $this->hasMany(RespostaModel::class, 'pergunta_id')->whereNull('respostas.deleted_at');
    }

    /**
     * Métodos "offline" utilizados p/ o download de dados do APP
     */
    public function respostasOffline()
    {
        return $this->hasMany(RespostaModel::class, 'pergunta_id')->withTrashed();
    }

    /**
     * Retorna um sinalizador para o usuário saber que a pergunta faz parte do grupo de perguntas com pontuação.
     */
    public function getPerguntaSinalizadaAttribute()
    {
        if (in_array($this->tipo_pergunta, [TipoPerguntaEnum::Binaria, TipoPerguntaEnum::BinariaCinza, TipoPerguntaEnum::Semaforica, TipoPerguntaEnum::SemaforicaCinza, TipoPerguntaEnum::EscolhaSimplesPontuacao, TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza, TipoPerguntaEnum::NumericaPontuacao]))
            return $this->pergunta . '<span class="text-danger">*</span>';
        else
            return $this->pergunta;
    }
}
