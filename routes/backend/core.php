<?php

use App\Http\Controllers\Backend\CadernoController;
use App\Http\Controllers\Backend\ChecklistAprovacaoLogsController;
use App\Http\Controllers\Backend\ChecklistController;
use App\Http\Controllers\Backend\ChecklistUnidadeProdutivaController;
use App\Http\Controllers\Backend\DadoController;
use App\Http\Controllers\Backend\DominioController;
use App\Http\Controllers\Backend\GeoTestController;
use App\Http\Controllers\Backend\PerguntasController;
use App\Http\Controllers\Backend\ImportadorController;
use App\Http\Controllers\Backend\IndicadorController;
use App\Http\Controllers\Backend\LogsController;
use App\Http\Controllers\Backend\MapaController;
use App\Http\Controllers\Backend\NovoProdutorUnidadeProdutivaController;
use App\Http\Controllers\Backend\PlanoAcaoColetivoController;
use App\Http\Controllers\Backend\PlanoAcaoController;
use App\Http\Controllers\Backend\ProdutorController;
use App\Http\Controllers\Backend\UnidadeOperacionalController;
use App\Http\Controllers\Backend\UnidadeProdutivaController;
use App\Http\Controllers\Backend\TemplatePerguntasController;
use App\Http\Controllers\Backend\TemplateCadernoController;
use App\Http\Controllers\Backend\RegiaoController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\SobreController;
use App\Http\Controllers\Backend\SoloCategoriaController;
use App\Http\Controllers\Backend\TermosDeUsoController;

Route::middleware(['permission_report_restrict'])->group(function () {
    Route::group([
        'prefix' => '',
        'as' => 'core.',
        'namespace' => 'Core',
    ], function () {
        Route::group(['prefix' => 'geo', 'as' => 'geo.'], function () {
            Route::get('/checkCidadesDentroEstados', [GeoTestController::class, 'checkCidadesDentroEstados'])->name('checkCidadesDentroEstados');
            Route::get('/checkCidadesCenterPoint', [GeoTestController::class, 'checkCidadesCenterPoint'])->name('checkCidadesCenterPoint');
            Route::get('/checkEstados', [GeoTestController::class, 'checkEstados'])->name('checkEstados');
        });

        /**
         * Dominios
         */
        Route::group(['prefix' => 'dominio', 'as' => 'dominio.'], function () {
            Route::get('/datatable', [DominioController::class, 'datatable'])->middleware('permission:view menu domains')->name('datatable');

            Route::get('/', [DominioController::class, 'index'])->middleware('permission:view menu domains')->name('index');
            Route::get('/create', [DominioController::class, 'create'])->middleware('can:create,App\Models\Core\DominioModel')->name('create');
            Route::post('/', [DominioController::class, 'store'])->middleware('can:create,App\Models\Core\DominioModel')->name('store');
            Route::get('/{dominio}/edit', [DominioController::class, 'edit'])->middleware('can:view,dominio')->name('edit');
            Route::patch('/{dominio}', [DominioController::class, 'update'])->middleware('can:update,dominio')->name('update');
            Route::delete('/{dominio}', [DominioController::class, 'destroy'])->middleware('can:delete,dominio')->name('destroy');
        });

        /**
         * Novo Produtor / Unidade Produtiva
         */
        Route::group(['prefix' => 'novo_produtor_unidade_produtiva', 'as' => 'novo_produtor_unidade_produtiva.'], function () {
            Route::get('/create', [NovoProdutorUnidadeProdutivaController::class, 'create'])->middleware('can:create,App\Models\Core\ProdutorModel')->name('create');
            Route::post('/', [NovoProdutorUnidadeProdutivaController::class, 'store'])->middleware('can:create,App\Models\Core\ProdutorModel')->name('store');

            Route::get('/{produtor}/{unidadeProdutiva}/produtor_edit', [ProdutorController::class, 'edit'])->middleware('can:update,produtor')->name('produtor_edit');
            Route::patch('/produtor/{produtor}/{unidadeProdutiva}', [ProdutorController::class, 'update'])->middleware('can:update,produtor')->name('produtor_update');

            Route::get('/{produtor}/{unidadeProdutiva}/unidade_produtiva_edit', [UnidadeProdutivaController::class, 'edit'])->middleware('can:update,produtor')->name('unidade_produtiva_edit');
            Route::patch('/unidade_produtiva/{produtor}/{unidadeProdutiva}', [UnidadeProdutivaController::class, 'update'])->middleware('can:update,produtor')->name('unidade_produtiva_update');
        });

        /**
         * Produtor
         */
        Route::group(['prefix' => 'produtor', 'as' => 'produtor.'], function () {
            Route::get('/{produtor}/view', [ProdutorController::class, 'view'])->middleware('permission:view menu farmers')->name('view');

            Route::get('/{produtor}/dashboard', [ProdutorController::class, 'dashboard'])->middleware('permission:view menu farmers')->name('dashboard');

            Route::get('/datatable/{dashboard?}', [ProdutorController::class, 'datatable'])->middleware('permission:view menu farmers')->name('datatable');

            Route::get('/datatableUnidadeProdutiva/{produtor}', [ProdutorController::class, 'datatableUnidadeProdutiva'])->name('datatable.unidade_produtiva');
            Route::get('/add-unidade-produtiva/{produtor}', [ProdutorController::class, 'addUnidadeProdutiva'])->name('add-unidade-produtiva');
            Route::get('/edit-unidade-produtiva/{produtor}/{pivot}', [ProdutorController::class, 'editUnidadeProdutiva'])->name('edit-unidade-produtiva');
            Route::get('/search-unidade-produtiva/{produtor}', [ProdutorController::class, 'searchUnidadeProdutiva'])->name('search-unidade-produtiva');
            Route::post('/store-unidade-produtiva/{produtor}', [ProdutorController::class, 'storeUnidadeProdutiva'])->name('store-unidade-produtiva');
            Route::post('/update-unidade-produtiva/{produtor}/{pivot}', [ProdutorController::class, 'updateUnidadeProdutiva'])->name('update-unidade-produtiva');
            Route::delete('/delete-unidade-produtiva/{produtor}/{pivot}', [ProdutorController::class, 'deleteUnidadeProdutiva'])->name('delete-unidade-produtiva');

            Route::get('/search-unidade-produtiva-redirect', [ProdutorController::class, 'searchUnidadeProdutivaRedirect'])->name('search-unidade-produtiva-redirect');

            Route::get('/', [ProdutorController::class, 'index'])->middleware('permission:view menu farmers')->name('index');
            Route::get('/create', [ProdutorController::class, 'create'])->middleware('can:create,App\Models\Core\ProdutorModel')->name('create');
            Route::post('/', [ProdutorController::class, 'store'])->middleware('can:create,App\Models\Core\ProdutorModel')->name('store');
            Route::get('/{produtor}/edit', [ProdutorController::class, 'edit'])->middleware('can:update,produtor')->name('edit');
            Route::patch('/{produtor}', [ProdutorController::class, 'update'])->middleware('can:update,produtor')->name('update');
            Route::delete('/{produtor}', [ProdutorController::class, 'destroy'])->middleware('can:delete,produtor')->name('destroy');

            Route::get('/sem_unidade', [ProdutorController::class, 'indexSemUnidade'])->middleware('permission:view menu farmers')->name('index_sem_unidade');
            Route::get('/datatable_sem_unidade', [ProdutorController::class, 'datatableSemUnidade'])->middleware('permission:view menu farmers')->name('datatable_sem_unidade');
            Route::get('/{produtorSemUnidade}/edit_sem_unidade', [ProdutorController::class, 'editSemUnidade'])->name('edit_sem_unidade');
            Route::patch('/{produtorSemUnidade}/update_sem_unidade', [ProdutorController::class, 'updateSemUnidade'])->name('update_sem_unidade');
        });


        /**
         * UnidadeProdutiva
         */
        Route::group(['prefix' => 'unidade_produtiva', 'as' => 'unidade_produtiva.'], function () {
            Route::get('/{unidadeProdutiva}/view', [UnidadeProdutivaController::class, 'view'])->middleware('permission:view menu productive units')->name('view');

            Route::get('produtor', [UnidadeProdutivaController::class, 'produtor'])->middleware('permission:view menu productive units')->name('produtor');
            Route::get('/datatableProdutor', [UnidadeProdutivaController::class, 'datatableProdutor'])->middleware('permission:view menu productive units')->name('datatableProdutor');

            Route::get('/datatable/{produtor?}', [UnidadeProdutivaController::class, 'datatable'])->middleware('permission:view menu productive units')->name('datatable');
            Route::get('/create/{produtor}', [UnidadeProdutivaController::class, 'create'])->middleware('can:create,App\Models\Core\UnidadeProdutivaModel')->name('create');
            Route::post('/', [UnidadeProdutivaController::class, 'store'])->middleware('can:create,App\Models\Core\UnidadeProdutivaModel')->name('store');
            Route::get('/{unidadeProdutiva}/edit', [UnidadeProdutivaController::class, 'edit'])->middleware('can:update,unidadeProdutiva')->name('edit');
            Route::patch('/{unidadeProdutiva}', [UnidadeProdutivaController::class, 'update'])->middleware('can:update,unidadeProdutiva')->name('update');
            Route::delete('/{unidadeProdutiva}', [UnidadeProdutivaController::class, 'destroy'])->middleware('can:delete,unidadeProdutiva')->name('destroy');

            Route::get('/invalidas', [UnidadeProdutivaController::class, 'indexInvalid'])->middleware('permission:view menu productive units')->name('invalid');
            Route::get('/datatableInvalid', [UnidadeProdutivaController::class, 'datatableInvalid'])->middleware('permission:view menu productive units')->name('datatableInvalid');

            Route::get('/{produtor?}', [UnidadeProdutivaController::class, 'index'])->middleware('permission:view menu productive units')->name('index');

            /**
             * Pessoas / Colaborador vinculados a Unid. Prod.
             */
            Route::group(['prefix' => '/{unidadeProdutiva}/colaboradores', 'as' => 'colaboradores.'], function () {
                Route::get('/', [UnidadeProdutivaController::class, 'colaboradoresIndex'])->name('index');
                Route::get('/datatable', [UnidadeProdutivaController::class, 'colaboradoresDatatable'])->name('datatable');
                Route::get('/create', [UnidadeProdutivaController::class, 'colaboradoresCreate'])->name('create');
                Route::get('/edit/{colaborador}', [UnidadeProdutivaController::class, 'colaboradoresEdit'])->name('edit');

                Route::post('/store', [UnidadeProdutivaController::class, 'colaboradoresStore'])->name('store');
                Route::post('/update/{colaborador}', [UnidadeProdutivaController::class, 'colaboradoresUpdate'])->name('update');
                Route::delete('/delete/{colaborador}', [UnidadeProdutivaController::class, 'colaboradoresDestroy'])->name('destroy');
            });

            /**
             * Infra-estrutura / Instalações vinculados a Unid. Prod.
             */
            Route::group(['prefix' => '/{unidadeProdutiva}/instalacoes', 'as' => 'instalacoes.'], function () {
                Route::get('/', [UnidadeProdutivaController::class, 'instalacoesIndex'])->name('index');
                Route::get('/datatable', [UnidadeProdutivaController::class, 'instalacoesDatatable'])->name('datatable');
                Route::get('/create', [UnidadeProdutivaController::class, 'instalacoesCreate'])->name('create');
                Route::get('/edit/{instalacao}', [UnidadeProdutivaController::class, 'instalacoesEdit'])->name('edit');

                Route::post('/store', [UnidadeProdutivaController::class, 'instalacoesStore'])->name('store');
                Route::post('/update/{instalacao}', [UnidadeProdutivaController::class, 'instalacoesUpdate'])->name('update');
                Route::delete('/delete/{instalacao}', [UnidadeProdutivaController::class, 'instalacoesDestroy'])->name('destroy');
            });

            /**
             * Uso do Solo vinculados a Unid. Prod.
             */
            Route::group(['prefix' => '/{unidadeProdutiva}/caracterizacoes', 'as' => 'caracterizacoes.'], function () {
                Route::get('/', [UnidadeProdutivaController::class, 'caracterizacoesIndex'])->name('index');
                Route::get('/datatable', [UnidadeProdutivaController::class, 'caracterizacoesDatatable'])->name('datatable');
                Route::get('/create', [UnidadeProdutivaController::class, 'caracterizacoesCreate'])->name('create');
                Route::get('/edit/{unidadeProdutivaCaracterizacao}', [UnidadeProdutivaController::class, 'caracterizacoesEdit'])->name('edit');

                Route::post('/store', [UnidadeProdutivaController::class, 'caracterizacoesStore'])->name('store');
                Route::post('/update/{unidadeProdutivaCaracterizacao}', [UnidadeProdutivaController::class, 'caracterizacoesUpdate'])->name('update');
                Route::delete('/delete/{unidadeProdutivaCaracterizacao}', [UnidadeProdutivaController::class, 'caracterizacoesDestroy'])->name('destroy');
            });

            /**
             * Arquivos vinculados a Unid. Prod.
             */
            Route::group(['prefix' => '/{unidadeProdutiva}/arquivos', 'as' => 'arquivos.'], function () {
                Route::get('/', [UnidadeProdutivaController::class, 'arquivosIndex'])->name('index');
                Route::get('/datatable', [UnidadeProdutivaController::class, 'arquivosDatatable'])->name('datatable');
                Route::get('/create', [UnidadeProdutivaController::class, 'arquivosCreate'])->name('create');
                Route::post('/store', [UnidadeProdutivaController::class, 'arquivosStore'])->name('store');
                Route::delete('/delete/{arquivo}', [UnidadeProdutivaController::class, 'arquivosDestroy'])->name('destroy');
                Route::get('/edit/{arquivo}', [UnidadeProdutivaController::class, 'arquivosEdit'])->name('edit');
                Route::post('/update/{arquivo}', [UnidadeProdutivaController::class, 'arquivosUpdate'])->name('update');
            });
        });

        /**
         * Unidades Operacionais
         */
        Route::group(['prefix' => 'unidade_operacional', 'as' => 'unidade_operacional.'], function () {
            Route::get('/', [UnidadeOperacionalController::class, 'index'])->name('index');
            Route::get('/datatable', [UnidadeOperacionalController::class, 'datatable'])->name('datatable');
            Route::get('/create', [UnidadeOperacionalController::class, 'create'])->name('create');
            Route::get('/{unidadeOperacional}/edit', [UnidadeOperacionalController::class, 'edit'])->name('edit');
            Route::delete('/{unidadeOperacional}', [UnidadeOperacionalController::class, 'destroy'])->name('destroy');
            Route::post('/', [UnidadeOperacionalController::class, 'store'])->name('store');
            Route::patch('/{unidadeOperacional}', [UnidadeOperacionalController::class, 'update'])->name('update');
        });


        /**
         * Perguntas (caderno de campo)
         */
        Route::group(['prefix' => 'template_perguntas', 'as' => 'template_perguntas.'], function () {
            Route::get('/', [TemplatePerguntasController::class, 'index'])->name('index');
            Route::get('/datatable', [TemplatePerguntasController::class, 'datatable'])->name('datatable');
            Route::get('/create', [TemplatePerguntasController::class, 'create'])->name('create');
            Route::get('/{templatePergunta}/edit', [TemplatePerguntasController::class, 'edit'])->name('edit');
            Route::delete('/{templatePergunta}', [TemplatePerguntasController::class, 'destroy'])->name('destroy');
            Route::post('/', [TemplatePerguntasController::class, 'store'])->name('store');
            Route::patch('/{templatePergunta}', [TemplatePerguntasController::class, 'update'])->name('update');

            /**
             * Respostas das perguntas (caderno de campo)
             */
            Route::group(['prefix' => '/{templatePergunta}/respostas', 'as' => 'respostas.'], function () {
                Route::get('/', [TemplatePerguntasController::class, 'respostasIndex'])->name('index');
                Route::get('/datatable', [TemplatePerguntasController::class, 'respostasDatatable'])->name('datatable');
                Route::get('/create', [TemplatePerguntasController::class, 'respostasCreate'])->name('create');
                Route::get('/edit/{templateResposta}', [TemplatePerguntasController::class, 'respostasEdit'])->name('edit');

                Route::post('/store', [TemplatePerguntasController::class, 'respostasStore'])->name('store');
                Route::post('/update/{templateResposta}', [TemplatePerguntasController::class, 'respostasUpdate'])->name('update');
                Route::delete('/delete/{templateResposta}', [TemplatePerguntasController::class, 'respostasDestroy'])->name('destroy');

                Route::get('/moveOrderUp/{templateResposta}', [TemplatePerguntasController::class, 'respostasMoveOrderUp'])->name('moveOrderUp');
                Route::get('/moveOrderDown/{templateResposta}', [TemplatePerguntasController::class, 'respostasMoveOrderDown'])->name('moveOrderDown');
                Route::get('/moveOrderTop/{templateResposta}', [TemplatePerguntasController::class, 'respostasMoveOrderTop'])->name('moveOrderTop');
                Route::get('/moveOrderBack/{templateResposta}', [TemplatePerguntasController::class, 'respostasMoveOrderBack'])->name('moveOrderBack');
            });
        });

        /**
         * Template (caderno de campo)
         */
        Route::group(['prefix' => 'templates_caderno', 'as' => 'templates_caderno.'], function () {
            Route::get('/', [TemplateCadernoController::class, 'index'])->middleware('permission:view menu caderno base')->name('index');
            Route::get('/datatable', [TemplateCadernoController::class, 'datatable'])->middleware('permission:view menu caderno base')->name('datatable');
            Route::get('/create', [TemplateCadernoController::class, 'create'])->middleware('can:create,App\Models\Core\TemplateModel')->name('create');
            Route::get('/{template}/edit', [TemplateCadernoController::class, 'edit'])->middleware('can:view,template')->name('edit');
            Route::delete('/{template}', [TemplateCadernoController::class, 'destroy'])->middleware('can:delete,template')->name('destroy');
            Route::post('/', [TemplateCadernoController::class, 'store'])->middleware('can:create,App\Models\Core\TemplateModel')->name('store');
            Route::patch('/{template}', [TemplateCadernoController::class, 'update'])->middleware('can:update,template')->name('update');

            /**
             * Perguntas vinculadas ao caderno de campo
             */
            Route::group(['prefix' => '/{template}/perguntas', 'as' => 'perguntas.'], function () {
                Route::get('/', [TemplateCadernoController::class, 'perguntasIndex'])->name('index');
                Route::get('/datatable', [TemplateCadernoController::class, 'perguntasDatatable'])->name('datatable');
                Route::get('/create', [TemplateCadernoController::class, 'perguntasCreate'])->name('create');

                Route::get('/store/{templatePergunta}', [TemplateCadernoController::class, 'perguntasStore'])->name('store');

                Route::get('/todasPerguntasDatatable', [TemplateCadernoController::class, 'todasPerguntasDatatable'])->name('todasPerguntasDatatable');
                Route::delete('/delete/{templatePerguntaTemplates}', [TemplateCadernoController::class, 'perguntasDestroy'])->name('destroy');

                Route::get('/moveOrderUp/{templatePerguntaTemplates}', [TemplateCadernoController::class, 'perguntasMoveOrderUp'])->name('moveOrderUp');
                Route::get('/moveOrderDown/{templatePerguntaTemplates}', [TemplateCadernoController::class, 'perguntasMoveOrderDown'])->name('moveOrderDown');
                Route::get('/moveOrderTop/{templatePerguntaTemplates}', [TemplateCadernoController::class, 'perguntasMoveOrderTop'])->name('moveOrderTop');
                Route::get('/moveOrderBack/{templatePerguntaTemplates}', [TemplateCadernoController::class, 'perguntasMoveOrderBack'])->name('moveOrderBack');
            });
        });

        /**
         * Cadernos aplicados a unidades produtivas
         */
        Route::group(['prefix' => 'cadernos', 'as' => 'cadernos.'], function () {
            Route::get('/produtor_unidade_produtiva/{produtor?}', [CadernoController::class, 'produtorUnidadeProdutiva'])->name('produtor_unidade_produtiva');
            Route::get('/datatable_produtor_unidade_produtiva/{produtor?}', [CadernoController::class, 'datatableProdutorUnidadeProdutiva'])->name('datatable_produtor_unidade_produtiva');

            Route::get('/{caderno}/pdf', [CadernoController::class, 'pdf'])->middleware('can:view,caderno')->name('pdf');
            Route::get('/{caderno}/sendEmail', [CadernoController::class, 'sendEmail'])->middleware('can:sendEmail,caderno')->name('sendEmail');

            Route::get('/{caderno}/view', [CadernoController::class, 'view'])->middleware('can:view,caderno')->name('view');
            Route::get('/datatable/{produtor?}', [CadernoController::class, 'datatable'])->middleware('permission:view menu caderno')->name('datatable');
            Route::get('/create/{produtor}/{unidadeProdutiva}', [CadernoController::class, 'create'])->middleware('can:create,App\Models\Core\CadernoModel')->name('create');
            Route::get('/{caderno}/edit', [CadernoController::class, 'edit'])->middleware('can:update,caderno')->name('edit');
            Route::delete('/{caderno}', [CadernoController::class, 'destroy'])->middleware('can:delete,caderno')->name('destroy');
            Route::post('/', [CadernoController::class, 'store'])->middleware('can:create,App\Models\Core\CadernoModel')->name('store');
            Route::patch('/{caderno}', [CadernoController::class, 'update'])->middleware('can:update,caderno')->name('update');

            Route::get('/datatableExcluidos', [CadernoController::class, 'datatableExcluidos'])->middleware('permission:view menu caderno')->name('datatableExcluidos');
            Route::get('/excluidos', [CadernoController::class, 'indexExcluidos'])->middleware('permission:view menu caderno')->name('excluidos');
            Route::post('/{caderno}/restore', [CadernoController::class, 'restore'])->middleware('can:restore,caderno')->name('restore');
            Route::delete('/forceDelete/{caderno}', [CadernoController::class, 'forceDelete'])->middleware('can:forceDelete,caderno')->name('forceDelete');

            Route::get('/{produtor?}', [CadernoController::class, 'index'])->middleware('permission:view menu caderno')->name('index');

            /**
             * Arquivos vinculados a cadernos aplicados
             */
            Route::group(['prefix' => '/{caderno}/arquivos', 'as' => 'arquivos.'], function () {
                Route::get('/', [CadernoController::class, 'arquivosIndex'])->name('index');
                Route::get('/datatable', [CadernoController::class, 'arquivosDatatable'])->name('datatable');
                Route::get('/create', [CadernoController::class, 'arquivosCreate'])->name('create');
                Route::post('/store', [CadernoController::class, 'arquivosStore'])->name('store');
                Route::delete('/delete/{arquivo}', [CadernoController::class, 'arquivosDestroy'])->name('destroy');
                Route::get('/edit/{arquivo}', [CadernoController::class, 'arquivosEdit'])->name('edit');
                Route::post('/update/{arquivo}', [CadernoController::class, 'arquivosUpdate'])->name('update');
            });
        });

        /**
         * regiao routes
         */
        Route::group(['prefix' => 'regiao', 'as' => 'regiao.'], function () {
            Route::get('/datatable', [RegiaoController::class, 'datatable'])->name('datatable');

            Route::get('/', [RegiaoController::class, 'index'])->name('index');
            Route::get('/create', [RegiaoController::class, 'create'])->name('create');
            Route::post('/', [RegiaoController::class, 'store'])->name('store');
            Route::get('/{regiao}/edit', [RegiaoController::class, 'edit'])->name('edit');
            Route::patch('/{regiao}', [RegiaoController::class, 'update'])->name('update');
            Route::delete('/{regiao}', [RegiaoController::class, 'destroy'])->name('destroy');
        });

        /**
         * uso do solo
         */
        Route::group(['prefix' => 'solo_categoria', 'as' => 'solo_categoria.'], function () {
            Route::get('/datatable', [SoloCategoriaController::class, 'datatable'])->name('datatable');

            Route::get('/', [SoloCategoriaController::class, 'index'])->name('index');
            Route::get('/create', [SoloCategoriaController::class, 'create'])->name('create');
            Route::post('/', [SoloCategoriaController::class, 'store'])->name('store');
            Route::get('/{soloCategoria}/edit', [SoloCategoriaController::class, 'edit'])->name('edit');
            Route::patch('/{soloCategoria}', [SoloCategoriaController::class, 'update'])->name('update');
        });

        /**
         * Termos de Uso
         */
        Route::group(['prefix' => 'termos_de_uso', 'as' => 'termos_de_uso.'], function () {
            Route::get('/datatable', [TermosDeUsoController::class, 'datatable'])->name('datatable');

            Route::get('/', [TermosDeUsoController::class, 'index'])->name('index');
            Route::get('/create', [TermosDeUsoController::class, 'create'])->name('create');
            Route::post('/', [TermosDeUsoController::class, 'store'])->name('store');
            Route::get('/{termosDeUso}/edit', [TermosDeUsoController::class, 'edit'])->name('edit');
            Route::patch('/{termosDeUso}', [TermosDeUsoController::class, 'update'])->name('update');
            Route::delete('/{termosDeUso}', [TermosDeUsoController::class, 'destroy'])->name('destroy');
        });

        /**
         * Perguntas (formulário aplicado)
         */

        Route::group(['prefix' => 'perguntas', 'as' => 'perguntas.'], function () {
            Route::get('/', [PerguntasController::class, 'index'])->middleware('permission:view menu pergunta checklist')->name('index');
            Route::get('/datatable', [PerguntasController::class, 'datatable'])->middleware('permission:view menu pergunta checklist')->name('datatable');
            Route::get('/create', [PerguntasController::class, 'create'])->name('create');
            Route::get('/{pergunta}/edit', [PerguntasController::class, 'edit'])->name('edit');
            Route::delete('/{pergunta}', [PerguntasController::class, 'destroy'])->name('destroy');
            Route::post('/', [PerguntasController::class, 'store'])->name('store');
            Route::patch('/{pergunta}', [PerguntasController::class, 'update'])->name('update');

            /**
             * Respostas das perguntas do formulário
             */
            Route::group(['prefix' => '/{pergunta}/respostas', 'as' => 'respostas.'], function () {
                Route::get('/', [PerguntasController::class, 'respostasIndex'])->name('index');
                Route::get('/datatable', [PerguntasController::class, 'respostasDatatable'])->name('datatable');
                Route::get('/create', [PerguntasController::class, 'respostasCreate'])->name('create');
                Route::get('/edit/{resposta}', [PerguntasController::class, 'respostasEdit'])->name('edit');

                Route::post('/store', [PerguntasController::class, 'respostasStore'])->name('store');
                Route::post('/update/{resposta}', [PerguntasController::class, 'respostasUpdate'])->name('update');
                Route::delete('/delete/{resposta}', [PerguntasController::class, 'respostasDestroy'])->name('destroy');

                Route::get('/moveOrderUp/{resposta}', [PerguntasController::class, 'respostasMoveOrderUp'])->name('moveOrderUp');
                Route::get('/moveOrderDown/{resposta}', [PerguntasController::class, 'respostasMoveOrderDown'])->name('moveOrderDown');
                Route::get('/moveOrderTop/{resposta}', [PerguntasController::class, 'respostasMoveOrderTop'])->name('moveOrderTop');
                Route::get('/moveOrderBack/{resposta}', [PerguntasController::class, 'respostasMoveOrderBack'])->name('moveOrderBack');
            });
        });

        /**
         * Template dos formulários para Aplicação
         */
        Route::group(['prefix' => 'checklist', 'as' => 'checklist.'], function () {
            Route::get('/', [ChecklistController::class, 'index'])->middleware('permission:view menu checklist base')->name('index');
            Route::get('/datatable', [ChecklistController::class, 'datatable'])->middleware('permission:view menu checklist base')->name('datatable');
            Route::get('/create', [ChecklistController::class, 'create'])->middleware('can:create,App\Models\Core\ChecklistModel')->name('create');
            Route::get('/{checklist}/edit', [ChecklistController::class, 'edit'])->middleware('can:view,checklist')->name('edit');
            Route::delete('/{checklist}', [ChecklistController::class, 'destroy'])->middleware('can:delete,checklist')->name('destroy');
            Route::post('/{checklist}/duplicate', [ChecklistController::class, 'duplicate'])->name('duplicate');
            Route::post('/', [ChecklistController::class, 'store'])->middleware('can:create,App\Models\Core\ChecklistModel')->name('store');
            Route::patch('/{checklist}', [ChecklistController::class, 'update'])->middleware('can:update,checklist')->name('update');
            Route::get('/{checklist}/view', [ChecklistController::class, 'view'])->middleware('can:view,checklist')->name('view');

            Route::get('/biblioteca', [ChecklistController::class, 'biblioteca'])->middleware('permission:view menu checklist base')->name('biblioteca');
            Route::get('/bibliotecaDatatable', [ChecklistController::class, 'datatableBiblioteca'])->middleware('permission:view menu checklist base')->name('datatableBiblioteca');

            /**
             * Domínios //Avaliar
             */
            Route::group(['prefix' => '{checklist}/dominios', 'as' => 'dominios.'], function () {
                Route::get('/', [ChecklistController::class, 'dominioIndex'])->name('index');
                Route::get('/datatable', [ChecklistController::class, 'dominiodatatable'])->name('datatable');
                Route::delete('/{dominio}', [ChecklistController::class, 'dominioDestroy'])->name('destroy');
                Route::get('/create', [ChecklistController::class, 'dominioCreate'])->name('create');
                Route::post('/', [ChecklistController::class, 'dominioStore'])->name('store');
            });

            /**
             * Categorias dos templates do formulário
             */
            Route::group(['prefix' => '{checklist}/categorias', 'as' => 'categorias.'], function () {
                Route::get('/', [ChecklistController::class, 'categoriasIndex'])->name('index');
                Route::get('/datatable', [ChecklistController::class, 'categoriasDatatable'])->name('datatable');

                Route::delete('/{checklistCategoria}', [ChecklistController::class, 'categoriasDestroy'])->name('destroy');

                Route::get('/create', [ChecklistController::class, 'categoriasCreate'])->name('create');
                Route::post('/', [ChecklistController::class, 'categoriasStore'])->name('store');

                Route::get('/{checklistCategoria}/edit', [ChecklistController::class, 'categoriasEdit'])->name('edit');
                Route::patch('/{checklistCategoria}', [ChecklistController::class, 'categoriasUpdate'])->name('update');

                Route::get('/moveOrderUp/{checklistCategoria}', [ChecklistController::class, 'categoriasMoveOrderUp'])->name('moveOrderUp');
                Route::get('/moveOrderDown/{checklistCategoria}', [ChecklistController::class, 'categoriasMoveOrderDown'])->name('moveOrderDown');
                Route::get('/moveOrderTop/{checklistCategoria}', [ChecklistController::class, 'categoriasMoveOrderTop'])->name('moveOrderTop');
                Route::get('/moveOrderBack/{checklistCategoria}', [ChecklistController::class, 'categoriasMoveOrderBack'])->name('moveOrderBack');
            });
        });

        /**
         * Perguntas vinculadas as categorias do template de Formulário Aplicado
         */
        Route::group(['prefix' => 'checklist/categorias/{checklistCategoria}/perguntas', 'as' => 'checklist.categorias.perguntas.'], function () {
            Route::get('/', [ChecklistController::class, 'perguntasIndex'])->name('index');
            Route::get('/datatable', [ChecklistController::class, 'perguntasDatatable'])->name('datatable');
            Route::get('/create/{pergunta}', [ChecklistController::class, 'perguntasCreate'])->name('create');

            Route::post('/', [ChecklistController::class, 'perguntasStore'])->name('store');

            Route::get('/{checklistPergunta}/edit', [ChecklistController::class, 'perguntasEdit'])->name('edit');
            Route::patch('/{checklistPergunta}', [ChecklistController::class, 'perguntasUpdate'])->name('update');

            Route::get('/todasPerguntas', [ChecklistController::class, 'perguntasTodasIndex'])->name('todasPerguntas');
            Route::get('/todasPerguntasDatatable', [ChecklistController::class, 'todasPerguntasDatatable'])->name('todasPerguntasDatatable');
            Route::delete('/delete/{checklistPergunta}', [ChecklistController::class, 'perguntasDestroy'])->name('destroy');

            Route::get('/moveOrderUp/{checklistPergunta}', [ChecklistController::class, 'perguntasMoveOrderUp'])->name('moveOrderUp');
            Route::get('/moveOrderDown/{checklistPergunta}', [ChecklistController::class, 'perguntasMoveOrderDown'])->name('moveOrderDown');
            Route::get('/moveOrderTop/{checklistPergunta}', [ChecklistController::class, 'perguntasMoveOrderTop'])->name('moveOrderTop');
            Route::get('/moveOrderBack/{checklistPergunta}', [ChecklistController::class, 'perguntasMoveOrderBack'])->name('moveOrderBack');
        });


        /**
         * Formulário Aplicado na Unidade Produtiva (ChecklistUnidadeProdutiva)
         */
        Route::group(['prefix' => 'checklist_unidade_produtiva', 'as' => 'checklist_unidade_produtiva.'], function () {
            Route::get('/comparar/view', [ChecklistUnidadeProdutivaController::class, 'compararView'])->middleware('permission:view menu checklist_unidade_produtiva')->name('compareView');
            Route::get('/comparar', [ChecklistUnidadeProdutivaController::class, 'comparar'])->middleware('permission:view menu checklist_unidade_produtiva')->name('compare');

            Route::get('/{checklistUnidadeProdutiva}/view', [ChecklistUnidadeProdutivaController::class, 'view'])->middleware('can:view,checklistUnidadeProdutiva')->name('view');
            Route::get('/{checklistUnidadeProdutiva}/pdf', [ChecklistUnidadeProdutivaController::class, 'pdf'])->middleware('can:view,checklistUnidadeProdutiva')->name('pdf');
            Route::get('/{checklistUnidadeProdutiva}/sendEmail', [ChecklistUnidadeProdutivaController::class, 'sendEmail'])->middleware('can:sendEmail,checklistUnidadeProdutiva')->name('sendEmail');

            Route::get('/datatable/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'datatable'])->middleware('permission:view menu checklist_unidade_produtiva')->name('datatable');

            Route::get('/create/{checklist}/{produtor}/{unidadeProdutiva}', [ChecklistUnidadeProdutivaController::class, 'create'])->middleware('can:create,App\Models\Core\ChecklistUnidadeProdutivaModel')->name('create');

            Route::get('/template/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'template'])->middleware('can:create,App\Models\Core\ChecklistUnidadeProdutivaModel')->name('template');
            Route::get('/datatableTemplate/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'datatableTemplate'])->middleware('permission:view menu checklist_unidade_produtiva')->name('datatableTemplate');

            Route::get('/{checklistUnidadeProdutiva}/edit', [ChecklistUnidadeProdutivaController::class, 'edit'])->middleware('can:view,checklistUnidadeProdutiva')->name('edit');
            Route::delete('/{checklistUnidadeProdutiva}', [ChecklistUnidadeProdutivaController::class, 'destroy'])->middleware('can:delete,checklistUnidadeProdutiva')->name('destroy');

            Route::delete('/forceDelete/{checklistUnidadeProdutiva}', [ChecklistUnidadeProdutivaController::class, 'forceDelete'])->middleware('can:forceDelete,checklistUnidadeProdutiva')->name('forceDelete');

            Route::post('/', [ChecklistUnidadeProdutivaController::class, 'store'])->middleware('can:create,App\Models\Core\ChecklistUnidadeProdutivaModel')->name('store');
            Route::patch('/{checklistUnidadeProdutiva}', [ChecklistUnidadeProdutivaController::class, 'update'])->middleware('can:update,checklistUnidadeProdutiva')->name('update');

            //Analise dos formulários aplicados
            Route::get('/analise', [ChecklistUnidadeProdutivaController::class, 'analiseIndex'])->name('analiseIndex');
            Route::get('/analise_datatable', [ChecklistUnidadeProdutivaController::class, 'analiseDatatable'])->name('analiseDatatable');

            Route::get('/produtor_unidade_produtiva/{checklist}/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'produtorUnidadeProdutiva'])->middleware('can:create,App\Models\Core\ChecklistUnidadeProdutivaModel')->name('produtor_unidade_produtiva');
            Route::get('/datatableProdutorUnidadeProdutiva/{checklist}/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'datatableProdutorUnidadeProdutiva'])->middleware('permission:view menu checklist_unidade_produtiva')->name('datatableProdutorUnidadeProdutiva');

            Route::post('/{checklistUnidadeProdutiva}/analise/store', [ChecklistAprovacaoLogsController::class, 'store'])->middleware('can:analize,checklistUnidadeProdutiva')->name('analiseStore');
            Route::post('/{checklistUnidadeProdutiva}/reanalyse', [ChecklistUnidadeProdutivaController::class, 'reanalyse'])->middleware('can:reanalyse,checklistUnidadeProdutiva')->name('reanalyse');

            Route::get('/datatableExcluidos', [ChecklistUnidadeProdutivaController::class, 'datatableExcluidos'])->middleware('permission:view menu checklist_unidade_produtiva')->name('datatableExcluidos');
            Route::get('/excluidos', [ChecklistUnidadeProdutivaController::class, 'indexExcluidos'])->middleware('permission:view menu checklist_unidade_produtiva')->name('excluidos');
            Route::post('/{checklistUnidadeProdutiva}/restore', [ChecklistUnidadeProdutivaController::class, 'restore'])->middleware('can:restore,checklistUnidadeProdutiva')->name('restore');

            Route::get('/{produtor?}', [ChecklistUnidadeProdutivaController::class, 'index'])->middleware('permission:view menu checklist_unidade_produtiva')->name('index');

            /**
             * Arquivos vinculados ao formulário aplicados
             */
            Route::group(['prefix' => '/{checklistUnidadeProdutiva}/arquivos', 'as' => 'arquivos.'], function () {
                Route::get('/', [ChecklistUnidadeProdutivaController::class, 'arquivosIndex'])->name('index');
                Route::get('/datatable', [ChecklistUnidadeProdutivaController::class, 'arquivosDatatable'])->name('datatable');
                Route::get('/create', [ChecklistUnidadeProdutivaController::class, 'arquivosCreate'])->name('create');
                Route::post('/store', [ChecklistUnidadeProdutivaController::class, 'arquivosStore'])->name('store');
                Route::delete('/delete/{arquivo}', [ChecklistUnidadeProdutivaController::class, 'arquivosDestroy'])->name('destroy');
                Route::get('/edit/{arquivo}', [ChecklistUnidadeProdutivaController::class, 'arquivosEdit'])->name('edit');
                Route::post('/update/{arquivo}', [ChecklistUnidadeProdutivaController::class, 'arquivosUpdate'])->name('update');
            });
        });

        /**
         * Plano de Ação - Unidade Produtiva
         */
        Route::group(['prefix' => 'plano_acao', 'as' => 'plano_acao.'], function () {
            Route::get('/produtor_unidade_produtiva/{produtor?}', [PlanoAcaoController::class, 'produtorUnidadeProdutiva'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('produtor_unidade_produtiva');
            Route::get('/datatableProdutorUnidadeProdutiva/{produtor?}', [PlanoAcaoController::class, 'datatableProdutorUnidadeProdutiva'])->middleware('permission:view menu plano_acao')->name('datatableProdutorUnidadeProdutiva');

            Route::get('/{planoAcao}/pdf/{debug?}', [PlanoAcaoController::class, 'pdf'])->middleware('can:view,planoAcao')->name('pdf');
            Route::get('/{planoAcao}/sendEmail', [PlanoAcaoController::class, 'sendEmail'])->middleware('can:sendEmail,planoAcao')->name('sendEmail');

            Route::get('/{planoAcao}/view', [PlanoAcaoController::class, 'view'])->middleware('can:view,planoAcao')->name('view');
            Route::get('/datatable/{produtor?}', [PlanoAcaoController::class, 'datatable'])->middleware('permission:view menu plano_acao')->name('datatable');

            Route::get('/create/{produtor}/{unidadeProdutiva}', [PlanoAcaoController::class, 'create'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('create');
            Route::post('/', [PlanoAcaoController::class, 'store'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('store');

            Route::get('/{planoAcao}/edit', [PlanoAcaoController::class, 'edit'])->middleware('can:update,planoAcao')->name('edit');
            Route::patch('/{planoAcao}', [PlanoAcaoController::class, 'update'])->middleware('can:update,planoAcao')->name('update');

            Route::delete('/{planoAcao}', [PlanoAcaoController::class, 'destroy'])->middleware('can:delete,planoAcao')->name('destroy');
            Route::delete('/forceDelete/{planoAcao}', [PlanoAcaoController::class, 'forceDelete'])->middleware('can:forceDelete,planoAcao')->name('forceDelete');

            //Com Checklist
            Route::get('/checklist_unidade_produtiva/{produtor?}', [PlanoAcaoController::class, 'checklistUnidadeProdutiva'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('checklist_unidade_produtiva');
            Route::get('/datatableChecklistUnidadeProdutiva/{produtor?}', [PlanoAcaoController::class, 'datatableChecklistUnidadeProdutiva'])->middleware('permission:view menu plano_acao')->name('datatableChecklistUnidadeProdutiva');

            Route::get('/create_com_checklist/{checklistUnidadeProdutiva}', [PlanoAcaoController::class, 'createComChecklist'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('create_com_checklist');
            Route::post('/store_com_checklist', [PlanoAcaoController::class, 'storeComChecklist'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('store_com_checklist');

            Route::get('/edit_com_checklist/{planoAcao}', [PlanoAcaoController::class, 'editComChecklist'])->middleware('can:view,planoAcao')->name('edit_com_checklist');
            Route::patch('/update_com_checklist/{planoAcao}', [PlanoAcaoController::class, 'updateComChecklist'])->middleware('can:update,planoAcao')->name('update_com_checklist');
            //Fim Com Checklist

            Route::get('/datatableExcluidos/{produtor?}', [PlanoAcaoController::class, 'datatableExcluidos'])->middleware('permission:view menu plano_acao')->name('datatableExcluidos');
            Route::get('/excluidos/{produtor?}', [PlanoAcaoController::class, 'indexExcluidos'])->middleware('permission:view menu plano_acao')->name('excluidos');
            Route::post('/{planoAcao}/restore', [PlanoAcaoController::class, 'restore'])->middleware('can:restore,planoAcao')->name('restore');
            Route::post('/{planoAcao}/reopen', [PlanoAcaoController::class, 'reopen'])->middleware('can:reopen,planoAcao')->name('reopen');

            Route::get('/{produtor?}', [PlanoAcaoController::class, 'index'])->middleware('permission:view menu plano_acao')->name('index');

            Route::group(['prefix' => '/{planoAcao}/item', 'as' => 'item.'], function () {
                Route::get('/datatable', [PlanoAcaoController::class, 'itemDatatable'])->name('datatable');
                Route::get('/create', [PlanoAcaoController::class, 'itemCreate'])->name('create');
                Route::get('/edit/{item}', [PlanoAcaoController::class, 'itemEdit'])->name('edit');

                Route::post('/store', [PlanoAcaoController::class, 'itemStore'])->middleware('can:create,App\Models\Core\PlanoAcaoItemModel')->name('store');
                Route::post('/update/{item}', [PlanoAcaoController::class, 'itemUpdate'])->middleware('can:create,App\Models\Core\PlanoAcaoItemModel')->name('update');
                Route::delete('/delete/{item}', [PlanoAcaoController::class, 'itemDestroy'])->middleware('can:delete,item')->name('destroy');
                Route::post('/{item}/reopen', [PlanoAcaoController::class, 'itemReopen'])->middleware('can:reopen,item')->name('reopen');

                Route::get('/', [PlanoAcaoController::class, 'itemIndex'])->middleware('permission:view menu plano_acao_item')->name('index');
                Route::get('/index_com_checklist', [PlanoAcaoController::class, 'itemIndexComChecklist'])->name('item_index_com_checklist');
                Route::get('/datatableComChecklist', [PlanoAcaoController::class, 'itemDatatableComChecklist'])->name('datatableComChecklist');

                Route::get('/modal_edit_com_checklist/{item}', [PlanoAcaoController::class, 'modalEditComChecklist'])->name('modal_edit_com_checklist');
                Route::post('/update_modal_com_checklist/{item}', [PlanoAcaoController::class, 'modalUpdateComChecklist'])->name('modal_update_com_checklist');

                Route::get('/prioridadeUp/{item}', [PlanoAcaoController::class, 'itemPrioridadeUp'])->name('prioridadeUp');
                Route::get('/prioridadeDown/{item}', [PlanoAcaoController::class, 'itemPrioridadeDown'])->name('prioridadeDown');
            });
        });

        Route::group(['prefix' => '/{planoAcao}/historico', 'as' => 'plano_acao.historico.'], function () {
            Route::get('/', [PlanoAcaoController::class, 'historicoIndex'])->middleware('permission:view menu plano_acao_historico')->name('index');
            // Route::get('/datatable', [PlanoAcaoController::class, 'historicoDatatable'])->name('datatable');
            Route::get('/create', [PlanoAcaoController::class, 'historicoCreate'])->name('create');
            Route::get('/edit/{historico}', [PlanoAcaoController::class, 'historicoEdit'])->name('edit');

            Route::get('/create_and_list', [PlanoAcaoController::class, 'historicoCreateAndList'])->name('create_and_list');
            Route::post('/store_create_and_list', [PlanoAcaoController::class, 'historicoStoreCreateAndList'])->name('store_create_and_list');

            Route::post('/store', [PlanoAcaoController::class, 'historicoStore'])->middleware('can:create,App\Models\Core\PlanoAcaoHistoricoModel')->name('store');
            Route::post('/update/{historico}', [PlanoAcaoController::class, 'historicoUpdate'])->middleware('can:create,App\Models\Core\PlanoAcaoHistoricoModel')->name('update');
            Route::delete('/delete/{historico}', [PlanoAcaoController::class, 'historicoDestroy'])->middleware('can:delete,historico')->name('destroy');
        });

        //Bypass permission scope
        Route::group(['prefix' => '/{planoAcaoId}/historico', 'as' => 'plano_acao.historico.'], function () {
            Route::get('/datatable', [PlanoAcaoController::class, 'historicoDatatable'])->name('datatable');
        });

        Route::group(['prefix' => '/{planoAcaoItem}/historico_item', 'as' => 'plano_acao_item.historico_item.'], function () {
            Route::get('/', [PlanoAcaoController::class, 'historicoItemIndex'])->middleware('permission:view menu plano_acao_item_historico')->name('index');
            Route::get('/datatable', [PlanoAcaoController::class, 'historicoItemDatatable'])->name('datatable');
            Route::get('/create', [PlanoAcaoController::class, 'historicoItemCreate'])->name('create');
            Route::get('/edit/{historico}', [PlanoAcaoController::class, 'historicoItemEdit'])->name('edit');

            Route::get('/create_and_list', [PlanoAcaoController::class, 'historicoItemCreateAndList'])->name('create_and_list');
            Route::post('/store_create_and_list', [PlanoAcaoController::class, 'historicoItemStoreCreateAndList'])->name('store_create_and_list');

            Route::post('/store', [PlanoAcaoController::class, 'historicoItemStore'])->middleware('can:create,App\Models\Core\PlanoAcaoItemHistoricoModel')->name('store');
            Route::post('/update/{historico}', [PlanoAcaoController::class, 'historicoItemUpdate'])->middleware('can:create,App\Models\Core\PlanoAcaoItemHistoricoModel')->name('update');
            Route::delete('/delete/{historico}', [PlanoAcaoController::class, 'historicoItemDestroy'])->middleware('can:delete,historico')->name('destroy');
        });



        /**
         * Plano de Ação Coletivo
         */
        Route::group(['prefix' => 'plano_acao_coletivo', 'as' => 'plano_acao_coletivo.'], function () {
            Route::get('/{planoAcao}/view', [PlanoAcaoColetivoController::class, 'view'])->middleware('can:view,planoAcao')->name('view');

            Route::get('/create', [PlanoAcaoColetivoController::class, 'create'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('create');
            Route::post('/', [PlanoAcaoColetivoController::class, 'store'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('store');

            Route::get('/{planoAcao}/edit/{versaoSimples?}', [PlanoAcaoColetivoController::class, 'edit'])->middleware('can:update,planoAcao')->name('edit');
            Route::patch('/{planoAcao}', [PlanoAcaoColetivoController::class, 'update'])->middleware('can:update,planoAcao')->name('update');

            Route::delete('/{planoAcao}', [PlanoAcaoColetivoController::class, 'destroy'])->middleware('can:delete,planoAcao')->name('destroy');
            Route::delete('/forceDelete/{planoAcao}', [PlanoAcaoColetivoController::class, 'forceDelete'])->middleware('can:forceDelete,planoAcao')->name('forceDelete');

            Route::get('/datatable/{produtor?}', [PlanoAcaoColetivoController::class, 'datatable'])->middleware('permission:view menu plano_acao')->name('datatable');

            Route::get('/{planoAcao}/pdf/{debug?}', [PlanoAcaoColetivoController::class, 'pdf'])->middleware('can:view,planoAcao')->name('pdf');

            Route::get('/datatableExcluidos', [PlanoAcaoColetivoController::class, 'datatableExcluidos'])->middleware('permission:view menu plano_acao')->name('datatableExcluidos');
            Route::get('/excluidos', [PlanoAcaoColetivoController::class, 'indexExcluidos'])->middleware('permission:view menu plano_acao')->name('excluidos');
            Route::post('/{planoAcao}/restore', [PlanoAcaoColetivoController::class, 'restore'])->middleware('can:restore,planoAcao')->name('restore');

            Route::post('/{planoAcao}/reopen', [PlanoAcaoColetivoController::class, 'reopen'])->middleware('can:reopen,planoAcao')->name('reopen');

            Route::get('/{produtor?}', [PlanoAcaoColetivoController::class, 'index'])->middleware('permission:view menu plano_acao')->name('index');

            Route::group(['prefix' => '/{planoAcao}/item/{versaoSimples?}', 'as' => 'item.'], function () {
                Route::get('/datatable', [PlanoAcaoColetivoController::class, 'itemDatatable'])->name('datatable');
                Route::get('/create', [PlanoAcaoColetivoController::class, 'itemCreate'])->name('create');
                Route::get('/edit/{item}', [PlanoAcaoColetivoController::class, 'itemEdit'])->name('edit');

                Route::post('/store', [PlanoAcaoColetivoController::class, 'itemStore'])->middleware('can:create,App\Models\Core\PlanoAcaoItemModel')->name('store');
                Route::post('/update/{item}', [PlanoAcaoColetivoController::class, 'itemUpdate'])->middleware('can:create,App\Models\Core\PlanoAcaoItemModel')->name('update');
                Route::delete('/delete/{item}', [PlanoAcaoColetivoController::class, 'itemDestroy'])->middleware('can:delete,item')->name('destroy');

                Route::get('/datatable_individuais', [PlanoAcaoColetivoController::class, 'itemIndividuaisDatatable'])->name('datatable_individuais');
                Route::get('/index_individuais/{unidadeProdutiva?}/{planoAcaoItem?}', [PlanoAcaoColetivoController::class, 'itemIndividuaisIndex'])->middleware('permission:view menu plano_acao_item')->name('index_individuais');
                Route::get('/edit_individuais/{item}', [PlanoAcaoColetivoController::class, 'itemIndividuaisEdit'])->name('edit_individuais');
                Route::post('/update_individuais/{item}', [PlanoAcaoColetivoController::class, 'itemIndividuaisUpdate'])->middleware('can:create,App\Models\Core\PlanoAcaoItemModel')->name('update_individuais');

                Route::post('/{item}/reopen_individual', [PlanoAcaoColetivoController::class, 'itemIndividualReopen'])->middleware('can:reopen,item')->name('reopenIndividual');
                Route::post('/{item}/reopen', [PlanoAcaoColetivoController::class, 'itemReopen'])->middleware('can:reopen,item')->name('reopen');

                Route::get('/{unidadeProdutiva?}', [PlanoAcaoColetivoController::class, 'itemIndex'])->middleware('permission:view menu plano_acao_item')->name('index');
            });

            Route::group(['prefix' => '/{planoAcao}/unidadeProdutiva', 'as' => 'unidade_produtiva.'], function () {
                Route::delete('/delete/{item}', [PlanoAcaoColetivoController::class, 'unidadeProdutivaDestroy'])->middleware('can:delete,item')->name('destroy');

                Route::get('/create', [PlanoAcaoColetivoController::class, 'unidadeProdutivaCreate'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('create');
                Route::get('/datatableCreate', [PlanoAcaoColetivoController::class, 'unidadeProdutivaCreateDatatable'])->middleware('permission:view menu plano_acao')->name('unidadeProdutivaCreateDatatable');
                Route::get('/store/{produtor}/{unidadeProdutiva}', [PlanoAcaoColetivoController::class, 'unidadeProdutivaStore'])->middleware('can:create,App\Models\Core\PlanoAcaoModel')->name('store');

                Route::get('/create_and_list/{planoAcaoFilhoId}', [PlanoAcaoColetivoController::class, 'unidadeProdutivaCreateAndList'])->name('create_and_list');
                Route::post('/store_create_and_list/{planoAcaoFilhoId}', [PlanoAcaoColetivoController::class, 'unidadeProdutivaStoreCreateAndList'])->name('store_create_and_list');

                Route::get('/datatable', [PlanoAcaoColetivoController::class, 'unidadeProdutivaDatatable'])->name('datatable');
                Route::get('/', [PlanoAcaoColetivoController::class, 'unidadeProdutivaIndex'])->middleware('permission:view menu plano_acao_item')->name('index');
            });
        });


        /*
        * Importação de "xls" no sistema (caderno, formulários, usuarios ...)
        */
        Route::group(['prefix' => 'importador', 'as' => 'importador.'], function () {
            Route::get('/create', [ImportadorController::class, 'create'])->name('create');
            Route::post('/', [ImportadorController::class, 'store'])->name('store');

            Route::get('/editProdutor', [ImportadorController::class, 'editProdutor'])->name('editProdutor');
            Route::post('/updateProdutor', [ImportadorController::class, 'updateProdutor'])->name('updateProdutor');

            Route::get('/createCaderno', [ImportadorController::class, 'createCaderno'])->name('createCaderno');
            Route::post('/caderno', [ImportadorController::class, 'storeCaderno'])->name('storeCaderno');

            Route::get('/createChecklistUnidadeProdutiva', [ImportadorController::class, 'createChecklistUnidadeProdutiva'])->name('createChecklistUnidadeProdutiva');
            Route::post('/checklist_unidade_produtiva', [ImportadorController::class, 'storeChecklistUnidadeProdutiva'])->name('storeChecklistUnidadeProdutiva');

            Route::get('/createUsuarios', [ImportadorController::class, 'createUsuarios'])->name('createUsuarios');
            Route::post('/usuarios', [ImportadorController::class, 'storeUsuarios'])->name('storeUsuarios');
        });

        /**
         * Acesso aos dados
         */
        Route::group(['prefix' => 'dado', 'as' => 'dado.'], function () {
            Route::get('/datatable', [DadoController::class, 'datatable'])->middleware('can:list,App\Models\Core\DadoModel')->name('datatable');
            Route::get('/', [DadoController::class, 'index'])->middleware('can:list,App\Models\Core\DadoModel')->name('index');

            Route::get('/view/{dado}', [DadoController::class, 'view'])->middleware('can:view,dado')->name('view');

            Route::get('/create', [DadoController::class, 'create'])->middleware('can:create,App\Models\Core\DadoModel')->name('create');
            Route::post('/', [DadoController::class, 'store'])->middleware('can:create,App\Models\Core\DadoModel')->name('store');

            Route::get('/{dado}/edit', [DadoController::class, 'edit'])->middleware('can:view,dado')->name('edit');
            Route::patch('/{dado}', [DadoController::class, 'update'])->middleware('can:update,dado')->name('update');

            Route::delete('/{dado}', [DadoController::class, 'destroy'])->middleware('can:delete,dado')->name('destroy');
        });

        /**
         * Logs
         */
        Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {
            Route::get('/datatable', [LogsController::class, 'datatable'])->name('datatable');
            Route::get('/', [LogsController::class, 'index'])->name('index');
        });

        /**
         * Sobre
         */
        /*
        Route::group(['prefix' => 'sobre', 'as' => 'sobre.'], function () {
            Route::get('/{sobre}/edit', [SobreController::class, 'edit'])->middleware('can:update,sobre')->name('edit');
            Route::patch('/{sobre}', [SobreController::class, 'update'])->middleware('can:update,sobre')->name('update');
            Route::post('/quillUpload', [SobreController::class, 'quillUpload'])->middleware('can:update,sobre')->name('quillUpload');

            Route::get('/', [SobreController::class, 'index'])->name('index');
        });
        */
    });
});


/**
 * Rotas do Report (ignorando o middleware "permission_report_restrict")
 */
Route::middleware(['cors'])->group(function () {
    Route::group([
        'prefix' => '',
        'as' => 'core.',
        'namespace' => 'Core',
    ], function () {
        Route::group(['prefix' => 'mapa', 'as' => 'mapa.'], function () {
            Route::any('/data', [MapaController::class, 'data'])->name('data');
            Route::get('/', [MapaController::class, 'index'])->name('index');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::any('/unidade_produtiva_data', [ReportController::class, 'unidadeProdutivaData'])->name('unidade_produtiva_data');
            Route::any('/unidade_produtiva_pessoa', [ReportController::class, 'unidadeProdutivaPessoaData'])->name('unidade_produtiva_pessoa');
            Route::any('/unidade_produtiva_infra', [ReportController::class, 'unidadeProdutivaInfraData'])->name('unidade_produtiva_infra');
            Route::any('/unidade_produtiva_uso_solo', [ReportController::class, 'unidadeProdutivaUsoSoloData'])->name('unidade_produtiva_uso_solo');
            Route::any('/unidade_produtiva_caderno', [ReportController::class, 'cadernoCampoData'])->name('unidade_produtiva_caderno');
            Route::any('/unidade_produtiva_checklist', [ReportController::class, 'checklistData'])->name('unidade_produtiva_checklist');
            Route::any('/unidade_produtiva_pda', [ReportController::class, 'pdaData'])->name('unidade_produtiva_pda');

            // Route::any('/unidade_produtiva_images', [ReportController::class, 'unidadeProdutivaImagesData'])->name('unidade_produtiva_images');
            // Route::any('/unidade_produtiva_caderno_images', [ReportController::class, 'cadernoCampoImagesData'])->name('unidade_produtiva_caderno_images');
            // Route::any('/unidade_produtiva_checklist_images', [ReportController::class, 'checklistImagesData'])->name('unidade_produtiva_checklist_images');

            Route::get('/', [ReportController::class, 'index'])->name('index');
        });

        Route::group(['prefix' => 'indicadores', 'as' => 'indicadores.'], function () {
            Route::any('/data', [IndicadorController::class, 'data'])->name('data');
            Route::any('/dataIndicadoresCadastrais', [IndicadorController::class, 'dataIndicadoresCadastrais'])->name('dataIndicadoresCadastrais');
            Route::any('/dataIndicadoresFormularios', [IndicadorController::class, 'dataIndicadoresFormularios'])->name('dataIndicadoresFormularios');
            Route::any('/dataIndicadoresPdas', [IndicadorController::class, 'dataIndicadoresPdas'])->name('dataIndicadoresPdas');
            Route::any('/dataIndicadoresCadernos', [IndicadorController::class, 'dataIndicadoresCadernos'])->name('dataIndicadoresCadernos');

            Route::get('/', [IndicadorController::class, 'index'])->name('index');

            Route::any('/dataChart_1_1_UnidadeProdutiva', [IndicadorController::class, 'dataChart_1_1_UnidadeProdutiva'])->name('dataChart_1_1_UnidadeProdutiva');
            Route::any('/dataChart_1_2_Produtor', [IndicadorController::class, 'dataChart_1_2_Produtor'])->name('dataChart_1_2_Produtor');
            Route::any('/dataChart_1_3_NovosProdutores', [IndicadorController::class, 'dataChart_1_3_NovosProdutores'])->name('dataChart_1_3_NovosProdutores');
            Route::any('/dataChart_1_4_UpasAtendidas', [IndicadorController::class, 'dataChart_1_4_UpasAtendidas'])->name('dataChart_1_4_UpasAtendidas');
            Route::any('/dataChart_1_5_AtendimentosRealizados', [IndicadorController::class, 'dataChart_1_5_AtendimentosRealizados'])->name('dataChart_1_5_AtendimentosRealizados');

            Route::any('/dataChart_1_6_TecnicosAtivos', [IndicadorController::class, 'dataChart_1_6_TecnicosAtivos'])->name('dataChart_1_6_TecnicosAtivos');

            Route::any('/dataChart_1_7_FormulariosAplicados', [IndicadorController::class, 'dataChart_1_7_FormulariosAplicados'])->name('dataChart_1_7_FormulariosAplicados');

            Route::any('/dataChart_1_8_Produtores', [IndicadorController::class, 'dataChart_1_8_Produtores'])->name('dataChart_1_8_Produtores');
            Route::any('/dataChart_1_8_Cadernos', [IndicadorController::class, 'dataChart_1_8_Cadernos'])->name('dataChart_1_8_Cadernos');
            Route::any('/dataChart_1_8_Formularios', [IndicadorController::class, 'dataChart_1_8_Formularios'])->name('dataChart_1_8_Formularios');
            Route::any('/dataChart_1_8_PlanoAcoes', [IndicadorController::class, 'dataChart_1_8_PlanoAcoes'])->name('dataChart_1_8_PlanoAcoes');

            Route::any('/dataChart_1_13b_DistribuicaoAtendimentoTecnico', [IndicadorController::class, 'dataChart_1_13b_DistribuicaoAtendimentoTecnico'])->name('dataChart_1_13b_DistribuicaoAtendimentoTecnico');

            Route::any('/dataChart_2_5_regularizacao_ambiental', [IndicadorController::class, 'dataChart_2_5_regularizacao_ambiental'])->name('dataChart_2_5_regularizacao_ambiental');

            Route::any('/dataChart_3_X_PerguntasFormularios', [IndicadorController::class, 'dataChart_3_X_PerguntasFormularios'])->name('dataChart_3_X_PerguntasFormularios');
            Route::any('/dataChart_3_X_PerguntasFormulariosPeriod', [IndicadorController::class, 'dataChart_3_X_PerguntasFormulariosPeriod'])->name('dataChart_3_X_PerguntasFormulariosPeriod');

            Route::any('/dataChart_5_X_PerguntasCadernos', [IndicadorController::class, 'dataChart_5_X_PerguntasCadernos'])->name('dataChart_5_X_PerguntasCadernos');
        });
    });
});
