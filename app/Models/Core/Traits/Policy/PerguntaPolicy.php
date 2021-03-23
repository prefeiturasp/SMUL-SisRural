<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\TipoPerguntaEnum;
use App\Models\Auth\User;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\PerguntaModel;
use App\Models\Core\RespostaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerguntaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário tem permissão para visualizar a pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function view(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para criar uma pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para atualizar a pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function update(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('edit pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para deleter a pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function delete(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        //Caso a pergunta já tenha sido utilizada, não permite a remoção dela
        if (ChecklistPerguntaModel::where('pergunta_id', $pergunta->id)->exists()) {
            return false;
        }

        if ($user->isAdmin() || $user->can('delete pergunta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se é possível inserir respostas para a pergunta
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function createRespostas(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        $tipo_pergunta = $pergunta->tipo_pergunta;
        if ($tipo_pergunta == TipoPerguntaEnum::Semaforica || $tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $tipo_pergunta == TipoPerguntaEnum::Binaria || $tipo_pergunta == TipoPerguntaEnum::BinariaCinza) {
            $count = RespostaModel::where("pergunta_id", $pergunta->id)->count();

            if ($tipo_pergunta == TipoPerguntaEnum::Semaforica && $count == 3) {
                return false;
            } else if ($tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza && $count == 4) {
                return false;
            } else if ($tipo_pergunta == TipoPerguntaEnum::Binaria && $count == 2) {
                return false;
            } else if ($tipo_pergunta == TipoPerguntaEnum::BinariaCinza && $count == 3) {
                return false;
            }
        }

        if ($user->isAdmin() || $user->can('create resposta checklist')) {
            return true;
        }

        return false;
    }

    /**
     * Determina se alguns campos do formulário podem ser editados.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PerguntaModel $pergunta
     * @return mixed
     */
    public function editForm(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        //Se a pergunta foi utilizada em alguma resposta vinculada a Unidade Produtiva, não permite alteração
        if (UnidadeProdutivaRespostaModel::where("pergunta_id", $pergunta->id)->exists()) {
            return false;
        }

        //Se a pergunta foi utilizada em alguma resposta vinculada ao Checklist (Checklist Finalizados), não permite alteração
        if (ChecklistSnapshotRespostaModel::where("pergunta_id", $pergunta->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determina se é possível ainda editar a pergunta ou não.
     *
     * @param  mixed $user
     * @param  mixed $pergunta
     * @return void
     */
    public function editTipoPergunta(?User $user, PerguntaModel $pergunta)
    {
        if ($user === null) {
            return false;
        }

        //Se existe respostas vinculadas na pergunta, não permite alterar
        if (RespostaModel::where("pergunta_id", $pergunta->id)->exists()) {
            return false;
        }

        return true;
    }
}
