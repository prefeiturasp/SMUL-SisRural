<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConcatenaPoligonosStoredProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP PROCEDURE IF EXISTS concatenaPoligonos;
        CREATE PROCEDURE `concatenaPoligonos`(IN states TEXT, IN cities TEXT, IN regions TEXT, OUT final_geo GEOMETRY)
        BEGIN
                            DECLARE position INT;
                            DECLARE geomInitial GEOMETRY;
                            DECLARE geomFinal GEOMETRY;
                            DECLARE geom GEOMETRY;
                            DECLARE notFound BOOLEAN DEFAULT FALSE;
                            
                            -- get states polygons
                            DECLARE curStates CURSOR FOR
                            SELECT id FROM states_tmp;
                        
                            -- get cities polygons
                            DECLARE curCities CURSOR FOR
                            SELECT id FROM cities_tmp;
                            
                            -- get regions polygons
                            DECLARE curRegions CURSOR FOR
                            SELECT id FROM regions_tmp;
                        
                            DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = TRUE; 
                            
                            DROP TABLE IF EXISTS states_tmp;
                            SET @select = concat('CREATE TEMPORARY TABLE states_tmp as SELECT * FROM estados WHERE id IN (', IF(states IS NULL OR states = '',0, states), ')');
                            PREPARE stm FROM @select;
                            EXECUTE stm;
                            DEALLOCATE PREPARE stm;
                        
                            DROP TABLE IF EXISTS cities_tmp;
                            SET @select = concat('CREATE TEMPORARY TABLE cities_tmp as SELECT * FROM cidades WHERE id IN (', IF(cities IS NULL OR cities = '',0, cities), ')');
                            PREPARE stm FROM @select;
                            EXECUTE stm;
                            DEALLOCATE PREPARE stm;
                            
                            DROP TABLE IF EXISTS regions_tmp;
                            SET @select = concat('CREATE TEMPORARY TABLE regions_tmp as SELECT * FROM regioes WHERE id IN (', IF(regions IS NULL OR regions = '',0, regions), ')');
                            PREPARE stm FROM @select;
                            EXECUTE stm;
                            DEALLOCATE PREPARE stm;
                     
                            OPEN curStates;
                                -- get the first area_id
                                FETCH curStates INTO position ;
                                -- put the shape into geomInitial for this area_id
                                SELECT poligono INTO geomInitial 
                                FROM estados  
                                WHERE id = position;
                            STATE: LOOP
                                IF notFound THEN
                                    LEAVE STATE;
                                END IF;
                        
                                -- get the second area_id
                                FETCH curStates INTO position;
                                -- put the second  shape into geomFinal for this area_id
                                SELECT poligono INTO geomFinal 
                                FROM estados  
                                WHERE id = position;
                        
                                -- performing a union operation between geomInitial and geomFinal
                                SET geomInitial = ST_UNION(geomInitial,geomFinal); -- enclosed st_union                 
                            END LOOP;
                            CLOSE curStates;
                        
                            SET geom = geomInitial;
                            SET geomInitial = null;
                            SET notFound = false;
                            SET position = 0;
                      
                            OPEN curCities;
                                -- get the first area_id
                                FETCH curCities INTO position ;
                                -- put the shape into geomInitial for this area_id
                                SELECT poligono INTO geomInitial 
                                FROM cidades
                                WHERE id = position;
                            CITY: LOOP
                                IF notFound THEN
                                    LEAVE CITY;
                                END IF;
                        
                                -- get the second area_id
                                FETCH curCities INTO position;
                                -- put the second  shape into geomFinal for this area_id
                                SELECT poligono INTO geomFinal 
                                FROM cidades
                                WHERE id = position;
                        
                                -- performing a union operation between geomInitial and geomFinal
                                SET geomInitial = ST_UNION(geomInitial,geomFinal); -- enclosed st_union                 
                            END LOOP;
                            CLOSE curCities;
                            
                            IF ST_IsEmpty(geomInitial) = 0 THEN
                                IF geom IS NOT NULL THEN
                                    SET geom = ST_UNION(geomInitial,geom);
                                ELSE
                                    SET geom = geomInitial;
                                END IF;
                            END IF;
                            
                            SET geomInitial = null;
                            SET notFound = false;
                            SET position = 0;
                           
                            OPEN curRegions;
                                -- get the first area_id
                                FETCH curRegions INTO position ;
                                -- put the shape into geomInitial for this area_id
                                SELECT poligono INTO geomInitial 
                                FROM regioes
                                WHERE id = position;
                            REGION: LOOP
                                IF notFound THEN
                                    LEAVE REGION;
                                END IF;
                        
                                -- get the second area_id
                                FETCH curRegions INTO position;
                                -- put the second  shape into geomFinal for this area_id
                                SELECT poligono INTO geomFinal 
                                FROM regioes
                                WHERE id = position;
                        
                                -- performing a union operation between geomInitial and geomFinal
                                SET geomInitial = ST_UNION(geomInitial,geomFinal); -- enclosed st_union                 
                            END LOOP;
                            CLOSE curRegions;
        
                            IF ST_IsEmpty(geomInitial) = 0 THEN
                                IF geom IS NOT NULL THEN
                                    SET geom = ST_UNION(geomInitial,geom);
                                ELSE
                                    SET geom = geomInitial;
                                END IF;
                            END IF;
        
                            SET final_geo = ST_GeomFromText(ST_ASTEXT(geom));
                        END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS concatenaPoligonos;');
    }
}
