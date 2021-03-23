<?php

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id', 'checklist_u_p_c_id')->references('id')->on('checklists')->onDelete('cascade');

            $table->string('unidade_produtiva_id');
            $table->foreign('unidade_produtiva_id', 'checklist_u_p_u_p_id')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->string('produtor_id');
            $table->foreign('produtor_id', 'checklist_u_p_p_id')->references('id')->on('produtores')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->enum("status", ChecklistStatusEnum::getValues())->default(ChecklistStatusEnum::Rascunho);

            $table->enum("status_flow", ChecklistStatusFlowEnum::getValues())->nullable()->default(null);

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
        Schema::dropIfExists('checklist_unidade_produtivas');
    }
}
