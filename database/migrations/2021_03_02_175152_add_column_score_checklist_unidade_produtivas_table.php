<?php

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnScoreChecklistUnidadeProdutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->string('pontuacao')->nullable();
            $table->string('pontuacaoFinal')->nullable();
            $table->string('pontuacaoPercentual')->nullable();
        });


        $list = ChecklistUnidadeProdutivaModel::withoutGlobalScopes()
            ->whereNull('pontuacao')
            ->where('status', ChecklistStatusEnum::Finalizado)
            ->get();

        foreach ($list as $v) {
            $score = $v->score();

            DB::table('checklist_unidade_produtivas')
                ->where('id', $v->id)
                ->update([
                    'pontuacao' => $score['pontuacao'],
                    'pontuacaoFinal' => $score['pontuacaoFinal'],
                    'pontuacaoPercentual' => $score['pontuacaoPercentual']
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->dropColumn(['pontuacao', 'pontuacaoFinal', 'pontuacaoPercentual']);
        });
    }
}
