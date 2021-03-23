<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeOperacionalAbrangenciaCidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_operacional_abrangencia_cidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidade_operacional_id');
            $table->foreign('unidade_operacional_id', 'unidade_operacional_cidade_foreign')->references('id')->on('unidade_operacionais')->onDelete('cascade');
            $table->unsignedBigInteger('cidade_id');
            $table->foreign('cidade_id', 'cidade_foreign')->references('id')->on('cidades')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['unidade_operacional_id', 'cidade_id'], 'unid_oper_abrangencia_cidades_u_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unidade_operacional_abrangencia_cidades');
    }
}
