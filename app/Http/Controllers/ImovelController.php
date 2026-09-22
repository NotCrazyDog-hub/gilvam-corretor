<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ImovelController extends Controller
{
    public function index(Request $request): View
    {
        $imoveis = Imovel::query()
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = $request->input('busca');
                $query->where(function ($q) use ($busca) {
                    $q->where('logradouro', 'like', "%{$busca}%")
                        ->orWhere('bairro', 'like', "%{$busca}%")
                        ->orWhere('municipio', 'like', "%{$busca}%")
                        ->orWhere('numero_matricula', 'like', "%{$busca}%")
                        ->orWhere('proprietario_nome', 'like', "%{$busca}%");
                });
            })
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->input('tipo')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('imoveis.index', [
            'imoveis' => $imoveis,
            'tipos' => Imovel::TIPOS,
        ]);
    }

    public function create(): View
    {
        return view('imoveis.create', [
            'tipos' => Imovel::TIPOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validarDados($request);

        $imovel = Imovel::create($validated);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Imóvel cadastrado com sucesso.');
    }

    public function show(Imovel $imovel): View
    {
        return view('imoveis.show', compact('imovel'));
    }

    public function edit(Imovel $imovel): View
    {
        return view('imoveis.edit', [
            'imovel' => $imovel,
            'tipos' => Imovel::TIPOS,
        ]);
    }

    public function update(Request $request, Imovel $imovel): RedirectResponse
    {
        $validated = $this->validarDados($request, $imovel->id);

        $imovel->update($validated);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $imovel): RedirectResponse
    {
        $imovel->delete();

        return redirect()
            ->route('imoveis.index')
            ->with('sucesso', 'Imóvel removido com sucesso.');
    }

    private function validarDados(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            // Tipo
            'tipo' => ['required', 'string', 'in:' . implode(',', array_keys(Imovel::TIPOS))],

            // Endereço
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:9'],

            // Áreas
            'area_terreno' => ['nullable', 'numeric', 'min:0'],
            'area_construida' => ['nullable', 'numeric', 'min:0'],
            'area_privativa' => ['nullable', 'numeric', 'min:0'],
            'area_comum' => ['nullable', 'numeric', 'min:0'],
            'area_total' => ['nullable', 'numeric', 'min:0'],

            // Registro / identificação
            'numero_matricula' => ['nullable', 'string', 'max:255'],
            'cartorio_registro' => ['nullable', 'string', 'max:255'],
            'inscricao_imobiliaria' => ['nullable', 'string', 'max:255'],
            'cadastro_municipal' => ['nullable', 'string', 'max:255'],
            'numero_lote' => ['nullable', 'string', 'max:100'],
            'numero_quadra' => ['nullable', 'string', 'max:100'],
            'condominio_nome' => ['nullable', 'string', 'max:255'],
            'bloco' => ['nullable', 'string', 'max:50'],
            'andar' => ['nullable', 'string', 'max:50'],
            'numero_unidade' => ['nullable', 'string', 'max:50'],
            'vagas_garagem' => ['nullable', 'integer', 'min:0'],
            'fracao_ideal' => ['nullable', 'string', 'max:50'],
            'confrontacoes' => ['nullable', 'string'],
            'caracteristicas_adicionais' => ['nullable', 'string'],

            // Situação jurídica
            'proprietario_nome' => ['nullable', 'string', 'max:255'],
            'matricula_atualizada_em' => ['nullable', 'date'],
            'onus_hipoteca' => ['boolean'],
            'onus_alienacao_fiduciaria' => ['boolean'],
            'onus_penhora' => ['boolean'],
            'onus_usufruto' => ['boolean'],
            'onus_indisponibilidade' => ['boolean'],
            'onus_servidao' => ['boolean'],
            'onus_promessa_compra_venda' => ['boolean'],
            'outros_direitos_reais' => ['nullable', 'string', 'max:255'],
            'existe_acoes' => ['boolean'],
            'descricao_acoes' => ['nullable', 'string'],
            'situacao_condominio' => ['nullable', 'string', 'max:255'],
            'situacao_tributaria' => ['nullable', 'string', 'max:255'],

            // Documentação
            'doc_certidao_onus_reais' => ['boolean'],
            'doc_certidao_acoes_reais' => ['boolean'],
            'doc_certidao_negativa_debitos' => ['boolean'],
            'doc_comprovante_inscricao_imobiliaria' => ['boolean'],
            'doc_certidao_quitacao_condominial' => ['boolean'],
            'doc_convencao_condominio' => ['boolean'],
            'doc_planta_aprovacao' => ['boolean'],
            'doc_habite_se' => ['boolean'],
            'doc_ambiental' => ['boolean'],
            'doc_rural' => ['boolean'],
            'doc_ccir' => ['boolean'],
            'doc_car' => ['boolean'],
            'doc_itr' => ['boolean'],
        ]);
    }
}
