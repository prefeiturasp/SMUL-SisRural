<?php

use App\Enums\PlanoAcaoStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanoAcoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plano_acoes', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->string('nome');

            $table->string('checklist_unidade_produtiva_id')->nullable();
            $table->foreign('checklist_unidade_produtiva_id')->references('id')->on('checklist_unidade_produtivas')->onDelete('cascade');

            $table->string('unidade_produtiva_id')->nullable();
            $table->foreign('unidade_produtiva_id', 'c_unid_prod_plano_acoes')->references('id')->on('unidade_produtivas')->onDelete('cascade');

            $table->string('produtor_id')->nullable();
            $table->foreign('produtor_id', 'c_prod_plano_acoes')->references('id')->on('produtores')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->enum("status", PlanoAcaoStatusEnum::getValues())->default(PlanoAcaoStatusEnum::NaoIniciado);

            $table->date('prazo')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->boolean('fl_coletivo')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('plano_acoes', function (Blueprint $table) {
            $table->string('plano_acao_coletivo_id')->nullable();
            $table->foreign('plano_acao_coletivo_id', 'c_plano_acao_coletivo_id')->references('id')->on('plano_acoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plano_acoes');
    }
}
