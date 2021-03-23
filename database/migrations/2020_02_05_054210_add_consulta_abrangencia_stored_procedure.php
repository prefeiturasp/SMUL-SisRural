<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsultaAbrangenciaStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP PROCEDURE IF EXISTS consultaAbragencia;
        CREATE PROCEDURE `consultaAbrangencia`(IN statesA TEXT, IN citiesA TEXT, IN regionsA TEXT, IN statesB TEXT, IN citiesB TEXT, 
									            IN regionsB TEXT, OUT result TEXT)
            BEGIN
                CALL concatenaPoligonos(statesA, citiesA, regionsA, @polygonA);
                CALL concatenaPoligonos(statesB, citiesB, regionsB, @polygonB);
                -- SELECT MBRCoveredBy(@polygonA, @polygonB) INTO result;
                SELECT ST_Within(@polygonA, @polygonB) INTO result;
            END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS consultaAbragencia;');
    }
}
