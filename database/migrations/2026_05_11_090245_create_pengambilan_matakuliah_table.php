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
        Schema::create('pengambilan_matakuliah', function (Blueprint $table) {
    $table->id();                                              // PK
    $table->string('nim');                                     // FK ke mahasiswa
    $table->string('kode_matkul');                             // Kode mata kuliah
    $table->string('nama_matkul');                             // Nama mata kuliah
    $table->integer('sks');                                    // Jumlah SKS
    $table->string('dosen');                                   // Nama dosen pengampu
    $table->integer('semester');                               // Semester pengambilan
    $table->year('tahun_akademik');                            // Tahun akademik
    $table->enum('status', ['aktif','mengulang','lulus']);     // Status pengambilan
    $table->decimal('nilai_akhir', 4, 2)->nullable();          // Nilai 0.00 - 100.00
    $table->enum('grade', ['A','B+','B','C+','C','D','E'])->nullable(); // Grade nilai
    $table->timestamps();

    $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete()->cascadeOnUpdate();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengambilan_matakuliah');
    }
};
