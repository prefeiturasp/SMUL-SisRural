<?php

use App\Http\Controllers\Backend\DashboardController;

Route::redirect('/', '/admin/dashboard', 301);

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
