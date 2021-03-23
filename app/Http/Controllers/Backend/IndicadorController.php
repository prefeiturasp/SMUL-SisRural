<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_13b_DistribuicaoAtendimentoTecnico;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_15_UnidadeProdutiva_FormularioAplicado;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_1_UnidadeProdutiva;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_2_Produtor;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_3_NovosProdutores;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_4_UpasAtendidas;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_5_AtendimentosRealizados;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_6_TecnicosAtivos;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_7_FormulariosAplicados;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_8_VisitasAplicacoesAtualizacoes;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_1_9_PlanoAcoes_Acoes;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_10_RelacaoPropriedade;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_11_Genero;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_11b_ComunidadeTradicional;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_12_Pessoas;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_13_FontesAgua;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_14_Esgoto;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_15_DestinacaoResiduosSolidos;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_1_UsoSolo;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_2_CertificacaoProducao;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_3_TamanhoUpa;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_4_Infraestrutura;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_5_RegularizacaoAmbiental;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_6_CanalComercializacao;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_7_Associativismo;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_8_RendaAgriculturaFamiliar;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_2_9_RendimentoComercializacao;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_3_3_VisitasAplicacoesAtualizacoesFormulario;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_3_7_PontuacoesFinais;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_3_X_PerguntasFormularios;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_3_X_PerguntasFormulariosPeriodo;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_5_1_CadernoCampo;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_5_2_CadernoCampo_Upas;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_5_5_Caderno_Tecnico;
use App\Http\Controllers\Backend\Traits\Indicadores\Chart_5_X_PerguntasCadernos;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\ChartService;
use App\Services\ReportService;

class IndicadorController extends Controller
{
    use Chart_1_1_UnidadeProdutiva;
    use Chart_1_2_Produtor;
    use Chart_1_3_NovosProdutores;
    use Chart_1_4_UpasAtendidas;
    use Chart_1_5_AtendimentosRealizados;
    use Chart_1_6_TecnicosAtivos;
    use Chart_1_7_FormulariosAplicados;
    use Chart_1_8_VisitasAplicacoesAtualizacoes;
    use Chart_1_9_PlanoAcoes_Acoes;
    use Chart_1_13b_DistribuicaoAtendimentoTecnico;
    use Chart_1_15_UnidadeProdutiva_FormularioAplicado;

    use Chart_2_1_UsoSolo;
    use Chart_2_2_CertificacaoProducao;
    use Chart_2_3_TamanhoUpa;
    use Chart_2_4_Infraestrutura;
    use Chart_2_5_RegularizacaoAmbiental;
    use Chart_2_6_CanalComercializacao;
    use Chart_2_7_Associativismo;
    use Chart_2_8_RendaAgriculturaFamiliar;
    use Chart_2_9_RendimentoComercializacao;

    use Chart_2_10_RelacaoPropriedade;
    use Chart_2_11_Genero;
    use Chart_2_11b_ComunidadeTradicional;
    use Chart_2_12_Pessoas;
    use Chart_2_13_FontesAgua;
    use Chart_2_14_Esgoto;
    use Chart_2_15_DestinacaoResiduosSolidos;

    use Chart_3_X_PerguntasFormularios;
    use Chart_3_X_PerguntasFormulariosPeriodo;
    use Chart_3_3_VisitasAplicacoesAtualizacoesFormulario;
    use Chart_3_7_PontuacoesFinais;

    use Chart_5_1_CadernoCampo;
    use Chart_5_2_CadernoCampo_Upas;
    use Chart_5_X_PerguntasCadernos;
    use Chart_5_5_Caderno_Tecnico;

    private $service;
    private $chartService;

    public function __construct(ReportService $service, ChartService $chartService)
    {
        $this->service = $service;
        $this->chartService = $chartService;
    }

    public function index(Request $request)
    {
        $dataIndicadorGerencial = config('app.endpoint_bi') . 'admin/indicadores/data';
        $dataIndicadoresCadastrais = config('app.endpoint_bi') . 'admin/indicadores/dataIndicadoresCadastrais';
        $dataIndicadoresFormularios = config('app.endpoint_bi') . 'admin/indicadores/dataIndicadoresFormularios';
        $dataIndicadoresPdas = config('app.endpoint_bi') . 'admin/indicadores/dataIndicadoresPdas';
        $dataIndicadoresCadernos = config('app.endpoint_bi') . 'admin/indicadores/dataIndicadoresCadernos';

        $viewFilter = $this->service->viewFilter($dataIndicadorGerencial, true, false, true, true);

        return \Response::view('backend.core.report.indicadores.index', [
            'viewFilter' => $viewFilter,
            'dataIndicadorGerencial' => $dataIndicadorGerencial,
            'dataIndicadoresCadastrais' => $dataIndicadoresCadastrais,
            'dataIndicadoresFormularios' => $dataIndicadoresFormularios,
            'dataIndicadoresPdas' => $dataIndicadoresPdas,
            'dataIndicadoresCadernos' => $dataIndicadoresCadernos
        ]);
    }

    private function checkRangeYear($dt_ini, $dt_end)
    {
        $difDays = (strtotime($dt_end) - strtotime($dt_ini)) / 60 / 60 / 24;

        return $difDays > 366;
    }

    public function data(Request $request)
    {
        if ($this->checkRangeYear($request->get('dt_ini'), $request->get('dt_end'))) {
            return response()->json(['message' => 'O intervalo máximo permitido é de um ano.'], 404);
        }

        $list = [];

        // \DB::enableQueryLog();
        // dd(AppHelper::debugQueryLogTime());

        $list['chart_1_1_unidade_produtiva'] = $this->getChart_1_1_UnidadeProdutiva($request); //ok, gráfico e CSV

        $list['chart_1_2_produtor'] = $this->getChart_1_2_Produtor($request); //ok, gráfico

        //$list['chart_1_3_novos_produtores'] = $this->getChart_1_3_NovosProdutores($request); //não aprovado

        $list['chart_1_4_upas_atendidas'] = $this->getChart_1_4_UpasAtendidas($request); //ok, gráfico

        $list['chart_1_5_atendimentos_realizados'] = $this->getChart_1_5_AtendimentosRealizados($request);

        $list['chart_1_6_tecnicos_ativos'] = $this->getChart_1_6_TecnicosAtivos($request);

        $list['chart_1_7_formularios_aplicados'] =  $this->getChart_1_7_FormulariosAplicados($request);

        $list['chart_1_8_visitas_aplicacoes_atualizacoes'] = $this->getChart_1_8_VisitasAplicacoesAtualizacoes($request);

        $list['chart_1_9_plano_acoes_acoes'] = $this->getChart_1_9_PlanoAcoes_Acoes($request);

        $list['chart_1_13b_distribuicao_atendimento_tecnico'] = $this->getChart_1_13b_DistribuicaoAtendimentoTecnico($request);

        $list['chart_1_15_unidade_produtiva_formulario_aplicado'] = $this->getChart_1_15_UnidadeProdutiva_FormularioAplicado($request);

        $list['chart_5_1_caderno_campo'] = $this->getChart_5_1_CadernoCampo($request);

        // echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";

        return response()->json($list);
    }

    public function dataIndicadoresCadastrais(Request $request)
    {
        if ($this->checkRangeYear($request->get('dt_ini'), $request->get('dt_end'))) {
            return response()->json(['message' => 'O intervalo máximo permitido é de um ano.'], 404);
        }

        $list = [];

        $list['chart_2_1_uso_solo'] = $this->getChart_2_1_UsoSolo($request);

        $list['chart_2_2_certificacao_producao'] = $this->getChart_2_2_CertificacaoProducao($request);

        $list['chart_2_3_tamanho_upa'] = $this->getChart_2_3_TamanhoUpa($request);

        $list['chart_2_4_infraestrutura'] = $this->getChart_2_4_Infraestrutura($request);

        $list['chart_2_5_regularizacao_ambiental'] = $this->getChart_2_5_RegularizacaoAmbiental($request);

        $list['chart_2_6_canal_comercializacao'] = $this->getChart_2_6_CanalComercializacao($request);

        $list['chart_2_7_associativismo'] = $this->getChart_2_7_Associativismo($request);

        $list['chart_2_8_renda_agricula_familiar'] = $this->getChart_2_8_RendaAgriculturaFamiliar($request);

        $list['chart_2_9_rendimento_comercializacao'] = $this->getChart_2_9_RendimentoComercializacao($request);

        $list['chart_2_10_relacao_propriedade'] = $this->getChart_2_10_RelacaoPropriedade($request);

        $list['chart_2_11_genero'] = $this->getChart_2_11_Genero($request);

        $list['chart_2_11b_comunidade_tradicional'] = $this->getChart_2_11b_ComunidadeTradicional($request);

        $list['chart_2_12_pessoas'] = $this->getChart_2_12_Pessoas($request);

        $list['chart_2_13_fontes_agua'] = $this->getChart_2_13_FontesAgua($request);

        $list['chart_2_14_esgoto'] = $this->getChart_2_14_Esgoto($request);

        $list['chart_2_15_destinacao_residuos_solidos'] = $this->getChart_2_15_DestinacaoResiduosSolidos($request);

        return response()->json($list);
    }

    public function dataIndicadoresFormularios(Request $request)
    {
        if ($this->checkRangeYear($request->get('dt_ini'), $request->get('dt_end'))) {
            return response()->json(['message' => 'O intervalo máximo permitido é de um ano.'], 404);
        }

        $flPeriodo = $request->get('fl_periodo');

        //Empty p/ remover os dados da tela
        $list['chart_3_x_perguntas_formularios'] = '';
        $list['chart_3_x_perguntas_formularios_periodo'] = '';

        if (@$flPeriodo == 'true') {
            $list['chart_3_x_perguntas_formularios_periodo'] = $this->getChart_3_X_PerguntasFormulariosPeriodo($request);
        } else {
            $list['chart_3_x_perguntas_formularios'] = $this->getChart_3_X_PerguntasFormularios($request);
        }

        $list['chart_1_15_unidade_produtiva_formulario_aplicado'] = $this->getChart_1_15_UnidadeProdutiva_FormularioAplicado($request);

        $list['chart_3_3_visitas_aplicacoes_atualizacoes_formulario'] = $this->getChart_3_3_VisitasAplicacoesAtualizacoesFormulario($request);

        $list['chart_3_7_pontuacoes_finais'] = $this->getChart_3_7_PontuacoesFinais($request);

        $list['chart_3_1_7_formularios_aplicados'] =  $this->getChart_1_7_FormulariosAplicados($request);

        return response()->json($list);
    }

    public function dataIndicadoresPdas(Request $request)
    {
        if ($this->checkRangeYear($request->get('dt_ini'), $request->get('dt_end'))) {
            return response()->json(['message' => 'O intervalo máximo permitido é de um ano.'], 404);
        }

        $list = [];

        return response()->json($list);
    }

    public function dataIndicadoresCadernos(Request $request)
    {
        if ($this->checkRangeYear($request->get('dt_ini'), $request->get('dt_end'))) {
            return response()->json(['message' => 'O intervalo máximo permitido é de um ano.'], 404);
        }

        $list = [];

        $list['chart_5_1_caderno_campo'] = $this->getChart_5_1_CadernoCampo($request);

        $list['chart_5_2_caderno_campo_upas'] = $this->getChart_5_2_CadernoCampo_Upas($request);

        $list['chart_5_5_caderno_tecnico'] = $this->getChart_5_5_Caderno_Tecnico($request);

        $list['chart_5_x_perguntas_cadernos'] = $this->getChart_5_X_PerguntasCadernos($request);

        return response()->json($list);
    }
}
