<?php

use App\Http\Controllers\Api\Core\ApiDadoController;
use App\Http\Controllers\Api\Core\AuthController;
use App\Http\Controllers\Api\Core\OfflineController;
use App\Http\Controllers\Api\Core\EstadoController;
use App\Http\Controllers\Api\Core\UnidadeProdutiva\UnidadeProdutivaController;

/**
 * Api utilizada pelo mobile.
 *
 * a) Retorno das roles permitidas para um determinado "document"
 * b) Login
 * c) Esqueci minha senha
 * d) Logout
 * e) Retorno dos dados do usuário
 */
Route::group(['prefix' => 'auth'], function () {
    //Api
    Route::post('document_roles', [AuthController::class, 'documentRoles'])->name('api-document');

    Route::post('login', [AuthController::class, 'login'])->name('api-login');
    Route::post('forgot', [AuthController::class, 'forgot'])->name('api-forgot');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout'])->name('api-logout');
        Route::get('user', [AuthController::class, 'user'])->name('api-user');
    });
});

/**
 * Retorno das categorias p/ serem consumidas na Unidade Produtiva (CMS)
 */
Route::group(['prefix' => 'unidades_produtivas', 'as' => 'unidades_produtivas.'], function () {
    Route::get('/soloCategorias', [UnidadeProdutivaController::class, 'soloCategorias'])->name('soloCategorias');
});

/**
 * Estados / Cidades, p/ serem consumidos no Produtor / Unidade Produtiva
 */
Route::group(['prefix' => 'estados', 'as' => 'estados.'], function () {
    Route::get('/cidades', [EstadoController::class, 'cidades'])->name('cidades');
    Route::get('/cidades/busca', [EstadoController::class, 'cidadesBusca'])->name('cidadesBusca');
});

/**
 * APP - Usuário não esta logado
 */
Route::group(['prefix' => 'offline', 'as' => 'offline.'], function () {
    Route::get('/migrations', [OfflineController::class, 'migrations'])->name('migrations');
    Route::get('/migrationsV2', [OfflineController::class, 'migrationsV2'])->name('migrationsV2');

    Route::get('/health', [OfflineController::class, 'health'])->name('health');

    Route::post('/dados_gerais', [OfflineController::class, 'dadosGerais'])->name('dadosGerais');

    Route::post('/test_upload', [OfflineController::class, 'testUpload'])->name('testUpload');
});

/**
 * APP - Usuário logado
 */
Route::group(['middleware' => ['auth:api', 'app_sync'], 'prefix' => 'offline', 'as' => 'offline.'], function () {
    Route::post('/update', [OfflineController::class, 'update'])->name('update');

    Route::post('/regioes', [OfflineController::class, 'regioes'])->name('regioes');
    Route::post('/produtores', [OfflineController::class, 'produtores'])->name('produtores');
    Route::post('/unidade_produtivas', [OfflineController::class, 'unidadeProdutivas'])->name('unidadeProdutivas');
    Route::post('/caderno_campo', [OfflineController::class, 'cadernoCampo'])->name('cadernoCampo');
    Route::post('/checklists', [OfflineController::class, 'checklists'])->name('checklists');
    Route::post('/plano_acoes', [OfflineController::class, 'plano_acoes'])->name('plano_acoes');

    Route::post('/checklist_score', [OfflineController::class, 'checklistScore'])->name('checklistScore');

    Route::post('/dados_gerais_auth', [OfflineController::class, 'dadosGeraisAuth'])->name('dadosGeraisAuth');

    Route::post('/file_upload/{table}', [OfflineController::class, 'fileUpload'])->name('fileUpload');

    Route::post('/unidades_produtivas', [ApiDadoController::class, 'unidadesProdutivas'])->name('unidades_produtivas');
});

/**
 * API - Acesso aos dados - Sampa+Rural
 * */
Route::group(['middleware' => ['auth:api-dados', 'throttle:30,1'], 'prefix' => 'dados', 'as' => 'dados.'], function () {
    Route::post('/unidades_produtivas', [ApiDadoController::class, 'unidadesProdutivas'])->name('unidades_produtivas');
});
