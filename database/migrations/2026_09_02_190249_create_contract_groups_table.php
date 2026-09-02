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
        Schema::create('contract_groups', function (Blueprint $table) {
            $table->id();
             $table->uuid('uuid')->unique();
 
            $table->string('client_type', 2)->nullable(); 
            $table->unsignedBigInteger('client_id')->nullable();
 
            $table->string('contract_type'); 
            $table->string('slug')->unique();
            $table->string('status')->default('draft'); 
 
            $table->timestamps();
 
            $table->index(['client_type', 'client_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_groups');
    }
};
