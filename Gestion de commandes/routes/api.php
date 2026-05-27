<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommandeController;

Route::get('/test', function() {
    return response()->json(['message' => 'GET works!']);
});

Route::post('/test-post', function() {
    return response()->json(['message' => 'POST works!', 'data' => request()->all()]);
});


Route::post('/commandes', [CommandeController::class, 'store']);
Route::get('/commandes', [CommandeController::class, 'indexApi']);
Route::get('/commandes/{id}', [CommandeController::class, 'show']);
Route::put('/commandes/{id}', [CommandeController::class, 'update']);
Route::delete('/commandes/{id}', [CommandeController::class, 'destroy']);
