<?php

use App\Enums\CadernoStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadernosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadernos', function (Blueprint $table) {
            $table->string('id')->index();
            $table->unsignedBigInteger('uid')->autoIncrement();

            $table->unsignedBigInteger('template_id');
            $table->foreign('template_id')->references('id')->on('templates');

            $table->string('produtor_id')->index();
            $table->foreign('produtor_id')->references('id')->on('produtores');

            $table->string('unidade_produtiva_id')->index();
            $table->foreign('unidade_produtiva_id')->references('id')->on('unidade_produtivas');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->text('protocolo')->nullable();

            $table->enum("status", CadernoStatusEnum::getValues())->default(CadernoStatusEnum::Rascunho);

            $table->boolean('app_sync')->nullable();

            $table->dateTime('finished_at')->nullable();

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
        Schema::dropIfExists('cadernos');
    }
}
