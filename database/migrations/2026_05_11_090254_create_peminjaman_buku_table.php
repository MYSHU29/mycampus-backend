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
        Schema::create('peminjaman_buku', function (Blueprint $table) {
    $table->id();                                               // PK
    $table->string('nim');                                      // FK ke mahasiswa
    $table->string('kode_buku');                                // Kode buku
    $table->string('judul_buku');                               // Judul buku
    $table->string('pengarang');                                // Pengarang buku
    $table->date('tanggal_pinjam');                             // Tanggal pinjam
    $table->date('tanggal_kembali_rencana');                    // Batas pengembalian
    $table->date('tanggal_kembali_aktual')->nullable();         // Tanggal dikembalikan
    $table->enum('status', ['dipinjam','dikembalikan','terlambat']); // Status pinjam
    $table->integer('denda')->default(0);                       // Denda keterlambatan
    $table->text('keterangan')->nullable();                     // Keterangan
    $table->timestamps();

    $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete()->cascadeOnUpdate();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_buku');
    }
};
