<?php

use Illuminate\Database\Migrations\Migration;

class CreateFunctionRemoveAccents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::unprepared("
            DROP FUNCTION removeAccents;
        ");
        } catch (\Exception $e) {
        }

        DB::unprepared("
                CREATE  FUNCTION `removeAccents`(v TEXT) RETURNS text CHARSET utf8
                    DETERMINISTIC
                BEGIN
                    DECLARE TextString TEXT ;
                SET TextString = v ;

                SET TextString = REPLACE(TextString, ':',' ') ;
                SET TextString = REPLACE(TextString, ';',' ') ;
                SET TextString = REPLACE(TextString, '.',' ') ;
                SET TextString = REPLACE(TextString, ',',' ') ;
                SET TextString = REPLACE(TextString, '?',' ') ;
                SET TextString = REPLACE(TextString, '¿',' ') ;
                SET TextString = REPLACE(TextString, '/',' ') ;
                SET TextString = REPLACE(TextString, '-',' ') ;
                SET TextString = REPLACE(TextString, '!',' ') ;
                SET TextString = REPLACE(TextString, 'á','a') ;
                SET TextString = REPLACE(TextString, 'é','e') ;
                SET TextString = REPLACE(TextString, 'í','i') ;
                SET TextString = REPLACE(TextString, 'ó','o') ;
                SET TextString = REPLACE(TextString, 'ú','u') ;
                SET TextString = REPLACE(TextString, 'à','a') ;
                SET TextString = REPLACE(TextString, 'è','e') ;
                SET TextString = REPLACE(TextString, 'ì','i') ;
                SET TextString = REPLACE(TextString, 'ò','o') ;
                SET TextString = REPLACE(TextString, 'ù','u') ;
                SET TextString = REPLACE(TextString, 'ã','a') ;
                SET TextString = REPLACE(TextString, 'õ','o') ;
                SET TextString = REPLACE(TextString, 'â','a') ;
                SET TextString = REPLACE(TextString, 'ê','e') ;
                SET TextString = REPLACE(TextString, 'î','i') ;
                SET TextString = REPLACE(TextString, 'ô','o') ;
                SET TextString = REPLACE(TextString, 'ô','o') ;
                SET TextString = REPLACE(TextString, 'ä','a') ;
                SET TextString = REPLACE(TextString, 'ë','e') ;
                SET TextString = REPLACE(TextString, 'ï','i') ;
                SET TextString = REPLACE(TextString, 'ö','o') ;
                SET TextString = REPLACE(TextString, 'ü','u') ;
                SET TextString = REPLACE(TextString, 'ç','c') ;
                SET TextString = REPLACE(TextString, 'Á','A') ;
                SET TextString = REPLACE(TextString, 'É','E') ;
                SET TextString = REPLACE(TextString, 'Í','I') ;
                SET TextString = REPLACE(TextString, 'Ó','O') ;
                SET TextString = REPLACE(TextString, 'Ú','U') ;
                SET TextString = REPLACE(TextString, 'À','A') ;
                SET TextString = REPLACE(TextString, 'È','E') ;
                SET TextString = REPLACE(TextString, 'Ì','I') ;
                SET TextString = REPLACE(TextString, 'Ò','O') ;
                SET TextString = REPLACE(TextString, 'Ù','U') ;
                SET TextString = REPLACE(TextString, 'Ã','A') ;
                SET TextString = REPLACE(TextString, 'Õ','O') ;
                SET TextString = REPLACE(TextString, 'Â','A') ;
                SET TextString = REPLACE(TextString, 'Ê','E') ;
                SET TextString = REPLACE(TextString, 'Î','I') ;
                SET TextString = REPLACE(TextString, 'Ô','O') ;
                SET TextString = REPLACE(TextString, 'Û','O') ;
                SET TextString = REPLACE(TextString, 'Ä','A') ;
                SET TextString = REPLACE(TextString, 'Ë','E') ;
                SET TextString = REPLACE(TextString, 'Ï','I') ;
                SET TextString = REPLACE(TextString, 'Ö','O') ;
                SET TextString = REPLACE(TextString, 'Ü','U') ;
                SET TextString = REPLACE(TextString, 'Ç','C') ;
                SET TextString = REPLACE(TextString, '\"',' ') ;
                SET TextString = REPLACE(TextString, '\'',' ') ;

                RETURN TextString ;

                    END
                ;;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
