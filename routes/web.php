<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('contracts')->name('contracts.')->group(function () {
    Route::get('/', [ContractController::class, 'index'])->name('index');
    Route::get('/create', [ContractController::class, 'create'])->name('create');
    Route::post('/', [ContractController::class, 'store'])->name('store');

    Route::get('/{group:uuid}/edit', [ContractController::class, 'edit'])->name('edit');
    Route::put('/{group:uuid}', [ContractController::class, 'update'])->name('update');

    Route::get('/{group:uuid}/versions', [ContractController::class, 'versions'])->name('versions');
    Route::get('/{group:uuid}/versions/{version}/download', [ContractController::class, 'download'])
        ->whereNumber('version')
        ->name('download');
});