<?php

namespace App\Services;

use App\Mail\Backend\Caderno\SendCaderno;
use App\Models\Core\CadernoModel;
use Illuminate\Mail\Mailable;
use Mail;
use VerumConsilium\Browsershot\Facades\PDF;

class CadernoNotificationService extends Mailable
{
    public function __construct()
    {
    }

    public function sendMail(CadernoModel $caderno)
    {
        $pdf = $this->getCadernoPDF($caderno);
        return Mail::send(new SendCaderno($caderno, $pdf->inline()));
    }

    public function getCadernoPDF(CadernoModel $caderno)
    {
        $perguntas = $caderno->getPerguntasRespostas();

        return PDF::loadView('backend.core.cadernos.pdf', compact('caderno', 'perguntas'))
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->margins(20, 20, 20, 20)
            ->format('A4')
            ->noSandbox();
    }
}
