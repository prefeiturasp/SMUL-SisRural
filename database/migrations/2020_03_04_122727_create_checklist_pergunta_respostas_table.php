<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistPerguntaRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_pergunta_respostas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('checklist_pergunta_id');
            $table->foreign('checklist_pergunta_id')->references('id')->on('checklist_perguntas')->onDelete('cascade');

            $table->unsignedBigInteger('resposta_id');
            $table->foreign('resposta_id')->references('id')->on('respostas')->onDelete('restrict');

            $table->float('peso')->nullable();

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
        Schema::dropIfExists('checklist_pergunta_respostas');
    }
}
