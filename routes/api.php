<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PuntosVentaController;

//API RESTful.
Route::post('/microservicios/softlogy/login', [PuntosVentaController::class, 'login'])->name('login');