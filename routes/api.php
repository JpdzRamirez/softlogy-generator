<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PuntosVentaController;

//API RESTful.
Route::post('/microservicios/softlogy/login', [PuntosVentaController::class, 'loginOAuth'])->name('login.oauth');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/get-tickets', [PuntosVentaController::class, 'loginOAuth'])->name('get.tickets');

});