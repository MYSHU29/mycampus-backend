<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            $table->string('kode_prestasi', 20)->nullable();
        });

        $prestasiMahasiswa = DB::table('prestasi_mahasiswa')
            ->orderByDesc('created_at')
            ->orderByDesc('id_prestasi')
            ->get(['id_prestasi']);

        foreach ($prestasiMahasiswa as $index => $prestasi) {
            DB::table('prestasi_mahasiswa')
                ->where('id_prestasi', $prestasi->id_prestasi)
                ->update(['kode_prestasi' => sprintf('PRESM-%03d', $index + 1)]);
        }

        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            $table->unique('kode_prestasi');
        });

        Schema::create('prestasi_kode_sequences', function (Blueprint $table) {
            $table->string('nama_sequence', 50)->primary();
            $table->unsignedBigInteger('nomor_terakhir')->default(0);
            $table->timestamps();
        });

        DB::table('prestasi_kode_sequences')->insert([
            'nama_sequence' => 'prestasi',
            'nomor_terakhir' => $prestasiMahasiswa->count(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_kode_sequences');

        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            $table->dropUnique(['kode_prestasi']);
            $table->dropColumn('kode_prestasi');
        });
    }
};
