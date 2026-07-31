<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            $table->float('skor_fuzzy')->nullable()->after('status_verifikasi');
            $table->string('kualitas_fuzzy', 50)->nullable()->after('skor_fuzzy');
        });
    }

    public function down(): void
    {
        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['skor_fuzzy', 'kualitas_fuzzy']);
        });
    }
};
