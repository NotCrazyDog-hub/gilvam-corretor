<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePessoaFisicaRequest;
use App\Http\Requests\UpdatePessoaFisicaRequest;
use App\Models\PessoaFisica;

class PessoaFisicaController extends Controller
{
    public function index()
    {
        $pessoasFisicas = PessoaFisica::latest()->paginate(15);

        return view('pessoas-fisicas.index', compact('pessoasFisicas'));
    }

    public function create()
    {
        return view('pessoas-fisicas.create');
    }

    public function store(StorePessoaFisicaRequest $request)
    {
        $validated = $request->validated();

        PessoaFisica::create($validated);

        return redirect()
            ->route('pessoas-fisicas.index')
            ->with('success', 'Pessoa física cadastrada com sucesso.');
    }

    public function show(PessoaFisica $pessoas_fisica)
    {
        return view('pessoas-fisicas.show', compact('pessoas_fisica'));
    }

    public function edit(PessoaFisica $pessoas_fisica)
{
    return view('pessoas-fisicas.edit', [
        'pessoasFisica' => $pessoas_fisica,
    ]);
}

    public function update(UpdatePessoaFisicaRequest $request, PessoaFisica $pessoas_fisica)
    {
        $validated = $request->validated();

        $pessoas_fisica->update($validated);

        return redirect()
            ->route('pessoas-fisicas.index')
            ->with('success', 'Pessoa física atualizada com sucesso.');
    }

    public function destroy(PessoaFisica $pessoas_fisica)
    {
        $pessoas_fisica->delete();

        return redirect()
            ->route('pessoas-fisicas.index')
            ->with('success', 'Pessoa física removida com sucesso.');
    }
}
