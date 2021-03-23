<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistDominiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_dominios', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');

            $table->unsignedBigInteger('dominio_id');
            $table->foreign('dominio_id')->references('id')->on('dominios')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['checklist_id', 'dominio_id']);

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_dominios');
    }
}
