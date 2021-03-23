<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistSnapshotRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_snapshot_respostas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('checklist_unidade_produtiva_id');
            $table->foreign('checklist_unidade_produtiva_id', 'check_s_r_c_u_p_id')->references('id')->on('checklist_unidade_produtivas')->onDelete('cascade');

            $table->unsignedBigInteger('pergunta_id');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('restrict');

            $table->unsignedBigInteger('resposta_id')->nullable();
            $table->foreign('resposta_id')->references('id')->on('respostas')->onDelete('restrict');

            $table->longText('resposta')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('checklist_snapshot_respostas');
    }
}
