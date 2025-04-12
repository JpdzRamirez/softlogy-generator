<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoftlogyDesk;

//API RESTful.

Route::post('/get-softlogymicro-token', [SoftlogyDesk::class, 'sessionSoftlogyMicroToken'])->name('get.micro.token');

Route::post('/get-BycriptText', [SoftlogyDesk::class, 'makeBycript'])->name('get.Bycript');

Route::post('/get-DecryptText', [SoftlogyDesk::class, 'makeDecrypt'])->name('get.Decrypt');
 
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-softlogydesk-token', [SoftlogyDesk::class, 'sessionSoftlogyDeskToken'])->name('get.desk.token');

    Route::post('/report-status-store', [SoftlogyDesk::class, 'reportStatusStore'])->name('report.status.store');

});
