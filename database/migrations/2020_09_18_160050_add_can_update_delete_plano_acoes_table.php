<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanUpdateDeletePlanoAcoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->boolean('can_view')->nullable();
            $table->boolean('can_update')->nullable();
            $table->boolean('can_delete')->nullable();
            $table->boolean('can_reopen')->nullable();
            $table->boolean('can_history')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->dropColumn(['can_view', 'can_apply', 'can_delete', 'can_reopen', 'can_history']);
        });
    }
}
