<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_respostas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->unsignedBigInteger('pergunta_id');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('restrict');

            $table->unsignedBigInteger('resposta_id')->nullable();
            $table->foreign('resposta_id')->references('id')->on('respostas')->onDelete('restrict');

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->longText('resposta')->nullable();

            $table->boolean('app_sync')->nullable();

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
        Schema::dropIfExists('unidade_produtiva_respostas');
    }
}
