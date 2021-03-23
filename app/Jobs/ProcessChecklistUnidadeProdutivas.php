<?php

namespace App\Jobs;

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessChecklistUnidadeProdutivas implements ShouldQueue 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $v = ChecklistUnidadeProdutivaModel::withoutGlobalScopes()
            ->whereNull('pontuacao')
            ->where('status', ChecklistStatusEnum::Finalizado)
            ->where('id', $this->id)
            ->first();

        if ($v) {
            $score = $v->score();

            if (@$score) {
                \DB::table('checklist_unidade_produtivas')
                    ->where('id', $v->id)
                    ->update([
                        'pontuacao' => $score['pontuacao'],
                        'pontuacaoFinal' => $score['pontuacaoFinal'],
                        'pontuacaoPercentual' => $score['pontuacaoPercentual']
                    ]);
            }
        }
    }
}
