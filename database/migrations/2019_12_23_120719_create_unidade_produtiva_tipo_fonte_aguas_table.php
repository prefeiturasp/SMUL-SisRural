<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaTipoFonteAguasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_tipo_fonte_aguas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_t_f_a')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('tipo_fonte_agua_id');
            $table->foreign('tipo_fonte_agua_id', 'c_unid_prod_t_f_a_cc')->references('id')->on('tipo_fonte_aguas')->onDelete('cascade');

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'tipo_fonte_agua_id'], 'uniq_unid_prod_tip_agua');

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
        Schema::dropIfExists('unidade_produtiva_tipo_fonte_aguas');
    }
}
