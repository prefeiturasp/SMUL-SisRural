<?php

use Illuminate\Database\Seeder;

/**
 * Esse Seeder foi criado para resolver o problema de todos os registros inseridos do MVP1 terem a mesma data de cadastro e atualização
 */
class SyncMvp1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = ['estados', 'cidades', 'regioes', 'assistencia_tecnica_tipos', 'pressao_sociais', 'certificacoes', 'risco_contaminacao_aguas', 'tipo_posses', 'dedicacoes', 'generos', 'outorgas', 'canal_comercializacoes', 'tipo_fonte_aguas', 'solo_categorias', 'dominios', 'unidade_operacionais', 'unidade_operacional_unidade_produtiva', 'user_unidade_operacionais', 'templates', 'template_perguntas', 'template_respostas', 'template_pergunta_templates', 'relacoes', 'instalacao_tipos', 'unidade_operacional_regioes', 'termos_de_usos', 'renda_agriculturas', 'rendimento_comercializacoes', 'grau_instrucoes', 'esgotamento_sanitarios', 'residuo_solidos'];
        foreach ($list as $k => $v) {
            try {
                DB::unprepared("UPDATE " . $v . " SET updated_at = DATE_ADD(updated_at, interval (1 + id) second), created_at = DATE_ADD(created_at, interval (1 + id) second)");
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }

        $listUid = ['unidade_produtivas', 'produtores', 'produtor_unidade_produtiva', 'cadernos', 'caderno_resposta_caderno', 'unidade_produtiva_caracterizacoes', 'unidade_produtiva_canal_comercializacoes', 'unidade_produtiva_tipo_fonte_aguas', 'unidade_produtiva_risco_contaminacao_aguas', 'unidade_produtiva_solo_categorias', 'unidade_produtiva_certificacoes', 'unidade_produtiva_pressao_sociais', 'colaboradores', 'instalacoes', 'caderno_arquivos', 'unidade_produtiva_arquivos', 'unidade_produtiva_residuo_solidos', 'unidade_produtiva_esgotamento_sanitarios'];
        foreach ($listUid as $k => $v) {
            try {
                DB::unprepared("UPDATE " . $v . " SET updated_at = DATE_ADD(updated_at, interval (1 + uid) second), created_at = DATE_ADD(created_at, interval (1 + uid) second)");
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }
    }
}
