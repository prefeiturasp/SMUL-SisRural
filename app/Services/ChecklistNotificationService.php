<?php

namespace App\Services;

use App\Mail\Backend\Checklist\SendChecklist;
use App\Mail\Backend\Checklist\SendFlowStatusChanged;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Mail\Mailable;
use Mail;
use VerumConsilium\Browsershot\Facades\PDF;

class ChecklistNotificationService extends Mailable
{
    public function __construct()
    {
    }

    public function sendMail(ChecklistUnidadeProdutivaModel $checklist)
    {
        $pdf = $this->getChecklistPDF($checklist);
        return Mail::send(new SendChecklist($checklist, $pdf->inline()));
    }

    public function getChecklistPDF(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $categorias = $checklistUnidadeProdutiva->getCategoriasAndRespostasChecklist();

        $score = $checklistUnidadeProdutiva->score();

        return PDF::loadView('backend.core.checklist_unidade_produtiva.pdf', compact('checklistUnidadeProdutiva', 'categorias', 'score'))
            ->showBackground()
            ->waitUntilNetworkIdle()
            // ->margins(0, 0, 0, 0)
            ->margins(20, 20, 20, 20)
            ->format('A4')
            ->noSandbox();
    }

    public static function sendAnalisisFlowMail(ChecklistUnidadeProdutivaModel $checklist)
    {
        return Mail::send(new SendFlowStatusChanged($checklist));
    }
}
