<?php

use App\Domains\Imoveis\Http\Controllers\ImovelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('imoveis/clientes/busca', [ImovelController::class, 'buscarClientes'])
        ->name('imoveis.clientes.busca');

    Route::resource('imoveis', ImovelController::class)
        ->parameters(['imoveis' => 'imovel']);
});