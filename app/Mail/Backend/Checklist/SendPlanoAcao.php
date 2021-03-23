<?php

namespace App\Mail\Backend\Checklist;

use App\Models\Core\PlanoAcaoModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendChecklist.
 */
class SendPlanoAcao extends Mailable
{
    use Queueable, SerializesModels;

    private $model;
    private $pdf;

    public function __construct(PlanoAcaoModel $model, $pdf)
    {
        $this->model = $model;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $produtor = $this->model->produtor;

        if (!$produtor->email) {
            throw new \Exception('Produtor/a ' . $produtor->nome . ' sem email cadastrado, o e-mail não pode ser enviado.');
        }

        return $this->to($produtor->email, $produtor->nome)
            ->markdown('backend.mail.plano_acao', ['planoAcao' => $this->model])
            ->attachData($this->pdf, 'plano_acao-' . $this->model->uid . '.pdf')
            ->subject('SisRural - Plano de Ação')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'));
    }
}
