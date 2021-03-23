<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Auth\User;
use App\Models\Core\AssistenciaTecnicaTipoModel;
use App\Models\Core\CanalComercializacaoModel;
use App\Models\Core\CertificacaoModel;
use App\Models\Core\DedicacaoModel;
use App\Models\Core\DominioModel;
use App\Models\Core\EsgotamentoSanitarioModel;
use App\Models\Core\EtiniaModel;
use App\Models\Core\GeneroModel;
use App\Models\Core\GrauInstrucaoModel;
use App\Models\Core\InstalacaoTipoModel;
use App\Models\Core\OutorgaModel;
use App\Models\Core\PressaoSocialModel;
use App\Models\Core\RelacaoModel;
use App\Models\Core\RendaAgriculturaModel;
use App\Models\Core\RendimentoComercializacaoModel;
use App\Models\Core\ResiduoSolidoModel;
use App\Models\Core\RiscoContaminacaoAguaModel;
use App\Models\Core\SoloCategoriaModel;
use App\Models\Core\TermosDeUsoModel;
use App\Models\Core\TipoFonteAguaModel;
use App\Models\Core\TipoPosseModel;
use App\Models\Core\Traits\Scope\DominioPermissionScope;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait OfflineDadosGeraisTrait
{

    public function dadosGerais(Request $request)
    {
        $data = [];

        $data['termos_de_usos'] = TermosDeUsoModel::whereUpdatedAt($request->input('updated_at_termos_de_usos'))->get()->toArray();
        $data['dominios'] = DominioModel::withoutGlobalScope(DominioPermissionScope::class)->whereUpdatedAt($request->input('updated_at_dominios'))->get()->toArray();


        /**
         * Usuários
         */
        $data['users'] = User::withTrashed()->whereUpdatedAt($request->input('updated_at_users'))->get()->map(function ($item) {
            return $item->only('id', 'first_name', 'last_name', 'email', 'uuid', 'document', 'created_at', 'updated_at');
        });


        /**
         * Dados Gerais
         */
        $data['assistencia_tecnica_tipos'] = AssistenciaTecnicaTipoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_assistencia_tecnica_tipos'))->get()->toArray();

        $data['generos'] = GeneroModel::withTrashed()->whereUpdatedAt($request->input('updated_at_generos'))->get()->toArray();

        $data['etinias'] = EtiniaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_etinias'))->get()->toArray();

        $data['tipo_posses'] = TipoPosseModel::withTrashed()->whereUpdatedAt($request->input('updated_at_tipo_posses'))->get()->toArray();

        $data['canal_comercializacoes'] = CanalComercializacaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_canal_comercializacoes'))->get()->toArray();

        $data['solo_categorias'] = SoloCategoriaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_solo_categorias'))->get()->toArray();

        $data['risco_contaminacao_aguas'] = RiscoContaminacaoAguaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_risco_contaminacao_aguas'))->get()->toArray();

        $data['tipo_fonte_aguas'] = TipoFonteAguaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_tipo_fonte_aguas'))->get()->toArray();

        $data['relacoes'] = RelacaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_relacoes'))->get()->toArray();

        $data['dedicacoes'] = DedicacaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_dedicacoes'))->get()->toArray();

        $data['outorgas'] = OutorgaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_outorgas'))->get()->toArray();

        $data['instalacao_tipos'] = InstalacaoTipoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_instalacao_tipos'))->get()->toArray();

        $data['certificacoes'] = CertificacaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_certificacoes'))->get()->toArray();

        $data['pressao_sociais'] = PressaoSocialModel::withTrashed()->whereUpdatedAt($request->input('updated_at_pressao_sociais'))->get()->toArray();

        $data['grau_instrucoes'] = GrauInstrucaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_grau_instrucoes'))->get()->toArray();

        $data['rendimento_comercializacoes'] = RendimentoComercializacaoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_rendimento_comercializacoes'))->get()->toArray();

        $data['renda_agriculturas'] = RendaAgriculturaModel::withTrashed()->whereUpdatedAt($request->input('updated_at_renda_agriculturas'))->get()->toArray();

        $data['esgotamento_sanitarios'] = EsgotamentoSanitarioModel::withTrashed()->whereUpdatedAt($request->input('updated_at_esgotamento_sanitarios'))->get()->toArray();

        $data['residuo_solidos'] = ResiduoSolidoModel::withTrashed()->whereUpdatedAt($request->input('updated_at_residuo_solidos'))->get()->toArray();


        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
