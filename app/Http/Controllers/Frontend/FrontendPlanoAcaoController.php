<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Core\PlanoAcaoModel;
use App\Services\PlanoAcaoNotificationService;

/**
 * Class FrontendPlanoAcaoController.
 */
class FrontendPlanoAcaoController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function pdf(PlanoAcaoModel $planoAcao, User $user, PlanoAcaoNotificationService $service)
    {
        if (!$planoAcao || !$planoAcao->id) {
            throw new \Exception('O plano de ação informado não existe.');
        }

        if (!$user || !$user->id) {
            throw new \Exception('O usuário informado não existe.');
        }

        if ($planoAcao->fl_coletivo) {
            $pdf = $service->getPlanoAcaoColetivoPDF($planoAcao);
        } else {
            $pdf = $service->getPlanoAcaoPDF($planoAcao);
        }

        return $pdf->inline();
    }
}
