<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQualidadeDisponibilidadeToUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->text('agua_qualidade')->nullable();
            $table->text('agua_disponibilidade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->dropColumn('agua_qualidade');
            $table->dropColumn('agua_disponibilidade');
        });
    }
}
