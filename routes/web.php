<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\BackendController;

use App\Http\Controllers\PuntosVentaController;

/*🏠
-----------------------------------------------
*****************Ruta default-home*************
-----------------------------------------------
 */
Route::get('/', function () {
    return view('auth-login');
})->name('home');

Route::post('/microservicios/softlogy/login', [PuntosVentaController::class, 'loginOAuth'])->name('login.oauth');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [FacturaController::class, 'index'])->name('dashboard');

    Route::post('/cufes-folios', [FacturaController::class, 'obtenerCUFES_Folios'])->name('obtener.cufes');
    Route::post('/refacturar-xml', [FacturaController::class, 'refacturarXML'])->name('refacturar.xml');
    Route::post('/errores-json', [BackendController::class, 'obtenerErrores_JSON'])->name('obtener.errores');
    Route::post('/cargar-clientes', [BackendController::class, 'cargarClientes_xlsx'])->name('cargar.clientes');

    Route::get('/descargar-formatos', [BackendController::class, 'descargarFormatoClientes_xlsx'])->name('descargar.formatos');
});