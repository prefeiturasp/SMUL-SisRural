<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Forms\ImportadorCadernoForm;
use App\Http\Controllers\Backend\Forms\ImportadorChecklistUnidadeProdutivaForm;
use App\Http\Controllers\Backend\Forms\ImportadorForm;
use App\Http\Controllers\Backend\Forms\ImportadorUsuariosForm;
use App\Http\Controllers\Controller;
use App\Services\ImportadorService;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class ImportadorController extends Controller
{
    use FormBuilderTrait;

    public function __construct(ImportadorService $service)
    {
        $this->service = $service;
    }

    /**
     * Importação do produtor/unidade produtiva
     *
     * @param  mixed $formBuilder
     * @return void
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ImportadorForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.importador.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Importar';

        return view('backend.core.importador.create_update', compact('form', 'title'));
    }

    public function store(Request $request)
    {
        $form = $this->form(ImportadorForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if ($request->hasFile('arquivo')) {
            $this->service->import($request->arquivo->getPathname());
        }

        return redirect(route('admin.dashboard'))->withFlashSuccess('Dados importados com sucesso!');
    }

    /**
     * Importação do caderno de campo
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function createCaderno(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ImportadorCadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.importador.storeCaderno'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Importar';

        return view('backend.core.importador.create_update', compact('form', 'title'));
    }

    public function storeCaderno(Request $request)
    {
        $form = $this->form(ImportadorCadernoForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if ($request->hasFile('arquivo')) {
            $this->service->importCaderno($request->arquivo->getPathname());
        }

        return redirect(route('admin.dashboard'))->withFlashSuccess('Dados importados com sucesso!');
    }


    /**
     * Importação de formulários aplicados
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function createChecklistUnidadeProdutiva(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ImportadorChecklistUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.importador.storeChecklistUnidadeProdutiva'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Importar';

        return view('backend.core.importador.create_update', compact('form', 'title'));
    }

    public function storeChecklistUnidadeProdutiva(Request $request)
    {
        $form = $this->form(ImportadorChecklistUnidadeProdutivaForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if ($request->hasFile('arquivo')) {
            $this->service->importChecklistUnidadeProdutiva($request->arquivo->getPathname());
        }

        return redirect(route('admin.dashboard'))->withFlashSuccess('Dados importados com sucesso!');
    }

    /**
     * Importação de usuários
     *
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function createUsuarios(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ImportadorUsuariosForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.importador.storeUsuarios'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Importar';

        return view('backend.core.importador.create_update', compact('form', 'title'));
    }

    public function storeUsuarios(Request $request)
    {
        $form = $this->form(ImportadorUsuariosForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if ($request->hasFile('arquivo')) {
            $this->service->importUsuarios($request->arquivo->getPathname());
        }

        return redirect(route('admin.dashboard'))->withFlashSuccess('Dados importados com sucesso!');
    }


    /**
     * Atualização de alguns campos do produtor/unidade produtiva
     *
     * @param  mixed $formBuilder
     * @return void
     */
    public function editProdutor(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(ImportadorForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.importador.updateProdutor'),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Importar';

        return view('backend.core.importador.create_update', compact('form', 'title'));
    }

    public function updateProdutor(Request $request)
    {
        $form = $this->form(ImportadorForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        if ($request->hasFile('arquivo')) {
            $this->service->importUpdateProdutor($request->arquivo->getPathname());
        }

        return redirect(route('admin.dashboard'))->withFlashSuccess('Dados importados com sucesso!');
    }
}
