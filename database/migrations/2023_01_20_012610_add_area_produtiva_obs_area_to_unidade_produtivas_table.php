<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaProdutivaObsAreaToUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->decimal('area_produtiva', 8, 2)->nullable();
            $table->text('observacoes_sobre_area')->nullable();
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
            $table->dropColumn('area_produtiva');
            $table->dropColumn('observacoes_sobre_area');
        });
    }
}
