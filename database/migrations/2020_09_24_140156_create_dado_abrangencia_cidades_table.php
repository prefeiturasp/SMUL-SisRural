<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadoAbrangenciaCidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dado_abrangencia_cidades', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dado_id');
            $table->foreign('dado_id', 'dado_cidade_foreign')->references('id')->on('dados')->onDelete('cascade');

            $table->unsignedBigInteger('cidade_id');
            $table->foreign('cidade_id', 'dado_cidade_id_foreign')->references('id')->on('cidades')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['dado_id', 'cidade_id'], 'dado_abrangencia_cidades_u_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dado_abrangencia_cidades');
    }
}
