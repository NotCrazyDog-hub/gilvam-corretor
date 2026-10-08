<?php

use App\Http\Controllers\{
    ClientController,
    ContractController,
    ImovelController,
    ProfileController
};
use App\Models\{
    Client,
    Imovel
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalPessoasFisicas' => Client::ofType(Client::TIPO_PF)->count(),
            'totalPessoasJuridicas' => Client::ofType(Client::TIPO_PJ)->count(),
            'totalImoveis' => Imovel::count(),
            'ultimasPessoasFisicas' => Client::ofType(Client::TIPO_PF)->latest()->take(5)->get(),
            'ultimasPessoasJuridicas' => Client::ofType(Client::TIPO_PJ)->latest()->take(5)->get(),
            'ultimosImoveis' => Imovel::latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    // Cadastros
    Route::resource('clients', ClientController::class);
    Route::get('imoveis/clientes/busca', [ImovelController::class, 'buscarClientes'])
       ->name('imoveis.clientes.busca');
    Route::resource('imoveis', ImovelController::class)
        ->parameters(['imoveis' => 'imovel']);


    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contratos
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');

        Route::get('/create/pf', [ContractController::class, 'createForPessoaFisica'])->name('create.pf');
        Route::get('/create/pj', [ContractController::class, 'createForPessoaJuridica'])->name('create.pj');

        // Mesmas URLs/nomes de antes, para não mexer no JS do formulário
        Route::get('/pessoas-fisicas/search', [ContractController::class, 'searchPessoasFisicas'])
            ->name('pessoas-fisicas.search');
        Route::get('/pessoas-juridicas/search', [ContractController::class, 'searchPessoasJuridicas'])
            ->name('pessoas-juridicas.search');

        Route::post('/', [ContractController::class, 'store'])->name('store');

        Route::get('/{group:uuid}/edit', [ContractController::class, 'edit'])->name('edit');
        Route::put('/{group:uuid}', [ContractController::class, 'update'])->name('update');

        Route::get('/{group:uuid}/versions', [ContractController::class, 'versions'])->name('versions');
        Route::get('/{group:uuid}/versions/{version}/download', [ContractController::class, 'download'])
            ->whereNumber('version')->name('download');
    });
});

require __DIR__.'/auth.php';
