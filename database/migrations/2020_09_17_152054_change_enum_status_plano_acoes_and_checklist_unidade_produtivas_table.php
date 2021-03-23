<?php

use App\Enums\ChecklistStatusEnum;
use App\Enums\PlanoAcaoStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEnumStatusPlanoAcoesAndChecklistUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->enum("status", PlanoAcaoStatusEnum::getValues())->default(PlanoAcaoStatusEnum::NaoIniciado)->change();
        });

        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->enum("status", ChecklistStatusEnum::getValues())->default(ChecklistStatusEnum::Rascunho)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
