<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeOperacionalRegioesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_operacional_regioes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidade_operacional_id');
            $table->foreign('unidade_operacional_id')->references('id')->on('unidade_operacionais')->onDelete('cascade');
            $table->unsignedBigInteger('regiao_id');
            $table->foreign('regiao_id')->references('id')->on('regioes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['unidade_operacional_id', 'regiao_id'], 'unid_op_abrangencia_regiao_u_r');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unidade_operacional_regioes');
    }
}
