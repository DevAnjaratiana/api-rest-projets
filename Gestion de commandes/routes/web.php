<?php

use App\Http\Controllers\CommandeController;
use Illuminate\Support\Facades\Route;

// Route pour l'interface web
Route::get('/', [CommandeController::class, 'index']);

// Routes API (préfixées par api/)
Route::get('/api/commandes', [CommandeController::class, 'indexApi']);
Route::post('/api/commandes', [CommandeController::class, 'store']);
Route::get('/api/commandes/{id}', [CommandeController::class, 'show']);
Route::put('/api/commandes/{id}', [CommandeController::class, 'update']);
Route::delete('/api/commandes/{id}', [CommandeController::class, 'destroy']);
