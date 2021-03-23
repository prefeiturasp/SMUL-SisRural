<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddManualToUnidadeOperacionalUnidadeProdutivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_operacional_unidade_produtiva', function (Blueprint $table) {
            $table->boolean('add_manual')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_operacional_unidade_produtiva', function (Blueprint $table) {
            $table->dropColumn(['add_manual']);
        });
    }
}
