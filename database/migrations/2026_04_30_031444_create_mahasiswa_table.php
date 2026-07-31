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
     Schema::create('mahasiswa', function (Blueprint $table) {
    $table->string('nim', 20)->primary();
    $table->string('nama', 100);
    $table->string('email', 100)->nullable();      // hapus unique
    $table->string('no_telp', 15)->nullable();
    $table->date('tanggal_lahir')->nullable();
    $table->enum('jenis_kelamin', ['L', 'P'])->nullable();  // tambah nullable
    $table->string('kota_asal', 50)->nullable();
    $table->text('alamat')->nullable();
    $table->string('prodi', 50)->nullable();
    $table->string('fakultas', 50)->nullable();
    $table->year('angkatan')->nullable();
    $table->tinyInteger('semester')->nullable();
    $table->decimal('ipk', 3, 2)->nullable();
    $table->enum('status', ['aktif', 'cuti', 'lulus', 'do'])->nullable(); // tambah nullable
    $table->string('foto', 255)->nullable();
    $table->text('catatan')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
