<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadoAbrangenciaRegioesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dado_abrangencia_regioes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dado_id');
            $table->foreign('dado_id')->references('id')->on('dados')->onDelete('cascade');

            $table->unsignedBigInteger('regiao_id');
            $table->foreign('regiao_id')->references('id')->on('regioes')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['dado_id', 'regiao_id'], 'dado_id_abrangencia_regiao_u_r');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dado_abrangencia_regioes');
    }
}
