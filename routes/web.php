<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoftlogyMicro;
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
// Default auth middleware route
Route::get('/login', function () {
    return redirect('/'); // Redirige a la vista principal
})->name('login');
/*🔏
-----------------------------------------------
*****************Authentication zone*************
-----------------------------------------------
 */
Route::post('/microservicios/softlogy/login', [PuntosVentaController::class, 'loginOAuth'])->name('login.oauth');

/*📊
-----------------------------------------------
*****************MICRO Services*************
-----------------------------------------------
 */
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [SoftlogyMicro::class, 'index'])->name('dashboard');

    Route::get('/softlogy-tickets', [SoftlogyMicro::class, 'tickets'])->name('softlogy.tickets');    
    
    Route::middleware(['profile:4,5,9,10'])->group(function () {        
        Route::get('/softlogy-tools', [SoftlogyMicro::class, 'tools'])->name('softlogy.tools');
        Route::get('/softlogy-tools-back', [SoftlogyMicro::class, 'backTools'])->name('back.tools');
        Route::get('/descargar-formatos', [BackendController::class, 'descargarFormatoClientes_xlsx'])->name('descargar.formatos');        
        Route::post('/cufes-folios', [FacturaController::class, 'obtenerCUFES_Folios'])->name('obtener.cufes');
        Route::post('/refacturar-xml', [FacturaController::class, 'refacturarXML'])->name('refacturar.xml');
        Route::post('/remplazar-folios', [FacturaController::class, 'remplazarFolios'])->name('remplazar.folios');    
        Route::post('/errores-json', [BackendController::class, 'obtenerErrores_JSON'])->name('obtener.errores');
        Route::post('/cargar-clientes', [BackendController::class, 'cargarClientes_xlsx'])->name('cargar.clientes');       
    });

});