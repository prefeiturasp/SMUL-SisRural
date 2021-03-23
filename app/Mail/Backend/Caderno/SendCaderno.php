<?php

namespace App\Mail\Backend\Caderno;

use App\Models\Core\CadernoModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendCaderno.
 */
class SendCaderno extends Mailable
{
    use Queueable, SerializesModels;

    private $model;
    private $pdf;

    public function __construct(CadernoModel $model, $pdf)
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
            ->markdown('backend.mail.caderno', ['caderno' => $this->model])
            ->attachData($this->pdf, 'caderno-' . $this->model->uid . '.pdf')
            ->subject('SisRural - Caderno de Campo')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'));
    }
}
