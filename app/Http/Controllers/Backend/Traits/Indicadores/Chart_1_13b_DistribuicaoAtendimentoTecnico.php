<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Models\Auth\User;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use DataTables;
use Illuminate\Http\Request;

trait Chart_1_13b_DistribuicaoAtendimentoTecnico
{
    function getChart_1_13b_DistribuicaoAtendimentoTecnico(Request $request)
    {
        $data = $this->getQueryChart_1_13b_DistribuicaoAtendimentoTecnico($request);

        return $data;
    }

    function dataChart_1_13b_DistribuicaoAtendimentoTecnico(Request $request)
    {
        // $data = $this->getQueryChart_1_13b_DistribuicaoAtendimentoTecnicoDatatable($request);
        $data = $this->getQueryChart_1_13b_DistribuicaoAtendimentoTecnicoDatatable($request);

        return DataTables::of($data)
            ->editColumn('uid', function ($row) {
                return $row->uid;
            })
            ->addColumn('nome', function ($row) {
                return @$row->nome ? $row->nome : '-';
            })
            ->addColumn('unidadeProdutiva', function ($row) {
                if (@$row->unidadeProdutiva) {
                    return $row->unidadeProdutiva;
                } else if ($row->produtor_id) {
                    return ProdutorModel::find($row->produtor_id)->unidadesProdutivas->pluck('nome')->join(", ");
                } else {
                    return '-';
                }
            })
            ->addColumn('actions', function ($row) {
                $externalDashUrl = null;

                if ($row->produtor_id) {
                    $externalDashUrl = route('admin.core.produtor.dashboard', $row->produtor_id);
                } else if ($row->type == 'Plano de Ação') {
                    $pda = PlanoAcaoModel::where('id', $row->id)->get(['id', 'plano_acao_coletivo_id', 'fl_coletivo'])->first();

                    if ($pda->fl_coletivo) {
                        $externalDashUrl = route('admin.core.plano_acao_coletivo.view', $pda->plano_acao_coletivo_id ? $pda->plano_acao_coletivo_id : $pda->id);
                    }
                }

                return view('backend.components.form-actions.index', compact('externalDashUrl'));
            })
            ->filterColumn('uid', function ($query, $param) {
                $query->where('C1.uid', '=', $param);
            })
            ->filterColumn('unidadeProdutiva', function ($query, $param) {
                $query->where('UP.nome', 'like', '%' . $param . '%');
            })
            ->filterColumn('nome', function ($query, $param) {
                $query->where('P.nome', 'like', '%' . $param . '%');
            })
            ->make(true);
    }

    function getQueryChart_1_13b_DistribuicaoAtendimentoTecnicoDatatable(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if ($request->get('fullname')) {
            $requestData['atuacao_tecnico_id'] = $this->chartService->getUserIDSByName(@$request->get('fullname'), @$requestData['atuacao_dominio_id'], @$requestData['atuacao_unidade_operacional_id']);
            $requestData['atuacao_unidade_operacional_id'] = null;
            $requestData['atuacao_dominio_id'] = null;
        }

        if (!$this->service->existFilterAtuacao($requestData)) {
            $requestData['atuacao_tecnico_id'] = $this->service->getAllTecnicosAtuacao();
        }

        $userProdutores = $this->chartService->getProdutores($requestData)
            ->select('produtores.user_id', 'produtores.id as produtor_id', 'produtores.id', \DB::raw('NULL as unidade_produtiva_id'), \DB::raw('"Cadastro Produtor" as type'), 'produtores.uid', \DB::raw('DATE_FORMAT(produtores.created_at, "%m/%Y") as date'));

        //Só considerou quem finalizou (não quem criou)
        $userCadernosFinished = $this->chartService->getCadernosFinalizados($requestData)
            ->with('produtor')
            ->select(\DB::raw('cadernos.finish_user_id as user_id'), 'produtor_id', 'cadernos.id', 'cadernos.unidade_produtiva_id', \DB::raw('"Caderno de Campo" as type'), 'cadernos.uid', \DB::raw('DATE_FORMAT(cadernos.created_at, "%m/%Y") as date'));

        if (@$requestData['atuacao_tecnico_id'])
            $userCadernosFinished->whereIn('cadernos.finish_user_id', $requestData['atuacao_tecnico_id']); //Fix p/ pegar apenas os usuários que finalizaram

        // dd($userCadernosFinished->get());

        //Só considerou quem finalizou (não quem criou)
        $userFormulariosFinished = $this->chartService->getFormulariosFinalizadosAtuacao($requestData, true)
            ->with('produtor')
            ->select('checklist_unidade_produtivas.finish_user_id as user_id', 'produtor_id', 'checklist_unidade_produtivas.id', 'unidade_produtiva_id', \DB::raw('"Formulário Aplicado" as type'), 'checklist_unidade_produtivas.uid', \DB::raw('DATE_FORMAT(checklist_unidade_produtivas.created_at, "%m/%Y") as date'));

        if (@$requestData['atuacao_tecnico_id'])
           $userFormulariosFinished->whereIn('checklist_unidade_produtivas.finish_user_id', $requestData['atuacao_tecnico_id']); //Fix p/ pegar apenas os usuários que finalizaram

        // dd($userFormulariosFinished->get());

        $userPdasCriados = $this->chartService->getPdasCreated($requestData, false)
            ->with('produtor')
            ->select('plano_acoes.user_id', 'plano_acoes.produtor_id', 'plano_acoes.id', 'unidade_produtiva_id', \DB::raw('"Plano de Ação" as type'), 'plano_acoes.uid', \DB::raw('DATE_FORMAT(plano_acoes.created_at, "%m/%Y") as date'));

        // dd($userPdasCriados->get());

        $userPdasHistoricos = $this->chartService->getPdasHistoricos($requestData, false)
            ->with('produtor')
            ->select('plano_acao_historicos.user_id', 'plano_acoes.produtor_id', 'plano_acoes.id', 'unidade_produtiva_id', \DB::raw('"Plano de Ação" as type'), 'plano_acoes.uid', \DB::raw('DATE_FORMAT(plano_acao_historicos.created_at, "%m/%Y") as date'));

        //dd($userPdasHistoricos->get());

        $userPdasItensHistorico = $this->chartService->getPdasAcoesHistoricos($requestData, false)
            ->with('produtor')
            ->select('plano_acao_item_historicos.user_id', 'plano_acoes.produtor_id', 'plano_acoes.id', 'unidade_produtiva_id', \DB::raw('"Plano de Ação" as type'), 'plano_acoes.uid', \DB::raw('DATE_FORMAT(plano_acao_item_historicos.created_at, "%m/%Y") as date'));

        // dd($userPdasItensHistorico->get());

        // dd($userProdutores->get(), $userCadernosFinished->get(), $userFormulariosFinished->get(), $userPdasCriados->get(), $userPdasHistoricos->get(), $userPdasItensHistorico->get());

        $produtores = $userProdutores
            ->union($userCadernosFinished)
            ->union($userFormulariosFinished)
            ->union($userPdasCriados)
            ->union($userPdasHistoricos)
            ->union($userPdasItensHistorico);

        $fromSub = \DB::query()
            ->select('C1.*')
            //->select('C1.uid', 'C1.produtor_id', 'C1.unidade_produtiva_id', 'C1.type', 'C1.date') //, 'C1.user_id'
            ->addSelect('P.nome as nome', 'UP.nome as unidadeProdutiva')
            ->fromSub($produtores, 'C1')
            ->leftJoin('produtores as P', 'P.id', '=', 'produtor_id')
            ->leftJoin('unidade_produtivas as UP', 'UP.id', '=', 'unidade_produtiva_id');

        //dd($fromSub->get());

        return $fromSub;
    }

    function getQueryChart_1_13b_DistribuicaoAtendimentoTecnico(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        if (!$this->service->existFilterAtuacao($requestData)) {
            $requestData['atuacao_tecnico_id'] = $this->service->getAllTecnicosAtuacao();
        }

        $userProdutores = $this->chartService->getProdutores($requestData)
            ->select(\DB::raw('produtores.user_id, count(distinct produtores.id) as total'))
            ->groupBy('user_id')
            ->get()
            ->toArray();

        // dd($userProdutores);

        //Cliente não quer a contagem dos cadernos criados
        // $userCadernosCreated = $this->chartService->getCadernos($requestData)
        //     ->select(\DB::raw('cadernos.user_id, count(cadernos.id) as total'))
        //     ->groupBy('user_id')
        //     ->get()
        //     ->toArray();

        $userCadernosFinished = $this->chartService->getCadernosFinalizados($requestData)
            ->select(\DB::raw('cadernos.finish_user_id as user_id, count(cadernos.id) as total'))
            ->groupBy('finish_user_id')
            ->get()
            ->toArray();

        //Cliente não quer a contagem dos formularios criados
        // $userFormulariosCreated = $this->chartService->getFormularios($requestData, true)
        //     ->select(\DB::raw('checklist_unidade_produtivas.user_id, count(checklist_unidade_produtivas.id) as total'))
        //     ->groupBy('user_id')
        //     ->get()
        //     ->toArray();

        $userFormulariosFinished = $this->chartService->getFormulariosFinalizadosAtuacao($requestData, true)
            ->select(\DB::raw('checklist_unidade_produtivas.finish_user_id as user_id, count(checklist_unidade_produtivas.id) as total'))
            ->groupBy('finish_user_id')
            ->get()
            ->toArray();

        // dd($userFormulariosFinished);

        $userPdasCriados = $this->chartService->getPdasCreated($requestData, false)
            ->select(\DB::raw('plano_acoes.id as id, plano_acoes.user_id, DATE_FORMAT(plano_acoes.created_at, "%m/%Y") as date'));

        // dd($userPdasCriados->get());

        $userPdasHistoricos = $this->chartService->getPdasHistoricos($requestData, false)
            ->select(\DB::raw('plano_acoes.id as id, plano_acao_historicos.user_id, DATE_FORMAT(plano_acao_historicos.created_at, "%m/%Y") as date'));

        // dd($userPdasHistoricos->get());

        $userPdasItensHistorico = $this->chartService->getPdasAcoesHistoricos($requestData, false)
            ->select(\DB::raw('plano_acoes.id as id, plano_acao_item_historicos.user_id, DATE_FORMAT(plano_acao_item_historicos.created_at, "%m/%Y") as date'));

        // dd($userPdasItensHistorico->get());

        $unionPdas = $userPdasCriados
            ->union($userPdasHistoricos)
            ->union($userPdasItensHistorico);

        // dd($unionPdas->get()->groupBy('user_id'));

        $unionPdasGroupDate = \DB::table(\DB::raw('(' . $unionPdas->toSql() . ') as a'))
            ->setBindings($unionPdas->getBindings())
            ->selectRaw('id, user_id, date')
            ->groupBy('id', 'user_id', 'date');

        // dd($unionPdasGroupDate->get()->groupBy('user_id'));

        // Debug geral
        // dd($userProdutores, $userCadernosFinished, $userFormulariosFinished, $unionPdasGroupDate->get()->groupBy('user_id'),  $userPdasCriados->get()->groupBy('user_id'), $userPdasHistoricos->get()->groupBy('user_id'), $userPdasItensHistorico->get()->groupBy('user_id'));

        // dd($userProdutores, $userCadernosFinished, $userFormulariosFinished, $unionPdas->get());

        $userPdas = \DB::table(\DB::raw('(' . $unionPdasGroupDate->toSql() . ') as a'))
            ->setBindings($unionPdasGroupDate->getBindings())
            ->distinct()
            ->select(\DB::raw('user_id, count(id) as total'))
            ->groupBy('user_id')
            ->get()
            ->toArray();

        // dd($userPdas);

        // dd($requestData);

        $userIds = collect(
            array_merge(
                $userProdutores,
                //$userCadernosCreated,
                $userCadernosFinished,
                //$userFormulariosCreated,
                $userFormulariosFinished,
                $userPdas
            )
        )
            ->groupBy('user_id')
            ->map(function ($values) {
                // return $values; //Debug
                return $values->sum('total');
            });

        // dd($userIds);

        $usersData = User::withoutGlobalScopes()
            ->withTrashed()
            ->whereIn('id', $userIds->keys())
            ->select(\DB::raw('id, first_name, last_name, concat(first_name," ",last_name) as full_name'))
            ->get();

        $users = $usersData->map(function ($v)  use ($userIds) {
            $v['total'] = $userIds[$v['id']];
            return $v;
        })
            ->groupBy('full_name')
            ->map(function ($values) {
                return $values->sum('total');
            })
            ->toArray();

        //dd($users);

        $ret = array_map(function ($key, $value) {
            return [
                "name" => $key,
                "total" => $value
            ];
        }, array_keys($users), $users);

        //dd($ret[0]);

        return $ret;
    }
}
