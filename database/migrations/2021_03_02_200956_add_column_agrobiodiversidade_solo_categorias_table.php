<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAgrobiodiversidadeSoloCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solo_categorias', function (Blueprint $table) {
            $table->float('min')->nullable();
            $table->float('max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solo_categorias', function (Blueprint $table) {
            $table->dropColumn(['min', 'max']);
        });
    }
}
