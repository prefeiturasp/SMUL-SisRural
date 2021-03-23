<?php

use App\Enums\PlanoAcaoEnum;
use App\Enums\TemplateChecklistStatusEnum;
use App\Enums\TipoPontuacaoEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dominio_id');
            $table->foreign('dominio_id')->references('id')->on('dominios')->onDelete('cascade');

            $table->unsignedBigInteger('copia_checklist_id')->nullable();

            $table->integer('versao')->default(0);

            $table->string('nome');

            $table->text('instrucoes')->nullable();

            $table->boolean('fl_fluxo_aprovacao')->default(false);

            $table->enum('status', TemplateChecklistStatusEnum::getValues())->default(TemplateChecklistStatusEnum::Rascunho);

            $table->enum('plano_acao', PlanoAcaoEnum::getValues())->default(PlanoAcaoEnum::NaoCriar);

            $table->string('formula')->nullable();

            $table->enum('tipo_pontuacao', TipoPontuacaoEnum::getValues())->default(TipoPontuacaoEnum::ComPontuacao);

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
        Schema::dropIfExists('checklists');
    }
}
