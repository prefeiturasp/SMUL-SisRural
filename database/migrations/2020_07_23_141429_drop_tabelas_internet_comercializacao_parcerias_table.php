<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTabelasInternetComercializacaoParceriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::dropIfExists('internet_tipos');
        } catch (Exception $e) {
        }

        try {
            Schema::dropIfExists('internet_operadoras');
        } catch (Exception $e) {
        }

        try {
            Schema::dropIfExists('tipo_comercializacoes');
        } catch (Exception $e) {
        }

        try {
            Schema::dropIfExists('tipo_parcerias');
        } catch (Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
