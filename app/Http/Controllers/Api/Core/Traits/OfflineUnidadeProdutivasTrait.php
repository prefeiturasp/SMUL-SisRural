<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait OfflineUnidadeProdutivasTrait
{

    /**
     * Retorno de Dados
     */
    public function unidadeProdutivas(Request $request)
    {
        $data = [];

        // \DB::enableQueryLog();

        /**
         * Unidades Produtivas
         */
        $unidade_produtivas_all = UnidadeProdutivaModel::withTrashed()
            ->select('id') //Otimização
            ->with(['canaisComercializacaoOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_canal_comercializacoes'));
            }])->with(['caracterizacoesOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_caracterizacoes'));
            }])->with(['riscosContaminacaoAguaOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_risco_contaminacao_aguas'));
            }])->with(['tiposFonteAguaOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_tipo_fonte_aguas'));
            }])->with(['solosCategoriaOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_solo_categorias'));
            }])->with(['certificacoesOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_certificacoes'));
            }])->with(['pressaoSociaisOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_pressao_sociais'));
            }])->with(['instalacoesOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_instalacoes'));
            }])->with(['colaboradoresOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_colaboradores'));
            }])->with(['arquivosManyOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_arquivos'));
            }])->with(['residuoSolidosOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_residuo_solidos'));
            }])->with(['esgotamentoSanitariosOffline' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtiva_esgotamento_sanitarios'));
            }])->get()->toArray();

        $data['unidade_produtiva_caracterizacoes'] = $this->mergeOfflineData($unidade_produtivas_all, 'caracterizacoes_offline');

        $data['unidade_produtiva_canal_comercializacoes'] = $this->mergeOfflineData($unidade_produtivas_all, 'canais_comercializacao_offline');

        $data['unidade_produtiva_risco_contaminacao_aguas'] = $this->mergeOfflineData($unidade_produtivas_all, 'riscos_contaminacao_agua_offline');

        $data['unidade_produtiva_tipo_fonte_aguas'] = $this->mergeOfflineData($unidade_produtivas_all, 'tipos_fonte_agua_offline');

        $data['unidade_produtiva_solo_categorias'] = $this->mergeOfflineData($unidade_produtivas_all, 'solos_categoria_offline');

        $data['unidade_produtiva_certificacoes'] = $this->mergeOfflineData($unidade_produtivas_all, 'certificacoes_offline');

        $data['unidade_produtiva_pressao_sociais'] = $this->mergeOfflineData($unidade_produtivas_all, 'pressao_sociais_offline');

        $data['unidade_produtiva_residuo_solidos'] = $this->mergeOfflineData($unidade_produtivas_all, 'residuo_solidos_offline');

        $data['unidade_produtiva_esgotamento_sanitarios'] = $this->mergeOfflineData($unidade_produtivas_all, 'esgotamento_sanitarios_offline');

        $data['instalacoes'] = $this->mergeOfflineData($unidade_produtivas_all, 'instalacoes_offline');

        $data['colaboradores'] = $this->mergeOfflineData($unidade_produtivas_all, 'colaboradores_offline');

        $data['unidade_produtiva_arquivos'] = $this->mergeOfflineData($unidade_produtivas_all, 'arquivos_many_offline');
        $data['unidade_produtiva_arquivos'] = $this->resolveLinks('arquivo', $data['unidade_produtiva_arquivos']);

        $data['unidade_produtivas'] = UnidadeProdutivaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_unidade_produtivas'))->get()->toArray();

        /*
        * Tratamento especial para unidades produtivas que não estão no escopo (UnidadeProdutivaPermissionScope)
        * São unidades produtivas fora da abrangência, que o usuário consegue aplicar/editar um "Formulário aplicado"
        */
        $checklist_unidade_produtiva_analise = ChecklistUnidadeProdutivaModel::withTrashed()
            ->whereDoesntHave('unidadeProdutivaScoped')
            ->select("unidade_produtiva_id")
            ->with(['unidade_produtiva' => function ($query) use ($request) {
                $query->whereUpdatedAt($request->input('updated_at_unidade_produtivas'));
            }])->get()->toArray();

        $foundUnidadeProdutiva = collect($data['unidade_produtivas'])->pluck('id', 'id')->toArray();

        foreach ($checklist_unidade_produtiva_analise as $k => $v) {
            if ($v['unidade_produtiva']) {
                $v['unidade_produtiva']['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');

                if (@!$foundUnidadeProdutiva[$v['unidade_produtiva']['id']]) {
                    $foundUnidadeProdutiva[$v['unidade_produtiva']['id']] = $v['unidade_produtiva']['id'];
                    $data['unidade_produtivas'][] = $v['unidade_produtiva'];
                }
            }
        }
        /**fim do tratamento especial */

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
