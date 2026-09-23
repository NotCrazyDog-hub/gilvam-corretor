<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ImovelController;
use App\Http\Controllers\PessoaFisicaController;
use App\Http\Controllers\PessoaJuridicaController;
use App\Http\Controllers\ProfileController;
use App\Models\Imovel;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalPessoasFisicas' => PessoaFisica::count(),
            'totalPessoasJuridicas' => PessoaJuridica::count(),
            'totalImoveis' => Imovel::count(),
            'ultimasPessoasFisicas' => PessoaFisica::latest()->take(5)->get(),
            'ultimasPessoasJuridicas' => PessoaJuridica::latest()->take(5)->get(),
            'ultimosImoveis' => Imovel::latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    // Cadastros
    Route::resource('pessoas-fisicas', PessoaFisicaController::class);
    Route::resource('pessoas-juridicas', PessoaJuridicaController::class);
    Route::resource('imoveis', ImovelController::class)
        ->parameters(['imoveis' => 'imovel']);

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contratos
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
});

require __DIR__.'/auth.php';
