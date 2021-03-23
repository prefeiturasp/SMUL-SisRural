<?php

namespace App\Repositories\Backend\Core;

use App\Enums\PlanoAcaoStatusEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PlanoAcaoColetivoRepository extends BaseRepository
{
    public function __construct(PlanoAcaoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Ao vincular novas unidades produtivas, cria um Plano de Ação Individual e replica todas as ações (items) p/ esse plano de ação individual.
     *
     * No plano de acao individual é adicionado uma referencia p/ o coletivo (referencias utilizadas na remoção e atualização do plano de ação coletivo)
     *
     * No item do plano de acao individual é adicionado uma referencia p/ o item do coletivo (referencias utilizadas na remoção e atualização do item do plano de ação coletivo)
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return PlanoAcaoModel
     */
    public function createUnidadeProdutiva(PlanoAcaoModel $planoAcao, ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva): PlanoAcaoModel
    {
        return DB::transaction(function () use ($planoAcao, $produtor, $unidadeProdutiva) {
            //Criar um novo plano de ação vinculado com o Plano de Ação Coletivo / Produtor / Unidade Produtiva
            $planoAcaoUnidadeProdutiva = $planoAcao->replicate();
            $planoAcaoUnidadeProdutiva->uid = null;
            $planoAcaoUnidadeProdutiva->fl_coletivo = 1;
            $planoAcaoUnidadeProdutiva->plano_acao_coletivo_id = $planoAcao->id;

            $planoAcaoUnidadeProdutiva->produtor_id = $produtor->id;
            $planoAcaoUnidadeProdutiva->unidade_produtiva_id = $unidadeProdutiva->id;

            if (\Auth::user()) {
                $planoAcaoUnidadeProdutiva->user_id = \Auth::user()->id;
            }

            $planoAcaoUnidadeProdutiva->save();

            //Vincular todas as ações do GRUPO para o plano de ação individual
            foreach ($planoAcao->itens as $k => $v) {
                $planoAcaoItem = $v->replicate();
                $planoAcaoItem->uid = null;
                $planoAcaoItem->plano_acao_id = $planoAcaoUnidadeProdutiva->id;
                $planoAcaoItem->plano_acao_item_coletivo_id = $v->id;
                $planoAcaoItem->fl_coletivo = 1;
                $planoAcaoItem->save();
            }

            return $planoAcaoUnidadeProdutiva;
        });

        throw new GeneralException('Favor tentar novamente');
    }

    /**
     * Criar um plano de ação coletivo
     *
     * @param  mixed $data
     * @return PlanoAcaoModel
     */
    public function create(array $data): PlanoAcaoModel
    {
        return DB::transaction(function () use ($data) {
            $data['fl_coletivo'] = 1;
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualizar um plano de ação coletivo
     *
     * @param  PlanoAcaoModel $model
     * @param  mixed $data
     * @return PlanoAcaoModel
     */
    public function update(PlanoAcaoModel $model, array $data)
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                foreach ($model->plano_acao_filhos as $k => $v) {
                    $v->nome = $model->nome;
                    $v->prazo = $model->prazo;
                    $v->status = $model->status;
                    $v->save();
                }

                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remover um plano de ação coletivo (remove os filhos juntos)
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     */
    public function delete(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {

                //Os filhos são removidos p/ não impactar na contagem (percentual) na visualização das tabelas
                foreach ($model->plano_acao_filhos as $k => $v) {
                    $v->delete();
                }

                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove fisicamente um plano de ação coletivo
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     *
     * @deprecated Essa ação não é possível por causa do Sync com o APP
     */
    public function forceDelete(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            foreach ($model->plano_acao_filhos()->withTrashed()->get() as $k => $vPlanoAcaoFilho) {
                $this->forceDeletePDA($vPlanoAcaoFilho);
            }

            $this->forceDeletePDA($model);

            return true;

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Força a remoção de um PDA (todos os filhos, históricos dos itens e históricos)
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     */
    private function forceDeletePDA(PlanoAcaoModel $model): bool
    {
        //Remover Acompanhamentos do PDA
        foreach ($model->historicos()->withTrashed()->get() as $vHistorico) {
            $vHistorico->forceDelete();
        }

        foreach ($model->itens()->withTrashed()->get() as $vItem) {
            //Remover Acompanhamentos das Ações
            foreach ($vItem->historicos()->withTrashed()->get() as $vHistoricoItem) {
                $vHistoricoItem->forceDelete();
            }

            //Remover Ações
            $vItem->forceDelete();
        }

        //Remover PDA
        $model->forceDelete();

        return true;
    }

    /**
     * Restaura um plano de ação coletivo removido (junto com os filhos)
     *
     * @param  PlanoAcaoModel $model
     * @return bool
     */
    public function restore(PlanoAcaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if (!$model->fl_coletivo) {
                throw new GeneralException('Não é possível manipular registros do plano de ação individual/formulário');
            }

            if ($model->restore()) {
                foreach ($model->plano_acao_filhos()->withTrashed()->get() as $k => $v) {
                    $v->restore();
                }
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Reabre um plano de ação coletivo que foi finalizado
     *
     * @param  PlanoAcaoModel $model
     * @return void
     */
    public function reopen(PlanoAcaoModel $model): PlanoAcaoModel
    {
        return DB::transaction(function () use ($model) {
            $model->status = PlanoAcaoStatusEnum::EmAndamento;
            $model->save();

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
