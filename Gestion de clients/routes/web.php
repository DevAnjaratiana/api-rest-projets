<?php

use App\Http\Controllers\ClientController;

Route::get('/', [ClientController::class, 'index']);
