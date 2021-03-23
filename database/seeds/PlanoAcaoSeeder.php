<?php

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\PlanoAcaoStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlanoAcaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = Carbon::now();

        $expiredAt = Carbon::now();
        $expiredAt->add(1, 'month');

        \App\Models\Core\ChecklistUnidadeProdutivaModel::insert([
            ['id' => 1, 'checklist_id' => 1, 'unidade_produtiva_id' => 1, 'produtor_id' => 1, 'user_id' => 5, 'status' => 'finalizado', 'status_flow' => null]
        ]);

        \App\Models\Core\ChecklistSnapshotRespostaModel::insert([
            ['id' => 1, 'checklist_unidade_produtiva_id' => 1, 'pergunta_id' => 1, 'resposta_id' => 1, 'resposta' => null],
            ['id' => 2, 'checklist_unidade_produtiva_id' => 1, 'pergunta_id' => 2, 'resposta_id' => 4, 'resposta' => null],
            ['id' => 3, 'checklist_unidade_produtiva_id' => 1, 'pergunta_id' => 3, 'resposta_id' => 8, 'resposta' => null],
            ['id' => 4, 'checklist_unidade_produtiva_id' => 1, 'pergunta_id' => 4, 'resposta_id' => 10, 'resposta' => null]
        ]);

        \App\Models\Core\PlanoAcaoModel::insert([
            ['id' => 1, 'nome' => 'Plano de Ação 1', "checklist_unidade_produtiva_id" => null, "unidade_produtiva_id" => 1, "produtor_id" => 1, "status" => PlanoAcaoStatusEnum::NaoIniciado, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Plano de Ação 2', "checklist_unidade_produtiva_id" => null, "unidade_produtiva_id" => 1, "produtor_id" => 1, "status" => PlanoAcaoStatusEnum::EmAndamento, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'Plano de Ação 3', "checklist_unidade_produtiva_id" => null, "unidade_produtiva_id" => 1, "produtor_id" => 1, "status" => PlanoAcaoStatusEnum::Concluido, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'Plano de Ação 4', "checklist_unidade_produtiva_id" => null, "unidade_produtiva_id" => 1, "produtor_id" => 1, "status" => PlanoAcaoStatusEnum::Cancelado, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 5, 'nome' => 'Plano de Ação com Checklist', "checklist_unidade_produtiva_id" => 1, "unidade_produtiva_id" => 1, "produtor_id" => 1, "status" => PlanoAcaoStatusEnum::NaoIniciado, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\PlanoAcaoItemModel::insert([
            ['id' => 1, 'plano_acao_id' => 1, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun",  "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'plano_acao_id' => 1, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun",  "status" => PlanoAcaoItemStatusEnum::EmAndamento, "prioridade" => PlanoAcaoPrioridadeEnum::AcaoRecomendada, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'plano_acao_id' => 1, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun",  "status" => PlanoAcaoItemStatusEnum::Concluido, "prioridade" => PlanoAcaoPrioridadeEnum::Atendida, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 4, 'plano_acao_id' => 2, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'plano_acao_id' => 3, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'plano_acao_id' => 4, "checklist_snapshot_resposta_id" => null, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 7, 'plano_acao_id' => 5, "checklist_snapshot_resposta_id" => 1, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'plano_acao_id' => 5, "checklist_snapshot_resposta_id" => 2, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 9, 'plano_acao_id' => 5, "checklist_snapshot_resposta_id" => 3, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 10, 'plano_acao_id' => 5, "checklist_snapshot_resposta_id" => 4, "descricao" => "Lorem Ipsun", "status" => PlanoAcaoItemStatusEnum::NaoIniciado, "prioridade" => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, "prazo" => $expiredAt, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\PlanoAcaoHistoricoModel::insert([
            ['id' => 1, 'plano_acao_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 1'],
            ['id' => 2, 'plano_acao_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 2'],
            ['id' => 3, 'plano_acao_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 3'],

            ['id' => 4, 'plano_acao_id' => 2, 'user_id' => 5, 'texto' => 'Lorem Ispun 4']
        ]);

        \App\Models\Core\PlanoAcaoItemHistoricoModel::insert([
            ['id' => 1, 'plano_acao_item_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 1'],
            ['id' => 2, 'plano_acao_item_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 2'],
            ['id' => 3, 'plano_acao_item_id' => 1, 'user_id' => 5, 'texto' => 'Lorem Ispun 3'],

            ['id' => 4, 'plano_acao_item_id' => 2, 'user_id' => 5, 'texto' => 'Lorem Ispun 4']
        ]);
    }
}
