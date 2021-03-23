<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeProdutivaResiduoSolidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_produtiva_residuo_solidos', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_res_sol_u_p_i')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('residuo_solido_id');
            $table->foreign('residuo_solido_id', 'c_unid_prod_res_sol_id')->references('id')->on('residuo_solidos')->onDelete('cascade');

            $table->boolean('app_sync')->nullable();

            $table->unique(['unidade_produtiva_id', 'residuo_solido_id'], 'uniq_unid_prod_res_sol_id');

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
        Schema::dropIfExists('unidade_produtiva_residuo_solidos');
    }
}
