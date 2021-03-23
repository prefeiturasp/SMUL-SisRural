<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Http\Controllers\Backend\Forms\ChecklistAprovacaoLogsForm;
use App\Http\Controllers\Controller;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Repositories\Backend\Core\ChecklistAprovacaoLogsRepository;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaRepository;
use Auth;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class ChecklistAprovacaoLogsController extends Controller
{
    use FormBuilderTrait;
    protected $repository;

    public function __construct(ChecklistAprovacaoLogsRepository $repository, ChecklistUnidadeProdutivaRepository $repositoryChecklistUnidadeProdutiva)
    {
        $this->repository = $repository;
        $this->repositoryChecklistUnidadeProdutiva = $repositoryChecklistUnidadeProdutiva;
    }

    /**
     * Aprovação de formulário aplicado que esta com o status "aguardando aprovação"
     *
     * Os usuários que possuem "permissão" de análise pode acessar esse bloco.
     *
     * Na tela de visualização do "Formulário aplicado" vai aparecer um form para essa ação.
     *
     * @param  Request $request
     * @param  ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva
     * @return void
     */
    public function store(Request $request, ChecklistUnidadeProdutivaModel $checklistUnidadeProdutiva)
    {
        $form = $this->form(ChecklistAprovacaoLogsForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = array_merge(
            $request->only(['status', 'message']),
            [
                'checklist_unidade_produtiva_id' => $checklistUnidadeProdutiva->id,
                'user_id' => Auth::user()->id
            ]
        );

        $this->repository->create($data);

        //Após criar o "log" de aprovação/reprovação, se o status do formulário for "Aprovado/Reprovado", ele finaliza altera o status do formulário aplicado.
        if (
            $data['status'] == ChecklistStatusFlowEnum::Aprovado ||
            $data['status'] == ChecklistStatusFlowEnum::Reprovado
        ) {
            $checklistUnidadeProdutiva->status = ChecklistStatusEnum::Finalizado;
        }

        $checklistUnidadeProdutiva->status_flow = $data['status'];
        $checklistUnidadeProdutiva->save();

        //Propaga respostas caso o formulário aplicado tenha sido finalizado
        if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Finalizado) {
            $this->repositoryChecklistUnidadeProdutiva->saveRespostasSnapshot($checklistUnidadeProdutiva);
        }

        $redirect = route('admin.core.checklist_unidade_produtiva.index');
        return redirect($redirect)->withFlashSuccess('Análise efetuada com sucesso!');
    }
}
