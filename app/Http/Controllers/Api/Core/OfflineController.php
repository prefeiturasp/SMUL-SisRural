<?php

namespace App\Http\Controllers\Api\Core;

use App\Enums\PlanoAcaoStatusEnum;
use App\Enums\ChecklistStatusEnum;
use App\Enums\TipoPerguntaEnum;
use App\Helpers\General\AppHelper;
use App\Http\Controllers\Api\Core\Traits\OfflineApiTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineCadernoCampoTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineChecklistTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineDadosGeraisAuthTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineDadosGeraisTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineMethodsTrait;
use App\Http\Controllers\Api\Core\Traits\OfflinePlanoAcaoTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineProdutoresTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineRegioesTrait;
use App\Http\Controllers\Api\Core\Traits\OfflineUnidadeProdutivasTrait;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessChecklistUnidadeProdutivas;
use App\Models\Core\CadernoArquivoModel;
use App\Models\Core\CadernoModel;
use App\Models\Core\CadernoRespostaCadernoModel;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\ChecklistUnidadeProdutivaArquivoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\ColaboradorModel;
use App\Models\Core\InstalacaoModel;
use App\Models\Core\PerguntaModel;
use App\Models\Core\PlanoAcaoHistoricoModel;
use App\Models\Core\PlanoAcaoItemHistoricoModel;
use App\Models\Core\PlanoAcaoItemModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\ProdutorUnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaArquivoModel;
use App\Models\Core\UnidadeProdutivaCanalComercializacaoModel;
use App\Models\Core\UnidadeProdutivaCaracterizacaoModel;
use App\Models\Core\UnidadeProdutivaCertificacaoModel;
use App\Models\Core\UnidadeProdutivaEsgotamentoSanitarioModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaPressaoSocialModel;
use App\Models\Core\UnidadeProdutivaResiduoSolidoModel;
use App\Models\Core\UnidadeProdutivaRespostaArquivoModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use App\Models\Core\UnidadeProdutivaRiscoContaminacaoAguaModel;
use App\Models\Core\UnidadeProdutivaSoloCategoriaModel;
use App\Models\Core\UnidadeProdutivaTipoFonteAguaModel;
use App\Repositories\Backend\Core\CadernoArquivoRepository;
use App\Repositories\Backend\Core\CadernoRepository;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaArquivoRepository;
use App\Repositories\Backend\Core\ChecklistUnidadeProdutivaRepository;
use App\Repositories\Backend\Core\ProdutorRepository;
use App\Repositories\Backend\Core\UnidadeProdutivaArquivoRepository;
use App\Repositories\Backend\Core\UnidadeProdutivaRepository;
use App\Repositories\Backend\Core\UnidadeProdutivaRespostaArquivoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

\Illuminate\Database\Query\Builder::macro(
    'whereUpdatedAt',
    function ($updatedAt) {
        if ($updatedAt) {
            $this->whereRaw(
                $this->from . ".updated_at > ?",
                $updatedAt
            );
        }
    }
);

class OfflineController extends Controller
{
    use OfflineMethodsTrait;
    use OfflineProdutoresTrait;
    use OfflineUnidadeProdutivasTrait;
    use OfflineCadernoCampoTrait;
    use OfflineRegioesTrait;
    use OfflineDadosGeraisTrait;
    use OfflineChecklistTrait;
    use OfflineDadosGeraisAuthTrait;
    use OfflineApiTrait;
    use OfflinePlanoAcaoTrait;

    public function health(Request $request)
    {
        return response()->json([
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }

    private function getIdsAppIds($ret)
    {
        $dynamicIds = [];
        foreach ($ret['success'] as $k => $v) {
            $ids = $ret['ids'][$k];
            $dynamicIds[$ids['appId']] = $ids['dbId'];
        }

        return $dynamicIds;
    }

    private function convertAppIds(&$data, $fkColumn, $idsAppIds)
    {
        foreach ($data as $k => &$v) {
            $newId = @$idsAppIds[$v[$fkColumn]];
            if ($newId) {
                $v[$fkColumn] = $newId;
            }
        }

        return $data;
    }

    private function saveRelatedUnidadeProdutiva($data, $idsAppIds, $model)
    {
        if ($data) {
            $this->convertAppIds($data, 'unidade_produtiva_id', $idsAppIds);

            $ret = $this->simpleUpdateOrCreate($model, $data);
            return $ret;
        }

        return null;
    }

    public function getMessageDuplicatedProdutor($dataApp)
    {
        if (@$dataApp['cpf']) {
            $produtor = @ProdutorModel::where("cpf", $dataApp['cpf'])->first();
            if ($produtor)
                return "O CPF: " . AppHelper::formatCpfCnpj($produtor->cpf) . " já está cadastrado. Nome do produtor/a encontrado: " . $produtor['nome'] . "."; // Código do produtor que contém o CPF informado: " . $produtor->id;
            else
                return 'O produtor/a já está cadastrado.';
        } else {
            return "O produtor/a já está cadastrado. Código do registro: " . $dataApp['id'];
        }
    }

    private function extractErrorId($list)
    {
        $found = array();

        $errorVersion = $list['errorVersion'];
        foreach ($errorVersion as $k => $v) {
            $found[] = $v['db']['id'];
        }

        $error = $list['error'];
        foreach ($error as $k => $v) {
            $found[] = $v['db']['id'];
        }

        return $found;
    }

    private function excludeDataWithParentError($list, $errors, $column)
    {
        foreach ($list as $k => $v) {
            if (in_array($v[$column], $errors)) {
                $v[$column] = null; //inválida o registro
                $list[$k] = $v;
            }
        }

        return $list;
    }

    public function update(Request $request, ProdutorRepository $repositoryProdutor, UnidadeProdutivaRepository $repositoryUnidadeProdutiva, CadernoRepository $repositoryCaderno, ChecklistUnidadeProdutivaRepository $repositoryChecklistUnidadeProdutiva)
    {
        $return = [];

        // Debug Request
        // info($request);

        $produtoresIdsAppIds = [];
        $unidadesProdutivasIdsAppIds = [];

        /**
         * Produtor vs Unidade Produtiva
         */

        $data = $request->only('produtores');
        if (@$data['produtores']) {
            //'O produtor com o CPF: @cpf@ já existe no sistema.'
            $ret = $this->simpleUpdateOrCreate(ProdutorModel::class, $data['produtores'], $repositoryProdutor, array($this, 'getMessageDuplicatedProdutor'));
            $return['produtores'] = $ret;

            $produtoresIdsAppIds = $this->getIdsAppIds($ret);
        }

        $repositoryUnidadeProdutiva->offlineMethod = true;
        $data = $request->only('unidade_produtivas');
        if (@$data['unidade_produtivas']) {
            $ret = $this->simpleUpdateOrCreate(UnidadeProdutivaModel::class, $data['unidade_produtivas'], $repositoryUnidadeProdutiva);
            $return['unidade_produtivas'] = $ret;

            $unidadesProdutivasIdsAppIds = $this->getIdsAppIds($ret);
        }

        $data = $request->only('produtor_unidade_produtiva');
        $produtor_unidade_produtiva = @$data['produtor_unidade_produtiva'];
        if (@$produtor_unidade_produtiva) {
            $this->convertAppIds($produtor_unidade_produtiva, 'produtor_id', $produtoresIdsAppIds);
            $this->convertAppIds($produtor_unidade_produtiva, 'unidade_produtiva_id', $unidadesProdutivasIdsAppIds);

            $ret = $this->simpleUpdateOrCreate(ProdutorUnidadeProdutivaModel::class, $produtor_unidade_produtiva, null, 'A unidade produtiva informada já encontra-se vinculada ao produtor/a.');
            $return['produtor_unidade_produtiva'] = $ret;
        }


        /**
         * Unidade Produtiva vs Relacionamentos
         */

        $return['unidade_produtiva_certificacoes'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_certificacoes, $unidadesProdutivasIdsAppIds, UnidadeProdutivaCertificacaoModel::class);

        $return['unidade_produtiva_pressao_sociais'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_pressao_sociais, $unidadesProdutivasIdsAppIds, UnidadeProdutivaPressaoSocialModel::class);

        $return['unidade_produtiva_canal_comercializacoes'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_canal_comercializacoes, $unidadesProdutivasIdsAppIds, UnidadeProdutivaCanalComercializacaoModel::class);

        $return['unidade_produtiva_tipo_fonte_aguas'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_tipo_fonte_aguas, $unidadesProdutivasIdsAppIds, UnidadeProdutivaTipoFonteAguaModel::class);

        $return['unidade_produtiva_risco_contaminacao_aguas'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_risco_contaminacao_aguas, $unidadesProdutivasIdsAppIds, UnidadeProdutivaRiscoContaminacaoAguaModel::class);

        $return['unidade_produtiva_solo_categorias'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_solo_categorias, $unidadesProdutivasIdsAppIds, UnidadeProdutivaSoloCategoriaModel::class);

        $return['unidade_produtiva_caracterizacoes'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_caracterizacoes, $unidadesProdutivasIdsAppIds, UnidadeProdutivaCaracterizacaoModel::class);

        $return['unidade_produtiva_residuo_solidos'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_residuo_solidos, $unidadesProdutivasIdsAppIds, UnidadeProdutivaResiduoSolidoModel::class);

        $return['unidade_produtiva_esgotamento_sanitarios'] = $this->saveRelatedUnidadeProdutiva($request->unidade_produtiva_esgotamento_sanitarios, $unidadesProdutivasIdsAppIds, UnidadeProdutivaEsgotamentoSanitarioModel::class);

        $return['colaboradores'] = $this->saveRelatedUnidadeProdutiva($request->colaboradores, $unidadesProdutivasIdsAppIds, ColaboradorModel::class);

        $return['instalacoes'] = $this->saveRelatedUnidadeProdutiva($request->instalacoes, $unidadesProdutivasIdsAppIds, InstalacaoModel::class);

        /**
         * Caderno de Campo vs Relacionamentos
         */
        $cadernosIdsAppIds = [];
        $data = $request->only('cadernos');
        if (@$data['cadernos']) {
            $cadernos = $data['cadernos'];

            $this->convertAppIds($cadernos, 'produtor_id', $produtoresIdsAppIds);
            $this->convertAppIds($cadernos, 'unidade_produtiva_id', $unidadesProdutivasIdsAppIds);

            $ret = $this->simpleUpdateOrCreate(CadernoModel::class, $cadernos, $repositoryCaderno);
            $return['cadernos'] = $ret;

            $cadernosIdsAppIds = $this->getIdsAppIds($ret);
        }

        $data = $request->only('caderno_resposta_caderno');
        $caderno_resposta_caderno = @$data['caderno_resposta_caderno'];
        if (@$caderno_resposta_caderno) {
            $this->convertAppIds($caderno_resposta_caderno, 'caderno_id', $cadernosIdsAppIds);

            $ret = $this->simpleUpdateOrCreate(CadernoRespostaCadernoModel::class, $caderno_resposta_caderno);
            $return['caderno_resposta_caderno'] = $ret;
        }

        /**
         * Checklist Aplicado
         */
        $data = $request->only('unidade_produtiva_respostas');
        if (@$data['unidade_produtiva_respostas']) {
            $unidade_produtiva_respostas = $data['unidade_produtiva_respostas'];
            $this->convertAppIds($unidade_produtiva_respostas, 'unidade_produtiva_id', $unidadesProdutivasIdsAppIds);

            //Invalida respostas duplicadas
            /*
            SELECT pergunta_id, unidade_produtiva_id, count(resposta_id) as total
            FROM unidade_produtiva_respostas, perguntas
            where unidade_produtiva_respostas.pergunta_id = perguntas.id and perguntas.tipo_pergunta != 'multipla-escolha'
            group by pergunta_id, unidade_produtiva_id having total > 1
            */
            $perguntasTipo = PerguntaModel::whereNotIn('tipo_pergunta', [TipoPerguntaEnum::MultiplaEscolha])->pluck('id', 'id')->toArray();
            foreach ($unidade_produtiva_respostas as $k => $v) {
                //Só verifica perguntas que sejam de uma escolha / resposta e tenham referencia a um ID
                if (@$perguntasTipo[$v['pergunta_id']]) {
                    $listRespostas = UnidadeProdutivaRespostaModel::where("pergunta_id", $v['pergunta_id'])->where("unidade_produtiva_id", $v['unidade_produtiva_id'])->where('id', '!=', $v['id']);
                    if ($listRespostas->exists()) {
                        $v['deleted_at'] =  Carbon::now();
                        $unidade_produtiva_respostas[$k] = $v;
                    }
                }
            }

            $ret = $this->simpleUpdateOrCreate(UnidadeProdutivaRespostaModel::class, $unidade_produtiva_respostas);
            $return['unidade_produtiva_respostas'] = $ret;
        }

        $data = $request->only('checklist_unidade_produtivas');
        if (@$data['checklist_unidade_produtivas']) {
            $checklist_unidade_produtivas = $data['checklist_unidade_produtivas'];

            $this->convertAppIds($checklist_unidade_produtivas, 'produtor_id', $produtoresIdsAppIds);
            $this->convertAppIds($checklist_unidade_produtivas, 'unidade_produtiva_id', $unidadesProdutivasIdsAppIds);

            //Cancela o registro do PDA Formulário caso já exista algum PDA para o mesmo produtor/unidade produtiva com algum dos status "vigentes"
            foreach ($checklist_unidade_produtivas as $k => $v) {
                $listChecklistRascunho = ChecklistUnidadeProdutivaModel::where("checklist_id", $v['checklist_id'])->where("unidade_produtiva_id", $v['unidade_produtiva_id'])->where("produtor_id", $v['produtor_id'])->where('id', '!=', $v['id'])->whereIn('status', array(ChecklistStatusEnum::Rascunho, ChecklistStatusEnum::AguardandoAprovacao, 'aguardando_pda'));
                if ($listChecklistRascunho->exists()) {
                    $v['deleted_at'] = Carbon::now();
                    $checklist_unidade_produtivas[$k] = $v;
                }
            }

            //Salvar questões já pré processadas (tabela 'unidade_produtiva_respostas'), é aplicado direto no model porque o "finalizar" também replica a lógica do CMS ... o Repository "duplicaria" toda a lógica.
            //$repositoryChecklistUnidadeProdutiva (Não funciona passando o ID fixo, ele sempre cria um, ignorando o ID passado pela API)
            $ret = $this->simpleUpdateOrCreate(ChecklistUnidadeProdutivaModel::class, $checklist_unidade_produtivas, null, 'Registro duplicado.', true);
            $return['checklist_unidade_produtivas'] = $ret;

            $errorsChecklistUnidadeProdutivas = $this->extractErrorId($ret);
        }

        $perguntasTipoAnexo = PerguntaModel::where("tipo_pergunta", "anexo")->pluck('id', 'id')->toArray();

        $data = $request->only('checklist_snapshot_respostas');
        if (@$data['checklist_snapshot_respostas']) {
            //Descarta todos registros que vieram no lote caso não tenha sido processado nenhum Formulário Aplicado
            if (is_null(@$errorsChecklistUnidadeProdutivas)) {
                foreach ($data['checklist_snapshot_respostas'] as $k => $v) {
                    $errorsChecklistUnidadeProdutivas[] = $v['checklist_unidade_produtiva_id'];
                }
                $errorsChecklistUnidadeProdutivas = array_unique($errorsChecklistUnidadeProdutivas);
            }

            $checklist_snapshot_respostas = $this->excludeDataWithParentError($data['checklist_snapshot_respostas'], $errorsChecklistUnidadeProdutivas, 'checklist_unidade_produtiva_id');

            //Força retirada do link no momento do sync
            foreach ($checklist_snapshot_respostas as $k => $v) {
                if (@$perguntasTipoAnexo[$v['pergunta_id']] && @$v['resposta']) {
                    $v['resposta'] = str_replace(\Storage::url(''), "", $v['resposta']);
                    $checklist_snapshot_respostas[$k] = $v;
                }
            }

            $ret = $this->simpleUpdateOrCreate(ChecklistSnapshotRespostaModel::class, $checklist_snapshot_respostas, null, -1);

            $return['checklist_snapshot_respostas'] = $ret;
        }

        if (@$checklist_snapshot_respostas) {
            $list_unidades = @collect($checklist_snapshot_respostas)->map(function($v) {
                return $v['checklist_unidade_produtiva_id'];
            })->unique()->toArray();

            foreach ($list_unidades as $v) {
                ProcessChecklistUnidadeProdutivas::dispatch($v);
            }
        }

        /**
         * PDA individual ou checklist
         * Tabelas manipuladas: plano_acoes, plano_acao_itens, plano_acao_item_historicos, plano_acao_historicos
         */
        $data = $request->only('plano_acoes');
        if (@$data['plano_acoes']) {
            $plano_acoes = $data['plano_acoes'];

            $this->convertAppIds($plano_acoes, 'produtor_id', $produtoresIdsAppIds);
            $this->convertAppIds($plano_acoes, 'unidade_produtiva_id', $unidadesProdutivasIdsAppIds);

            foreach ($plano_acoes as $k => $v) {
                //Cancela o registro do PDA Formulário caso já exista algum PDA para o mesmo produtor/unidade produtiva com algum dos status "vigentes"
                if ($v['checklist_unidade_produtiva_id']) {
                    $listPdasVigente = PlanoAcaoModel::where("checklist_unidade_produtiva_id", $v['checklist_unidade_produtiva_id'])->where("unidade_produtiva_id", $v['unidade_produtiva_id'])->where("produtor_id", $v['produtor_id'])->where('id', '!=', $v['id'])->whereIn('status', array(PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::AguardandoAprovacao, PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento));
                    if ($listPdasVigente->exists()) {
                        $v['deleted_at'] = Carbon::now();
                        $plano_acoes[$k] = $v;
                    }
                }

                //Cancela o registro do PDA Individual caso já exista algum PDA para o mesmo produtor/unidade produtiva com algum dos status "vigentes"
                if (!$v['checklist_unidade_produtiva_id'] && !$v['fl_coletivo']) {
                    $listPdasVigente = PlanoAcaoModel::where('fl_coletivo', 0)->where('checklist_unidade_produtiva_id', null)->where("unidade_produtiva_id", $v['unidade_produtiva_id'])->where("produtor_id", $v['produtor_id'])->where('id', '!=', $v['id'])->whereIn('status', array(PlanoAcaoStatusEnum::Rascunho, PlanoAcaoStatusEnum::AguardandoAprovacao, PlanoAcaoStatusEnum::NaoIniciado, PlanoAcaoStatusEnum::EmAndamento));
                    if ($listPdasVigente->exists()) {
                        $v['deleted_at'] = Carbon::now();
                        $plano_acoes[$k] = $v;
                    }
                }
            }

            $ret = $this->simpleUpdateOrCreate(PlanoAcaoModel::class, $plano_acoes);
            $return['plano_acoes'] = $ret;
        }

        $data = $request->only('plano_acao_itens');
        if (@$data['plano_acao_itens']) {
            $plano_acao_itens = $data['plano_acao_itens'];

            $ret = $this->simpleUpdateOrCreate(PlanoAcaoItemModel::class, $plano_acao_itens);
            $return['plano_acao_itens'] = $ret;
        }

        $data = $request->only('plano_acao_item_historicos');
        if (@$data['plano_acao_item_historicos']) {
            $plano_acao_item_historicos = $data['plano_acao_item_historicos'];

            $ret = $this->simpleUpdateOrCreate(PlanoAcaoItemHistoricoModel::class, $plano_acao_item_historicos);
            $return['plano_acao_item_historicos'] = $ret;
        }

        $data = $request->only('plano_acao_historicos');
        if (@$data['plano_acao_historicos']) {
            $plano_acao_historicos = $data['plano_acao_historicos'];

            $ret = $this->simpleUpdateOrCreate(PlanoAcaoHistoricoModel::class, $plano_acao_historicos);
            $return['plano_acao_historicos'] = $ret;
        }

        return response()->json($return);
    }

    public function migrationsV2(Request $request)
    {

        $migrations = [];

        $produtores = ['produtores', 'assistencia_tecnica_tipos', 'generos', 'etinias', 'produtor_unidade_produtiva', 'grau_instrucoes', 'rendimento_comercializacoes', 'renda_agriculturas'];

        $unidadesProdutivas = [
            'unidade_produtivas', 'tipo_posses', 'unidade_produtiva_canal_comercializacoes',
            'unidade_produtiva_caracterizacoes', 'unidade_produtiva_risco_contaminacao_aguas', 'unidade_produtiva_tipo_fonte_aguas', 'canal_comercializacoes',
            'solo_categorias', 'risco_contaminacao_aguas', 'tipo_fonte_aguas', 'relacoes', 'instalacao_tipos', 'colaboradores', 'instalacoes',
            'dedicacoes', 'outorgas',
            'unidade_produtiva_solo_categorias',
            'certificacoes', 'pressao_sociais', 'unidade_produtiva_pressao_sociais', 'unidade_produtiva_certificacoes', 'unidade_produtiva_arquivos',
            'esgotamento_sanitarios', 'residuo_solidos', 'unidade_produtiva_esgotamento_sanitarios', 'unidade_produtiva_residuo_solidos'
        ];

        $unidadesOperacionais = ['unidade_operacionais'];

        $cadernos = ['users', 'cadernos', 'template_pergunta_templates', 'template_perguntas', 'template_respostas', 'templates', 'caderno_resposta_caderno', 'caderno_arquivos'];

        $regiao = ['estados', 'cidades'];

        $dadosGerais = ['termos_de_usos', 'dominios'];

        $checklist = ['perguntas', 'respostas', 'checklists', 'checklist_categorias', 'checklist_perguntas', 'checklist_unidade_produtivas', 'unidade_produtiva_respostas', 'checklist_snapshot_respostas', 'checklist_pergunta_respostas', 'unidade_produtiva_resposta_arquivos', 'checklist_aprovacao_logs', 'checklist_unidade_produtiva_arquivos'];

        $pda = ['plano_acao_historicos', 'plano_acao_item_historicos', 'plano_acao_itens', 'plano_acoes'];

        $tables = array_merge($produtores, $unidadesProdutivas, $unidadesOperacionais, $cadernos, $regiao, $dadosGerais, $checklist, $pda);

        // $total = 0;
        // foreach ($tables as $table) {
        //     $total += \DB::table($table)->count();
        // }
        // echo ($total);
        // die();

        foreach ($tables as $k => $table) {
            $columns = $this->getCreateTableV2($table);
            $hash = md5(json_encode($columns));
            $migrations[] = ['table' => $table, 'hash' => $hash, 'columns' => $columns];
        }

        return response()->json([
            'migrations' => $migrations,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }

    /**
     * Migrations
     */
    public function migrations(Request $request)
    {
        die('deprecated!');

        $migrations = [];

        $produtores = ['produtores', 'assistencia_tecnica_tipos', 'generos', 'etinias', 'produtor_unidade_produtiva'];

        $unidadesProdutivas = [
            'unidade_produtivas', 'tipo_posses', 'unidade_produtiva_canal_comercializacoes',
            'unidade_produtiva_caracterizacoes', 'unidade_produtiva_risco_contaminacao_aguas', 'unidade_produtiva_tipo_fonte_aguas', 'canal_comercializacoes',
            'solo_categorias', 'risco_contaminacao_aguas', 'tipo_fonte_aguas', 'relacoes', 'instalacao_tipos', 'colaboradores', 'instalacoes',
            'dedicacoes', 'outorgas',
            'unidade_produtiva_solo_categorias',
            'certificacoes', 'pressao_sociais', 'unidade_produtiva_pressao_sociais', 'unidade_produtiva_certificacoes', 'unidade_produtiva_arquivos'
        ];

        $unidadesOperacionais = ['unidade_operacionais'];

        $cadernos = ['users', 'cadernos', 'template_pergunta_templates', 'template_perguntas', 'template_respostas', 'templates', 'caderno_resposta_caderno', 'caderno_arquivos'];

        $regiao = ['estados', 'cidades'];

        $dadosGerais = ['termos_de_usos', 'dominios'];

        $tables = array_merge($produtores, $unidadesProdutivas, $unidadesOperacionais, $cadernos, $regiao, $dadosGerais);

        foreach ($tables as $k => $v) {
            $migrations[] = ['table' => $v, 'migration' => $this->getCreateTable($v)];
        }

        return response()->json([
            'migrations' => $migrations,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }

    public function fileUpload(
        Request $request,
        string $table,
        CadernoArquivoRepository $repositoryCadernoArquivo,
        UnidadeProdutivaArquivoRepository $repositoryUnidadeProdutivaArquivo,
        UnidadeProdutivaRespostaArquivoRepository $repositoryUnidadeProdutivaRespostaArquivo,
        ChecklistUnidadeProdutivaArquivoRepository $repositoryChecklistUnidadeProdutivaArquivo
    ) {
        // Debug Request
        // info($request);
        $file = $request->file('app_arquivo_caminho');

        $all = $request->all();

        if (@$all['data']) {
            $data = json_decode($all['data'], true);
            unset($data['arquivo']);

            $ret = [];

            switch ($table) {
                case ('caderno_arquivos'):
                    $ret = $this->simpleUpdateOrCreate(CadernoArquivoModel::class, [$data]);

                    if ($file && count($ret['success']) > 0) {
                        $repositoryCadernoArquivo->upload($file, CadernoArquivoModel::where('id', $data['id'])->first());
                    }

                    break;

                case ('unidade_produtiva_arquivos'):
                    $ret = $this->simpleUpdateOrCreate(UnidadeProdutivaArquivoModel::class, [$data]);

                    if ($file && count($ret['success']) > 0) {
                        $repositoryUnidadeProdutivaArquivo->upload($file, UnidadeProdutivaArquivoModel::where('id', $data['id'])->first());
                    }

                    break;

                case ('unidade_produtiva_resposta_arquivos'):
                    $ret = $this->simpleUpdateOrCreate(UnidadeProdutivaRespostaArquivoModel::class, [$data]);

                    if ($file && count($ret['success']) > 0) {
                        $repositoryUnidadeProdutivaRespostaArquivo->upload($file, UnidadeProdutivaRespostaArquivoModel::where('id', $data['id'])->first());
                    }

                    break;

                case ('checklist_unidade_produtiva_arquivos'):
                    $ret = $this->simpleUpdateOrCreate(ChecklistUnidadeProdutivaArquivoModel::class, [$data]);

                    if ($file && count($ret['success']) > 0) {
                        $repositoryChecklistUnidadeProdutivaArquivo->upload($file, ChecklistUnidadeProdutivaArquivoModel::where('id', $data['id'])->first());
                    }

                    break;
            }

            $return[$table] = $ret;
        } else {
            $return[$table] = [
                'error' => ['app' => null, 'db' => null, 'message' => 'Não foi possível publicar o arquivo enviado, tente novamente.'],
                'errorVersion' => [],
                'success' => [],
                'ids' => []
            ];
        }

        // info($return);

        return response()->json($return);
    }

    public function testUpload(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            throw new \Exception('Errorrrr');
        }

        $filename = $file->getClientOriginalName();

        $path = 'temp/' . $filename;
        // \Storage::put($path, file_get_contents($file->getRealPath()));
        \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

        // $path = $file->storeAs('temp', $filename, ['disk' => 'public']);

        return response()->json([
            'path' => $path,
            'success' => 1,
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
