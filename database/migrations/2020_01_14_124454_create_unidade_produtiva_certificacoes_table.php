<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaCertificacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_certificacoes', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_certificacao')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('certificacao_id');
            $table->foreign('certificacao_id', 'c_unid_prod_certificacao_id')->references('id')->on('certificacoes')->onDelete('cascade');

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'certificacao_id'], 'uniq_unid_prod_cert');

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
        Schema::dropIfExists('unidade_produtiva_certificacoes');
    }
}
