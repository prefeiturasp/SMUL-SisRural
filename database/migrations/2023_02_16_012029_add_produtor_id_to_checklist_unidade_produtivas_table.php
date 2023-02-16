<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProdutorIdToChecklistUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
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
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->dropForeign(['produtor_id']);
            $table->dropColumn('produtor_id');
            $table->string('unidade_produtiva_id', 255)->change();
        });
    }
}
