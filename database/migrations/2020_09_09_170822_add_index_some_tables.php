<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private function addIndex($tableName, $columnName)
    {
        try {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnName) {
                $table->index($columnName, $tableName . '_' . $columnName);
            });
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function up()
    {
        $this->addIndex('canal_comercializacoes', 'id');
        $this->addIndex('certificacoes', 'id');
        $this->addIndex('checklists', 'id');
        $this->addIndex('checklist_categorias', 'id');
        $this->addIndex('checklist_dominios', 'checklist_id');
        $this->addIndex('checklist_users', 'checklist_id');
        $this->addIndex('checklist_unidade_operacionais', 'checklist_id');
        $this->addIndex('checklist_aprovacao_users', 'checklist_id');
        $this->addIndex('checklist_pergunta_respostas', 'id');
        $this->addIndex('checklist_perguntas', 'checklist_categoria_id');
        $this->addIndex('cidades', 'id');
        $this->addIndex('dedicacoes', 'id');
        $this->addIndex('dominios', 'id');
        $this->addIndex('dominio_abrangencia_cidades', 'dominio_id');
        $this->addIndex('dominio_abrangencia_estados', 'dominio_id');
        $this->addIndex('dominio_abrangencia_regioes', 'dominio_id');
        $this->addIndex('estados', 'uf');
        $this->addIndex('estados', 'id');
        $this->addIndex('etinias', 'id');
        $this->addIndex('generos', 'id');
        $this->addIndex('instalacao_tipos', 'id');
        $this->addIndex('outorgas', 'id');
        $this->addIndex('perguntas', 'id');
        $this->addIndex('pressao_sociais', 'id');
        $this->addIndex('produtor_unidade_produtiva', 'unidade_produtiva_id');
        $this->addIndex('regioes', 'id');
        $this->addIndex('relacoes', 'id');
        $this->addIndex('respostas', 'id');
        $this->addIndex('risco_contaminacao_aguas', 'id');
        $this->addIndex('solo_categorias', 'id');
        $this->addIndex('template_pergunta_templates', 'template_pergunta_id');
        $this->addIndex('template_perguntas', 'id');
        $this->addIndex('template_respostas', 'id');
        $this->addIndex('templates', 'id');
        $this->addIndex('termos_de_usos', 'id');
        $this->addIndex('tipo_fonte_aguas', 'id');
        $this->addIndex('tipo_posses', 'id');
        $this->addIndex('unidade_operacionais', 'id');
        $this->addIndex('unidade_operacional_abrangencia_cidades', 'unidade_operacional_id');
        $this->addIndex('unidade_operacional_abrangencia_estados', 'unidade_operacional_id');
        $this->addIndex('unidade_operacional_regioes', 'unidade_operacional_id');
        $this->addIndex('users', 'id');
        $this->addIndex('user_dominios', 'user_id');
        $this->addIndex('user_unidade_operacionais', 'user_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
