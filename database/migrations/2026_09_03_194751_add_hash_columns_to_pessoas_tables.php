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
    Schema::table('pessoas_fisicas', function (Blueprint $table) {
        $table->string('cpf_hash', 64)->nullable()->unique()->after('cpf');
        $table->string('rg_hash', 64)->nullable()->after('rg');
    });

    Schema::table('pessoas_juridicas', function (Blueprint $table) {
        $table->string('representante_legal_cpf_hash', 64)->nullable()->after('representante_legal_cpf');
    });
}

public function down(): void
{
    Schema::table('pessoas_fisicas', function (Blueprint $table) {
        $table->dropColumn(['cpf_hash', 'rg_hash']);
    });

    Schema::table('pessoas_juridicas', function (Blueprint $table) {
        $table->dropColumn('representante_legal_cpf_hash');
    });
}
};
