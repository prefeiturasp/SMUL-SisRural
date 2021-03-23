<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsEstadosCidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Descomentar p/ o teste do GeoTestController
        // Schema::table('cidades', function (Blueprint $table) {
        //     $table->boolean('fl_inside_state')->default(false);
        //     $table->boolean('fl_inside_state_ok')->default(false);
        //     $table->boolean('fl_center_point')->default(false);
        //     $table->boolean('fl_center_point_ok')->default(false);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cidades', function (Blueprint $table) {
            $table->dropColumn(['fl_inside_state', 'fl_inside_state_ok', 'fl_center_point', 'fl_center_point_ok']);
        });
    }
}
