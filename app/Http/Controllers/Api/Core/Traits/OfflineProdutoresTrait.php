<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\ProdutorModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait OfflineProdutoresTrait
{

    public function produtores(Request $request)
    {

        $data = [];

        // \DB::enableQueryLog();

        $produtores_all = ProdutorModel::withTrashed()
            ->select('id')
            ->with(['unidadesProdutivasOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_produtor_unidade_produtiva'));
            }])->get()->toArray();

        $data['produtor_unidade_produtiva'] = $this->mergeOfflineData($produtores_all, 'unidades_produtivas_offline');

        $data['produtores'] = ProdutorModel::withTrashed()->whereUpdatedAt($request->input('updated_at_produtores'))->get()->toArray();

        $foundProdutorUnidadeProdutiva = collect($data['produtor_unidade_produtiva'])->pluck('id', 'id')->toArray();
        $foundProdutores = collect($data['produtores'])->pluck('id', 'id')->toArray();

        /*
        * Tratamento especial para unidades produtivas que não estão no escopo (UnidadeProdutivaPermissionScope)
        * São unidades produtivas fora da abrangência, que o usuário consegue aplicar/editar um "Formulário aplicado"
        */

        $checklist_unidade_produtiva_analise = ChecklistUnidadeProdutivaModel::withTrashed()
            ->whereDoesntHave('unidadeProdutivaScoped')
            ->select("produtor_id")
            ->with(['produtor' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_produtores'));
                $query->with(['unidadesProdutivasOffline']);
            }])->get()->toArray();


        foreach ($checklist_unidade_produtiva_analise as $k => $v) {
            if ($v['produtor']) {
                $produtor_unidade_produtiva = $v['produtor']['unidades_produtivas_offline'];
                foreach ($produtor_unidade_produtiva as $vv) {
                    //add
                    if (@!$foundProdutorUnidadeProdutiva[$vv['id']]) {
                        $foundProdutorUnidadeProdutiva[$vv['id']] = $vv['id'];
                        $data['produtor_unidade_produtiva'][] = $vv;
                    }
                }

                //remove referencia p/ nao add nos "produtores"
                unset($v['produtor']['unidades_produtivas_offline']);

                //produtores
                $v['produtor']['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');

                //add
                if (@!$foundProdutores[$v['produtor']['id']]) {
                    $foundProdutores[$v['produtor']['id']] = $v['produtor']['id'];
                    $data['produtores'][] = $v['produtor'];
                }
            }
        }

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        /**fim do tratamento especial */

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
