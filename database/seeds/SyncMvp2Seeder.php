<?php

use Illuminate\Database\Seeder;

/**
 * Esse Seeder foi criado para resolver o problema de todos os registros inseridos do MVP2 terem a mesma data de cadastro e atualização
 */
class SyncMvp2Seeder extends Seeder
{
    public function run()
    {
        $list = ['perguntas', 'respostas', 'checklists', 'checklist_categorias', 'checklist_perguntas', 'checklist_dominios', 'checklist_unidade_operacionais', 'checklist_users', 'checklist_pergunta_respostas'];

        foreach ($list as $k => $v) {
            try {
                DB::unprepared("UPDATE " . $v . " SET updated_at = DATE_ADD(updated_at, interval (1 + id) second), created_at = DATE_ADD(created_at, interval (1 + id) second)");
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }

        $listUid = ['checklist_unidade_produtivas', 'unidade_produtiva_respostas', 'checklist_snapshot_respostas'];
        foreach ($listUid as $k => $v) {
            try {
                DB::unprepared("UPDATE " . $v . " SET updated_at = DATE_ADD(updated_at, interval (1 + uid) second), created_at = DATE_ADD(created_at, interval (1 + uid) second)");
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }
    }
}
