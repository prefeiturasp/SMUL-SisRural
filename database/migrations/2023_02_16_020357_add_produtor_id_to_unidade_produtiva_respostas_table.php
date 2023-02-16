<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProdutorIdToUnidadeProdutivaRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_produtiva_respostas', function (Blueprint $table) {
            $table->string('produtor_id', 255)->nullable();
            $table->foreign('produtor_id')->references('id')
            ->on('produtores')->onDelete('set null');            
            $table->string('unidade_produtiva_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_produtiva_respostas', function (Blueprint $table) {
            $table->dropForeign(['produtor_id']);
            $table->dropColumn('produtor_id');
            $table->string('unidade_produtiva_id', 255)->change();
        });
    }
}
