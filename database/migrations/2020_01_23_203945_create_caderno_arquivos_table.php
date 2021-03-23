<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadernoArquivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caderno_arquivos', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('arquivo')->nullable();

            $table->string('app_arquivo')->nullable();
            $table->string('app_arquivo_caminho')->nullable();

            $table->text('descricao')->nullable();

            $table->enum('tipo', ['arquivo', 'imagem'])->nullable();

            $table->string('lat')->nullable();

            $table->string('lng')->nullable();

            $table->string('caderno_id');
            $table->foreign('caderno_id', 'c_caderno_id')->references('id')->on('cadernos')->onDelete('cascade');

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
        Schema::dropIfExists('caderno_arquivos');
    }
}
