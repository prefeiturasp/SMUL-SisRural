<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeOperacionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_operacionais', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nome');

            $table->string('telefone')->nullable();

            $table->string('endereco')->nullable();

            $table->unsignedBigInteger('dominio_id');

            $table->foreign('dominio_id')->references('id')->on('dominios');

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
        Schema::dropIfExists('unidade_operacionais');
    }
}
