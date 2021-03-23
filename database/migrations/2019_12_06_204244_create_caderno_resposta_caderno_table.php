<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadernoRespostaCadernoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caderno_resposta_caderno', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('caderno_id');
            $table->foreign('caderno_id')->references('id')->on('cadernos');

            $table->unsignedBigInteger('template_pergunta_id');
            $table->foreign('template_pergunta_id')->references('id')->on('template_perguntas');

            $table->unsignedBigInteger('template_resposta_id')->nullable();
            $table->foreign('template_resposta_id')->references('id')->on('template_respostas');

            $table->text('resposta')->nullable();

            // Agora aceita multiplas respostas p/ a mesma pergunta
            // $table->unique(['caderno_id', 'template_pergunta_id'], 'uniq_cad_temp_perg');

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
        Schema::dropIfExists('caderno_resposta_caderno');
    }
}
