<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\BackendController;

Route::get('/', function () {
    return view('generator-home');
});

Route::post('/cufes-folios', [FacturaController::class, 'obtenerCUFES_Folios'])->name('obtener.cufes');
Route::post('/errores-json', [BackendController::class, 'obtenerErrores_JSON'])->name('obtener.errores');
