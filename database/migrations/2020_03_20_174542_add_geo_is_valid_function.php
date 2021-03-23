<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeoIsValidFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP FUNCTION IF EXISTS geoIsValid;
            CREATE FUNCTION `geoIsValid`(geoText longtext) RETURNS int(11)
                BEGIN
                    SET @result = 0;
                    SELECT ST_IsValid(ST_GeomFromText(geoText)) INTO @result;
                    RETURN @result;
                END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS geoIsValid;");
    }
}