<?php

use App\Http\Controllers\Frontend\FrontendCadernoController;
use App\Http\Controllers\Frontend\FrontendChecklistUnidadeProdutivaController;
use App\Http\Controllers\Frontend\FrontendPlanoAcaoController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\User\AccountController;
use App\Http\Controllers\Frontend\User\ProfileController;
use App\Http\Controllers\Frontend\User\DashboardController;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/formulario_unidade_produtiva/pdf/{checklistUnidadeProdutiva}/{user}', [FrontendChecklistUnidadeProdutivaController::class, 'pdf'])->name('pdf');
Route::get('/pda/pdf/{planoAcao}/{user}', [FrontendPlanoAcaoController::class, 'pdf'])->name('pdf_pda');
Route::get('/caderno/pdf/{caderno}/{user}', [FrontendCadernoController::class, 'pdf'])->name('pdf_caderno');

//Hoje a página de politica é a mesma dos termos de uso, rever futuramente.
Route::get('/politica-de-privacidade', [HomeController::class, 'termosUso'])->name('politica-de-privacidade');

Route::get('/termos-de-uso/{acceptTerms?}', [HomeController::class, 'termosUso'])->name('termos-de-uso');
Route::post('/termos-de-uso', [HomeController::class, 'storeTermosUso'])->name('store-termos-de-uso')->middleware('auth');

Route::group(['middleware' => ['auth', 'password_expires']], function () {
    Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
        // User Dashboard Specific
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User Account Specific
        Route::get('account', [AccountController::class, 'index'])->name('account');

        // User Profile Specific
        Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});
