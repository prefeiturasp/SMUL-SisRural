<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_respostas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('template_pergunta_id');
            $table->foreign('template_pergunta_id')->references('id')->on('template_perguntas');

            $table->text('descricao');

            $table->integer('ordem');

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
        Schema::dropIfExists('template_respostas');
    }
}
