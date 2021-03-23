<?php

use App\Enums\PlanoAcaoPrioridadeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistPerguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('checklist_categoria_id');
            $table->foreign('checklist_categoria_id')->references('id')->on('checklist_categorias')->onDelete('cascade');

            $table->unsignedBigInteger('pergunta_id');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('cascade');

            $table->float('peso_pergunta')->nullable();

            $table->boolean('fl_obrigatorio')->nullable();
            $table->boolean('fl_plano_acao')->nullable();

            $table->enum('plano_acao_prioridade', PlanoAcaoPrioridadeEnum::getValues())->nullable();

            $table->integer('ordem')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->unique(['checklist_categoria_id', 'pergunta_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_perguntas');
    }
}
