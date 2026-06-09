<?php

use App\Http\Controllers\CarbonTradeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/peta-emisi', [PetaController::class, 'emisi'])->name('peta-emisi');
Route::get('/peta-absorpsi', [PetaController::class, 'absorpsi'])->name('peta-absorpsi');
Route::get('/carbon-trade', [CarbonTradeController::class, 'index'])->name('carbon-trade');
