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
        Schema::create('permissions', function (Blueprint $table) {
    $table->id();                               // PK auto increment
    $table->string('nama_permission', 100);     // Nama izin: mahasiswa.create
    $table->string('modul', 50)->nullable();    // Modul: mahasiswa, laporan
    $table->string('aksi', 50)->nullable();     // Aksi: create, read, update, delete
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
