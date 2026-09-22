<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();

            // Tipo do imóvel
            $table->string('tipo');

            // Endereço completo
            $table->string('logradouro');
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('municipio');
            $table->string('estado', 2);
            $table->string('cep', 9)->nullable();

            // Áreas
            $table->decimal('area_terreno', 12, 2)->nullable();
            $table->decimal('area_construida', 12, 2)->nullable();
            $table->decimal('area_privativa', 12, 2)->nullable();
            $table->decimal('area_comum', 12, 2)->nullable();
            $table->decimal('area_total', 12, 2)->nullable();

            // Registro / identificação
            $table->string('numero_matricula')->nullable();
            $table->string('cartorio_registro')->nullable();
            $table->string('inscricao_imobiliaria')->nullable();
            $table->string('cadastro_municipal')->nullable();
            $table->string('numero_lote')->nullable();
            $table->string('numero_quadra')->nullable();
            $table->string('condominio_nome')->nullable();
            $table->string('bloco')->nullable();
            $table->string('andar')->nullable();
            $table->string('numero_unidade')->nullable();
            $table->unsignedTinyInteger('vagas_garagem')->nullable();
            $table->string('fracao_ideal')->nullable();
            $table->text('confrontacoes')->nullable();
            $table->text('caracteristicas_adicionais')->nullable();

            // Situação jurídica
            $table->string('proprietario_nome')->nullable();
            $table->date('matricula_atualizada_em')->nullable();
            $table->boolean('onus_hipoteca')->default(false);
            $table->boolean('onus_alienacao_fiduciaria')->default(false);
            $table->boolean('onus_penhora')->default(false);
            $table->boolean('onus_usufruto')->default(false);
            $table->boolean('onus_indisponibilidade')->default(false);
            $table->boolean('onus_servidao')->default(false);
            $table->boolean('onus_promessa_compra_venda')->default(false);
            $table->string('outros_direitos_reais')->nullable();
            $table->boolean('existe_acoes')->default(false);
            $table->text('descricao_acoes')->nullable();
            $table->string('situacao_condominio')->nullable();
            $table->string('situacao_tributaria')->nullable();

            // Documentação (checklist)
            $table->boolean('doc_certidao_onus_reais')->default(false);
            $table->boolean('doc_certidao_acoes_reais')->default(false);
            $table->boolean('doc_certidao_negativa_debitos')->default(false);
            $table->boolean('doc_comprovante_inscricao_imobiliaria')->default(false);
            $table->boolean('doc_certidao_quitacao_condominial')->default(false);
            $table->boolean('doc_convencao_condominio')->default(false);
            $table->boolean('doc_planta_aprovacao')->default(false);
            $table->boolean('doc_habite_se')->default(false);
            $table->boolean('doc_ambiental')->default(false);
            $table->boolean('doc_rural')->default(false);
            $table->boolean('doc_ccir')->default(false);
            $table->boolean('doc_car')->default(false);
            $table->boolean('doc_itr')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
