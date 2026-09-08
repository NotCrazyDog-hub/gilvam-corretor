<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePessoaJuridicaRequest;
use App\Http\Requests\UpdatePessoaJuridicaRequest;
use App\Models\PessoaJuridica;

class PessoaJuridicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $pessoasJuridicas = PessoaJuridica::latest()->paginate(15);

        return view('pessoas-juridicas.index', compact('pessoasJuridicas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pessoas-juridicas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePessoaJuridicaRequest $request)
{
    $validated = $request->validated();

    PessoaJuridica::create($validated);

    return redirect()
        ->route('pessoas-juridicas.index')
        ->with('success', 'Pessoa jurídica cadastrada com sucesso.');
}

    /**
     * Display the specified resource.
     */
     public function show(PessoaJuridica $pessoas_juridica)
    {
        return view('pessoas-juridicas.show', compact('pessoas_juridica'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PessoaJuridica $pessoas_juridica)
    {
        return view('pessoas-juridicas.edit', compact('pessoas_juridica'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePessoaJuridicaRequest $request, PessoaJuridica $pessoas_juridica)
{
    $validated = $request->validated();

    $pessoas_juridica->update($validated);

    return redirect()
        ->route('pessoas-juridicas.index')
        ->with('success', 'Pessoa jurídica atualizada com sucesso.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PessoaJuridica $pessoas_juridica)
    {
        $pessoas_juridica->delete();

        return redirect()
            ->route('pessoas-juridicas.index')
            ->with('success', 'Pessoa jurídica removida com sucesso.');
    }
}
