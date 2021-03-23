<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaRespostaArquivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_resposta_arquivos', function (Blueprint $table) {

            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('arquivo')->nullable();

            $table->string('app_arquivo')->nullable();
            $table->string('app_arquivo_caminho')->nullable();


            $table->string('unidade_produtiva_resposta_id');
            $table->foreign('unidade_produtiva_resposta_id', 'c_unidade_produtiva_resposta_id')->references('id')->on('unidade_produtiva_respostas')->onDelete('cascade');

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
        Schema::dropIfExists('unidade_produtiva_resposta_arquivos');
    }
}
