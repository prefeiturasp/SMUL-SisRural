<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistUnidadeOperacionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_unidade_operacionais', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');

            $table->unsignedBigInteger('unidade_operacional_id');
            $table->foreign('unidade_operacional_id')->references('id')->on('unidade_operacionais')->onDelete('cascade');

            $table->timestamps();

            $table->unique(['checklist_id', 'unidade_operacional_id'], 'uniq_checklist_unid_op_checklist_id_unid_op_id');

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
        Schema::dropIfExists('checklist_unidade_operacionais');
    }
}
