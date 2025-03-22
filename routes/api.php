<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoftlogyDesk;

//API RESTful.

Route::get('/get-token', [SoftlogyDesk::class, 'authToken'])->name('get.change');
    
