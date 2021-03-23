<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Models\Auth\User;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Auth\Access\HandlesAuthorization;
use Request;

class ChecklistUnidadeProdutivaPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     *
     * Otimização das queries p/ o sync mobile
     *
     * @param mixed $caderno
     * @param mixed $user
     *
     * @return [type]
     */
    private function checkChecklistScoped($checklistUnidadeProdutiva, $user)
    {
        return $this->remember("ChecklistUnidadeProdutivaPolicy-checkChecklistScoped-{$user->id}-{$checklistUnidadeProdutiva->checklist_id}", function () use ($user, $checklistUnidadeProdutiva) {
            return $checklistUnidadeProdutiva->checklistScoped()->exists();
        });
    }

    /**
     * Determina se o usuário tem permissão para visualizar
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function view(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        //Se o formulário foi removido, desabilida a opção de visualizar, p/ todos os usuários.
        if ($checklistUnidadeProdutiva->deleted_at) {
            return false;
        }

        if ($user->isAdmin() || $user->isAdminLOP()) {
            return true;
        }

        //Admin, Domínio, Unid. Operacional e Técnico possuem essa permissão
        if (!$user->can('view menu checklist_unidade_produtiva')) {
            return false;
        }

        //Tem permissão para aplicar o formulário (template) e enxerga a unidade produtiva
        if ($this->checkChecklistScoped($checklistUnidadeProdutiva, $user) || $checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists()) {
            return true;
        }

        //Pode não ter permissão no template e na unidade produtiva, mas ele tem permissão para ANALISAR um determinado template, então libera p/ visualização
        if ($checklistUnidadeProdutiva->analistaAutorizado()->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar um formulário
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('create checklist_unidade_produtiva')) {
            /**
             * Tratamento especifico para o POST (/store), validando se ainda não existe nenhum formulário em rascunho p/ o mesmo produtor/unidadeprodutiva/checklist
             *
             * Essa ação pode ocorrer com concorrencia de telas.
             */
            if (request()->has('status')) {
                $checklist_id = request('checklist_id');
                $produtor_id = request('produtor_id');
                $unidade_produtiva_id = request('unidade_produtiva_id');

                if (ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklist_id, 'produtor_id' => $produtor_id, 'unidade_produtiva_id' => $unidade_produtiva_id, 'status' => ChecklistStatusEnum::Rascunho])->exists()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode editar um formulário aplicado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function update(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if (
            $checklistUnidadeProdutiva->status == ChecklistStatusEnum::Finalizado
            || $checklistUnidadeProdutiva->status == ChecklistStatusEnum::AguardandoAprovacao
            || $checklistUnidadeProdutiva->status == ChecklistStatusEnum::AguardandoPda
        ) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('edit checklist_unidade_produtiva')) {
            return false;
        }

        /**
         *
         * Verifica, via escopo do checklist, se o usuário logado possui
         * permissão de aplicação do referido checklist, caso positivo
         * libera para edição.
         *
         * Teste realizado visando barrar um técnico que, mesmo com
         * a permissao 'edit checklist_unidade_produtiva' não tem permissão de editar esse
         * checklist_unidade_produtiva específico.
         *
         * -----------------
         *
         * A REGRA "checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists" NÃO EXISTE MAIS,
         * Se o usuário tem permissão para aplicar, ele consequentemente pode EDITAR //Ignorado: O usuário precisa ter permissão para "enxergar" a unidade produtiva (abrangencia), porque pode mostrar formulários fora da abrangência do usuário (ANALISE)
         */
        if (
            $this->checkChecklistScoped($checklistUnidadeProdutiva, $user)

            //Comentei essa regra porque se o usuário tem permissão para APLICAR aquele formulário, não interessa qual é a UNIDADE PRODUTIVA
            //$checklistUnidadeProdutiva->checklistScoped()->exists() && $checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário tem permissão para arquivar/remover um formulário aplicado
     *
     * O formulário aplicado precisa ser de um formulário que o usuário tem permissão para aplicar (->checklistScoped())
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function delete(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Se não pode "Aplicar", não permite fazer nenhuma ação
        if (!$this->checkChecklistScoped($checklistUnidadeProdutiva, $user)) {
            return false;
        }

        if ($user->can('delete checklist_unidade_produtiva')) {
            //Chamada checklist()->first() p/ não injetar no model os dados (OfflineController)
            $isFluxoAprovacao = $checklistUnidadeProdutiva->checklist()->first()->fl_fluxo_aprovacao;

            if ($user->isTecnico()) {
                //Se Rascunho, permite
                if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
                    return true;
                }
            }

            if ($user->isUnidOperacional()) {
                //Se Rascunho, permite
                if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
                    return true;
                }

                //Sem Não tem Fluxo, permite remover
                if (!$isFluxoAprovacao) {
                    return true;
                }

                //Se tem Fluxo, não permite remover
                if ($isFluxoAprovacao) {
                    return false;
                }
            }

            if ($user->isDominio()) {
                //Não interessa se tem ou não fluxo de aprovação, permite sempre remover
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário tem permissão para excluir definitivamente um formulário aplicado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     *
     * @deprecated Foi desabilidade a remoção física por causa do SYNC com o APP
     */
    public function forceDelete(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        return false;

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('delete checklist_unidade_produtiva')) {
            return false;
        }

        if (!$this->checkChecklistScoped($checklistUnidadeProdutiva, $user)) {
            //Comentei essa linha, porque se o usuário pode APLICAR, ele pode fazer QUALQUER COISA com o Formulário
            //if (!$checklistUnidadeProdutiva->checklistScoped()->exists() || !$checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists()) {
            return false;
        }

        //Unidade Operacional ou Técnico
        if (($user->isUnidOperacional() || $user->isTecnico()) && $checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode restaurar o formulário aplicado.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     *
     * */
    public function restore(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('delete checklist_unidade_produtiva')) {
            return false;
        }

        //Se não pode "Aplicar", não permite fazer nenhuma ação
        if (!$this->checkChecklistScoped($checklistUnidadeProdutiva, $user)) {
            return false;
        }

        //Não permite -> Se tem algum checklist com o STATUS = rascunho || aguardando pda || aguardando aprovação (Algum formulário em andamento)
        $list = ChecklistUnidadeProdutivaModel::where(['checklist_id' => $checklistUnidadeProdutiva->checklist->id, 'produtor_id' => $checklistUnidadeProdutiva->produtor->id, 'unidade_produtiva_id' => $checklistUnidadeProdutiva->unidade_produtiva->id])->whereIn('status', [ChecklistStatusEnum::Rascunho, ChecklistStatusEnum::AguardandoPda, ChecklistStatusEnum::AguardandoAprovacao]);
        if ($list->exists()) {
            return false;
        }

        // Poderia restaurar se o formulário que vai ser restaurado já esta finalizado ou cancelado
        // if (in_array($checklistUnidadeProdutiva->status, [ChecklistStatusEnum::Finalizado, ChecklistStatusEnum::Cancelado]))

        $isFluxoAprovacao = $checklistUnidadeProdutiva->checklist->fl_fluxo_aprovacao;

        if ($user->isTecnico()) {
            //Se Rascunho, permite
            if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
                return true;
            }
        }

        if ($user->isUnidOperacional()) {
            //Se Rascunho, permite
            if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho) {
                return true;
            }

            //Sem Não tem Fluxo, permite remover
            if (!$isFluxoAprovacao) {
                return true;
            }

            //Se tem Fluxo, não permite remover
            if ($isFluxoAprovacao) {
                return false;
            }
        }

        if ($user->isDominio()) {
            //Não interessa se tem ou não fluxo de aprovação, permite sempre remover
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para fazer a analise no formulário aplicado. (Fluxo de Aprovação)
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function analize(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (in_array($user->id, $checklistUnidadeProdutiva->checklist->usuariosAprovacao->pluck('id')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode reanalizar o formulário aplicado.
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function reanalyse(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (
            count($checklistUnidadeProdutiva->analiseLogs->pluck('id')->toArray()) && //Conta porque só pode reanalizar depois que já teve alguma analise
            $checklistUnidadeProdutiva->status_flow == ChecklistStatusFlowEnum::AguardandoRevisao &&
            in_array($user->id, $checklistUnidadeProdutiva->checklist->usuariosAprovacao->pluck('id')->toArray())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode enviar ou não email para o produtor
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     */
    public function sendEmail(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        return false; //cliente pediu para retirar

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('edit checklist_unidade_produtiva')) {
            return false;
        }

        if (!$checklistUnidadeProdutiva->checklistScoped()->exists()) {
            //Comentei essa linha, porque se o usuário pode APLICAR, ele pode fazer QUALQUER COISA com o Formulário
            //if (!$checklistUnidadeProdutiva->checklistScoped()->exists() || !$checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists()) {
            return false;
        }

        //Se tem permissão para editar, status for finalizado e pode "aplicar" o formulário ... então é permitido enviar o email p/ o produtor
        if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Finalizado && $checklistUnidadeProdutiva->checklistScoped()->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode cancelar um formulário
     *
     * - Apenas domínio e unid. operacional podem fazer essa ação
     * - Apenas formulários concluídos
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return mixed
     *
     */
    public function cancel(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->can('edit checklist_unidade_produtiva')) {
            return false;
        }

        if (!$checklistUnidadeProdutiva->checklistScoped()->exists()) {
            //Comentei essa linha, porque se o usuário pode APLICAR, ele pode fazer QUALQUER COISA com o Formulário
            //if (!$checklistUnidadeProdutiva->checklistScoped()->exists() || !$checklistUnidadeProdutiva->unidadeProdutivaScoped()->exists()) {
            return false;
        }

        //Se é usuário tem permissão para aplicar aquele template, checklist com o status finalizado e o usuário é do tipo Unidade Operacional ou Técnico
        if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Finalizado && $checklistUnidadeProdutiva->checklistScoped()->exists() && ($user->isDominio() || $user->isUnidOperacional())) {
            return true;
        }

        return false;
    }

    public function createPda(?User $user, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        if ($user === null) {
            return false;
        }

        $checklistPerguntas = ChecklistPerguntaModel::with('pergunta')->where("fl_plano_acao", 1)->whereIn('checklist_categoria_id', $checklistUnidadeProdutiva->checklist->categorias->pluck('id'))->orderBy('plano_acao_prioridade', 'ASC')->get();
        if (count($checklistPerguntas) > 0 && ($user->isTecnico() || $user->unidadesOperacionais())) {
            return true;
        }

        return false;
    }
}
