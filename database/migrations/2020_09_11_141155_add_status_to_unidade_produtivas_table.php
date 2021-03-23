<?php

use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->enum('status', ProdutorUnidadeProdutivaStatusEnum::getValues())->default(ProdutorUnidadeProdutivaStatusEnum::Ativo);
            $table->text('status_observacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_observacao']);
        });
    }
}
