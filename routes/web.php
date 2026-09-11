<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PessoaFisicaController;
use App\Http\Controllers\PessoaJuridicaController;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('pessoas-fisicas', PessoaFisicaController::class);
    Route::resource('pessoas-juridicas', PessoaJuridicaController::class);
});


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'totalPessoasFisicas' => PessoaFisica::count(),
        'totalPessoasJuridicas' => PessoaJuridica::count(),
        'ultimasPessoasFisicas' => PessoaFisica::latest()->take(5)->get(),
        'ultimasPessoasJuridicas' => PessoaJuridica::latest()->take(5)->get(),
    ]);
})->middleware(['auth',])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('contracts')->name('contracts.')->group(function () {
    Route::get('/', [ContractController::class, 'index'])->name('index');

    Route::get('/create', [ContractController::class, 'chooseClientType'])->name('create');
    Route::get('/create/pf', [ContractController::class, 'createForPessoaFisica'])->name('create.pf');
    Route::get('/create/pj', [ContractController::class, 'createForPessoaJuridica'])->name('create.pj');

    Route::get('/pessoas-fisicas/search', [ContractController::class, 'searchPessoasFisicas'])
        ->name('pessoas-fisicas.search');

    Route::post('/', [ContractController::class, 'store'])->name('store');

    Route::get('/{group:uuid}/edit', [ContractController::class, 'edit'])->name('edit');
    Route::put('/{group:uuid}', [ContractController::class, 'update'])->name('update');

    Route::get('/{group:uuid}/versions', [ContractController::class, 'versions'])->name('versions');
    Route::get('/{group:uuid}/versions/{version}/download', [ContractController::class, 'download'])
        ->whereNumber('version')->name('download');
});

require __DIR__.'/auth.php';