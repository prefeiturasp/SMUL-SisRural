<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaCaracterizacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_caracterizacoes', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('area')->nullable();

            $table->string('quantidade')->nullable();

            $table->text('descricao')->nullable();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_car_id_foreign')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('solo_categoria_id');
            $table->foreign('solo_categoria_id', 'c_carac_cat_id_foreign')->references('id')->on('solo_categorias')->onDelete('cascade');

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
        Schema::dropIfExists('unidade_produtiva_caracterizacoes');
    }
}
