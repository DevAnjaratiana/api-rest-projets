<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::post('/clients', [ClientController::class, 'store']);
Route::get('/clients', [ClientController::class, 'indexApi']);
Route::get('/clients/{id}', [ClientController::class, 'show']);
Route::put('/clients/{id}', [ClientController::class, 'update']);
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
