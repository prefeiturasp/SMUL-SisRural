<?php

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Database\Seeder;

//php artisan db:seed --class=SyncMvp4Seeder
class SyncMvp4Seeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Seeder que resolve os dados dos migrations pertencente ao  MVP4
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $this->fixLatLng();

        $this->fixUserId();

        $this->fixScore();

        $this->fixAbrangencia();

        $this->enableForeignKeys();
    }

    private function fixLatLng() {
        DB::unprepared("update cidades set lat = '-0.9321899', lng = '-48.2797696' where id = 200;");
        DB::unprepared("update cidades set lat = '-3.846163', lng = '-32.412215' where id = 1500;");
        DB::unprepared("update cidades set lat = '-7.845984', lng = '-34.908060' where id = 1509;");
        DB::unprepared("update cidades set lat = '-8.592616', lng = '-35.117058' where id = 1600;");
        DB::unprepared("update cidades set lat = '-0.771132', lng = '-47.176508' where id = 5354;");

        DB::unprepared("update cidades set lat = '-22.887357', lng = '-42.026920' where id = 5548;");
        DB::unprepared("update cidades set lat = '-22.760842', lng = '-41.891206' where id = 5549;");
        DB::unprepared("update cidades set lat = '-15.793208', lng = '-47.890110' where id = 5569;");
    }

    private function fixUserId() {
        \DB::table('unidade_produtivas')
            ->whereNull('user_id')
            ->update(array('user_id' => 5));

        \DB::table('produtores')
            ->whereNull('user_id')
            ->update(array('user_id' => 5));

        \DB::table('checklist_unidade_produtivas')
            ->whereNull('finish_user_id')
            ->where('status', 'finalizado')
            ->update(array('finish_user_id' => DB::raw("`user_id`"), 'finished_at' => DB::raw("`updated_at`")));

        \DB::table('cadernos')
            ->whereNull('finish_user_id')
            ->where('status', 'finalizado')
            ->update(array('finish_user_id' => DB::raw("`user_id`"), 'finished_at' => DB::raw("`updated_at`")));
    }

    private function fixScore() {
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

    private function fixAbrangencia() {
        DB::table('unidade_operacionais')
            ->update([
                'abrangencia_at' => \DB::raw('unidade_operacionais.created_at'),
            ]);
    }
}
