<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDominioAbrangenciaRegioesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dominio_abrangencia_regioes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dominio_id');
            $table->foreign('dominio_id', 'c_d_ab_reg_dominio_id')->references('id')->on('dominios')->onDelete('cascade');
            $table->unsignedBigInteger('regiao_id');
            $table->foreign('regiao_id', 'c_d_ab_reg_regiao_id')->references('id')->on('regioes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['dominio_id', 'regiao_id'], 'dominio_abrangencia_regiao_d_r');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dominio_abrangencia_regioes');
    }
}
