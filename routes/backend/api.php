<?php

use App\Http\Controllers\Api\Core\UnidadeProdutiva\UnidadeProdutivaController;

Route::group(['prefix' => 'api'], function () {
    Route::get('produtores/busca', [UnidadeProdutivaController::class, 'produtores'])->name('produtores');
    Route::get('unidades/busca', [UnidadeProdutivaController::class, 'unidadesProdutivas'])->name('unidadesProdutivas');
    Route::get('regioes', [UnidadeProdutivaController::class, 'regioes'])->name('regioes');
});
