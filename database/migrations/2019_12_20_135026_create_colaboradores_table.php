<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('nome');
            $table->string('cpf')->nullable();
            $table->string('funcao')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->unsignedBigInteger('relacao_id')->nullable();
            $table->foreign('relacao_id')->references('id')->on('relacoes');

            $table->unsignedBigInteger('dedicacao_id')->nullable();
            $table->foreign('dedicacao_id')->references('id')->on('dedicacoes');

            //$table->unsignedBigInteger('genero_id');
            //$table->foreign('genero_id')->references('id')->on('generos');

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id')->references('id')->on('unidade_produtivas');

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
        Schema::dropIfExists('colaboradores');
    }
}
