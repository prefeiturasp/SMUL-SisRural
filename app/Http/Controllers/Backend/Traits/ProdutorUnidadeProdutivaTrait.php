<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\ProdutorUnidadeProdutivaForm;
use App\Models\Core\ProdutorModel;
use App\Models\Core\ProdutorUnidadeProdutivaModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait ProdutorUnidadeProdutivaTrait
{
    /**
     * Cadastro - Adicionar unidade produtiva em um produtor
     *
     * @param  FormBuilder $formBuilder
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function addUnidadeProdutiva(FormBuilder $formBuilder, ProdutorModel $produtor)
    {
        $form = $formBuilder->create(ProdutorUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.produtor.store-unidade-produtiva', compact('produtor')),
            'class' => 'needs-validation',
            'novalidate' => true,
        ]);

        $title = 'Unidade Produtiva';

        $back = route('admin.core.produtor.search-unidade-produtiva', compact('produtor'));

        return view('backend.core.produtor.add-unidade-produtiva', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function storeUnidadeProdutiva(Request $request, ProdutorModel $produtor)
    {
        $data = $request->only(['unidade_produtiva_id', 'contato', 'tipo_posse_id']);

        $unidade = $data['unidade_produtiva_id'];
        $tipo_posse_id = $data['tipo_posse_id'];
        $contato = @$data['contato'];

        $return = $produtor->unidadesProdutivasWithTrashed()->syncWithoutDetaching([$unidade => ['contato' => !!$contato, 'tipo_posse_id' => $tipo_posse_id]]); // 'deleted_at' => null //não funciona
        //Restaura o registro (softDelete) porque o deleted_at não funciona dentro do syncWithoutDetaching
        if (count($return['updated']) > 0) {
            ProdutorUnidadeProdutivaModel::withTrashed()->where('unidade_produtiva_id', $return['updated'][0])->where('produtor_id', $produtor->id)->restore();
        }

        return redirect()->route('admin.core.produtor.search-unidade-produtiva', compact('produtor'))->withFlashSuccess('Unidade Produtiva adicionada com sucesso!');
    }

    /**
     * Edição
     *
     * @param  FormBuilder $formBuilder
     * @param  ProdutorModel $produtor
     * @param  mixed $pivot é o registro de relacionamento entre "produtor" vs "unidade produtiva" (tabela produtor_unidade_produtiva)
     * @return void
     */
    public function editUnidadeProdutiva(FormBuilder $formBuilder, ProdutorModel $produtor, $pivot)
    {
        $form = $formBuilder->create(ProdutorUnidadeProdutivaForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.produtor.update-unidade-produtiva', compact('produtor', 'pivot')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => ProdutorUnidadeProdutivaModel::find($pivot)
        ]);

        $title = 'Editar Unidade Produtiva';

        $back = route('admin.core.produtor.search-unidade-produtiva', compact('produtor'));

        return view('backend.core.produtor.add-unidade-produtiva', compact('form', 'title', 'back'));
    }

    /**
     * Edição - POST
     *
     * @param  Request $request
     * @param  ProdutorModel $produtor
     * @param  mixed $pivot é o registro de relacionamento entre "produtor" vs "unidade produtiva" (tabela produtor_unidade_produtiva)
     * @return void
     */
    public function updateUnidadeProdutiva(Request $request, ProdutorModel $produtor, $pivot)
    {
        $data = $request->only(['unidade_produtiva_id', 'contato', 'tipo_posse_id']);

        try {
            ProdutorUnidadeProdutivaModel::find($pivot)->update($data);
        } catch (\Exception $e) {
            return redirect()->route('admin.core.produtor.search-unidade-produtiva', compact('produtor'))->withFlashDanger('O Produtor já possuí a Unidade Produtiva selecionada!');
        }

        return redirect()->route('admin.core.produtor.search-unidade-produtiva', compact('produtor'))->withFlashSuccess('Unidade Produtiva atualizada com sucesso!');
    }

    /**
     * Listagem de unidades produtivas vinculadas ao produtor
     */
    public function searchUnidadeProdutiva(ProdutorModel $produtor)
    {
        $addUrl = route('admin.core.produtor.add-unidade-produtiva', ["produtor" => $produtor]);
        $urlDatatable = route('admin.core.produtor.datatable.unidade_produtiva', ["produtor" => $produtor]);

        return view('backend.core.produtor.search-unidade-produtiva', compact('addUrl', 'urlDatatable'));
    }

    /**
     * API Datatable "searchUnidadeProdutiva()"
     */
    public function datatableUnidadeProdutiva(ProdutorModel $produtor)
    {
        return DataTables::of(ProdutorModel::find($produtor->id)->unidadesProdutivas()->get())
            ->editColumn('uid', function ($row) {
                return $row->uid;
            })->addColumn('tipoPosse', function ($row) {
                return @$row->pivot->tipoPosse->nome;
            })->addColumn('actions', function ($row) {
                $params = ['pivot' => $row->pivot->id, 'produtor' => $row->pivot->produtor_id];
                
                $externalEditUrl = route('admin.core.unidade_produtiva.edit', $row->id);
                $relationEditUrl = route('admin.core.produtor.edit-unidade-produtiva', $params);
                $deleteUrl = route('admin.core.produtor.delete-unidade-produtiva', $params);
                return view('backend.components.form-actions.index', compact('externalEditUrl', 'relationEditUrl', 'deleteUrl', 'row'));
            })
            ->make(true);
    }

    /**
     * Desvincular/remover uma unidade produtiva vs produtor (tabela produtor_unidade_produtiva)
     */
    public function deleteUnidadeProdutiva(ProdutorModel $produtor, $pivot)
    {
        ProdutorUnidadeProdutivaModel::find($pivot)->delete();

        if (ProdutorModel::where('id', $produtor->id)->first()) {
            return redirect()->route('admin.core.produtor.search-unidade-produtiva', compact('produtor'))->withFlashSuccess('Unidade Produtiva removida com sucesso!');
        } else {
            return redirect()->route('admin.core.produtor.search-unidade-produtiva-redirect');
        }
    }

    public function searchUnidadeProdutivaRedirect() {
        return view('backend.core.produtor.search-unidade-produtiva-redirect');
    }
}
