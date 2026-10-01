<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 2);                       // pf | pj
            $table->string('nome');                          // nome (PF) ou razão social (PJ)
            $table->string('nome_fantasia')->nullable();
            $table->string('endereco');

            // PF
            $table->text('cpf')->nullable();                 // criptografado
            $table->string('cpf_hash', 64)->nullable()->unique();
            $table->text('rg')->nullable();                  // criptografado
            $table->string('rg_hash', 64)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('conjuge_nome')->nullable();
            $table->text('conjuge_rg')->nullable();          // criptografado
            $table->string('nacionalidade')->nullable();
            $table->string('profissao')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telefone_secundario')->nullable();
            $table->string('email')->nullable();

            // PJ
            $table->string('cnpj', 14)->nullable()->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('representante_legal_nome')->nullable();
            $table->text('representante_legal_cpf')->nullable(); // criptografado
            $table->string('representante_legal_cpf_hash', 64)->nullable();
            $table->string('representante_legal_cargo')->nullable();

            // Temporárias: mapeiam o ID antigo -> novo. Removidas na migration 4.
            $table->string('legacy_type', 2)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->unique(['legacy_type', 'legacy_id']);

            $table->timestamps();
            $table->index('tipo');
        });

        // O Laravel acessa como dono da tabela (ignora RLS); isso só bloqueia a API pública do Supabase.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE clients ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};