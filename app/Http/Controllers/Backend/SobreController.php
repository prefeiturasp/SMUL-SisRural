<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Forms\SobreForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Models\Core\SobreModel;

class SobreController extends Controller
{
    use FormBuilderTrait;

    public function __construct()
    {
    }

    /**
     * Listagem de termos de uso
     *
     * @return void
     */
    public function index()
    {
        return view('backend.core.sobre.index')
            ->withSobre(SobreModel::first());
    }

    /**
     * Edição
     *
     * @param  SobreModel $sobre
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function edit(SobreModel $sobre, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(SobreForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.sobre.update', compact('sobre')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $sobre,
        ]);

        $title = 'Editar Sobre';

        return view('backend.core.sobre.create_update', compact('form', 'title'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param   SobreModel $sobre
     * @return void
     */
    public function update(Request $request, SobreModel $sobre)
    {
        $form = $this->form(SobreForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['texto']);
        $sobre->update($data);

        return redirect()->route('admin.core.sobre.index')->withFlashSuccess('Sobre alterado com sucesso!');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function quillUpload(Request $request)
    {
        $request->validate([
            'file_upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('file_upload');

        $imageName = time() . '.' . $file->guessClientExtension();
        $path = '/quill/' . $imageName;

        \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

        //P/ S3 vai retornar a url do S3, para 'local' retorna relativo.
        return response()->json(['path' => str_replace(url('/'), '', \Storage::url($path))]);
    }
}
