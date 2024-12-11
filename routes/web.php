<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;


Route::get('/', function () {
    return view('generator-home');
});

Route::post('/procesar-txt', [FacturaController::class, 'procesarTxt'])->name('procesarTxt');
