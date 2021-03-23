<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanUpdateCadernosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadernos', function (Blueprint $table) {
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
        Schema::table('cadernos', function (Blueprint $table) {
            $table->dropColumn(['can_view', 'can_update', 'can_delete']);
        });
    }
}
