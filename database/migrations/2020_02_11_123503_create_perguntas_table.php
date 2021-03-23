<?php

use App\Enums\TipoPerguntaEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('tipo_pergunta', TipoPerguntaEnum::getValues())->default(TipoPerguntaEnum::Texto);

            $table->text('tabela_colunas')->nullable();

            $table->text('tabela_linhas')->nullable();

            $table->text('pergunta');

            $table->text('texto_apoio')->nullable();

            $table->boolean('fl_arquivada')->nullable()->default(false);

            $table->text('plano_acao_default')->nullable();

            $table->text('tags')->nullable();

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
        Schema::dropIfExists('perguntas');
    }
}
