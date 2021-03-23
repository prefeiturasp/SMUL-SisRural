<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeOperacionalUnidadeProdutivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_operacional_unidade_produtiva', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('unidade_produtiva_id')->index();

            $table->unsignedBigInteger('unidade_operacional_id');

            $table->foreign('unidade_produtiva_id', 'unid_prod_id_foreign')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->foreign('unidade_operacional_id', 'unid_op_id_foreign')->references('id')->on('unidade_operacionais')->onDelete('cascade');

            $table->unique(['unidade_produtiva_id', 'unidade_operacional_id'], 'uniq_unid_prod_unid_ope');

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
        Schema::dropIfExists('unidade_operacional_unidade_produtiva');
    }
}
