<?php

namespace App\Services;

use App\Mail\Backend\Checklist\SendPlanoAcao;
use App\Models\Core\PlanoAcaoModel;
use Illuminate\Mail\Mailable;
use Mail;
use VerumConsilium\Browsershot\Facades\PDF;

class PlanoAcaoNotificationService extends Mailable
{
    public function __construct()
    {
    }

    /**
     * Dispara o email anexando o PDA do Plano de ação gerado
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function sendMail(PlanoAcaoModel $planoAcao)
    {
        $pdf = $this->getPlanoAcaoPDF($planoAcao);
        return Mail::send(new SendPlanoAcao($planoAcao, $pdf->inline()));
    }

    /**
     * Gera o PDF do plano de ação
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function getPlanoAcaoPDF(PlanoAcaoModel $planoAcao)
    {
        return PDF::loadView('backend.core.plano_acao.pdf', compact('planoAcao'))
            ->showBackground()
            ->waitUntilNetworkIdle()
            // ->margins(0, 0, 0, 0)
            ->margins(20, 20, 20, 20)
            ->format('A4')
            ->noSandbox();
    }

    /**
     * Gera o PDA do plano de ação coletivo
     *
     * @param  PlanoAcaoModel $planoAcao
     * @return mixed
     */
    public function getPlanoAcaoColetivoPDF(PlanoAcaoModel $planoAcao)
    {
        return PDF::loadView('backend.core.plano_acao_coletivo.pdf', compact('planoAcao'))
            ->showBackground()
            ->waitUntilNetworkIdle()
            // ->margins(0, 0, 0, 0)
            ->margins(20, 20, 20, 20)
            ->format('A4')
            ->noSandbox();
    }
}
