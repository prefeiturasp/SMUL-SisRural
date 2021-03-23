<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaRiscoContaminacaoAguasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_risco_contaminacao_aguas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_t_f_a_a')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('risco_contaminacao_agua_id');
            $table->foreign('risco_contaminacao_agua_id', 'c_unid_prod_r_c_a')->references('id')->on('risco_contaminacao_aguas')->onDelete('cascade');

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'risco_contaminacao_agua_id'], 'uniq_unid_prod_risc_cont');

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
        Schema::dropIfExists('unidade_produtiva_risco_contaminacao_aguas');
    }
}
