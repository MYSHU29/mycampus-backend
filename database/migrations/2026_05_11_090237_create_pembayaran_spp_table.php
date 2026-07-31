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
        Schema::create('pembayaran_spp', function (Blueprint $table) {
    $table->id();                                           // PK
    $table->string('nim');                                  // FK ke mahasiswa
    $table->string('kode_bayar')->unique();                 // Kode unik transaksi
    $table->integer('semester');                            // Semester ke berapa
    $table->year('tahun_akademik');                         // Tahun akademik
    $table->decimal('jumlah_bayar', 12, 2);                // Nominal pembayaran
    $table->enum('metode_bayar', ['tunai','transfer','va']); // Metode bayar
    $table->enum('status_bayar', ['lunas','belum','cicil']); // Status bayar
    $table->date('tanggal_bayar')->nullable();              // Tanggal transaksi
    $table->string('bukti_bayar')->nullable();              // Foto bukti bayar
    $table->text('keterangan')->nullable();                 // Keterangan tambahan
    $table->timestamps();

    $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete()->cascadeOnUpdate();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spp');
    }
};
