<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etinias', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('nome');

            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('produtores', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            // $table->enum('tipo', ['individual', 'familiar'])->nullable();
            $table->string('nome');

            $table->boolean('fl_agricultor_familiar')->nullable();
            $table->boolean('fl_agricultor_familiar_dap')->nullable();
            $table->string('agricultor_familiar_numero')->nullable();
            $table->string('agricultor_familiar_data')->nullable();
            $table->boolean('fl_assistencia_tecnica')->nullable();
            $table->unsignedBigInteger('assistencia_tecnica_tipo_id')->nullable();
            $table->string('assistencia_tecnica_periodo')->nullable();
            $table->boolean('fl_internet')->nullable();
            // $table->unsignedBigInteger('internet_tipo_id')->nullable();
            // $table->unsignedBigInteger('internet_operadora_id')->nullable();
            //$table->unsignedBigInteger('tipo_parceria_id')->nullable();
            $table->text('tipo_parcerias_obs')->nullable();
            $table->unsignedBigInteger('genero_id')->nullable();
            $table->string('nome_social')->nullable();
            $table->unsignedBigInteger('etinia_id')->nullable();
            $table->boolean('fl_portador_deficiencia')->nullable();
            $table->text('portador_deficiencia_obs')->nullable();
            $table->string('data_nascimento')->nullable();
            $table->string('rg')->nullable();
            //$table->enum('tipo_pessoa_fisica', ['fisica', 'juridica'])->nullable();
            $table->string('cpf')->nullable()->unique();
            $table->string('cnpj')->nullable();
            $table->string('nota_fiscal_produtor')->nullable();
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('subprefeitura')->nullable();
            // $table->string('municipio')->nullable();
            // $table->string('estado')->nullable();
            $table->string('telefone_1')->nullable();
            $table->string('telefone_2')->nullable();
            $table->string('email')->nullable();
            $table->boolean('fl_comunidade_tradicional')->nullable();
            $table->string('comunidade_tradicional_obs')->nullable();

            $table->boolean('fl_cnpj')->nullable();
            $table->boolean('fl_nota_fiscal_produtor')->nullable();
            $table->boolean('fl_tipo_parceria')->nullable();
            $table->boolean('fl_reside_unidade_produtiva')->nullable();

            $table->boolean('app_sync')->nullable();

            $table->unsignedBigInteger('estado_id')->nullable();
            $table->foreign('estado_id')->references('id')->on('estados');

            $table->unsignedBigInteger('cidade_id')->nullable();
            $table->foreign('cidade_id')->references('id')->on('cidades');

            $table->foreign('etinia_id')->references('id')->on('etinias');
            $table->foreign('assistencia_tecnica_tipo_id')->references('id')->on('assistencia_tecnica_tipos');
          
            $table->foreign('genero_id')->references('id')->on('generos');

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
        Schema::dropIfExists('produtores');
        Schema::dropIfExists('etinias');
    }
}
