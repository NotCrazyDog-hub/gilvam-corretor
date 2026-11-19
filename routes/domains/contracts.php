<?php

use App\Domains\Contracts\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('contratos')->name('contracts.')->group(function () {
    Route::get('/', [ContractController::class, 'index'])->name('index');

    Route::get('/novo/pf', [ContractController::class, 'createForPessoaFisica'])->name('create.pf');
    Route::get('/novo/pj', [ContractController::class, 'createForPessoaJuridica'])->name('create.pj');
    Route::get('/novo/{contractType}', [ContractController::class, 'create'])->name('create');

    Route::get('/pessoas-fisicas/busca', [ContractController::class, 'searchPessoasFisicas'])
        ->name('pessoas-fisicas.search');
    Route::get('/pessoas-juridicas/busca', [ContractController::class, 'searchPessoasJuridicas'])
        ->name('pessoas-juridicas.search');

    Route::post('/', [ContractController::class, 'store'])->name('store');

    Route::get('/{group:uuid}/editar', [ContractController::class, 'edit'])->name('edit');
    Route::put('/{group:uuid}', [ContractController::class, 'update'])->name('update');

    Route::get('/{group:uuid}/versoes/{version}', [ContractController::class, 'show'])
        ->whereNumber('version')
        ->name('show');
    Route::get('/{group:uuid}/versoes', [ContractController::class, 'versions'])->name('versions');
    Route::get('/{group:uuid}/versoes/{version}/download', [ContractController::class, 'download'])
        ->whereNumber('version')
        ->name('download');

    Route::get('/imoveis/busca', [ContractController::class, 'searchImoveis'])
        ->name('imoveis.search');
});
