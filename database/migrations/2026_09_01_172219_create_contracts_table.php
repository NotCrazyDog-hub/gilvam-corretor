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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');

            $table->json('content_json');      
            $table->longText('content_html')->nullable(); 

            $table->json('contratante');       
            $table->json('contratado');
            $table->json('imovel');

            $table->string('file_path')->nullable();  
            $table->string('file_format')->default('docx'); 
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['contract_group_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
