<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Forms\NovoProdutorUnidadeProdutivaForm;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProdutorRequest;
use App\Models\Core\ProdutorModel;
use App\Repositories\Backend\Core\NovoProdutorUnidadeProdutivaRepository;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Services\UnidadeProdutivaService;
use Exception;

class NovoProdutorUnidadeProdutivaController extends Controller
{
    use FormBuilderTrait;

    /**
     * @var NovoProdutorUnidadeProdutivaRepository
     */
    protected $repository;

    /**
     * @var UnidadeProdutivaService
     */
    protected $service;

    public function __construct(NovoProdutorUnidadeProdutivaRepository $repository, UnidadeProdutivaService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Cadastro rápido do produtor/unidade produtiva
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(NovoProdutorUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.novo_produtor_unidade_produtiva.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
            'enctype' => 'multipart/form-data'
        ]);

        $title = 'Criar Produtor/Unidade Produtiva';

        return view('backend.core.novo_produtor_unidade_produtiva.create_update', compact('form', 'title'));
    }

    /**
     * Cadastro - POST
     *
     * @param  ProdutorRequest $request - faz um tratamento para normalizar o cpf/cnpj
     * @return void
     */
    public function store(ProdutorRequest $request)
    {
        $form = $this->form(NovoProdutorUnidadeProdutivaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->all();

        //CPF único para "produtores"
        if (@$data['cpf']) {
            $request->validate([
                'cpf' => 'unique:produtores',
            ], [
                'O CPF informado já encontra-se utilizado pelo produtor/a "' . @ProdutorModel::withoutGlobalScopes()->where("cpf", $request->cpf)->first()->nome . '".'
            ]);
        }

        //Cadastro fora da abrangência retorna erro
        try {
            $return = $this->repository->create($data);
            $produtor = $return['produtor'];
            $unidadeProdutiva = $return['unidadeProdutiva'];
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        //Sync das abrangencias da unidade produtiva cadastrada
        $this->service->syncAbrangencias($unidadeProdutiva);

        return redirect(route('admin.core.novo_produtor_unidade_produtiva.produtor_edit', ['produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva]))->withFlashSuccess('Produtor/Unidade Produtiva cadastrado com sucesso!');
    }
}
