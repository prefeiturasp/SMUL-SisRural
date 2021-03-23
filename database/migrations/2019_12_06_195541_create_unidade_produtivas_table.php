<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtivas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('nome');
            $table->string('cep')->nullable();
            $table->text('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('subprefeitura')->nullable();
            // $table->string('municipio')->nullable();
            // $table->string('estado')->nullable();
            $table->string('car')->nullable();
            $table->string('ccir')->nullable();
            $table->string('itr')->nullable();
            $table->string('matricula')->nullable();
            $table->string('upa')->nullable();
            $table->text('gargalos')->nullable();

            $table->unsignedBigInteger('outorga_id')->nullable();
            $table->boolean('fl_risco_contaminacao')->nullable();
            $table->text('risco_contaminacao_observacoes')->nullable();
            $table->string('irrigacao')->nullable();
            $table->string('irrigacao_area_coberta')->nullable();
            $table->text('instalacao_maquinas_observacao')->nullable();
            $table->string('croqui_propriedade')->nullable();

            $table->boolean('fl_certificacoes')->nullable();
            $table->enum('fl_car', ['sim', 'nao', 'nao_se_aplica'])->nullable();
            $table->boolean('fl_ccir')->nullable();
            $table->boolean('fl_itr')->nullable();
            $table->boolean('fl_matricula')->nullable();
            $table->boolean('fl_comercializacao')->nullable();

            $table->text('outros_usos_descricao')->nullable();

            $table->enum('fl_producao_processa', ['sim', 'nao', 'nao_tem_interesse'])->nullable();
            $table->text('producao_processa_descricao')->nullable();

            $table->text('area_total_solo')->nullable();

            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->text('certificacoes_descricao')->nullable();
            $table->text('pressao_social_descricao')->nullable();
            $table->boolean('fl_pressao_social')->nullable();

            $table->unsignedBigInteger('estado_id')->nullable();
            $table->foreign('estado_id')->references('id')->on('estados');

            $table->unsignedBigInteger('cidade_id')->nullable();
            $table->foreign('cidade_id')->references('id')->on('cidades');

            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users');

            $table->boolean('fl_fora_da_abrangencia_app')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->text('socios')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unidade_produtivas');
    }
}
