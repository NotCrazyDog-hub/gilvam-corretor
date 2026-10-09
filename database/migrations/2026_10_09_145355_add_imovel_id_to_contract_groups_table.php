<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_groups', function (Blueprint $table) {
            // Imóvel que originou o contrato. Nullable para não quebrar os contratos
            // já existentes; nullOnDelete porque o contrato guarda um snapshot do
            // imóvel em JSON e não deve ser afetado se o imóvel for removido.
            $table->foreignId('imovel_id')
                ->nullable()
                ->after('client_id')
                ->constrained('imoveis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contract_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('imovel_id');
        });
    }
};