<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Http\Controllers\Backend\Forms\PlanoAcaoHistoricoForm;
use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Models\Core\PlanoAcaoModel;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

trait PlanoAcaoHistoricoTrait
{
    /**
     * Listagem de históricos do Plano de ação
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  Request $request
     * @return void
     */
    public function historicoIndex(PlanoAcaoModel $planoAcao, Request $request)
    {
        $title = 'Acompanhamento do plano de ação';
        $urlAdd = route('admin.core.plano_acao.historico.create', ["planoAcao" => $planoAcao]);
        $urlDatatable = route('admin.core.plano_acao.historico.datatable', ["planoAcaoId" => $planoAcao->id]);

        return view('backend.core.plano_acao.historico.index', compact('urlAdd', 'urlDatatable', 'title', 'planoAcao'));
    }

    /**
     * API Database "historicoIndex()"
     *
     * @param  string $planoAcaoId
     * @return void
     */
    public function historicoDatatable(string $planoAcaoId)
    {
        $planoAcao = PlanoAcaoModel::withoutGlobalScopes()->where("id", $planoAcaoId)->first();

        return DataTables::of($planoAcao->historicos()->getQuery())
            ->addColumn('usuario', function ($row) {
                return $row->usuario->first_name;
            })->addColumn('actions', function ($row) {
                $params = ['planoAcao' => $row->plano_acao_id, 'historico' => $row->id];

                $editUrl = route('admin.core.plano_acao.historico.edit', $params);
                $deleteUrl = route('admin.core.plano_acao.historico.destroy', $params);

                return view('backend.components.form-actions.index', compact('editUrl', 'deleteUrl', 'row'));
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })->filterColumn('usuario', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('usuario', function ($q) use ($keyword) {
                        $q->where('users.first_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->make(true);
    }

    /**
     * Cadastro
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoCreate(PlanoAcaoModel $planoAcao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.historico.store', ['planoAcao' => $planoAcao]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
        ]);

        $title = 'Adicionar Novo Acompanhamento';

        $back = route('admin.core.plano_acao.historico.index', compact('planoAcao'));

        return view('backend.core.plano_acao.historico.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro - POST
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function historicoStore(Request $request, PlanoAcaoModel $planoAcao)
    {
        $data = $request->only(['texto']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryHistorico->create($data);

        return redirect()->route('admin.core.plano_acao.historico.index', compact('planoAcao'))->withFlashSuccess('Acompanhamento criado com sucesso!');
    }

    /**
     * Edição
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoHistoricoModel $historico
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoEdit(PlanoAcaoModel $planoAcao, PlanoAcaoHistoricoModel $historico, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.historico.update', ['planoAcao' => $planoAcao, 'historico' => $historico]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
            'model' => $historico,
        ]);

        $title = 'Editar Acompanhamento';

        $back = route('admin.core.plano_acao.historico.index', compact('planoAcao'));

        return view('backend.core.plano_acao.historico.create_update', compact('form', 'title', 'back', 'historico'));
    }

    /**
     * Edição - POST
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoHistoricoModel $historico
     * @param  Request $request
     * @return void
     */
    public function historicoUpdate(PlanoAcaoModel $planoAcao, PlanoAcaoHistoricoModel $historico, Request $request)
    {
        $data = $request->only(['texto']);
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryHistorico->update($historico, $data);

        return redirect()->route('admin.core.plano_acao.historico.index', compact('planoAcao'))->withFlashSuccess('Acompanhamento atualizada com sucesso!');
    }

    /**
     * Remover histórico
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  PlanoAcaoHistoricoModel $historico
     * @return void
     */
    public function historicoDestroy(PlanoAcaoModel $planoAcao, PlanoAcaoHistoricoModel $historico)
    {
        $this->repositoryHistorico->delete($historico);

        return redirect()->route('admin.core.plano_acao.historico.index', compact('planoAcao'))->withFlashSuccess('Acompanhamento deletada com sucesso');
    }

    /**
     * Listagem e formulário p/ cadastrar um histórico no PDA
     *
     * @param  PlanoAcaoModel $planoAcao
     * @param  FormBuilder $formBuilder
     * @return void
     */
    public function historicoCreateAndList(PlanoAcaoModel $planoAcao, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PlanoAcaoHistoricoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.plano_acao.historico.store_create_and_list', ['planoAcao' => $planoAcao]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => ['planoAcao' => $planoAcao],
        ]);

        $urlDatatable = route('admin.core.plano_acao.historico.datatable', ["planoAcaoId" => $planoAcao->id]);

        return view('backend.core.plano_acao.historico.modal_create_list', compact('form', 'urlDatatable'));
    }

    /**
     * POST p/ salvar o histórico da listagem/formulário "historicoStoreCreateAndList()"
     *
     * @param  Request $request
     * @param  PlanoAcaoModel $planoAcao
     * @return void
     */
    public function historicoStoreCreateAndList(Request $request, PlanoAcaoModel $planoAcao)
    {
        $data = $request->only(['texto']);
        $data['user_id'] = auth()->user()->id;
        $data['plano_acao_id'] = $planoAcao->id;

        $this->repositoryHistorico->create($data);

        return redirect()->route('admin.core.plano_acao.historico.create_and_list', compact('planoAcao'))->withFlashSuccess('Acompanhamento criado com sucesso!');
    }
}
