<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAbrangenciaAtUnidadeOperacionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_operacionais', function (Blueprint $table) {
            $table->timestamp('abrangencia_at', 0)->nullable();
        });

        DB::table('unidade_operacionais')
            ->update([
                'abrangencia_at' => \DB::raw('unidade_operacionais.created_at'),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_operacionais', function (Blueprint $table) {
            $table->dropColumn(['abrangencia_at']);
        });
    }
}
