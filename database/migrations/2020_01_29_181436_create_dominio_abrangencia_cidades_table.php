<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDominioAbrangenciaCidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dominio_abrangencia_cidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dominio_id');
            $table->foreign('dominio_id')->references('id')->on('dominios')->onDelete('cascade');
            $table->unsignedBigInteger('cidade_id');
            $table->foreign('cidade_id')->references('id')->on('cidades')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['dominio_id', 'cidade_id'], 'dominio_abrangencia_cidades_d_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dominio_abrangencia_cidades');
    }
}
