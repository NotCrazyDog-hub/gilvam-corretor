<?php

use App\Domains\Imoveis\Http\Controllers\ImovelController;
use Illuminate\Support\Facades\Route;
use App\Domains\Imoveis\Http\Controllers\ImovelPdfController;

Route::get('imoveis/{imovel}/pdf', ImovelPdfController::class)->name('imoveis.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('imoveis/clientes/busca', [ImovelController::class, 'buscarClientes'])
        ->name('imoveis.clientes.busca');

    Route::get('imoveis/{imovel}/pdf', ImovelPdfController::class)->name('imoveis.pdf');

    Route::resource('imoveis', ImovelController::class)
        ->parameters(['imoveis' => 'imovel']);
});
