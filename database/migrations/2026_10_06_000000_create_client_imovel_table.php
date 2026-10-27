<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_imovel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('papel', 30); // proprietario, comprador, vendedor, intermediador
            $table->timestamps();

            // Mesmo cliente pode ter vários papéis no mesmo imóvel, mas não o mesmo papel duas vezes.
            $table->unique(['imovel_id', 'client_id', 'papel'], 'client_imovel_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_imovel');
    }
};
