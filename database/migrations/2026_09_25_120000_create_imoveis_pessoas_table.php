<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imoveis_pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();

            // Cria pessoa_id (unsignedBigInteger) + pessoa_type (string) + índice.
            // pessoa_type vai guardar App\Models\PessoaFisica ou App\Models\PessoaJuridica.
            $table->morphs('pessoa');

            // proprietario, comprador, vendedor, intermediador, corretor...
            $table->string('papel');

            $table->timestamps();

            // Evita duplicar a mesma pessoa no mesmo papel, no mesmo imóvel.
            $table->unique(
                ['imovel_id', 'pessoa_id', 'pessoa_type', 'papel'],
                'imoveis_pessoas_unica'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imoveis_pessoas');
    }
};
