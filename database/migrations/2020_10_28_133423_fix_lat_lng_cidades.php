<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixLatLngCidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("update cidades set lat = '-0.9321899', lng = '-48.2797696' where id = 200;");
        DB::unprepared("update cidades set lat = '-3.846163', lng = '-32.412215' where id = 1500;");
        DB::unprepared("update cidades set lat = '-7.845984', lng = '-34.908060' where id = 1509;");
        DB::unprepared("update cidades set lat = '-8.592616', lng = '-35.117058' where id = 1600;");
        DB::unprepared("update cidades set lat = '-0.771132', lng = '-47.176508' where id = 5354;");

        DB::unprepared("update cidades set lat = '-22.887357', lng = '-42.026920' where id = 5548;");
        DB::unprepared("update cidades set lat = '-22.760842', lng = '-41.891206' where id = 5549;");
        DB::unprepared("update cidades set lat = '-15.793208', lng = '-47.890110' where id = 5569;");
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
