<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $tipo  = in_array($request->query('tipo'), [Client::TIPO_PF, Client::TIPO_PJ], true)
            ? $request->query('tipo')
            : null;
        $busca = trim((string) $request->query('q', ''));

        $clients = Client::query()
            ->when($tipo, fn ($q) => $q->ofType($tipo))
            ->when(mb_strlen($busca) >= 2, fn ($q) => $q->search($busca))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients', 'tipo', 'busca'));
    }

    public function create(Request $request)
    {
        $tipo = $request->query('tipo', Client::TIPO_PF);
        abort_unless(in_array($tipo, [Client::TIPO_PF, Client::TIPO_PJ], true), 404);

        return view('clients.create', compact('tipo'));
    }

    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        // A FK com contract_groups é RESTRICT: sem esta checagem o banco devolveria erro 500.
        if ($client->contractGroups()->exists()) {
            return redirect()
                ->route('clients.index')
                ->with('error', 'Este cliente possui contratos e não pode ser removido.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente removido com sucesso.');
    }
}