<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaoDeObraExternaToProdutoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->boolean('fl_contrata_mao_de_obra_externa')->nullable();
            $table->text('mao_de_obra_externa_tipo')->nullable();
            $table->text('mao_de_obra_externa_periodicidade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->dropColumn('fl_contrata_mao_de_obra_externa');
            $table->dropColumn('mao_de_obra_externa_tipo');
            $table->dropColumn('mao_de_obra_externa_periodicidade');
        });
    }
}
