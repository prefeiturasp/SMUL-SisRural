<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadoUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dado_unidade_produtivas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('unidade_produtiva_id')->index();

            $table->unsignedBigInteger('dado_id')->index();

            $table->foreign('unidade_produtiva_id', 'dado_unid_prod_id_foreign')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->foreign('dado_id', 'dado_up_id_foreign')->references('id')->on('dados')->onDelete('cascade');

            $table->unique(['unidade_produtiva_id', 'dado_id'], 'uniq_dado_unid_ope');

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
        Schema::dropIfExists('dado_unidade_produtivas');
    }
}
