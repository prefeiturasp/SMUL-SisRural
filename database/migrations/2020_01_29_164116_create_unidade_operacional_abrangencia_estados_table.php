<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeOperacionalAbrangenciaEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_operacional_abrangencia_estados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidade_operacional_id');
            $table->foreign('unidade_operacional_id', 'unidade_operacional_estado_foreign')->references('id')->on('unidade_operacionais')->onDelete('cascade');
            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id', 'estado_foreign')->references('id')->on('estados')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['unidade_operacional_id', 'estado_id'], 'unid_oper_abrangencia_estados_u_e');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unidade_operacional_abrangencia_estados');
    }
}
