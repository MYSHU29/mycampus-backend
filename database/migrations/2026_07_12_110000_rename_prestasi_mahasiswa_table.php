<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verifikasi_prestasi', function (Blueprint $table) {
            $table->dropForeign(['id_prestasi']);
        });

        Schema::rename('prestasi_mahasiswa', 'prestasi');

        Schema::table('verifikasi_prestasi', function (Blueprint $table) {
            $table->foreign('id_prestasi')
                ->references('id_prestasi')
                ->on('prestasi')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('verifikasi_prestasi', function (Blueprint $table) {
            $table->dropForeign(['id_prestasi']);
        });

        Schema::rename('prestasi', 'prestasi_mahasiswa');

        Schema::table('verifikasi_prestasi', function (Blueprint $table) {
            $table->foreign('id_prestasi')
                ->references('id_prestasi')
                ->on('prestasi_mahasiswa')
                ->cascadeOnDelete();
        });
    }
};
