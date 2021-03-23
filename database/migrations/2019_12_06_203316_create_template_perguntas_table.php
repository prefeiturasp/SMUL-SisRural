<?php

use App\Enums\TipoTemplatePerguntaEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatePerguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('pergunta');

            $table->text('tags')->nullable();

            $table->enum('tipo', TipoTemplatePerguntaEnum::getValues());

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
        Schema::dropIfExists('template_perguntas');
    }
}
