<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoloCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solo_categorias', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nome');

            $table->enum('tipo', ['geral', 'outros']);

            $table->enum('tipo_form', ['todos', 'hectares'])->nullable();

            // $table->boolean('app_sync')->nullable();

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
        Schema::dropIfExists('solo_categorias');
    }
}
