<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Clients\Models\Client;
use App\Domains\Imoveis\Models\Imovel;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
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
});

foreach ([
    'admin',
    'clients',
    'contracts',
    'imoveis',
    'profile',
] as $domain) {
    require __DIR__."/domains/{$domain}.php";
}
require __DIR__ . '/auth.php';