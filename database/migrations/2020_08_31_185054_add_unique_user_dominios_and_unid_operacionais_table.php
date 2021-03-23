<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueUserDominiosAndUnidOperacionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_dominios', function (Blueprint $table) {
            $table->unique(['user_id', 'dominio_id'], 'user_dominio_index_unique');
        });

        Schema::table('user_unidade_operacionais', function (Blueprint $table) {
            $table->unique(['user_id', 'unidade_operacional_id'], 'user_unidade_operacionais_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_dominios', function (Blueprint $table) {
            // $sm = Schema::getConnection()->getDoctrineSchemaManager();
            // $doctrineTable = $sm->listTableDetails('user_dominios');

            // if ($doctrineTable->hasIndex('user_id'))
            //     $table->dropIndex('user_id');

            // if ($doctrineTable->hasIndex('dominio_id'))
            //     $table->dropIndex('dominio_id');

            $table->dropIndex('user_dominio_index_unique');
        });

        Schema::table('user_unidade_operacionais', function (Blueprint $table) {
            $table->dropIndex('user_unidade_operacionais_index_unique');
        });
    }
}
