<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistUnidadeProdutivaArquivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_unidade_produtiva_arquivos', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('nome')->nullable();

            $table->string('arquivo')->nullable();

            $table->string('app_arquivo')->nullable();
            $table->string('app_arquivo_caminho')->nullable();

            $table->text('descricao')->nullable();
            $table->enum('tipo', ['arquivo', 'imagem'])->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();

            $table->string('checklist_unidade_produtiva_id');
            $table->foreign('checklist_unidade_produtiva_id', 'c_checklist_unidade_produtiva_id')->references('id')->on('checklist_unidade_produtivas')->onDelete('cascade');

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
        Schema::dropIfExists('checklist_unidade_produtiva_arquivos');
    }
}
