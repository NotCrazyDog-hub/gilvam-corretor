<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;


class Imovel extends Model
{
    protected $table = 'imoveis';
    use HasFactory;

    /**
     * Tipos de imóvel disponíveis. Usado nos <select> das views
     * e para validação no controller.
     */
    public const TIPOS = [
        'casa' => 'Casa',
        'apartamento' => 'Apartamento',
        'terreno' => 'Terreno',
        'sala_comercial' => 'Sala Comercial',
        'loja' => 'Loja',
        'galpao' => 'Galpão',
        'sitio' => 'Sítio',
        'fazenda' => 'Fazenda',
        'chacara' => 'Chácara',
        'outro' => 'Outro',
    ];

    /**
     * Papéis que uma pessoa (física ou jurídica) pode ter em relação
     * a este imóvel. Usado na tabela pivô imoveis_pessoas.
     */
    public const PAPEIS = [
        'proprietario' => 'Proprietário',
        'comprador' => 'Comprador',
        'vendedor' => 'Vendedor',
        'intermediador' => 'Intermediador',
    ];

    protected $fillable = [
        // Tipo
        'tipo',

        // Endereço
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'estado',
        'cep',

        // Áreas
        'area_terreno',
        'area_construida',
        'area_privativa',
        'area_comum',
        'area_total',

        // Registro / identificação
        'numero_matricula',
        'cartorio_registro',
        'inscricao_imobiliaria',
        'cadastro_municipal',
        'numero_lote',
        'numero_quadra',
        'condominio_nome',
        'bloco',
        'andar',
        'numero_unidade',
        'vagas_garagem',
        'fracao_ideal',
        'confrontacoes',
        'caracteristicas_adicionais',

        // Situação jurídica
        'proprietario_nome',
        'matricula_atualizada_em',
        'onus_hipoteca',
        'onus_alienacao_fiduciaria',
        'onus_penhora',
        'onus_usufruto',
        'onus_indisponibilidade',
        'onus_servidao',
        'onus_promessa_compra_venda',
        'outros_direitos_reais',
        'existe_acoes',
        'descricao_acoes',
        'situacao_condominio',
        'situacao_tributaria',

        // Documentação
        'doc_certidao_onus_reais',
        'doc_certidao_acoes_reais',
        'doc_certidao_negativa_debitos',
        'doc_comprovante_inscricao_imobiliaria',
        'doc_certidao_quitacao_condominial',
        'doc_convencao_condominio',
        'doc_planta_aprovacao',
        'doc_habite_se',
        'doc_ambiental',
        'doc_rural',
        'doc_ccir',
        'doc_car',
        'doc_itr',
    ];

    protected $casts = [
        'area_terreno' => 'decimal:2',
        'area_construida' => 'decimal:2',
        'area_privativa' => 'decimal:2',
        'area_comum' => 'decimal:2',
        'area_total' => 'decimal:2',
        'matricula_atualizada_em' => 'date',
        'onus_hipoteca' => 'boolean',
        'onus_alienacao_fiduciaria' => 'boolean',
        'onus_penhora' => 'boolean',
        'onus_usufruto' => 'boolean',
        'onus_indisponibilidade' => 'boolean',
        'onus_servidao' => 'boolean',
        'onus_promessa_compra_venda' => 'boolean',
        'existe_acoes' => 'boolean',
        'doc_certidao_onus_reais' => 'boolean',
        'doc_certidao_acoes_reais' => 'boolean',
        'doc_certidao_negativa_debitos' => 'boolean',
        'doc_comprovante_inscricao_imobiliaria' => 'boolean',
        'doc_certidao_quitacao_condominial' => 'boolean',
        'doc_convencao_condominio' => 'boolean',
        'doc_planta_aprovacao' => 'boolean',
        'doc_habite_se' => 'boolean',
        'doc_ambiental' => 'boolean',
        'doc_rural' => 'boolean',
        'doc_ccir' => 'boolean',
        'doc_car' => 'boolean',
        'doc_itr' => 'boolean',
    ];

    /**
     * Nome legível do tipo (ex: "sala_comercial" -> "Sala Comercial").
     */
    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    /**
     * Todas as fotos do imóvel, na ordem de envio.
     */
    public function imagens(): HasMany
    {
        return $this->hasMany(ImovelImagem::class)->orderBy('ordem');
    }

    /**
     * Foto de capa do imóvel (usada na listagem).
     */
    public function imagemPrincipal(): HasOne
    {
        return $this->hasOne(ImovelImagem::class)->where('principal', true);
    }

    /**
     * Pessoas físicas ligadas a este imóvel (qualquer papel), via imoveis_pessoas.
     */
    public function pessoasFisicas(): MorphToMany
    {
        return $this->morphedByMany(PessoaFisica::class, 'pessoa', 'imoveis_pessoas')
            ->withPivot('papel')
            ->withTimestamps();
    }

    /**
     * Pessoas jurídicas ligadas a este imóvel (qualquer papel), via imoveis_pessoas.
     */
    public function pessoasJuridicas(): MorphToMany
    {
        return $this->morphedByMany(PessoaJuridica::class, 'pessoa', 'imoveis_pessoas')
            ->withPivot('papel')
            ->withTimestamps();
    }

    /**
     * Todas as pessoas (PF + PJ) ligadas a este imóvel, opcionalmente
     * filtradas por papel (ex: 'proprietario'). Junta as duas relações
     * porque o Eloquent não tem uma única relação polimórfica que misture
     * dois models diferentes automaticamente.
     */
    public function pessoas(?string $papel = null): Collection
    {
        $fisicas = $this->pessoasFisicas();
        $juridicas = $this->pessoasJuridicas();

        if ($papel) {
            $fisicas->wherePivot('papel', $papel);
            $juridicas->wherePivot('papel', $papel);
        }

        return $fisicas->get()->merge($juridicas->get());
    }

    /**
     * Atalho para os proprietários (PF e/ou PJ) cadastrados via imoveis_pessoas.
     */
    public function getProprietariosAttribute(): Collection
    {
        return $this->pessoas('proprietario');
    }

    /**
     * Endereço formatado em uma única linha, pronto para exibição/listagem.
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->municipio && $this->estado
                ? "{$this->municipio}/{$this->estado}"
                : ($this->municipio ?: $this->estado),
        ]);

        return implode(', ', $partes);
    }

    /**
     * Indica se algum ônus foi marcado, para exibir um alerta rápido nas telas.
     */
    public function getPossuiOnusAttribute(): bool
    {
        return $this->onus_hipoteca
            || $this->onus_alienacao_fiduciaria
            || $this->onus_penhora
            || $this->onus_usufruto
            || $this->onus_indisponibilidade
            || $this->onus_servidao
            || $this->onus_promessa_compra_venda;
    }
}
