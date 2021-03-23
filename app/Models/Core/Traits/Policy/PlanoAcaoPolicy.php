<?php

namespace App\Models\Core\Traits\Policy;

use App\Enums\PlanoAcaoStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Services\PlanoAcaoService;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Este Policy corresponde aos seguintes escopos
 *
 * - Plano de Ação Individual
 * - Plano de Ação Coletivo
 * - Plano de Ação criado a partir de um Formulário
 */
class PlanoAcaoPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     *  Determina se o usuário tem permissão para visualizar o plano de acao
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function view(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('view menu plano_acao')) {
            return true;
        }

        return false;
    }

    /**
     *  Determina se o usuário tem permissão para criar o plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->can('create plano_acao')) {
            /**
             * Tratamento especifico para o POST (/store), validando se ainda não existe nenhum PDA concorrente
             *
             * Essa ação pode ocorrer com concorrencia de telas.
             */
            if (request()->has('nome')) {
                $service = new PlanoAcaoService();

                $checklist_unidade_produtiva_id = @request('checklist_unidade_produtiva_id');
                $produtor_id = @request('produtor_id');
                $unidade_produtiva_id = @request('unidade_produtiva_id');

                //PDA com Formulário
                if ($checklist_unidade_produtiva_id) {
                    $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::withoutGlobalScopes()->findOrFail($checklist_unidade_produtiva_id);

                    if (!$service->permiteCriarPdaComChecklistPerguntas($checklistUnidadeProdutiva->checklist)) {
                        return false;
                    }

                    if (!$service->permiteCriarPdaComChecklist($checklistUnidadeProdutiva)) {
                        return false;
                    }

                    if (!$service->permiteCriarPdaComChecklistConcluido($checklistUnidadeProdutiva)) {
                        return false;
                    }
                } else if ($produtor_id && $unidade_produtiva_id) {
                    //PDA Individual
                    $flPermiteCriarPda = $this->remember("PlanoAcaoPolicy-create-flPermiteCriarPda-{$produtor_id}-{$unidade_produtiva_id}", function () use ($service, $produtor_id, $unidade_produtiva_id) {
                        return $service->permiteCriarPda(ProdutorModel::withoutGlobalScopes()->findOrFail($produtor_id), UnidadeProdutivaModel::withoutGlobalScopes()->findOrFail($unidade_produtiva_id));
                    });

                    if (!$flPermiteCriarPda) {
                        return false;
                    }
                }

                //PDA coletivo
                //Não tem regra nenhuma.
            }

            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para editar o plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function update(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Cliente informou que pode editar o plano de ação independente do status, mas só depois de reabrir o status
        if ($planoAcao->status == PlanoAcaoStatusEnum::Concluido || $planoAcao->status == PlanoAcaoStatusEnum::Cancelado) {
            return false;
        }

        //Se tiver com o status "Aguardando Aprovação" não pode alterar, até ser revisado
        if ($planoAcao->status == PlanoAcaoStatusEnum::AguardandoAprovacao) {
            return false;
        }

        if ($user->can('edit plano_acao') && $this->checkPermissionPlanoAcao($planoAcao, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário tem permissão para reabrir o plano de ação
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function reopen(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        return $this->remember("PlanoAcaoPolicy-reopen-{$user->id}-{$planoAcao->id}", function () use ($user, $planoAcao) {
            //Só pode reabrir se o status for Concluído ou Cancelado
            if ($planoAcao->status == PlanoAcaoStatusEnum::Concluido || $planoAcao->status == PlanoAcaoStatusEnum::Cancelado) {
                if ($user->isAdmin()) {
                    return true;
                }

                if ($this->checkPermissionPlanoAcao($planoAcao, $user)) {

                    //PDA derivado de Formulário (Checklist), Status vigente/em andamento, não permite reabrir
                    if (!$planoAcao->fl_coletivo && $planoAcao->checklist_unidade_produtiva_id && PlanoAcaoModel::where(['produtor_id' => $planoAcao->produtor_id, 'unidade_produtiva_id' => $planoAcao->unidade_produtiva_id, 'checklist_unidade_produtiva_id' => $planoAcao->checklist_unidade_produtiva_id, 'fl_coletivo' => 0])->whereIn('status', [PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento])->exists()) {
                        return false;
                    }

                    //PDA individual, Status vigente/em andamento, não permite reabrir
                    if (!$planoAcao->fl_coletivo && !$planoAcao->checklist_unidade_produtiva_id && PlanoAcaoModel::where(['produtor_id' => $planoAcao->produtor_id, 'unidade_produtiva_id' => $planoAcao->unidade_produtiva_id, 'checklist_unidade_produtiva_id' => null, 'fl_coletivo' => 0])->whereIn('status', [PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento])->exists()) {
                        return false;
                    }

                    return true;
                }
            }

            return false;
        });
    }

    /**
     *  Determina se o usuário tem permissão para remover o plano de ação
     *
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function delete(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $this->remember("PlanoAcaoPolicy-delete-{$user->id}-{$planoAcao->id}", function () use ($user, $planoAcao) {
            //Verifica se o usuário tem acesso ao plano de ação
            if ($user->can('delete plano_acao')) {
                //Individual / Checklist / Coletivo (Pai)

                if ($this->checkPermissionPlanoAcao($planoAcao, $user)) {
                    //Individual
                    if (!$planoAcao->fl_coletivo && !$planoAcao->checklist_unidade_produtiva_id) {
                        return true;
                    }

                    //Coletivo (Pai)
                    if ($planoAcao->fl_coletivo && !$planoAcao->plano_acao_coletivo_id) {
                        return true;
                    }

                    //Formulário
                    if (!$planoAcao->fl_coletivo && $planoAcao->checklist_unidade_produtiva_id) {
                        //Chamada "checklist_unidade_produtiva()->first()" p/ não injetar no model os dados (OfflineController)
                        $isFluxoAprovacao = $planoAcao->checklist_unidade_produtiva()->first()->checklist->fl_fluxo_aprovacao;
                        $isObrigatorio = $planoAcao->checklist_unidade_produtiva()->first()->checklist->fl_obrigatorio;

                        if ($user->isTecnico()) {
                            //Técnico && Status = rascunho
                            if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                                return true;
                            }
                        } else if ($user->isUnidOperacional()) {
                            //Unidade Operacional/Domínio && Status = rascunho
                            if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                                return true;
                            }

                            //Opcional ou Obrigatório && Não tem Fluxo
                            if (!$isFluxoAprovacao) {
                                return true;
                            }

                            //Opcional && Tem Fluxo Aprovação
                            if (!$isObrigatorio && $isFluxoAprovacao) {
                                return true;
                            }

                            //Obrigatorio && Tem Fluxo Aprovação
                            if ($isObrigatorio && $isFluxoAprovacao) {
                                return false;
                            }
                        } else if ($user->isDominio()) {
                            //Domínio && Status = rascunho
                            if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                                return true;
                            }

                            //Opcional ou Obrigatório && Não tem Fluxo
                            if (!$isFluxoAprovacao) {
                                return true;
                            }

                            //Opcional && Tem Fluxo
                            if (!$isObrigatorio && $isFluxoAprovacao) {
                                return true;
                            }

                            //Obrigatorio && Tem Fluxo Aprovação
                            if ($isObrigatorio && $isFluxoAprovacao) {
                                //Só permite se o Formulário aplicado foi removido, todos os STATUS
                                if ($planoAcao->checklist_unidade_produtiva()->first()->deleted_at) {
                                    return true;
                                }
                            }
                        }
                    }
                }

                //Coletivo (Filho x Unidade Produtiva)
                if ($planoAcao->fl_coletivo && $planoAcao->plano_acao_coletivo_id) {
                    $planoAcaoPai = PlanoAcaoModel::withoutTrashed()->where('id', $planoAcao->plano_acao_coletivo_id)->first();
                    if ($planoAcaoPai && $this->checkPermissionPlanoAcao($planoAcaoPai, $user)) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    /**
     * Verifica se o usuário tem permissão para excluir definitivamente um PDA aplicado
     * - técnico ou unidade operacional -> Apenas PDA nao iniciado
     *
     * @param \App\Models\Auth\User|null $user
     * @param \App\Models\Core\PlanoAcaoModel $planoAcao
     * @return mixed
     *
     * @deprecated Desabilitado temporariamente até definir o que será feito com o APP
     */
    public function forceDelete(?User $user, PlanoAcaoModel $planoAcao)
    {
        return false;

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Unidade Operacional ou Técnico
        if (($user->isUnidOperacional() || $user->isTecnico()) && $planoAcao->status == PlanoAcaoStatusEnum::NaoIniciado) {
            return true;
        }

        return false;
    }


    /**
     * Restaurar plano de ação
     *
     * Quem remove tem permissão p/ restaurar
     *
     * @param  User $user
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function restore(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $this->remember("PlanoAcaoPolicy-restore-{$user->id}-{$planoAcao->id}", function () use ($user, $planoAcao) {

            //Verifica se o usuário tem acesso ao plano de ação
            if ($user->can('delete plano_acao') && $this->checkPermissionPlanoAcao($planoAcao, $user)) {

                //Individual
                if (!$planoAcao->fl_coletivo && !$planoAcao->checklist_unidade_produtiva_id) {
                    //PDA individual, Status vigente/em andamento, não permite restaurar
                    if (!$planoAcao->checklist_unidade_produtiva_id && !$planoAcao->fl_coletivo && in_array($planoAcao->status, [PlanoAcaoStatusEnum::EmAndamento, PlanoAcaoStatusEnum::NaoIniciado]) && PlanoAcaoModel::where(['produtor_id' => $planoAcao->produtor_id, 'unidade_produtiva_id' => $planoAcao->unidade_produtiva_id, 'checklist_unidade_produtiva_id' => null, 'fl_coletivo' => 0])->whereIn('status', [PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento])->exists()) {
                        return false;
                    }

                    return true;
                }

                //Coletivo (Pai)
                if ($planoAcao->fl_coletivo && !$planoAcao->plano_acao_coletivo_id) {
                    return true;
                }

                //Formulário
                if (!$planoAcao->fl_coletivo && $planoAcao->checklist_unidade_produtiva_id) {

                    //Se existe um outro plano de ação vinculado, não permite mais restaurar
                    if (count($planoAcao->checklist_unidade_produtiva->plano_acao) > 0) {
                        return false;
                    }

                    //Se o formulário estiver removido, não permite restaurar
                    if ($planoAcao->checklist_unidade_produtiva->deleted_at) {
                        return false;
                    }

                    //Essa lógica parece não se aplicar mais, porque o PDA SÓ PODE TER UM FORMULÁRIO CORRESPONDENTE
                    //PDA derivado de Formulário (Checklist), Status vigente/em andamento, não permite restaurar ... verifica se o PDA que quer restaurar esta com o status Em Andamento/Nao iniciad ... se não, pode permitir restaurar.
                    // if (($planoAcao->status == PlanoAcaoStatusEnum::EmAndamento || $planoAcao->status == PlanoAcaoStatusEnum::NaoIniciado) && $planoAcao->checklist_unidade_produtiva && PlanoAcaoModel::where(['produtor_id' => $planoAcao->produtor_id, 'unidade_produtiva_id' => $planoAcao->unidade_produtiva_id, 'checklist_unidade_produtiva_id' => $planoAcao->checklist_unidade_produtiva_id, 'fl_coletivo' => 0])->whereIn('status', [PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento])->exists()) {
                    //     return false;
                    // }

                    $isFluxoAprovacao = $planoAcao->checklist_unidade_produtiva->checklist->fl_fluxo_aprovacao;
                    $isObrigatorio = $planoAcao->checklist_unidade_produtiva->checklist->fl_obrigatorio;

                    if ($user->isTecnico()) {
                        //Técnico && Status = rascunho
                        if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                            return true;
                        }
                    } else if ($user->isUnidOperacional()) {
                        //Unidade Operacional/Domínio && Status = rascunho
                        if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                            return true;
                        }

                        //Opcional ou Obrigatório && Não tem Fluxo
                        if (!$isFluxoAprovacao) {
                            return true;
                        }

                        //Opcional && Tem Fluxo Aprovação
                        if (!$isObrigatorio && $isFluxoAprovacao) {
                            return true;
                        }

                        //Obrigatorio && Tem Fluxo Aprovação
                        //Nunca vai cair aqui
                        if ($isObrigatorio && $isFluxoAprovacao) {
                            return false;
                        }
                    } else if ($user->isDominio()) {
                        //Domínio && Status = rascunho
                        if ($planoAcao->status == PlanoAcaoStatusEnum::Rascunho) {
                            return true;
                        }

                        //Opcional ou Obrigatório && Não tem Fluxo
                        if (!$isFluxoAprovacao) {
                            return true;
                        }

                        //Opcional && Tem Fluxo
                        if (!$isObrigatorio && $isFluxoAprovacao) {
                            return true;
                        }

                        //Obrigatorio && Tem Fluxo Aprovação
                        if ($isObrigatorio && $isFluxoAprovacao) {
                            //Só permite se o Formulário aplicado foi restaurado, todos os STATUS
                            //Essa é a única alteração comparado com a lógica do "PlanoAcaoPolicy- > delete()"
                            if (!$planoAcao->checklist_unidade_produtiva->deleted_at) {
                                return true;
                            }
                        }
                    }
                }
            }

            return false;
        });
    }

    /**
     * Define se o usuário tem permissão para criar um novo item no plano de ação
     *
     * @param  User $user
     * @param  PlanoAcaoModel $planoAcao
     * @return bool
     */
    public function createItem(?User $user, PlanoAcaoModel $planoAcao)
    {
        if ($user === null) {
            return false;
        }

        //Se o plano de ação tiver um formulário vinculado, não permite criar itens (nem sendo o Admin)
        if ($planoAcao->checklist_unidade_produtiva_id) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->can('create plano_acao') && $this->checkPermissionPlanoAcao($planoAcao, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Permite disparar email pro cliente do plano de ação individual
     *
     * @param  User $user
     * @param  PlanoAcaoModel $planoAcao
     * @return bool
     */
    public function sendEmail(?User $user, PlanoAcaoModel $planoAcao)
    {
        return false; //cliente pediu para retirar

        if ($user === null) {
            return false;
        }

        //Plano de ação coletivo nunca dispara email
        if ($planoAcao->fl_coletivo) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        //Se tem permissão para editar permite enviar o email p/ o produtor (técnicos e unidades operacionais)
        if ($user->can('edit plano_acao')) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário tem permissão p/ adicionar um acompanhemtno no Plano de Ação
     */
    public function history(?User $user, PlanoAcaoModel $planoAcao): bool
    {
        //Se não for coletivo, ignora, porque o tratamento fica no can('update')
        if (!$planoAcao->fl_coletivo) {
            return true;
        }

        //Se coletivo, só pode adicionar se for escopado
        if ($planoAcao->fl_coletivo && $planoAcao->usuarioScoped()->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário tem permissão para "acessar" o Plano de ação
     *
     * O acesso é a base para "editar/restaurar/deletar"
     * @param  mixed $planoAcao
     * @return bool
     *
     */
    private function checkPermissionPlanoAcao(PlanoAcaoModel $planoAcao, User $user): bool
    {
        return $this->remember("PlanoAcaoPolicy-checkPermissionPlanoAcao-{$user->id}-{$planoAcao->id}", function () use ($user, $planoAcao) {
            //Individual
            //Se esta em sua abrangencia, permite editar
            if (!$planoAcao->fl_coletivo && !$planoAcao->checklist_unidade_produtiva_id && $planoAcao->unidadeProdutivaScoped()->exists()) {
                return true;
            }

            //Formulário
            //Se tem acesso ao formulário aplicado e acesso a "aplicação" do formulário aplicado, permite editar
            if (!$planoAcao->fl_coletivo && $planoAcao->checklist_unidade_produtiva_id && $planoAcao->checklistUnidadeProdutivaScoped()->exists() && $planoAcao->checklistUnidadeProdutivaScoped()->first()->checklistScoped()->exists()) {
                return true;
            }

            //Coletivo
            //Só permite editar se o PDA faz parte da hierarquia do usuário
            if ($planoAcao->fl_coletivo && $planoAcao->usuarioScoped()->exists()) {
                return true;
            }

            return false;
        });
    }
}
