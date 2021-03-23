<?php

namespace App\Mail\Backend\Checklist;

use App\Enums\ChecklistStatusFlowEnum;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendFlowStatusChanged.
 */
class SendFlowStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    private $model;
    public $checklist_name;
    public $link;

    public function __construct(ChecklistUnidadeProdutivaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->checklist_name = $this->model->checklist->nome;
        $this->link = route('admin.core.checklist_unidade_produtiva.view', $this->model);

        switch($this->model->status_flow) {
            case null:
                $to = $this->model->checklist->usuariosAprovacao->pluck('email');
                $view = 'backend.mail.checklist_flow_analisis';
            break;
            case ChecklistStatusFlowEnum::AguardandoRevisao:
                $to = $this->model->usuario->email;
                $view = 'backend.mail.checklist_flow_revision';
            break;
            case ChecklistStatusFlowEnum::Aprovado:
                $to = $this->model->usuario->email;
                $view = 'backend.mail.checklist_flow_approved';
            break;
            case ChecklistStatusFlowEnum::Reprovado:
                $to = $this->model->usuario->email;
                $view = 'backend.mail.checklist_flow_reproved';
            break;
        }

        return $this->to($to)
            ->markdown($view)
            ->subject('SisRural - Formulário - Alteração de Status')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'));
    }
}
