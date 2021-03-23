<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Services\CadernoNotificationService;

/**
 * Class FrontendCadernoController.
 */
class FrontendCadernoController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function pdf(CadernoModel $caderno, User $user, CadernoNotificationService $service)
    {
        if (!$caderno || !$caderno->id) {
            throw new \Exception('O caderno informado não existe.');
        }

        if (!$user || !$user->id) {
            throw new \Exception('O usuário informado não existe.');
        }

        $pdf = $service->getCadernoPDF($caderno);

        return $pdf->inline();
    }
}
