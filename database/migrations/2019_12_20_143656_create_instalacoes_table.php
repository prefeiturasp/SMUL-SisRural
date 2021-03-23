<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstalacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instalacoes', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->text('descricao')->nullable();

            $table->string('quantidade')->nullable();

            $table->string('area')->nullable();

            $table->text('observacao')->nullable();

            $table->string('localizacao')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id')->references('id')->on('unidade_produtivas');

            $table->unsignedBigInteger('instalacao_tipo_id');
            $table->foreign('instalacao_tipo_id')->references('id')->on('instalacao_tipos');

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
        Schema::dropIfExists('instalacoes');
    }
}
