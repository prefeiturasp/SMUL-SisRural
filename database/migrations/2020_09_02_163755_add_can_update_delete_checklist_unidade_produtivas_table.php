<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanUpdateDeleteChecklistUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->boolean('can_view')->nullable();
            $table->boolean('can_update')->nullable();
            $table->boolean('can_delete')->nullable();
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
            $table->dropColumn(['can_view', 'can_apply', 'can_delete']);
        });
    }
}
