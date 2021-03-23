<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeraPoligonoUfViaCidadesStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP PROCEDURE IF EXISTS geraPoligonoUFViaCidades;
        CREATE PROCEDURE `geraPoligonoUFViaCidades`()
            BEGIN
                DECLARE stateId INT;
                DECLARE notFound BOOLEAN DEFAULT FALSE;

                DECLARE curStates CURSOR FOR
                SELECT id FROM estados;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = TRUE; 
                DECLARE CONTINUE HANDLER FOR 3040 SELECT concat('Estado ID ', stateId, ' não foi gerado');
                SET SESSION group_concat_max_len = 1000000;    
                
                OPEN curStates;
                    FETCH curStates INTO stateId;
                STATE: LOOP
                    IF notFound THEN
                        LEAVE STATE;
                    END IF;    
                    SET @ids = (Select group_concat(id SEPARATOR ', ') FROM cidades WHERE estado_id = stateId);

                    call concatenaPoligonos('', @ids, '', @result);

                    UPDATE estados 
                    SET poligono = (select IF(ST_GeometryType(@result) = 'POLYGON', ST_GeomFromText(CONCAT(REPLACE(ST_ASTEXT(@result), 'POLYGON', 'MULTIPOLYGON('), ')')), ST_GeomFromText(ST_ASTEXT(@result)))) 
                    WHERE id = stateId;
                    
                    SELECT concat('Estado ID ', stateId, ' gerado OK');
                    FETCH curStates INTO stateId;
                END LOOP STATE;
            END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS geraPoligonoUFViaCidades;');
    }
}
