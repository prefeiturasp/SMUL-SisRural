<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaEsgotamentoSanitariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_esgotamento_sanitarios', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_esg_san_u_p_i')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('esgotamento_sanitario_id');
            $table->foreign('esgotamento_sanitario_id', 'c_unid_prod_esg_san_id')->references('id')->on('esgotamento_sanitarios')->onDelete('cascade');

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'esgotamento_sanitario_id'], 'uniq_unid_prod_esg_sani_id');

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
        Schema::dropIfExists('unidade_produtiva_esgotamento_sanitarios');
    }
}
