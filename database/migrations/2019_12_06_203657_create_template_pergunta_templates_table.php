<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatePerguntaTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_pergunta_templates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('template_pergunta_id');
            $table->foreign('template_pergunta_id')->references('id')->on('template_perguntas');

            $table->unsignedBigInteger('template_id');
            $table->foreign('template_id')->references('id')->on('templates');

            $table->integer('ordem');

            $table->unique(['template_pergunta_id', 'template_id'], 'uniq_temp_perg_temp');
 
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
        Schema::dropIfExists('template_pergunta_templates');
    }
}
