<?php

use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRendaInstrucaoToProdutoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->enum('status', ProdutorUnidadeProdutivaStatusEnum::getValues())->default(ProdutorUnidadeProdutivaStatusEnum::Ativo);
            $table->text('status_observacao')->nullable();

            $table->unsignedBigInteger('renda_agricultura_id')->nullable();
            $table->foreign('renda_agricultura_id')->references('id')->on('renda_agriculturas');

            $table->unsignedBigInteger('rendimento_comercializacao_id')->nullable();
            $table->foreign('rendimento_comercializacao_id')->references('id')->on('rendimento_comercializacoes');

            $table->text('outras_fontes_renda')->nullable();

            $table->unsignedBigInteger('grau_instrucao_id')->nullable();
            $table->foreign('grau_instrucao_id')->references('id')->on('grau_instrucoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->dropForeign('produtores_renda_agricultura_id_foreign');
            $table->dropForeign('produtores_rendimento_comercializacao_id_foreign');
            $table->dropForeign('produtores_grau_instrucao_id_foreign');

            $table->dropColumn(['status', 'status_observacao', 'renda_agricultura_id', 'rendimento_comercializacao_id', 'outras_fontes_renda', 'grau_instrucao_id']);
        });
    }
}
