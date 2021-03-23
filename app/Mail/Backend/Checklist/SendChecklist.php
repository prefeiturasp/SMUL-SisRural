<?php

namespace App\Mail\Backend\Checklist;

use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendChecklist.
 */
class SendChecklist extends Mailable
{
    use Queueable, SerializesModels;


    private $model;
    private $pdf;

    public function __construct(ChecklistUnidadeProdutivaModel $model, $pdf)
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
            ->markdown('backend.mail.checklist', ['checklistUnidadeProdutiva' => $this->model])
            ->attachData($this->pdf, 'checklist-' . $this->model->uid . '.pdf')
            ->subject('SisRural - Formulário')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'));
    }
}
