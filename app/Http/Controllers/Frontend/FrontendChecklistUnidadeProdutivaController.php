<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Services\ChecklistNotificationService;

/**
 * Class FrontendChecklistUnidadeProdutivaController.
 */
class FrontendChecklistUnidadeProdutivaController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function pdf(ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva, User $user, ChecklistNotificationService $service)
    {
        if (!$checklistUnidadeProdutiva || !$checklistUnidadeProdutiva->id) {
            throw new \Exception('O formulário informado não existe.');
        }

        if (!$user || !$user->id) {
            throw new \Exception('O usuário informado não existe.');
        }

        $pdf = $service->getChecklistPDF($checklistUnidadeProdutiva);
        
        return $pdf->inline();
    }
}
