<?php

use App\Enums\CorEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respostas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pergunta_id');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('cascade');

            $table->text('descricao');

            $table->enum('cor', CorEnum::getValues())->nullable();

            $table->text('texto_apoio')->nullable();

            $table->integer('ordem')->nullable();

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
        Schema::dropIfExists('respostas');
    }
}
