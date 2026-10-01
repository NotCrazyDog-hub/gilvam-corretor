<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();

            $table->string('contract_type');
            $table->string('slug')->unique();
            $table->string('status')->default('draft');

            $table->timestamps();

            // O Postgres não cria índice automático para FK.
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_groups');
    }
};