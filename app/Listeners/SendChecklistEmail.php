<?php

namespace App\Listeners;

use App\Events\ChecklistUnidadeProdutivaFinished;
use App\Services\ChecklistNotificationService;
use Illuminate\Support\Facades\Session;

class SendChecklistEmail
{
    private $checklistNotificationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ChecklistNotificationService $service)
    {
        $this->checklistNotificationService = $service;
    }

    /**
     * Handle the event.
     *
     * @param  ChecklistUnidadeProdutivaFinished  $event
     * @return void
     */
    public function handle(ChecklistUnidadeProdutivaFinished $event)
    {
        // Foi desabilitado o disparo de email ao finalizar o Formulário, porque o usuário pode fazer manualmente na tabela.
        // Caso pessam para habilitar novamente, só descomentar as linhas abaixo.

        /*
            $model = $event->model;
            if (!$model->produtor->email) {
                Session::flash('flash_warning', 'Produtor sem email cadastrado, o arquivo PDF não será enviado');
            } else {
                $this->checklistNotificationService->sendMail($model);
            }
        */
    }
}
