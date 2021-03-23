<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueKeysToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('produtores', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('produtor_unidade_produtiva', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('cadernos', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('caderno_resposta_caderno', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('colaboradores', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('instalacoes', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_caracterizacoes', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_canal_comercializacoes', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_tipo_fonte_aguas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_risco_contaminacao_aguas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_solo_categorias', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_certificacoes', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_pressao_sociais', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('caderno_arquivos', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_arquivos', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_respostas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('checklist_snapshot_respostas', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('plano_acao_itens', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('plano_acao_item_historicos', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('plano_acao_historicos', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('checklist_aprovacao_logs', function (Blueprint $table) {
            $table->unique('id');
        });
        Schema::table('unidade_produtiva_resposta_arquivos', function (Blueprint $table) {
            $table->unique('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('produtores', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('produtor_unidade_produtiva', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('cadernos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('caderno_resposta_caderno', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('colaboradores', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('instalacoes', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_caracterizacoes', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_canal_comercializacoes', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_tipo_fonte_aguas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_risco_contaminacao_aguas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_solo_categorias', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_certificacoes', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_pressao_sociais', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('caderno_arquivos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_arquivos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_respostas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('checklist_snapshot_respostas', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('plano_acao_itens', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('plano_acao_item_historicos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('plano_acao_historicos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('checklist_aprovacao_logs', function (Blueprint $table) {
            $table->dropUnique('id');
        });
        Schema::table('unidade_produtiva_resposta_arquivos', function (Blueprint $table) {
            $table->dropUnique('id');
        });
    }
}
