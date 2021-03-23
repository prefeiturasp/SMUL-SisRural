<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDominioAbrangenciaEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dominio_abrangencia_estados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dominio_id');
            $table->foreign('dominio_id', 'c_d_ab_est_dominio_id')->references('id')->on('dominios')->onDelete('cascade');
            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id', 'c_d_ab_est_estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['dominio_id', 'estado_id'], 'dominio_abrangencia_estados_d_e');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dominio_abrangencia_estados');
    }
}
