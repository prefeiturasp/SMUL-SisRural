<?php

use App\Enums\PlanoAcaoClassificacaoEnum;
use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanoAcaoItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plano_acao_itens', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('plano_acao_id');
            $table->foreign('plano_acao_id')->references('id')->on('plano_acoes')->onDelete('cascade');

            $table->string('checklist_snapshot_resposta_id')->nullable();
            $table->foreign('checklist_snapshot_resposta_id')->references('id')->on('checklist_snapshot_respostas')->onDelete('cascade');

            $table->unsignedBigInteger('checklist_pergunta_id')->nullable();
            $table->foreign('checklist_pergunta_id')->references('id')->on('checklist_perguntas')->onDelete('cascade');

            $table->text('descricao')->nullable();

            $table->enum("status", PlanoAcaoItemStatusEnum::getValues())->default(PlanoAcaoItemStatusEnum::NaoIniciado);

            $table->enum('prioridade', PlanoAcaoPrioridadeEnum::getValues())->default(PlanoAcaoPrioridadeEnum::PriorizacaoTecnica);

            $table->date('prazo')->nullable();

            $table->timestamp('finished_at')->nullable();

            $table->text('ultima_observacao')->nullable();
            $table->timestamp('ultima_observacao_data')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->boolean('fl_coletivo')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('plano_acao_itens', function (Blueprint $table) {
            $table->string('plano_acao_item_coletivo_id')->nullable();
            $table->foreign('plano_acao_item_coletivo_id', 'c_pl_ac_it_col_id')->references('id')->on('plano_acao_itens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plano_acao_itens');
    }
}
