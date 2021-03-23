<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaRepository;
use Error;
use Exception;
use Illuminate\Http\Request;

trait OfflineApiTrait
{

    public function checklistScore(Request $request)
    {
        $request->validate([
            'checklist_unidade_produtiva_id' => 'required',
        ]);

        $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::where("id", $request->checklist_unidade_produtiva_id)->first();

        if (!$checklistUnidadeProdutiva) {
            return response()->json([
                'message' => 'Formulário não encontrado'
            ], 401);
        }

        return response()->json([
            'data' =>  $checklistUnidadeProdutiva->score(),
        ], 200, array(), JSON_PRETTY_PRINT);
    }

    /*
    public function checklistPdf(Request $request, ChecklistNotificationService $service)
    {
        $request->validate([
            'checklist_unidade_produtiva_id' => 'required',
        ]);

        $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::where("id", $request->checklist_unidade_produtiva_id)->first();

        if (!$checklistUnidadeProdutiva) {
            return response()->json([
                'message' => 'Formulário não encontrado'
            ], 401);
        }

        $pdf = $service->getChecklistPDF($checklistUnidadeProdutiva);

        $path = 'checklist_unidade_produtiva_pdf/' . $checklistUnidadeProdutiva->id . '.pdf';
        \Storage::put($path, $pdf->inline());

        return response()->json([
            'data' => \Storage::url('/') . $path,
        ], 200, array(), JSON_PRETTY_PRINT);
    }
    */
}
