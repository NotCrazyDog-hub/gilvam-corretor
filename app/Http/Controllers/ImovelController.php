<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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

        $this->salvarImagens($request, $imovel);
        $this->sincronizarPessoas($request, $imovel);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Imóvel cadastrado com sucesso.');
    }

    public function show(Imovel $imovel): View
    {
        $imovel->load('imagens');

        return view('imoveis.show', compact('imovel'));
    }

    public function edit(Imovel $imovel): View
    {
        $imovel->load(['imagens', 'pessoasFisicas', 'pessoasJuridicas']);

        return view('imoveis.edit', [
            'imovel' => $imovel,
            'tipos' => Imovel::TIPOS,
        ]);
    }

    public function update(Request $request, Imovel $imovel): RedirectResponse
    {
        $validated = $this->validarDados($request, $imovel->id);

        $imovel->update($validated);

        $this->removerImagens($request, $imovel);
        $this->salvarImagens($request, $imovel);
        $this->sincronizarPessoas($request, $imovel);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $imovel): RedirectResponse
    {
        Storage::disk('public')->deleteDirectory("imoveis/{$imovel->id}");

        $imovel->delete();

        return redirect()
            ->route('imoveis.index')
            ->with('sucesso', 'Imóvel removido com sucesso.');
    }

    /**
     * Salva as novas imagens enviadas no formulário.
     * A primeira imagem do imóvel vira a capa (principal).
     */
    private function salvarImagens(Request $request, Imovel $imovel): void
    {
        if (!$request->hasFile('imagens')) {
            return;
        }

        $temPrincipal = $imovel->imagens()->where('principal', true)->exists();
        $ordem = (int) $imovel->imagens()->max('ordem');

        foreach ($request->file('imagens') as $arquivo) {
            $caminho = $arquivo->store("imoveis/{$imovel->id}", 'public');

            $imovel->imagens()->create([
                'caminho' => $caminho,
                'principal' => !$temPrincipal,
                'ordem' => ++$ordem,
            ]);

            $temPrincipal = true;
        }
    }

    /**
     * Remove as imagens marcadas com "Remover" na tela de edição.
     * Se a capa for removida, a primeira que sobrar assume.
     */
    private function removerImagens(Request $request, Imovel $imovel): void
    {
        $ids = $request->input('remover_imagens', []);

        if (empty($ids)) {
            return;
        }

        $imovel->imagens()->whereIn('id', $ids)->get()->each(function ($imagem) {
            Storage::disk('public')->delete($imagem->caminho);
            $imagem->delete();
        });

        if (!$imovel->imagens()->where('principal', true)->exists()) {
            $imovel->imagens()->orderBy('ordem')->first()?->update(['principal' => true]);
        }
    }

    /**
     * Recria os vínculos de pessoas (físicas e jurídicas) deste imóvel
     * a partir do que veio do formulário. Simplesmente apaga tudo e
     * recria, porque "papel" faz parte da chave única da pivô e o
     * conjunto de papéis de cada pessoa pode mudar inteiro a cada envio.
     *
     * Espera um array assim, vindo do componente Alpine do formulário:
     * pessoas[0][tipo] = 'fisica'|'juridica'
     * pessoas[0][id] = 5
     * pessoas[0][papeis][] = 'proprietario'
     * pessoas[0][papeis][] = 'intermediador'
     */
    private function sincronizarPessoas(Request $request, Imovel $imovel): void
    {
        $imovel->pessoasFisicas()->detach();
        $imovel->pessoasJuridicas()->detach();

        // Agrupa por pessoa (tipo + id) e junta os papéis antes de anexar,
        // pra nunca tentar inserir a mesma combinação imovel+pessoa+papel
        // duas vezes (isso violaria a constraint única imoveis_pessoas_unica).
        $pessoas = collect($request->input('pessoas', []))
            ->filter(fn ($pessoa) => !empty($pessoa['id']) && !empty($pessoa['tipo']))
            ->groupBy(fn ($pessoa) => $pessoa['tipo'] . '-' . $pessoa['id'])
            ->map(function ($grupo) {
                $primeiro = $grupo->first();

                $papeis = $grupo
                    ->flatMap(fn ($pessoa) => $pessoa['papeis'] ?? [])
                    ->unique()
                    ->intersect(array_keys(Imovel::PAPEIS))
                    ->values();

                return [
                    'id' => $primeiro['id'],
                    'tipo' => $primeiro['tipo'],
                    'papeis' => $papeis,
                ];
            });

        foreach ($pessoas as $pessoa) {
            foreach ($pessoa['papeis'] as $papel) {
                if ($pessoa['tipo'] === 'fisica') {
                    $imovel->pessoasFisicas()->attach($pessoa['id'], ['papel' => $papel]);
                } elseif ($pessoa['tipo'] === 'juridica') {
                    $imovel->pessoasJuridicas()->attach($pessoa['id'], ['papel' => $papel]);
                }
            }
        }
    }

    private function validarDados(Request $request, ?int $ignorarId = null): array
    {
        $validated = $request->validate([
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

            // Imagens
            'imagens' => ['nullable', 'array', 'max:10'],
            'imagens.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            // Pessoas vinculadas (proprietário, comprador, vendedor, intermediador...)
            'pessoas' => ['nullable', 'array'],
            'pessoas.*.tipo' => ['required_with:pessoas', 'in:fisica,juridica'],
            'pessoas.*.id' => [
                'required_with:pessoas',
                'integer',
                function (string $attribute, $value, \Closure $fail) use ($request) {
                    // Extrai o índice do item a partir do nome do campo (ex: "pessoas.2.id" -> 2)
                    preg_match('/^pessoas\.(\d+)\.id$/', $attribute, $partes);
                    $indice = $partes[1] ?? null;
                    $tipo = $request->input("pessoas.{$indice}.tipo");

                    $existe = match ($tipo) {
                        'fisica' => \App\Models\PessoaFisica::whereKey($value)->exists(),
                        'juridica' => \App\Models\PessoaJuridica::whereKey($value)->exists(),
                        default => false,
                    };

                    if (!$existe) {
                        $fail('Uma das pessoas selecionadas não foi encontrada.');
                    }
                },
            ],
            'pessoas.*.papeis' => ['nullable', 'array'],
            'pessoas.*.papeis.*' => ['in:' . implode(',', array_keys(Imovel::PAPEIS))],

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

        // Imagens e pessoas não são colunas da tabela "imoveis": são
        // salvas à parte em salvarImagens() e sincronizarPessoas().
        unset($validated['imagens']);
        unset($validated['pessoas']);

        return $validated;
    }
}
