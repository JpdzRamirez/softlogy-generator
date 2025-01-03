<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\BackendController;

Route::get('/', [FacturaController::class, 'index'])->name('home');

Route::post('/cufes-folios', [FacturaController::class, 'obtenerCUFES_Folios'])->name('obtener.cufes');
Route::post('/refacturar-xml', [FacturaController::class, 'refacturarXML'])->name('refacturar.xml');
Route::post('/errores-json', [BackendController::class, 'obtenerErrores_JSON'])->name('obtener.errores');
Route::post('/cargar-clientes', [BackendController::class, 'cargarClientes_xlsx'])->name('cargar.clientes');

Route::get('/descargar-formatos', [BackendController::class, 'descargarFormatoClientes_xlsx'])->name('descargar.formatos');