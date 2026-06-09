<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [ApiController::class, 'dashboard']);
Route::get('/peta-emisi', [ApiController::class, 'petaEmisi']);
Route::get('/peta-absorpsi', [ApiController::class, 'petaAbsorpsi']);
Route::get('/carbon-trade', [ApiController::class, 'carbonTrade']);
