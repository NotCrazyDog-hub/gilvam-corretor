<?php

use App\Domains\Admin\Http\Controllers\UserController;
use App\Domains\Contracts\Http\Controllers\ContractController;
use App\Domains\Clients\Http\Controllers\ClientController;
use App\Domains\Imoveis\Http\Controllers\ImovelController;
use App\Domains\Profile\Http\Controllers\ProfileController;
use App\Domains\Clients\Models\Client;
use App\Domains\Imoveis\Models\Imovel;
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
    // URL em português, mas nomes (clients.*) e parâmetro ({client}) preservados
    Route::resource('clientes', ClientController::class)
        ->names('clients')
        ->parameters(['clientes' => 'client']);

    // Busca de clientes usada no cadastro de imóveis (vem da branch map).
    // Precisa ficar ANTES do resource de imoveis para não ser capturada por imoveis/{imovel}
    Route::get('imoveis/clientes/busca', [ImovelController::class, 'buscarClientes'])
        ->name('imoveis.clientes.busca');

    Route::resource('imoveis', ImovelController::class)
        ->parameters(['imoveis' => 'imovel']);

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contratos
    Route::prefix('contratos')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');

        // URLs antigas continuam válidas para links salvos.
        Route::get('/novo/pf', [ContractController::class, 'createForPessoaFisica'])->name('create.pf');
        Route::get('/novo/pj', [ContractController::class, 'createForPessoaJuridica'])->name('create.pj');
        Route::get('/novo/{contractType}', [ContractController::class, 'create'])->name('create');

        // Mesmos nomes de antes; só a URL mudou
        Route::get('/pessoas-fisicas/busca', [ContractController::class, 'searchPessoasFisicas'])
            ->name('pessoas-fisicas.search');
        Route::get('/pessoas-juridicas/busca', [ContractController::class, 'searchPessoasJuridicas'])
            ->name('pessoas-juridicas.search');

        Route::post('/', [ContractController::class, 'store'])->name('store');

        Route::get('/{group:uuid}/editar', [ContractController::class, 'edit'])->name('edit');
        Route::put('/{group:uuid}', [ContractController::class, 'update'])->name('update');

        Route::get('/{group:uuid}/versoes', [ContractController::class, 'versions'])->name('versions');
        Route::get('/{group:uuid}/versoes/{version}/download', [ContractController::class, 'download'])
            ->whereNumber('version')->name('download');

        Route::get('/imoveis/busca', [ContractController::class, 'searchImoveis'])->name('imoveis.search');
    });
});

// Área do administrador: só quem tem role = admin entra.
// É aqui que fica o cadastro de usuários (não existe mais cadastro público).
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('usuarios', UserController::class)
        ->only(['index', 'create', 'store', 'edit', 'update'])
        ->parameters(['usuarios' => 'user'])
        ->names('users');
});

require __DIR__.'/auth.php';
