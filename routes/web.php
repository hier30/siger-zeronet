<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarbonDataController;
use App\Http\Controllers\CarbonTradeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SertifikatKarbonController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/peta-emisi', [PetaController::class, 'emisi'])->name('peta-emisi');
Route::get('/peta-absorpsi', [PetaController::class, 'absorpsi'])->name('peta-absorpsi');
Route::get('/carbon-trade', [CarbonTradeController::class, 'index'])->name('carbon-trade');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::resource('data-carbon', CarbonDataController::class)
        ->except(['show'])
        ->parameters(['data-carbon' => 'carbonData']);

    Route::resource('sertifikat-karbon', SertifikatKarbonController::class)
        ->except(['show'])
        ->parameters(['sertifikat-karbon' => 'sertifikatKarbon']);
});
