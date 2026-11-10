<?php

use App\Domains\Clients\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('clientes', ClientController::class)
        ->names('clients')
        ->parameters(['clientes' => 'client']);
});