<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadoAbrangenciaEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dado_abrangencia_estados', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dado_id');
            $table->foreign('dado_id', 'dado_estado_foreign')->references('id')->on('dados')->onDelete('cascade');

            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id', 'dado_estado_id_foreign')->references('id')->on('estados')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['dado_id', 'estado_id'], 'dado_abrangencia_estados_u_e');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dado_abrangencia_estados');
    }
}
