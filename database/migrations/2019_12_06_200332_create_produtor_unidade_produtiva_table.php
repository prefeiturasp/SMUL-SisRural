<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutorUnidadeProdutivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtor_unidade_produtiva', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->string('produtor_id');
            $table->foreign('produtor_id')->references('id')->on('produtores')->onDelete('cascade');

            $table->unsignedBigInteger('tipo_posse_id');
            $table->foreign('tipo_posse_id')->references('id')->on('tipo_posses')->onDelete('cascade');

            $table->boolean('contato')->nullable()->default(false);

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'produtor_id'],'uniq_unid_prod_prod');

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
        Schema::dropIfExists('produtor_unidade_produtiva');
    }
}
